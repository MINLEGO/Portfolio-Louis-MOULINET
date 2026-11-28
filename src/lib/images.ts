import type { ImageMetadata } from 'astro';

// Les images des projets sont dans src/assets/media/ pour passer par le pipeline d'images d'Astro.
// L'Admin (Keystatic) les range dans des sous-dossiers nommés d'après leur place dans projects.json.
const files = import.meta.glob<ImageMetadata>('../assets/media/**/*.{png,jpg,jpeg,webp,avif,gif,svg}', {
  eager: true,
  import: 'default',
});

/** Chemin "./media/<fichier>" de projects.json -> image de src/assets/media/. Une URL externe (ou vide) reste une chaîne. */
export function resolveImage(src: string): ImageMetadata | string {
  if (!src || /^(https?:)?\/\//.test(src)) return src;
  const image = files[src.replace(/^(\.?\/)?media\//, '../assets/media/')];
  if (!image) throw new Error(`projects.json : image « ${src} » introuvable. Déposez le fichier dans src/assets/media/.`);
  return image;
}
