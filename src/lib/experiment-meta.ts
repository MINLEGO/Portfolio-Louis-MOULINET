// Métadonnées des Expérimentations (public/experimentations/<slug>/meta.json), lues au build,
// et règle qui lie une Expérimentation à sa Source.

export type Demo =
  | { type: 'galerie'; images?: string[]; legendes?: string[] }
  | { type: 'video'; url: string }
  | { type: 'html'; fichier: string; hauteur?: number };

/** Contenu de meta.json. */
export interface ExperimentMeta {
  titre: string;
  date?: string;
  source_url?: string;
  /** Numéro de la Démonstration placée sous le titre. */
  entete?: number;
  demos?: Demo[];
}

const metas = import.meta.glob<ExperimentMeta>('/public/experimentations/*/meta.json', {
  eager: true,
  import: 'default',
});

export const builtExperiments = Object.entries(metas).map(([file, meta]) => ({ ...meta, slug: file.split('/').at(-2)! }));

const normUrl = (url: string) => url.replace(/^https?:\/\/(www\.)?/, '').replace(/\/+$/, '');

/** Vrai si le `source_url` d'une Expérimentation désigne cette Source (URL ou URL canonique, normalisées). */
export function isSourceOf(source: { url: string; canonical_url: string }, sourceUrl = ''): boolean {
  const wanted = normUrl(sourceUrl);
  return normUrl(source.url) === wanted || normUrl(source.canonical_url) === wanted;
}
