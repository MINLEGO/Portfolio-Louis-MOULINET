import type { APIRoute } from 'astro';
import { relay } from '../lib/veille';

// Relais JSON vers VeilleFacile : même adresse et même interface HTTP qu'avec le PHP.
export const prerender = false;

export const GET: APIRoute = ({ url }) => relay.handle(url);
