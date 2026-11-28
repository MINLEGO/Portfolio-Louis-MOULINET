# Admin : Keystatic en mode GitHub plutôt qu'un Admin fait maison

L'Admin doit permettre de modifier souvent les Projets et les Expérimentations depuis le site en ligne. Or ces pages sont générées au build et leurs images passent par le pipeline d'Astro (ADR 0002). Nous utilisons Keystatic en mode GitHub, servi sur `/keystatic` par l'adaptateur Node : chaque enregistrement est un commit sur le dépôt, et Coolify reconstruit le site.

Alternatives écartées :

- **Admin fait maison qui écrit dans un volume persistant** : il faudrait rendre les pages Projets et Veille à la requête, faire le rendu markdown à l'exécution et abandonner le pipeline d'images. C'est contraire à l'objectif de performance de l'ADR 0002.
- **Admin fait maison qui commit via l'API GitHub** : même résultat que Keystatic, mais l'authentification, l'éditeur de Blocs et l'envoi de fichiers seraient à écrire.

## Conséquences

- Git reste la source de vérité du contenu. L'historique remplace la corbeille et les sauvegardes.
- Une modification apparaît sur le site après la reconstruction (environ 1 à 2 min).
- L'authentification passe par GitHub : il faut une GitHub App et les variables `KEYSTATIC_GITHUB_CLIENT_ID`, `KEYSTATIC_GITHUB_CLIENT_SECRET`, `KEYSTATIC_SECRET` et `PUBLIC_KEYSTATIC_GITHUB_APP_SLUG`.
- Les fichiers suivent les conventions de Keystatic :
  - Une Expérimentation est un seul `index.md` : métadonnées en en-tête JSON, puis le texte.
  - Les Projets forment un singleton (`src/data/projects.json`), une liste réordonnable.
  - Keystatic nomme les fichiers envoyés d'après leur place dans les données (`media/projects/0/image.png`, `demos/2/value/fichier.html`) et les renomme quand on réordonne. Un lien relatif écrit dans une Démonstration HTML vers un autre fichier casserait.
- Un Brouillon n'est pas publié sur le site, mais le dépôt est public, et le texte d'une Expérimentation reste lisible à son URL dans `public/`.
- `server.mjs` remplace le point d'entrée d'Astro pour ajouter `Content-Security-Policy: sandbox allow-scripts` aux fichiers de `/experimentations/`, qu'Astro sert sans en-tête.
