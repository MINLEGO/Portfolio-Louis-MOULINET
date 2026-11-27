import type { APIRoute } from 'astro';
import { projects } from '../lib/projects';

export const prerender = false;

export const GET: APIRoute = ({ url, redirect }) => {
  // Sans `id`, l'ancienne page affichait le projet 1.
  const id = url.searchParams.get('id') ?? '1';
  const project = projects.find((p) => String(p.id) === id);
  if (!project) {
    return new Response('Projet introuvable', { status: 404, headers: { 'Content-Type': 'text/plain; charset=utf-8' } });
  }
  return redirect(`/projets/${project.slug}`, 301);
};
