/** Cartes d'attente de la Veille, partagées par le gabarit et le script client. */
export const skeletons = (n: number) =>
  '<div class="vs-card vs-skeleton" aria-hidden="true"><div class="vs-thumb"></div><div class="vs-body"><span></span><span></span><span></span></div></div>'.repeat(n);
