# Relais PHP vers VeilleFacile plutôt qu'un appel depuis le navigateur

Les Sources de la section Veille viennent de l'API VeilleFacile. VeilleFacile propose une clé en lecture seule (`READ_API_KEY`) et le CORS, ce qui permettrait au JavaScript du portfolio de l'appeler directement. Nous passons quand même par un relais PHP (`public/veille_api.php`) qui garde la clé côté serveur.

Raisons :

- Une clé placée dans le navigateur est lisible par n'importe qui. Elle donne accès à **toutes** les Sources, y compris les Sources privées (mot-clé `privé`). Le relais les exclut côté serveur, donc elles restent réellement privées.
- Le relais met la liste complète en cache (10 min, et sert l'ancien cache si l'API est indisponible). Il peut donc trier par date de parution, ce que l'API ne propose pas, et ne sollicite pas l'API (ni ses embeddings) à chaque visite.
- Les miniatures (Microlink, image tirée du README) sont résolues une seule fois par Source et mises en cache sur le serveur, au lieu d'une fois par visiteur. Le quota gratuit de Microlink est faible.
- Les résumés sont rendus en HTML par Parsedown, comme le texte des Expérimentations.

## Conséquences

- Le portfolio a besoin de `VEILLEFACILE_URL` et `VEILLEFACILE_READ_KEY` (variables d'environnement, ou `.env` à la racine en local).
- `CORS_ORIGINS` n'est pas nécessaire côté VeilleFacile.
- Les nouvelles Sources apparaissent avec jusqu'à 10 minutes de retard.
