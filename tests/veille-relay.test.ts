// Tests au point d'entrée HTTP du relais : seules les réponses (statut, JSON) sont observées.
// L'API VeilleFacile (fetch) et l'horloge sont simulées.
import { mkdtempSync, rmSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join } from 'node:path';
import { afterEach, beforeEach, describe, expect, test } from 'vitest';
import { createRelay } from '../src/lib/veille';

const API = 'https://veille.test';
const ENV = { VEILLEFACILE_URL: API, VEILLEFACILE_READ_KEY: 'cle-de-test' };

function source(id: number, extra: Record<string, unknown> = {}) {
  return {
    id,
    url: `https://example.com/${id}`,
    canonical_url: `https://example.com/${id}`,
    origin_site: 'example.com',
    title: `Source ${id}`,
    summary: `## What it is\nRésumé ${id}`,
    keywords: ['agents'],
    published_at: `2026-01-${String(id).padStart(2, '0')}T00:00:00+00:00`,
    created_at: '2026-02-01T00:00:00+00:00',
    ...extra,
  };
}

let cacheDir: string;
beforeEach(() => { cacheDir = mkdtempSync(join(tmpdir(), 'relay-test-')); });
afterEach(() => rmSync(cacheDir, { recursive: true, force: true }));

/** Faux VeilleFacile : `sources` est modifiable en cours de test, `down` simule une panne. */
function setup(sources: ReturnType<typeof source>[] = [], options: Partial<Parameters<typeof createRelay>[0]> = {}) {
  const api = {
    sources,
    down: false,
    calls: [] as { url: URL; authorization: string | null }[],
    other: (_url: URL): Response => new Response('', { status: 404 }),
  };
  const clock = { now: Date.parse('2026-03-01T12:00:00Z') };
  const fakeFetch: typeof fetch = async (input, init) => {
    const url = new URL(String(input));
    api.calls.push({ url, authorization: new Headers(init?.headers).get('authorization') });
    if (url.origin !== API || url.pathname !== '/api/sources') return api.other(url);
    if (api.down) return new Response('panne', { status: 503 });
    const offset = Number(url.searchParams.get('offset') ?? 0);
    const limit = Number(url.searchParams.get('limit'));
    return Response.json({ results: api.sources.slice(offset, offset + limit) });
  };
  const relay = createRelay({ fetch: fakeFetch, now: () => clock.now, env: ENV, cacheDir, ...options });
  const get = async (query: Record<string, string> = {}) => {
    const res = await relay.handle(new URL(`http://localhost/veille_api.php?${new URLSearchParams(query)}`));
    return { status: res.status, type: res.headers.get('content-type'), body: (await res.json()) as any };
  };
  const ids = async (query: Record<string, string> = {}) => (await get(query)).body.results.map((s: any) => s.id);
  return { api, clock, get, ids };
}

describe('Liste', () => {
  test('renvoie les Sources triées par date de parution, à défaut de capture', async () => {
    const { get, api } = setup([
      source(1),
      source(3),
      source(2, { published_at: null, created_at: '2026-01-02T00:00:00+00:00' }),
    ]);
    const { status, type, body } = await get();
    expect(status).toBe(200);
    expect(type).toBe('application/json; charset=utf-8');
    expect(body).toMatchObject({ total: 3, offset: 0, stale_since: null });
    expect(body.results.map((s: any) => [s.id, s.date_kind])).toEqual([[3, 'parution'], [2, 'capture'], [1, 'parution']]);
    expect(api.calls[0]?.authorization).toBe('Bearer cle-de-test');
  });

  test('met une Source au format d\'affichage', async () => {
    const { get } = setup([
      source(1, {
        origin_site: 'www.youtube.com',
        url: 'https://www.youtube.com/watch?v=n9xKblqyQ28',
        summary: '## What it is\nUne vidéo.\n\n## Ignored\nx\n\n## Key points\n- un',
      }),
    ]);
    const [s] = (await get()).body.results;
    expect(s).toEqual({
      id: 1,
      url: 'https://www.youtube.com/watch?v=n9xKblqyQ28',
      canonical_url: 'https://example.com/1',
      title: 'Source 1',
      site: 'youtube.com',
      format: 'Vidéo',
      date: '2026-01-01T00:00:00+00:00',
      date_kind: 'parution',
      keywords: ['agents'],
      excerpt: 'Une vidéo.',
      summary_html: expect.any(String),
      thumb: 'https://i.ytimg.com/vi/n9xKblqyQ28/hqdefault.jpg',
      exp: null,
    });
    expect(s.summary_html.replace(/\n/g, '')).toBe(
      "<h3>Ce que c'est</h3><p>Une vidéo.</p><h3>Points clés</h3><ul><li>un</li></ul>",
    );
  });

  test('déduit le Format du site d\'origine', async () => {
    const sites = ['youtube.com', 'youtu.be', 'github.com', 'huggingface.co', 'blog.google'];
    const { get } = setup(sites.map((origin_site, i) => source(sites.length - i, { origin_site })));
    expect((await get()).body.results.map((s: any) => s.format)).toEqual(['Vidéo', 'Vidéo', 'Code', 'Modèle', 'Article']);
  });

  test('lit toutes les pages de l\'API', async () => {
    const { get } = setup(Array.from({ length: 150 }, (_, i) => source(i + 1, { published_at: null })));
    expect((await get()).body.total).toBe(150);
  });

  test('relie une Source à son Expérimentation par URL normalisée', async () => {
    const { get } = setup(
      [source(2, { url: 'https://www.github.com/a/b/' }), source(1, { canonical_url: 'https://github.com/c/d' })],
      { experiments: [{ slug: 'un', source_url: 'http://github.com/a/b' }, { slug: 'deux', source_url: 'https://github.com/c/d/' }] },
    );
    expect((await get()).body.results.map((s: any) => s.exp)).toEqual(['un', 'deux']);
  });
});

describe('Sources privées', () => {
  // Une Source privée qui répond à tous les critères ne doit jamais sortir.
  const secret = source(9, {
    title: 'SECRET', keywords: ['Agents', 'PRIVÉ'], origin_site: 'github.com', url: 'https://github.com/moi/secret',
  });

  test.each([
    ['liste', {}],
    ['recherche', { q: 'secret' }],
    ['filtre par Format', { format: 'Code' }],
    ['filtre par Sujet', { kw: 'agents' }],
    ['filtre par le Sujet privé', { kw: 'privé' }],
  ] as [string, Record<string, string>][])('absente : %s', async (_nom, query) => {
    const { get } = setup([secret, source(1)]);
    const { body } = await get(query);
    expect(JSON.stringify(body)).not.toContain('secret');
    expect(JSON.stringify(body)).not.toContain('SECRET');
    expect(body.total).toBe(body.results.length);
  });

  test('absente même si « privé » est écrit en Unicode décomposé', async () => {
    const { ids } = setup([source(9, { keywords: ['privé '] }), source(1)]);
    expect(await ids()).toEqual([1]);
  });

  test('aucune miniature, et aucune résolution déclenchée', async () => {
    const { get, api } = setup([secret, source(1)]);
    expect((await get({ thumb: '9' })).body).toEqual({ thumb: null });
    expect(api.calls.map(c => c.url.pathname)).toEqual(['/api/sources']);
  });
});

describe('Source devenue privée', () => {
  const MIN = 60_000;
  const open = source(9, { title: 'SECRET' });
  const closed = source(9, { title: 'SECRET', keywords: ['privé'] });

  test.each([
    ['ancienne recherche encore fraîche', 0],
    ['ancienne recherche expirée, API en panne', 11],
  ])('après un rafraîchissement de la liste : %s', async (_nom, wait) => {
    const { api, clock, get, ids } = setup([open, source(1)]);
    await ids();
    clock.now += 9 * MIN;
    expect(await ids({ q: 'x' })).toEqual([9, 1]);
    api.sources = [closed, source(1)];
    clock.now += 2 * MIN;
    expect(await ids()).toEqual([1]);
    api.down = true;
    clock.now += wait * MIN;
    expect((await get({ q: 'x' })).body).toMatchObject({ total: 1, results: [{ id: 1 }] });
    expect((await get({ thumb: '9' })).body).toEqual({ thumb: null });
  });

  test("vue privée par une recherche : l'ancienne liste et l'ancienne miniature ne la ressortent pas", async () => {
    const { api, clock, get, ids } = setup([open, source(1)]);
    api.other = () => Response.json({ data: { image: { url: 'https://cdn.test/secret.png' } } });
    expect((await get({ thumb: '9' })).body.thumb).toBe('https://cdn.test/secret.png');
    api.sources = [closed, source(1)];
    await ids({ q: 'x' });
    api.down = true;
    clock.now += 11 * MIN;
    expect((await get()).body).toMatchObject({ total: 1, results: [{ id: 1 }] });
    expect((await get({ thumb: '9' })).body).toEqual({ thumb: null });

    // Un redémarrage garde les caches sur disque : il doit aussi garder ce qui a été vu privé.
    const restarted = setup();
    restarted.api.down = true;
    restarted.clock.now = clock.now + 11 * MIN;
    expect(await restarted.ids()).toEqual([1]);
  });

  test('redevenue publique, elle réapparaît', async () => {
    const { api, clock, ids } = setup([closed, source(1)]);
    expect(await ids()).toEqual([1]);
    api.sources = [open, source(1)];
    clock.now += 11 * MIN;
    expect(await ids()).toEqual([9, 1]);
  });
});

describe('Paramètres', () => {
  const many = Array.from({ length: 120 }, (_, i) => source(i + 1, { published_at: null }));

  test.each([
    [{}, 24],
    [{ limit: '1' }, 1],
    [{ limit: '0' }, 1],
    [{ limit: '-5' }, 1],
    [{ limit: 'abc' }, 1],
    [{ limit: '100' }, 100],
    [{ limit: '1000' }, 100],
  ] as [Record<string, string>, number][])('limit %o : %i résultats', async (query, count) => {
    const { get } = setup(many);
    expect((await get(query)).body.results).toHaveLength(count);
  });

  test('offset pagine et n\'est jamais négatif', async () => {
    const { get } = setup(many);
    expect((await get({ offset: '118' })).body).toMatchObject({ total: 120, offset: 118, results: [{ id: 119 }, { id: 120 }] });
    expect((await get({ offset: '-3', limit: '1' })).body).toMatchObject({ offset: 0, results: [{ id: 1 }] });
    expect((await get({ offset: '500' })).body).toMatchObject({ total: 120, results: [] });
  });

  test('q est coupé à 200 caractères puis débarrassé de ses espaces', async () => {
    const { get, api } = setup([source(1)]);
    await get({ q: ' ' + '😀'.repeat(300) });
    expect(api.calls.at(-1)?.url.searchParams.get('q')).toBe('😀'.repeat(199));
  });

  test('q vide après nettoyage : pas de recherche', async () => {
    const { get, api } = setup([source(1)]);
    await get({ q: '   ' });
    expect(api.calls.at(-1)?.url.searchParams.has('q')).toBe(false);
  });

  test('kw est coupé à 100 caractères', async () => {
    const kw = 'a'.repeat(100);
    const { ids } = setup([source(1, { keywords: [kw.toUpperCase()] }), source(2)]);
    expect(await ids({ kw: kw + 'zzz' })).toEqual([1]);
  });

  test('format et Sujet filtrent avant la pagination', async () => {
    const { get } = setup([source(1), source(2, { origin_site: 'github.com' }), source(3, { keywords: ['rag'] })]);
    expect((await get({ format: 'Article', kw: 'AGENTS' })).body).toMatchObject({ total: 1, results: [{ id: 1 }] });
  });

  test('la recherche conserve l\'ordre de pertinence de l\'API', async () => {
    const { ids } = setup([source(1), source(3), source(2)]);
    expect(await ids({ q: 'x' })).toEqual([1, 3, 2]);
  });
});

describe('Cache', () => {
  const MIN = 60_000;

  test.each([[{}], [{ q: 'agents' }]] as Record<string, string>[][])('%o est gardé 10 minutes', async query => {
    const { api, clock, ids } = setup([source(1)]);
    expect(await ids(query)).toEqual([1]);
    api.sources = [source(2)];
    clock.now += 10 * MIN - 1000;
    expect(await ids(query)).toEqual([1]);
    expect(api.calls).toHaveLength(1);
    clock.now += 2000;
    expect(await ids(query)).toEqual([2]);
  });

  test('des requêtes simultanées ne sollicitent l\'API qu\'une fois', async () => {
    const { api, ids } = setup([source(1)]);
    await Promise.all([ids(), ids(), ids()]);
    expect(api.calls).toHaveLength(1);
  });

  test.each([[{}], [{ q: 'agents' }]] as Record<string, string>[][])('%o : l\'ancien cache est servi si l\'API échoue', async query => {
    const { api, clock, get } = setup([source(1)]);
    expect((await get(query)).body.stale_since).toBeNull();
    api.down = true;
    clock.now += 11 * MIN;
    const { status, body } = await get(query);
    expect(status).toBe(200);
    expect(body.results).toHaveLength(1);
    expect(body.stale_since).toMatch(/^\d{2}\/\d{2}\/2026 \d{2}:\d{2}$/);
  });

  test.each([[{}], [{ q: 'agents' }], [{ thumb: '1' }]] as Record<string, string>[][])('%o : 502 lisible sans cache', async query => {
    const { api, get } = setup([source(1)]);
    api.down = true;
    expect(await get(query)).toEqual({
      status: 502,
      type: 'application/json; charset=utf-8',
      body: { error: 'VeilleFacile est indisponible pour le moment.' },
    });
  });
});

describe("Après un échec de l'API", () => {
  const MIN = 60_000;

  test("ancien cache : servi sans rappeler l'API pendant 10 minutes", async () => {
    const { api, clock, get, ids } = setup([source(1)]);
    await get();
    api.down = true;
    clock.now += 11 * MIN;
    await get();
    api.down = false;
    api.sources = [source(2)];
    clock.now += 10 * MIN - 1000;
    expect((await get()).body).toMatchObject({ results: [{ id: 1 }], stale_since: expect.any(String) });
    expect(api.calls).toHaveLength(2);
    clock.now += 2000;
    expect(await ids()).toEqual([2]);
    expect(api.calls).toHaveLength(3);
  });

  test("sans cache : 502 sans rappeler l'API pendant 1 minute seulement", async () => {
    const { api, clock, get, ids } = setup([source(1)]);
    api.down = true;
    expect((await get()).status).toBe(502);
    api.down = false;
    clock.now += MIN - 1000;
    expect((await get()).status).toBe(502);
    expect(api.calls).toHaveLength(1);
    clock.now += 2000;
    expect(await ids()).toEqual([1]);
  });
});

describe('Données de secours (API non configurée)', () => {
  test('la liste vient de l\'instantané, sans aucun appel sortant', async () => {
    const { api, get } = setup([], { env: {} });
    const { status, body } = await get({ limit: '100' });
    expect(status).toBe(200);
    expect(body.total).toBe(20);
    expect(body.stale_since).not.toBeNull();
    expect(api.calls).toEqual([]);
  });

  test('l\'instantané est trié par date de parution, à défaut de capture', async () => {
    const { get } = setup([], { env: {} });
    const dates = (await get({ limit: '100' })).body.results.map((s: any) => Date.parse(s.date));
    expect(dates).toEqual([...dates].sort((a, b) => b - a));
  });

  test('la recherche répond 502', async () => {
    const { get } = setup([], { env: {} });
    expect((await get({ q: 'agents' })).status).toBe(502);
  });
});

describe('Résumés nettoyés', () => {
  const html = async (summary: string) => (await setup([source(1, { summary })]).get()).body.results[0].summary_html as string;
  /** Balises réellement présentes (le HTML échappé n'en est pas une). */
  const tags = (markup: string) => markup.match(/<[a-z][^>]*>/gi) ?? [];

  test.each([
    '<script>alert(1)</script>',
    '<img src=x onerror=alert(1)>',
    '<a href="javascript:alert(1)">x</a>',
    '<iframe src="https://evil.test"></iframe>',
    'texte <svg/onload=alert(1)>',
    '[x](javascript:alert(1))',
    '[x](JaVaScRiPt&#58;alert(1))',
    '![x](javascript:alert(1))',
    '<javascript:alert(1)>',
    '[x](vbscript:msgbox)',
    '[x](data:text/html;base64,PHNjcmlwdD4=)',
    '[x][r]\n\n[r]: javascript:alert(1)',
    '[x](https://ok.test "a\\" onmouseover=\\"alert(1)")',
    '```"><script>alert(1)</script>\ncode\n```',
  ])('%j', async attack => {
    for (const summary of [attack, `## What it is\n${attack}`]) {
      const out = await html(summary);
      expect(out).not.toMatch(/<(script|iframe|svg)/i);
      for (const tag of tags(out)) {
        expect(tag).not.toMatch(/\son\w+\s*=(?=(?:[^"]*"[^"]*")*[^"]*$)/i);
        expect(tag).not.toMatch(/(href|src)="\s*(javascript|vbscript|data):/i);
      }
    }
  });

  test('le markdown utile est conservé', async () => {
    const out = await html('Un `code`, **gras**, [lien](https://ok.test) et https://auto.test\n\n- a');
    expect(out).toContain('<code>code</code>');
    expect(out).toContain('<strong>gras</strong>');
    expect(out).toContain('<a href="https://ok.test">lien</a>');
    expect(out).toContain('<a href="https://auto.test">https://auto.test</a>');
    expect(out).toContain('<li>a</li>');
  });

  test('le HTML du résumé s\'affiche comme du texte', async () => {
    expect(await html('La balise <video> est citée.')).toContain('La balise &lt;video&gt; est citée.');
  });
});

describe('Miniature', () => {
  const README = [
    '![badge](https://img.shields.io/x.svg)',
    '<img src="./docs/logo-white.svg">',
    '<img src="./docs/logo-black.svg">',
  ].join('\n');

  const repo = source(1, { origin_site: 'github.com', url: 'https://github.com/moi/outil/tree/main' });
  const model = source(2, { origin_site: 'huggingface.co', url: 'https://huggingface.co/org/modele' });

  const readme = (text: string) => (url: URL) =>
    url.pathname.endsWith('/text') ? Response.json({ text }) : new Response('', { status: 404 });

  test('première image du README qui n\'est pas un badge, variante lisible sur fond clair', async () => {
    const { api, get } = setup([repo]);
    api.other = readme(README);
    expect(await get({ thumb: '1' })).toMatchObject({
      status: 200,
      body: { thumb: 'https://raw.githubusercontent.com/moi/outil/HEAD/docs/logo-black.svg?sanitize=true' },
    });
    const call = api.calls.at(-1)!;
    expect(call.url.href).toBe(`${API}/api/sources/1/text?length=200000`);
    expect(call.authorization).toBe('Bearer cle-de-test');
  });

  test('image Markdown relative d\'un modèle', async () => {
    const { api, get } = setup([model]);
    api.other = readme('![Aperçu](/assets/a.png)');
    expect((await get({ thumb: '2' })).body).toEqual({ thumb: 'https://huggingface.co/org/modele/resolve/main/assets/a.png' });
  });

  test.each([
    [repo, 'https://opengraph.githubassets.com/1/moi/outil'],
    [model, 'https://cdn-thumbnails.huggingface.co/social-thumbnails/models/org/modele.png'],
  ])('sans image dans le README : image sociale du dépôt', async (src, expected) => {
    const { get } = setup([src]);
    expect((await get({ thumb: String(src.id) })).body).toEqual({ thumb: expected });
  });

  test('autre site : Microlink, sans lui transmettre la clé', async () => {
    const { api, get } = setup([source(3)]);
    api.other = () => Response.json({ data: { image: null, logo: { url: 'https://cdn.test/logo.png' } } });
    expect((await get({ thumb: '3' })).body).toEqual({ thumb: 'https://cdn.test/logo.png' });
    const call = api.calls.at(-1)!;
    expect(call.url.href).toBe('https://api.microlink.io/?url=https%3A%2F%2Fexample.com%2F3');
    expect(call.authorization).toBeNull();
  });

  test('une URL de miniature qui n\'est pas en http(s) est refusée', async () => {
    const { api, get } = setup([source(3)]);
    api.other = () => Response.json({ data: { image: { url: 'javascript:alert(1)' } } });
    expect((await get({ thumb: '3' })).body).toEqual({ thumb: null });
  });

  test('résolue une seule fois, gardée 30 jours', async () => {
    const { api, clock, get } = setup([source(3)]);
    let n = 0;
    api.other = () => Response.json({ data: { image: { url: `https://cdn.test/${++n}.png` } } });
    const thumb = async () => (await get({ thumb: '3' })).body.thumb;
    expect(await thumb()).toBe('https://cdn.test/1.png');
    clock.now += 30 * 86400_000 - 1000;
    expect(await thumb()).toBe('https://cdn.test/1.png');
    clock.now += 2000;
    expect(await thumb()).toBe('https://cdn.test/2.png');
  });

  test("un échec de résolution n'est pas gardé : nouvel essai après 1 minute", async () => {
    const { api, clock, get } = setup([source(3)]);
    api.other = () => { throw new TypeError('réseau'); };
    expect((await get({ thumb: '3' })).body).toEqual({ thumb: null });
    api.other = () => Response.json({ data: { image: { url: 'https://cdn.test/ok.png' } } });
    clock.now += 60_000;
    expect((await get({ thumb: '3' })).body).toEqual({ thumb: 'https://cdn.test/ok.png' });
  });

  const image = (url: string) => () => Response.json({ data: { image: { url } } });
  const down = () => new Response('panne', { status: 503 });
  const quota = () => Response.json({ status: 'fail' }, { status: 429 });
  const unreachable = (): Response => { throw new TypeError('réseau'); };
  const fromReadme = 'https://raw.githubusercontent.com/moi/outil/HEAD/a.png';
  const newImage = 'https://cdn.test/new.png';

  test.each([
    ['Microlink injoignable', source(3), image('https://cdn.test/old.png'), unreachable, image(newImage), 'https://cdn.test/old.png'],
    ['quota Microlink dépassé', source(3), image('https://cdn.test/old.png'), quota, image(newImage), 'https://cdn.test/old.png'],
    ['/text en échec', repo, readme('![a](a.png)'), down, readme(`![b](${newImage})`), fromReadme],
  ])('miniature expirée, %s : ancienne resservie, nouvel essai après 10 minutes', async (_nom, src, before, failing, after, old) => {
    const { api, clock, get } = setup([src]);
    const thumb = async () => (await get({ thumb: String(src.id) })).body.thumb;
    api.other = before;
    expect(await thumb()).toBe(old);
    clock.now += 31 * 86400_000;
    api.other = failing;
    expect(await thumb()).toBe(old);
    api.other = after;
    clock.now += 9 * 60_000;
    expect(await thumb()).toBe(old);
    clock.now += 2 * 60_000;
    expect(await thumb()).toBe(newImage);
  });

  test('/text en échec sans ancienne miniature : image sociale, sans la garder 30 jours', async () => {
    const { api, clock, get } = setup([repo]);
    const thumb = async () => (await get({ thumb: '1' })).body.thumb;
    expect(await thumb()).toBe('https://opengraph.githubassets.com/1/moi/outil');
    api.other = readme('![a](a.png)');
    clock.now += 60_000;
    expect(await thumb()).toBe(fromReadme);
  });

  test('identifiant inconnu : aucune résolution', async () => {
    const { api, get } = setup([source(1)]);
    expect((await get({ thumb: '42' })).body).toEqual({ thumb: null });
    expect((await get({ thumb: 'abc' })).body).toEqual({ thumb: null });
    expect(api.calls).toHaveLength(1);
  });

  test('un README hostile ne bloque pas le serveur', async () => {
    const { api, get } = setup([repo]);
    api.other = readme('<img '.repeat(40_000) + '!['.repeat(40_000));
    const start = performance.now();
    await get({ thumb: '1' });
    expect(performance.now() - start).toBeLessThan(2000);
  });
});
