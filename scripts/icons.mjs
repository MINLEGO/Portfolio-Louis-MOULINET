// Sous-ensemble de Material Symbols : la police servie ne contient que les icônes utilisées par le site.
// Une icône absente s'affiche en toutes lettres.
//   npm run icons                    après l'ajout d'une icône : régénère la police et sa liste (accès à Google Fonts)
//   node scripts/icons.mjs --check   échoue si une icône utilisée manque (lancé par `npm run build`)
import { globSync, readFileSync, writeFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';

process.chdir(fileURLToPath(new URL('..', import.meta.url)));
const FONT = 'src/styles/fonts/material-symbols-outlined.woff2';
const LIST = 'src/styles/fonts/material-symbols.json';

// Noms posés par du code à l'exécution, que la recherche ci-dessous ne voit pas : ceux du menu (Header.astro).
// Ceux des Formats (`${ICONS…}` dans scripts/veille.ts) sont lus dans leur fichier, plus bas.
const DYNAMIC = ['menu', 'close'];
const FORMAT_ICONS = 'src/data/format-icons.json';
// Valeur présente dans projects.json qui n'est pas une icône : elle affiche la lettre « l » de la police, comme avant.
const LETTERS = ['l'];

const read = (file) => readFileSync(file, 'utf8');
/** nom -> premier endroit qui l'utilise */
const used = new Map();
const add = (name, where) => name && !used.has(name) && used.set(name, where);

DYNAMIC.forEach((name) => add(name, 'scripts/icons.mjs, DYNAMIC'));
Object.values(JSON.parse(read(FORMAT_ICONS))).forEach((name) => add(name, FORMAT_ICONS));
for (const { title, detail } of JSON.parse(read('src/data/projects.json')).projects) {
  for (const n of [1, 2, 3]) add(detail[`feature${n}_icon`], `projects.json, « ${title} », feature${n}_icon`);
}
// Icônes écrites en dur : <span class="material-symbols-outlined …">nom</span> (le HTML des Expérimentations compris).
for (const file of globSync(['src/**/*.{astro,ts}', 'public/experimentations/*/index.md'])) {
  for (const [, name] of read(file).matchAll(/material-symbols-outlined[^<>]*>\s*([^<>{}$\s][^<]*?)\s*</g)) add(name, file);
}

const fail = (message, names) => {
  console.error(`${message}\n${names.map((name) => `  - « ${name} » (${used.get(name)})`).join('\n')}`);
  // `exitCode` et non `process.exit()`, qui fait planter Node sous Windows après un `fetch`.
  process.exitCode = 1;
};

async function update() {
  const get = async (url) => {
    // Sans navigateur récent dans le User-Agent, Google Fonts répond avec du TTF au lieu du woff2.
    const res = await fetch(url, { headers: { 'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36' } });
    if (!res.ok) throw new Error(`${res.status} ${url}`);
    return Buffer.from(await res.arrayBuffer());
  };
  // Google Fonts répond 200 même pour un nom inconnu : la liste officielle des icônes fait foi.
  const codepoints = await get('https://raw.githubusercontent.com/google/material-design-icons/master/variablefont/MaterialSymbolsOutlined%5BFILL%2CGRAD%2Copsz%2Cwght%5D.codepoints');
  const known = new Set(codepoints.toString().split('\n').map((line) => line.split(' ')[0]));
  const unknown = [...used.keys()].filter((name) => !known.has(name) && !LETTERS.includes(name));
  if (unknown.length) return fail('Noms inconnus de Material Symbols (https://fonts.google.com/icons) :', unknown);

  const names = [...used.keys()].sort();
  // Google exige des noms triés.
  const css = await get(`https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&icon_names=${names.filter((name) => !LETTERS.includes(name))}`);
  const font = await get(css.toString().match(/url\((https:\/\/fonts\.gstatic\.com\/[^)]+)\)/)[1]);
  if (font.subarray(0, 4).toString() !== 'wOF2') throw new Error('Google Fonts n’a pas renvoyé un fichier woff2.');
  writeFileSync(FONT, font);
  writeFileSync(LIST, JSON.stringify(names, null, 2) + '\n');
  console.log(`${FONT} : ${names.length} noms, ${font.length} octets.`);
}

if (!process.argv.includes('--check')) {
  await update();
} else {
  const included = new Set(JSON.parse(read(LIST)));
  const missing = [...used.keys()].filter((name) => !included.has(name));
  if (missing.length) {
    fail('Icônes absentes de la police. Vérifiez le nom (en minuscules, ex. « arrow_back »), puis lancez `npm run icons`.', missing);
  }
}
