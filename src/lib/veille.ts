// Relais VeilleFacile, côté serveur uniquement : la clé ne doit jamais atteindre le navigateur
// (docs/adr/0001-relais-php-vers-veillefacile.md). Ne pas importer depuis un script client.
import { createHash } from 'node:crypto';
import { readFileSync, renameSync, writeFileSync } from 'node:fs';
import { readFile, rename, writeFile } from 'node:fs/promises';
import { tmpdir } from 'node:os';
import { join } from 'node:path';
import MarkdownIt from 'markdown-it';
import sanitizeHtml from 'sanitize-html';
import fixture from './veille_fixture.json';

// `astro dev` et `npm start` ne chargent pas `.env` ; les variables déjà définies restent prioritaires.
try { process.loadEnvFile(); } catch { /* pas de .env : environnement réel ou données de secours */ }

const TTL = 10 * 60_000;
/** Après un échec sans aucun cache : délai court, pour ne pas prolonger une panne brève. */
const RETRY = 60_000;
const THUMB_TTL = 30 * 86_400_000;
const SECTIONS: Record<string, string> = {
  'What it is': "Ce que c'est",
  'Why it matters': "Pourquoi c'est important",
  'Key points': 'Points clés',
};

/** Source telle que renvoyée par l'API VeilleFacile (champs utilisés). */
interface RawSource {
  id: number;
  url: string;
  canonical_url?: string | null;
  origin_site: string;
  title: string;
  summary?: string | null;
  keywords: string[];
  published_at?: string | null;
  created_at: string;
}

/** Source publique au format d'affichage. `summary_html` est déjà nettoyé. */
export interface Source {
  id: number;
  url: string;
  canonical_url: string;
  title: string;
  site: string;
  format: 'Vidéo' | 'Code' | 'Modèle' | 'Article';
  date: string;
  date_kind: 'parution' | 'capture';
  keywords: string[];
  excerpt: string;
  summary_html: string;
  thumb: string | null;
}

export interface SourceSet {
  data: Source[];
  /** Instant des données, en millisecondes. */
  at: number;
  /** Vrai si l'API est injoignable (ancien cache) ou non configurée (données de secours). */
  stale: boolean;
}

export interface ExperimentLink {
  slug: string;
  source_url?: string;
}

const experimentMetas = import.meta.glob<Omit<ExperimentLink, 'slug'>>('/public/experimentations/*/meta.json', {
  eager: true,
  import: 'default',
});
export const builtExperiments: ExperimentLink[] = Object.entries(experimentMetas).map(([file, meta]) => ({
  ...meta,
  slug: file.split('/').at(-2)!,
}));

/** Clé de comparaison entre l'URL d'une Source et le `source_url` d'une Expérimentation. */
export const normUrl = (url: string) => url.replace(/^https?:\/\/(www\.)?/, '').replace(/\/+$/, '');

// `html: false` : le HTML des résumés (générés depuis des pages externes) est échappé, pas interprété.
const markdown = new MarkdownIt({ html: false, linkify: true });
markdown.linkify.set({ fuzzyLink: false, fuzzyEmail: false });

// Seconde barrière : seules les balises que produit le markdown passent.
const renderSummary = (text: string) =>
  sanitizeHtml(markdown.render(text), {
    allowedTags: [
      'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'br', 'hr', 'a', 'img', 'ul', 'ol', 'li', 'blockquote',
      'pre', 'code', 'em', 'strong', 's', 'table', 'thead', 'tbody', 'tr', 'th', 'td',
    ],
    allowedAttributes: {
      a: ['href', 'title'], img: ['src', 'alt', 'title'], ol: ['start'], code: ['class'], th: ['style'], td: ['style'],
    },
    allowedStyles: { '*': { 'text-align': [/^(left|center|right)$/] } },
    allowedSchemes: ['http', 'https', 'mailto'],
    allowProtocolRelative: false,
  }).trim();

function sections(summary: string): Map<string, string> {
  const out = new Map<string, string>();
  for (const chunk of summary.split(/^##\s+/m)) {
    const [head = '', ...body] = chunk.split('\n');
    const title = SECTIONS[head.trim()];
    if (title) out.set(title, body.join('\n').trim());
  }
  return out;
}

function formatOf(site: string): Source['format'] {
  if (site.includes('youtube.com') || site === 'youtu.be') return 'Vidéo';
  if (site === 'github.com') return 'Code';
  if (site === 'huggingface.co') return 'Modèle';
  return 'Article';
}

const youtubeId = (url: string) => url.match(/(?:[?&]v=|youtu\.be\/|\/shorts\/|\/embed\/)([\w-]{11})/)?.[1];

const isPrivate = (s: RawSource) => s.keywords.some(k => k.normalize('NFC').trim().toLowerCase() === 'privé');

/** Format API -> format d'affichage ; exclut les Sources privées avant tout cache et toute réponse. */
const prepare = (raw: RawSource[]): Source[] =>
  raw.filter(s => !isPrivate(s)).map(s => {
    const parts = sections(s.summary ?? '');
    const video = youtubeId(s.url);
    return {
      id: s.id,
      url: s.url,
      canonical_url: s.canonical_url ?? s.url,
      title: s.title,
      site: s.origin_site.replace(/^www\./, ''),
      format: formatOf(s.origin_site),
      date: s.published_at || s.created_at,
      date_kind: s.published_at ? 'parution' : 'capture',
      keywords: s.keywords,
      excerpt: parts.get("Ce que c'est") ?? '',
      summary_html: renderSummary(
        parts.size ? [...parts].map(([h, body]) => `### ${h}\n\n${body}`).join('\n\n') : (s.summary ?? ''),
      ),
      thumb: video ? `https://i.ytimg.com/vi/${video}/hqdefault.jpg` : null,
    };
  });

const byDateDesc = (a: Source, b: Source) => (Date.parse(b.date) || 0) - (Date.parse(a.date) || 0);

const repoOf = (repoUrl: string) => new URL(repoUrl).pathname.replace(/^\/+|\/+$/g, '').split('/').slice(0, 2).join('/');

/** Première image du README qui n'est pas un badge. */
function readmeImage(text: string, repoUrl: string): string | null {
  // Répétitions bornées : le README est externe, une regex non bornée bloquerait le serveur.
  const images = [...text.matchAll(/!\[[^\]]{0,1000}\]\(\s{0,100}<?([^)\s>]+)|<img[^>]{1,2000}src=["']([^"']+)/gi)]
    .map(m => m[2] || m[1]!)
    .filter(src => !/shields\.io|badge|trendshift/i.test(src));
  let [pick, second] = images;
  if (!pick) return null;
  // Logo décliné clair/sombre : garder la variante lisible sur fond clair.
  if (second && pick.includes('white') && pick.replaceAll('white', 'black') === second) pick = second;
  if (/^https?:\/\//.test(pick)) return pick;
  const path = pick.replace(/^\.\//, '').replace(/^\/+/, '');
  // shortcut: suppose une URL owner/repo (pas de datasets/ ni spaces/ sur HF), à adapter si on capture ces pages.
  if (repoUrl.includes('github.com')) {
    // raw.githubusercontent sert les SVG en text/plain sauf avec ?sanitize=true
    return `https://raw.githubusercontent.com/${repoOf(repoUrl)}/HEAD/${path}${path.endsWith('.svg') ? '?sanitize=true' : ''}`;
  }
  return `https://huggingface.co/${repoOf(repoUrl)}/resolve/main/${path}`;
}

const socialImage = (repoUrl: string) =>
  repoUrl.includes('github.com')
    ? `https://opengraph.githubassets.com/1/${repoOf(repoUrl)}`
    : `https://cdn-thumbnails.huggingface.co/social-thumbnails/models/${repoOf(repoUrl)}.png`;

/** Paramètre entier : absent -> défaut, illisible -> 0 (comme le `(int)` de PHP). */
function intParam(value: string | null, fallback: number): number {
  if (value === null) return fallback;
  const n = Math.trunc(parseFloat(value));
  return Number.isFinite(n) ? n : 0;
}

const cut = (value: string | null, length: number) => Array.from(value ?? '').slice(0, length).join('');

/** `jj/mm/aaaa hh:mm`, heure locale du serveur. */
const formatDate = (at: number) =>
  new Date(at)
    .toLocaleString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })
    .replace(',', '');

const json = (body: unknown, status = 200) =>
  new Response(JSON.stringify(body), { status, headers: { 'Content-Type': 'application/json; charset=utf-8' } });

export interface RelayOptions {
  fetch?: typeof fetch;
  /** Horloge, en millisecondes. */
  now?: () => number;
  env?: Record<string, string | undefined>;
  cacheDir?: string;
  experiments?: ExperimentLink[];
}

export function createRelay({
  fetch: send = fetch,
  now = Date.now,
  env = process.env,
  cacheDir = tmpdir(),
  experiments = builtExperiments,
}: RelayOptions = {}) {
  const configured = () => Boolean(env.VEILLEFACILE_URL && env.VEILLEFACILE_READ_KEY);

  async function api<T>(path: string, query: Record<string, string>): Promise<T> {
    if (!configured()) throw new Error('VeilleFacile non configuré');
    const base = env.VEILLEFACILE_URL!.replace(/\/+$/, '');
    const res = await send(`${base}${path}?${new URLSearchParams(query)}`, {
      headers: { Authorization: `Bearer ${env.VEILLEFACILE_READ_KEY}` },
      signal: AbortSignal.timeout(15_000),
    });
    if (res.status !== 200) throw new Error(`VeilleFacile ${path} : HTTP ${res.status}`);
    return (await res.json()) as T;
  }

  type Entry<T> = { data: T; at: number; stale: boolean };
  const pending = new Map<string, Promise<Entry<any>>>();

  /** Échecs récents : l'API n'est pas rappelée avant `until`. */
  const failures = new Map<string, { until: number; error: unknown }>();

  async function refresh<T>(key: string, ttl: number, load: () => Promise<T>): Promise<Entry<T>> {
    const file = join(cacheDir, `veille-node_${createHash('md5').update(key).digest('hex')}.json`);
    const hit: { data: T; at: number } | null = await readFile(file, 'utf8').then(JSON.parse).catch(() => null);
    if (hit && now() - hit.at < ttl) return { ...hit, stale: false };
    const orStale = (error: unknown): Entry<T> => {
      if (hit) return { ...hit, stale: true };
      throw error;
    };
    const failed = failures.get(key);
    if (failed && now() < failed.until) return orStale(failed.error);
    let data: T;
    try {
      data = await load();
    } catch (error) {
      // Les échecs expirés sont purgés : une clé par recherche, la table ne doit pas grossir sans fin.
      for (const [k, f] of failures) if (f.until <= now()) failures.delete(k);
      failures.set(key, { until: now() + (hit ? TTL : RETRY), error });
      return orStale(error);
    }
    failures.delete(key);
    const at = now();
    // Écriture puis renommage : jamais de fichier à moitié écrit. Un cache non inscriptible n'est pas fatal.
    await writeFile(`${file}.tmp`, JSON.stringify({ at, data }))
      .then(() => rename(`${file}.tmp`, file))
      .catch(error => console.warn('Cache Veille non écrit :', error.message));
    return { data, at, stale: false };
  }

  /** Cache fichier : sert l'ancien cache si `load` échoue (et attend avant de réessayer), un seul appel à la fois par clé. */
  function cached<T>(key: string, ttl: number, load: () => Promise<T>): Promise<Entry<T>> {
    let entry = pending.get(key);
    if (!entry) {
      entry = refresh(key, ttl, load).finally(() => pending.delete(key));
      pending.set(key, entry);
    }
    return entry;
  }

  // Sources vues privées par la réponse de l'API la plus récente qui les contenait : retirées de tout cache servi,
  // même plus ancien. Gardées sur disque, comme les caches qu'elles filtrent.
  const privateFile = join(cacheDir, 'veille-node_privees.json');
  const privateIds = new Set<number>();
  try {
    for (const id of JSON.parse(readFileSync(privateFile, 'utf8'))) privateIds.add(id);
  } catch { /* aucune Source privée vue */ }

  function rememberPrivate(raw: RawSource[]) {
    let changed = false;
    for (const s of raw) {
      const hidden = isPrivate(s);
      if (hidden === privateIds.has(s.id)) continue;
      changed = true;
      if (hidden) privateIds.add(s.id);
      else privateIds.delete(s.id);
    }
    if (!changed) return;
    // Synchrone : deux réponses simultanées ne peuvent pas entremêler leurs écritures.
    try {
      writeFileSync(`${privateFile}.tmp`, JSON.stringify([...privateIds]));
      renameSync(`${privateFile}.tmp`, privateFile);
    } catch (error) {
      console.warn('Cache Veille non écrit :', (error as Error).message);
    }
  }

  const withoutPrivate = (set: Entry<Source[]>): Entry<Source[]> =>
    ({ ...set, data: set.data.filter(s => !privateIds.has(s.id)) });

  async function fetchSources(query: Record<string, string>) {
    const { results } = await api<{ results: RawSource[] }>('/api/sources', query);
    rememberPrivate(results);
    return results;
  }

  /**
   * Toutes les Sources publiques, triées par date de parution puis de capture.
   * Données de secours si l'API n'est pas configurée ; lève si elle échoue sans cache.
   */
  async function allSources(): Promise<SourceSet> {
    try {
      return withoutPrivate(await cached('all', TTL, async () => {
        const all: RawSource[] = [];
        for (let offset = 0; ; offset += 100) {
          const page = await fetchSources({ limit: '100', offset: String(offset) });
          all.push(...page);
          if (page.length < 100) break;
        }
        return prepare(all).sort(byDateDesc);
      }));
    } catch (error) {
      if (configured()) throw error;
      const raw = fixture.results as RawSource[];
      return { data: prepare(raw), at: Math.max(...raw.map(s => Date.parse(s.created_at))), stale: true };
    }
  }

  /** Recherche hybride de l'API, ordre de pertinence conservé. */
  const search = async (q: string) =>
    withoutPrivate(await cached(`q:${q.toLowerCase()}`, TTL, async () => prepare(await fetchSources({ q, limit: '100' }))));

  const isRepo = (s: Source) => s.site === 'github.com' || s.site === 'huggingface.co';

  /** Lève si la résolution échoue : `cached` ressert alors l'ancienne miniature, et réessaiera. */
  async function resolveThumb(s: Source): Promise<string> {
    if (isRepo(s)) {
      const { text } = await api<{ text: string }>(`/api/sources/${s.id}/text`, { length: '200000' });
      return readmeImage(text, s.url) ?? socialImage(s.url);
    }
    const res = await send(`https://api.microlink.io/?url=${encodeURIComponent(s.url)}`, {
      signal: AbortSignal.timeout(10_000),
    });
    const body = (await res.json()) as { data?: { image?: { url?: string }; logo?: { url?: string } } };
    const url = body.data?.image?.url ?? body.data?.logo?.url;
    // L'URL vient d'un tiers et finit dans un `src` : http(s) uniquement.
    if (typeof url !== 'string' || !/^https?:\/\//i.test(url)) throw new Error(`Microlink : aucune image (HTTP ${res.status})`);
    return url;
  }

  /** Sans aucune miniature en cache : image sociale pour un dépôt (non gardée), rien sinon. */
  const thumbOf = (s: Source): Promise<string | null> =>
    cached(`thumb:${s.id}${s.url}`, THUMB_TTL, () => resolveThumb(s)).then(
      entry => entry.data,
      () => (isRepo(s) ? socialImage(s.url) : null),
    );

  async function list(params: URLSearchParams) {
    const q = cut(params.get('q'), 200).trim();
    const format = params.get('format') ?? '';
    const keyword = cut(params.get('kw'), 100).toLowerCase();
    const offset = Math.max(0, intParam(params.get('offset'), 0));
    const limit = Math.min(100, Math.max(1, intParam(params.get('limit'), 24)));

    const res = q === '' ? await allSources() : await search(q);
    const slugs = new Map(experiments.map(e => [normUrl(e.source_url ?? ''), e.slug]));
    const items = res.data.filter(
      s => (!format || s.format === format) && (!keyword || s.keywords.some(k => k.toLowerCase() === keyword)),
    );
    return {
      results: items.slice(offset, offset + limit).map(s => ({
        ...s,
        exp: slugs.get(normUrl(s.url)) ?? slugs.get(normUrl(s.canonical_url)) ?? null,
      })),
      total: items.length,
      offset,
      stale_since: res.stale ? formatDate(res.at) : null,
    };
  }

  /** Point d'entrée HTTP du relais. */
  async function handle(url: URL): Promise<Response> {
    try {
      const thumb = url.searchParams.get('thumb');
      if (thumb === null) return json(await list(url.searchParams));
      const id = intParam(thumb, 0);
      // Seules les Sources publiques connues peuvent déclencher une résolution de miniature.
      const source = (await allSources()).data.find(s => s.id === id);
      if (!source) return json({ thumb: null });
      return json({ thumb: await thumbOf(source) });
    } catch (error) {
      console.warn('Relais VeilleFacile :', error instanceof Error ? error.message : error);
      return json({ error: 'VeilleFacile est indisponible pour le moment.' }, 502);
    }
  }

  return { handle, allSources };
}

/** Instance partagée par la route du relais et les pages Veille (un seul cache, un seul appel en cours). */
export const relay = createRelay();
