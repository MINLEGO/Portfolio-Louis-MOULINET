// @ts-check
import node from '@astrojs/node';
import { defineConfig } from 'astro/config';

// Statique par défaut ; une route rendue à la requête exporte `prerender = false` (ADR 0002).
export default defineConfig({
  site: 'https://louismoulinet.com',
  adapter: node({ mode: 'standalone' }),
  // Le défaut ('jsx') supprime les retours à la ligne entre éléments en ligne, ce qui change le rendu.
  compressHTML: true,
});
