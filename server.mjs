// Point d'entrée de production : le serveur d'Astro, plus un en-tête qu'il ne sait pas poser sur les fichiers de public/.
// Une Démonstration (HTML, SVG) ouverte directement reste isolée du domaine, comme dans l'iframe de la Veille.
import http from 'node:http';

process.env.ASTRO_NODE_AUTOSTART = 'disabled';
const { handler } = await import('./dist/server/entry.mjs');

const port = Number(process.env.PORT ?? 8080);
http
  .createServer((req, res) => {
    if (req.url?.startsWith('/experimentations/')) res.setHeader('Content-Security-Policy', 'sandbox allow-scripts');
    handler(req, res);
  })
  .listen(port, process.env.HOST ?? '0.0.0.0', () => console.log(`Serveur sur le port ${port}`));
