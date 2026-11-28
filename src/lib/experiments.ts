// Expérimentations : public/experimentations/<slug>/index.md (en-tête JSON et texte), lues au build et liées à leur Source par URL.
import type { ImageMetadata, MarkdownInstance } from 'astro';
import { builtExperiments, isSourceOf, type ExperimentMeta } from './experiment-meta';
import type { Source } from './veille';

// Dimensions des images, mesurées au build par Astro : leur place est réservée avant le chargement.
// Seules les dimensions servent : les fichiers restent servis depuis /experimentations/<slug>/.
const images = import.meta.glob<ImageMetadata>('/public/experimentations/*/**/*.{png,jpg,jpeg,gif,webp,avif,svg}', {
  eager: true,
  import: 'default',
});

export interface Experiment extends ExperimentMeta {
  /** Dimensions des images du dossier : nom de fichier -> [largeur, hauteur]. */
  sizes: Record<string, [number, number]>;
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

const sizesOf = (folder: string): Experiment['sizes'] =>
  Object.fromEntries(
    Object.entries(images)
      .filter(([file]) => file.startsWith(folder))
      .map(([file, { width, height }]) => [file.slice(folder.length), [width, height]]),
  );

/** Expérimentations de la plus récente à la plus ancienne, jointes aux Sources données. */
export async function joinExperiments(sources: Source[]): Promise<Experiment[]> {
  const experiments = await Promise.all(
    builtExperiments.map(async meta => ({
      ...meta,
      base: `/experimentations/${meta.slug}/`,
      sizes: sizesOf(`/public/experimentations/${meta.slug}/`),
      html: await render(texts[`/public/experimentations/${meta.slug}/index.md`]),
      source: sources.findLast(s => isSourceOf(s, meta.source_url)) ?? null,
    })),
  );
  return experiments.sort((a, b) => (b.date ?? '').localeCompare(a.date ?? ''));
}
