// Partagé entre le serveur et le script du navigateur : aucune dépendance Node ici.

export const youtubeId = (url: string) => url.match(/(?:[?&]v=|youtu\.be\/|\/shorts\/|\/embed\/)([\w-]{11})/)?.[1];

/** Coupe en caractères, sans casser un emoji. */
export const cut = (value: string | null, length: number) => Array.from(value ?? '').slice(0, length).join('');

/** Recherche `q` telle que la page et le relais l'acceptent (le relais retire en plus les espaces autour). */
export const cutQuery = (value: string | null) => cut(value, 200);
