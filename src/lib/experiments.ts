// Expérimentations : public/experimentations/<slug>/{meta.json,index.md}, lues au build et liées à leur Source par URL.
import MarkdownIt from 'markdown-it';
import { builtExperiments, normUrl, type Source } from './veille';

export type Demo =
  | { type: 'galerie'; images?: string[]; legendes?: string[] }
  | { type: 'video'; url: string }
  | { type: 'html'; fichier: string; hauteur?: number };

/** Contenu de meta.json. */
interface Meta {
  titre: string;
  date?: string;
  source_url?: string;
  /** Numéro de la Démonstration placée sous le titre. */
  entete?: number;
  demos?: Demo[];
}

export interface Experiment extends Meta {
  slug: string;
  /** Dossier des fichiers de Démonstration, chemin absolu. */
  base: string;
  html: string;
  source: Source | null;
}

/** Données que la page Veille transmet à son script (`window.VEILLE`). */
export interface VeillePageData {
  mode: 'home' | 'archive';
  q: string;
  experiments: Experiment[];
}

const texts = import.meta.glob<string>('/public/experimentations/*/index.md', {
  eager: true,
  query: '?raw',
  import: 'default',
});

// Contenu de confiance, écrit par l'auteur du site : HTML autorisé, aucun nettoyage (à l'inverse des résumés).
const markdown = new MarkdownIt({ html: true, linkify: true });
markdown.linkify.set({ fuzzyLink: false, fuzzyEmail: false });

/** Expérimentations de la plus récente à la plus ancienne, jointes aux Sources données. */
export function joinExperiments(sources: Source[]): Experiment[] {
  const bySourceUrl = new Map(sources.flatMap(s => [[normUrl(s.url), s], [normUrl(s.canonical_url), s]] as const));
  return (builtExperiments as (Meta & { slug: string })[])
    .map(meta => ({
      ...meta,
      slug: meta.slug,
      base: `/experimentations/${meta.slug}/`,
      html: markdown.render(texts[`/public/experimentations/${meta.slug}/index.md`] ?? ''),
      source: bySourceUrl.get(normUrl(meta.source_url ?? '')) ?? null,
    }))
    .sort((a, b) => (b.date ?? '').localeCompare(a.date ?? ''));
}
