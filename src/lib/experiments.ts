// Expérimentations : public/experimentations/<slug>/{meta.json,index.md}, lues au build et liées à leur Source par URL.
import type { MarkdownInstance } from 'astro';
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

// Contenu de confiance, écrit par l'auteur du site : rendu markdown d'Astro, HTML gardé tel quel, aucun nettoyage
// (à l'inverse des résumés). Réglages : `markdown` dans astro.config.mjs.
const texts = import.meta.glob<MarkdownInstance<Record<string, unknown>>>('/public/experimentations/*/index.md', {
  eager: true,
});

async function render(text: MarkdownInstance<Record<string, unknown>> | undefined): Promise<string> {
  if (!text) return '';
  let html = await text.compiledContent();
  // Astro donne un `id` à chaque titre, sans option pour l'éviter : retirés, ils pourraient doubler ceux de la page.
  for (const { depth, slug } of text.getHeadings()) html = html.replace(`<h${depth} id="${slug}">`, `<h${depth}>`);
  return html;
}

/** Expérimentations de la plus récente à la plus ancienne, jointes aux Sources données. */
export async function joinExperiments(sources: Source[]): Promise<Experiment[]> {
  const experiments = await Promise.all(
    builtExperiments.map(async meta => ({
      ...meta,
      base: `/experimentations/${meta.slug}/`,
      html: await render(texts[`/public/experimentations/${meta.slug}/index.md`]),
      source: sources.findLast(s => isSourceOf(s, meta.source_url)) ?? null,
    })),
  );
  return experiments.sort((a, b) => (b.date ?? '').localeCompare(a.date ?? ''));
}
