import type { APIRoute } from 'astro';
import { filters } from '../lib/projects';

export const prerender = false;

export const GET: APIRoute = ({ url, redirect }) => {
  const query = new URLSearchParams(url.search);
  // L'ancien `?filter=` devient le fragment lu par la page ; le reste de la query est conservé.
  const filter = filters.find(({ key }) => key === query.get('filter'));
  query.delete('filter');
  return redirect('/projets' + (query.size ? `?${query}` : '') + (filter ? `#${filter.key}` : ''), 301);
};
