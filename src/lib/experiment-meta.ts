// Métadonnées des Expérimentations (en-tête JSON de public/experimentations/<slug>/index.md, écrit par l'Admin), lues au build,
// et règle qui lie une Expérimentation à sa Source.

export type Demo =
  | { type: 'galerie'; images?: string[]; legendes?: string[] }
  | { type: 'video'; url: string }
  | { type: 'html'; fichier: string; hauteur?: number };

/** Métadonnées d'une Expérimentation, telles que les reçoit la page. */
export interface ExperimentMeta {
  titre: string;
  date?: string;
  source_url?: string;
  /** Nombre de Démonstrations placées sous le titre. */
  entete?: number;
  demos?: Demo[];
}

/** En-tête de index.md, au format de Keystatic (schéma : keystatic.config.ts). */
interface StoredMeta {
  titre: string;
  date?: string | null;
  brouillon?: boolean;
  source_url?: string | null;
  entete?: number | null;
  demos?: (
    | { discriminant: 'galerie'; value: { images: { image: string | null; legende: string }[] } }
    | { discriminant: 'video'; value: { url: string | null } }
    | { discriminant: 'html'; value: { fichier: string | null; hauteur: number | null } }
  )[];
}

const files = import.meta.glob<string>('/public/experimentations/*/index.md', { eager: true, query: '?raw', import: 'default' });
const metas = Object.fromEntries(
  Object.entries(files).map(([file, text]) => {
    const frontmatter = text.match(/^---\r?\n([^]*?)\r?\n---/)?.[1];
    if (!frontmatter) throw new Error(`${file} : en-tête --- manquant (il est écrit par l'Admin).`);
    return [file, JSON.parse(frontmatter) as StoredMeta];
  }),
);

/** Blocs de Keystatic -> Démonstrations au format attendu par le script de la Veille. */
const toDemo = (demo: NonNullable<StoredMeta['demos']>[number]): Demo => {
  if (demo.discriminant === 'galerie') {
    const images = demo.value.images.filter((i) => i.image);
    return { type: 'galerie', images: images.map((i) => i.image!), legendes: images.map((i) => i.legende) };
  }
  if (demo.discriminant === 'video') return { type: 'video', url: demo.value.url ?? '' };
  return { type: 'html', fichier: demo.value.fichier ?? '', hauteur: demo.value.hauteur ?? undefined };
};

/** Expérimentations publiées (les Brouillons sont exclus). */
export const builtExperiments = Object.entries(metas)
  .filter(([, meta]) => !meta.brouillon)
  .map(([file, { titre, date, source_url, entete, demos }]) => ({
    titre,
    date: date ?? undefined,
    source_url: source_url ?? undefined,
    entete: entete ?? undefined,
    demos: demos?.map(toDemo),
    slug: file.split('/').at(-2)!,
  }));

const normUrl = (url: string) => url.replace(/^https?:\/\/(www\.)?/, '').replace(/\/+$/, '');

/** Vrai si le `source_url` d'une Expérimentation désigne cette Source (URL ou URL canonique, normalisées). */
export function isSourceOf(source: { url: string; canonical_url: string }, sourceUrl = ''): boolean {
  const wanted = normUrl(sourceUrl);
  return normUrl(source.url) === wanted || normUrl(source.canonical_url) === wanted;
}
