import type { APIRoute } from 'astro';
import { projects } from '../lib/projects';

// Généré au build : les projets suivent projects.json. Une nouvelle page fixe s'ajoute ici.
export const GET: APIRoute = ({ site }) => {
  const pages = [
    ['/', '1.0'],
    ['/projets', '0.8'],
    ['/veille', '0.6'],
    ['/veille/archive', '0.4'],
    ...projects.map(({ slug }) => [`/projets/${slug}`, '0.7']),
  ];
  const urls = pages.map(
    ([path, priority]) => `  <url>\n    <loc>${new URL(path, site).href}</loc>\n    <priority>${priority}</priority>\n  </url>`,
  );
  return new Response(
    `<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n${urls.join('\n')}\n</urlset>\n`,
    { headers: { 'Content-Type': 'application/xml; charset=utf-8' } },
  );
};
