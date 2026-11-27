# Migration vers Astro en rendu hybride

Le portfolio était du PHP sans framework : `<head>`, header et footer copiés dans chaque page, CSS global de 2 200 lignes, polices et images non optimisées. Les évolutions prévues sont surtout visuelles, avec un objectif de performance maximale. Nous migrons vers Astro : pages statiques générées au build (accueil, projets, détail projet) et rendu serveur à la requête (adaptateur Node) uniquement pour `/veille`, `/veille/archive`, le relais VeilleFacile et les redirections des anciennes URLs `.php` (leur réponse dépend de la requête : paramètres conservés, `id` de projet).

Alternatives écartées :

- **PHP + `include`** : règle la duplication à moindre coût, mais n'apporte ni composants, ni optimisation d'images et de polices au build.
- **Next.js / Nuxt** : orientés application, envoient du JS client sur chaque page, à l'opposé de l'objectif de performance.
- **Laravel / Symfony** : MVC complet, surdimensionné pour quatre pages sans base de données.

## Conséquences

- L'ADR 0001 reste valable : le relais est porté en Node, la clé VeilleFacile ne quitte jamais le serveur et les Sources privées sont exclues côté serveur. Même cache (10 min, ancien cache servi si l'API tombe, miniatures 30 jours).
- Parsedown disparaît avec le PHP. Les résumés (contenu externe) sont rendus par markdown-it puis nettoyés par sanitize-html ; les textes des Expérimentations (contenu de confiance, HTML autorisé) par le rendu markdown d'Astro, au build.
- Les pages Veille restent rendues à la requête pour joindre les Expérimentations aux Sources live ; tout le reste demande un redéploiement pour changer, comme avant.
- Les anciennes URLs `.php` sont redirigées en 301 vers les nouvelles (`/projets/<slug>`, `/veille`, …).
- Le déploiement Coolify passe d'une image PHP/Apache à une image Node multi-stage.
