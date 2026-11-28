// @ts-check
import { satteri } from '@astrojs/markdown-satteri';
import node from '@astrojs/node';
import react from '@astrojs/react';
import keystatic from '@keystatic/astro';
import { defineConfig } from 'astro/config';

// Statique par défaut ; une route rendue à la requête exporte `prerender = false` (ADR 0002).
export default defineConfig({
  // Seule source du domaine : canonicals, Open Graph, données structurées, sitemap et robots.txt en découlent.
  site: 'https://louis-moulinet.fr',
  adapter: node({ mode: 'standalone' }),
  // Admin sur /keystatic (ADR 0003) ; React ne sert qu'à lui, aucune page publique n'en envoie.
  integrations: [react(), keystatic()],
  // Le défaut ('jsx') supprime les retours à la ligne entre éléments en ligne, ce qui change le rendu.
  compressHTML: true,
  // Textes des Expérimentations : réglés pour leur rendu d'origine (ni typographie « intelligente »,
  // ni coloration du code, ni notes de bas de page).
  markdown: {
    syntaxHighlight: false,
    processor: satteri({ features: { gfm: { footnotes: false }, smartPunctuation: false } }),
  },
  // shortcut: CSS non minifié. Lightning CSS (défaut de Vite) supprime `backdrop-filter` quand
  // `-webkit-backdrop-filter` le suit, et Chrome perd le flou. À réactiver avec un autre minifieur
  // (vérifier les 11 déclarations) si le poids du CSS pèse dans les mesures Lighthouse.
  // Les polices restent des fichiers : sous 4 Ko, Vite les recopierait en base64 dans le CSS, qui bloque l'affichage.
  vite: { build: { cssMinify: false, assetsInlineLimit: (file) => (file.endsWith('.woff2') ? false : undefined) } },
});
