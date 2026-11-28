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

/** Bloc d'une page Projet, au format `fields.blocks` de Keystatic (schéma : keystatic.config.ts). */
export type Block =
  | { discriminant: 'images'; value: { images: (string | null)[]; alt?: string } }
  | { discriminant: 'texte'; value: { titre?: string; contenu?: string } }
  | { discriminant: 'features'; value: { cartes: { icone?: string; titre?: string; description?: string; style: 'large' | 'normal' | 'accent' }[] } }
  | { discriminant: 'code'; value: { fichier?: string; version?: string; tags: string[]; code?: string } }
  | { discriminant: 'note'; value: { image?: string | null; alt?: string; texte?: string } };
// Les champs facultatifs : Keystatic n'écrit pas un texte vide.

/** Projets publiés dans l'ordre du JSON, avec le slug de leur URL `/projets/<slug>` et leur numéro (« 01 ») tiré de leur position. */
export const projects = data.projects
  .filter((project) => !project.brouillon)
  .map((project, i) => ({
    ...project,
    detail: { ...project.detail, blocs: project.detail.blocs as Block[] },
    slug: slugify(project.title),
    number: String(i + 1).padStart(2, '0'),
  }));


// Vérifié au chargement du module : le build échoue au lieu de publier une page qui en masque une autre.
const ids = new Set<number>();
for (const { id, title } of data.projects) {
  if (ids.has(id)) throw new Error(`projects.json : l'identifiant ${id} est utilisé deux fois (dont « ${title} »).`);
  ids.add(id);
}
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

/** Filtres de la liste : clé d'URL (`/projets#<clé>`) -> texte cherché dans la catégorie, sans casse. */
export const filters = [
  { key: 'stage2026', category: 'Stage_2026', label: 'Stage 2026', style: 'color: #bb0012' },
  { key: 'web', category: 'Web', label: 'Développement Web' },
  { key: 'software', category: 'Software', label: 'Ingénierie Logicielle' },
  { key: 'other', category: 'Other', label: 'Autres' },
];
