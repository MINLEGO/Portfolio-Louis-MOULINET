import type { APIRoute } from 'astro';

// Convention des anciennes URLs : un endpoint à la requête par fichier `.php`.
export const prerender = false;

export const GET: APIRoute = ({ url, redirect }) => redirect('/' + url.search, 301);
