// Admin (ADR 0003) : /keystatic. En production, chaque enregistrement est un commit sur GitHub, puis Coolify reconstruit.
import { collection, config, fields, singleton } from '@keystatic/core';

const media = { directory: 'src/assets/media', publicPath: './media/' };
const texte = (label: string) => fields.text({ label, multiline: true });

export default config({
  storage: import.meta.env.PROD
    ? { kind: 'github', repo: { owner: 'MINLEGO', name: 'Portfolio-Louis-MOULINET' } }
    : { kind: 'local' },
  ui: { brand: { name: 'Portfolio' } },
  singletons: {
    projets: singleton({
      label: 'Projets',
      path: 'src/data/projects',
      format: { data: 'json' },
      schema: {
        projects: fields.array(
          fields.object({
            title: fields.text({ label: 'Titre', validation: { isRequired: true } }),
            id: fields.integer({
              label: 'Identifiant',
              description: 'Unique et définitif : les anciennes URL project_detail.php?id= et la mise en avant de l’accueil en dépendent.',
              validation: { isRequired: true },
            }),
            brouillon: fields.checkbox({ label: 'Brouillon', description: 'Invisible sur le site public.' }),
            category: fields.text({ label: 'Catégorie', description: 'Ex. Stage_2026/Web : les filtres de la liste cherchent Web, Software, Other…' }),
            tags: fields.array(fields.text({ label: 'Tag' }), { label: 'Tags', itemLabel: (p) => p.value }),
            description: texte('Description'),
            image: fields.image({ label: 'Miniature', ...media }),
            image_alt: fields.text({ label: 'Texte alternatif de la miniature' }),
            detail: fields.object(
              {
                subtitle: fields.text({ label: 'Sous-titre (accueil)' }),
                status: fields.text({ label: 'Statut' }),
                stack: fields.text({ label: 'Stack' }),
                year: fields.text({ label: 'Année' }),
                blocs: fields.blocks(
                  {
                    images: {
                      label: 'Images',
                      schema: fields.object({
                        images: fields.array(fields.image({ label: 'Image', ...media }), {
                          label: 'Images',
                          description: 'Plusieurs images forment un carrousel.',
                        }),
                        alt: fields.text({ label: 'Texte alternatif' }),
                      }),
                    },
                    texte: {
                      label: 'Texte',
                      itemLabel: (p) => p.fields.titre.value || 'Texte',
                      schema: fields.object({
                        titre: fields.text({ label: 'Titre' }),
                        contenu: fields.text({ label: 'Contenu (Markdown)', multiline: true }),
                      }),
                    },
                    features: {
                      label: 'Features',
                      schema: fields.object({
                        cartes: fields.array(
                          fields.object({
                            icone: fields.text({ label: 'Icône', description: 'Nom Material Symbols. Une nouvelle icône demande `npm run icons`.' }),
                            titre: fields.text({ label: 'Titre' }),
                            description: texte('Description'),
                            style: fields.select({
                              label: 'Style',
                              options: [
                                { label: 'Large', value: 'large' },
                                { label: 'Normal', value: 'normal' },
                                { label: 'Accent', value: 'accent' },
                              ],
                              defaultValue: 'normal',
                            }),
                          }),
                          { label: 'Cartes', itemLabel: (p) => p.fields.titre.value || 'Carte' },
                        ),
                      }),
                    },
                    code: {
                      label: 'Code',
                      itemLabel: (p) => p.fields.fichier.value || 'Code',
                      schema: fields.object({
                        fichier: fields.text({ label: 'Fichier' }),
                        version: fields.text({ label: 'Version' }),
                        tags: fields.array(fields.text({ label: 'Tag' }), { label: 'Tags techniques', itemLabel: (p) => p.value }),
                        code: fields.text({ label: 'Code', multiline: true }),
                      }),
                    },
                    note: {
                      label: 'Note',
                      schema: fields.object({
                        image: fields.image({ label: 'Image', ...media }),
                        alt: fields.text({ label: 'Texte alternatif' }),
                        texte: texte('Texte'),
                      }),
                    },
                  },
                  { label: 'Blocs' },
                ),
              },
              { label: 'Page du projet' },
            ),
          }),
          { label: 'Projets', itemLabel: (p) => (p.fields.brouillon.value ? '[Brouillon] ' : '') + p.fields.title.value },
        ),
      },
    }),
  },
  collections: {
    experimentations: collection({
      label: 'Expérimentations',
      path: 'public/experimentations/*/',
      slugField: 'titre',
      // Un seul fichier index.md : métadonnées en en-tête JSON, puis le texte.
      format: { data: 'json', contentField: 'index' },
      entryLayout: 'content',
      columns: ['titre', 'date'],
      schema: {
        titre: fields.slug({ name: { label: 'Titre' }, slug: { label: 'Dossier', description: 'Nom du dossier, définitif.' } }),
        date: fields.date({ label: 'Date' }),
        brouillon: fields.checkbox({ label: 'Brouillon', description: 'Invisible sur le site public.' }),
        source_url: fields.url({ label: 'URL de la Source', description: 'URL de la Source dans VeilleFacile.' }),
        entete: fields.integer({ label: 'Démonstrations sous le titre', description: 'Les N premières Démonstrations ; les suivantes se placent avec {{demo:N}} dans le texte.' }),
        demos: fields.blocks(
          {
            galerie: {
              label: 'Galerie',
              schema: fields.object({
                images: fields.array(
                  fields.object({
                    image: fields.image({ label: 'Image' }),
                    legende: fields.text({ label: 'Légende' }),
                  }),
                  { label: 'Images', itemLabel: (p) => p.fields.legende.value || 'Image' },
                ),
              }),
            },
            video: { label: 'Vidéo', schema: fields.object({ url: fields.url({ label: 'URL YouTube' }) }) },
            html: {
              label: 'Page HTML',
              schema: fields.object({
                fichier: fields.file({ label: 'Fichier HTML' }),
                hauteur: fields.integer({ label: 'Hauteur (px)', defaultValue: 400 }),
              }),
            },
          },
          { label: 'Démonstrations' },
        ),
        index: fields.markdoc({ label: 'Texte', extension: 'md' }),
      },
    }),
  },
});
