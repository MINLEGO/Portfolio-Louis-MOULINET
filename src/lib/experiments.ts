// Expérimentations : public/experimentations/<slug>/{meta.json,index.md}, lues au build et liées à leur Source par URL.
import MarkdownIt from 'markdown-it';
import { builtExperiments, isSourceOf, type ExperimentMeta } from './experiment-meta';
import type { Source } from './veille';

export interface Experiment extends ExperimentMeta {
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
  return builtExperiments
    .map(meta => ({
      ...meta,
      base: `/experimentations/${meta.slug}/`,
      html: markdown.render(texts[`/public/experimentations/${meta.slug}/index.md`] ?? ''),
      source: sources.findLast(s => isSourceOf(s, meta.source_url)) ?? null,
    }))
    .sort((a, b) => (b.date ?? '').localeCompare(a.date ?? ''));
}
