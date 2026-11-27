import data from '../data/projects.json';

const slugify = (title: string) =>
  title
    .toLowerCase()
    // Les ligatures ne se décomposent pas en NFD.
    .replaceAll('œ', 'oe')
    .replaceAll('æ', 'ae')
    .normalize('NFD')
    .replace(/\p{M}/gu, '')
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-|-$/g, '');

/** Projets dans l'ordre du JSON, avec le slug de leur URL `/projets/<slug>`. */
export const projects = data.projects.map((project) => ({ ...project, slug: slugify(project.title) }));

// Vérifié au chargement du module : le build échoue au lieu de publier une page qui en masque une autre.
const titles = new Map<string, string>();
for (const { slug, title } of projects) {
  if (!slug) throw new Error(`projects.json : le titre « ${title} » ne donne aucun slug.`);
  if (titles.has(slug)) {
    throw new Error(
      `projects.json : les projets « ${titles.get(slug)} » et « ${title} » donnent la même URL /projets/${slug}. Modifiez l'un des titres.`,
    );
  }
  titles.set(slug, title);
}

/** Les chemins "./media/…" du JSON deviennent absolus (les URL externes restent telles quelles). */
export const absolutePath = (src: string) => src.replace(/^\.\//, '/');

/** Filtres de la liste : clé d'URL (`/projets#<clé>`) -> texte cherché dans la catégorie, sans casse. */
export const filters = [
  { key: 'stage2026', category: 'Stage_2026', label: 'Stage 2026', style: 'color: #bb0012' },
  { key: 'web', category: 'Web', label: 'Développement Web' },
  { key: 'software', category: 'Software', label: 'Ingénierie Logicielle' },
  { key: 'other', category: 'Other', label: 'Autres' },
];
