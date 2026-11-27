import type { APIRoute } from 'astro';

export const prerender = false;

export const GET: APIRoute = ({ url, redirect }) => redirect('/veille/archive' + url.search, 301);
