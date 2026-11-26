<?php
// Accès à VeilleFacile côté serveur (voir docs/adr/0001-relais-php-vers-veillefacile.md).
require_once __DIR__ . '/Parsedown.php';

const VF_TTL = 600;
const VF_SECTIONS = ['What it is' => "Ce que c'est", 'Why it matters' => "Pourquoi c'est important", 'Key points' => 'Points clés'];

function vf_env(string $key): string
{
    $value = getenv($key);
    if ($value !== false && $value !== '') return $value;
    static $file = null;
    if ($file === null) {
        $file = [];
        $path = __DIR__ . '/../../.env'; // racine du projet, hors du dossier servi
        foreach (is_readable($path) ? file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [] as $line) {
            if (str_starts_with(ltrim($line), '#') || !str_contains($line, '=')) continue;
            [$name, $val] = explode('=', $line, 2);
            $file[trim($name)] = trim(trim($val), '"\'');
        }
    }
    return $file[$key] ?? '';
}

/** Cache fichier : renvoie ['data', 'at', 'stale']. Sert l'ancien cache si $fetch échoue. N'enregistre pas null. */
function vf_cache(string $key, int $ttl, callable $fetch): array
{
    $file = sys_get_temp_dir() . '/veille_' . md5($key) . '.json';
    $cached = is_file($file) ? ['data' => json_decode(file_get_contents($file), true), 'at' => filemtime($file), 'stale' => false] : null;
    if ($cached && time() - $cached['at'] < $ttl) return $cached;
    try {
        $data = $fetch();
    } catch (Throwable $e) {
        if ($cached) return ['stale' => true] + $cached;
        throw $e;
    }
    if ($data !== null) file_put_contents($file, json_encode($data), LOCK_EX);
    return ['data' => $data, 'at' => time(), 'stale' => false];
}

function vf_get(string $path, array $query = []): array
{
    $url = rtrim(vf_env('VEILLEFACILE_URL'), '/') . $path . ($query ? '?' . http_build_query($query) : '');
    $ctx = stream_context_create(['http' => [
        'header' => 'Authorization: Bearer ' . vf_env('VEILLEFACILE_READ_KEY'),
        'timeout' => 15,
        'ignore_errors' => true,
    ]]);
    $body = @file_get_contents($url, false, $ctx);
    $status = (int) (explode(' ', $http_response_header[0] ?? '')[1] ?? 0);
    if ($body === false || $status !== 200) throw new RuntimeException("VeilleFacile $path : HTTP $status");
    return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
}

/** Toutes les Sources publiques, triées par date de parution puis de capture. */
function vf_all_sources(): array
{
    try {
        return vf_cache('all', VF_TTL, function () {
            $all = [];
            for ($offset = 0; ; $offset += 100) {
                $page = vf_get('/api/sources', ['limit' => 100, 'offset' => $offset])['results'];
                $all = array_merge($all, $page);
                if (count($page) < 100) break;
            }
            $sources = vf_prepare($all);
            usort($sources, fn($a, $b) => strtotime($b['date']) <=> strtotime($a['date']));
            return $sources;
        });
    } catch (Throwable $e) {
        // Hors ligne et sans cache : fausses données (instantané de l'API).
        $fixture = json_decode(file_get_contents(__DIR__ . '/veille_fixture.json'), true);
        return ['data' => vf_prepare($fixture['results']), 'at' => filemtime(__DIR__ . '/veille_fixture.json'), 'stale' => true];
    }
}

/** Recherche hybride de l'API, ordre de pertinence conservé. */
function vf_search(string $q): array
{
    return vf_cache('q:' . mb_strtolower($q), VF_TTL, fn() => vf_prepare(vf_get('/api/sources', ['q' => $q, 'limit' => 100])['results']));
}

/** Format API -> format d'affichage ; exclut les Sources privées. */
function vf_prepare(array $raw): array
{
    $out = [];
    $md = (new Parsedown())->setSafeMode(true); // résumés générés à partir de pages externes
    foreach ($raw as $s) {
        if (in_array('privé', array_map('mb_strtolower', $s['keywords']), true)) continue;
        $sections = vf_sections($s['summary'] ?? '');
        $summaryMd = $sections
            ? implode("\n\n", array_map(fn($h, $b) => "### $h\n\n$b", array_keys($sections), $sections))
            : ($s['summary'] ?? '');
        $youtube = vf_youtube_id($s['url']);
        $out[] = [
            'id' => $s['id'],
            'url' => $s['url'],
            'canonical_url' => $s['canonical_url'] ?? $s['url'],
            'title' => $s['title'],
            'site' => preg_replace('/^www\./', '', $s['origin_site']),
            'format' => vf_format($s['origin_site']),
            'date' => $s['published_at'] ?: $s['created_at'],
            'date_kind' => $s['published_at'] ? 'parution' : 'capture',
            'keywords' => array_values($s['keywords']),
            'excerpt' => $sections["Ce que c'est"] ?? '',
            'summary_html' => $md->text($summaryMd),
            'thumb' => $youtube ? "https://i.ytimg.com/vi/$youtube/hqdefault.jpg" : null,
        ];
    }
    return $out;
}

function vf_sections(string $summary): array
{
    $out = [];
    foreach (preg_split('/^##\s+/m', $summary) as $chunk) {
        [$head, $body] = array_pad(explode("\n", $chunk, 2), 2, '');
        if (isset(VF_SECTIONS[trim($head)])) $out[VF_SECTIONS[trim($head)]] = trim($body);
    }
    return $out;
}

function vf_format(string $site): string
{
    return match (true) {
        str_contains($site, 'youtube.com'), $site === 'youtu.be' => 'Vidéo',
        $site === 'github.com' => 'Code',
        $site === 'huggingface.co' => 'Modèle',
        default => 'Article',
    };
}

function vf_youtube_id(string $url): ?string
{
    return preg_match('~(?:[?&]v=|youtu\.be/|/shorts/|/embed/)([\w-]{11})~', $url, $m) ? $m[1] : null;
}

/** Miniature d'une Source non-YouTube, résolue une fois puis mise en cache 30 jours. */
function vf_thumb(array $s): ?string
{
    return vf_cache('thumb:' . $s['id'] . $s['url'], 30 * 86400, function () use ($s) {
        if (in_array($s['site'], ['github.com', 'huggingface.co'], true)) {
            try {
                $text = vf_get("/api/sources/{$s['id']}/text", ['length' => 200000])['text'];
                if ($img = vf_readme_image($text, $s['url'])) return $img;
            } catch (Throwable $e) {
            }
            return vf_og_image($s['url']);
        }
        return vf_microlink($s['url']);
    })['data'];
}

/** Première image du README qui n'est pas un badge (règles de Q33). */
function vf_readme_image(string $text, string $repoUrl): ?string
{
    preg_match_all('~!\[[^\]]*\]\(\s*<?([^)\s>]+)|<img[^>]+src=["\']([^"\']+)~i', $text, $m, PREG_SET_ORDER);
    $images = [];
    foreach ($m as $match) {
        $src = ($match[2] ?? '') !== '' ? $match[2] : $match[1];
        if (!preg_match('~shields\.io|badge|trendshift~i', $src)) $images[] = $src;
    }
    if (!$images) return null;
    $pick = $images[0];
    // Logo décliné clair/sombre : garder la variante lisible sur fond clair.
    if (isset($images[1]) && str_contains($pick, 'white') && str_replace('white', 'black', $pick) === $images[1]) $pick = $images[1];
    return vf_absolute($pick, $repoUrl);
}

function vf_absolute(string $src, string $repoUrl): string
{
    if (preg_match('~^https?://~', $src)) return $src;
    $src = ltrim(preg_replace('~^\./~', '', $src), '/');
    // shortcut: suppose une URL owner/repo (pas de datasets/ ni spaces/ sur HF), à adapter si on capture ces pages.
    $repo = implode('/', array_slice(explode('/', trim(parse_url($repoUrl, PHP_URL_PATH), '/')), 0, 2));
    if (str_contains($repoUrl, 'github.com')) {
        // raw.githubusercontent sert les SVG en text/plain sauf avec ?sanitize=true
        return "https://raw.githubusercontent.com/$repo/HEAD/$src" . (str_ends_with($src, '.svg') ? '?sanitize=true' : '');
    }
    return "https://huggingface.co/$repo/resolve/main/$src";
}

function vf_og_image(string $repoUrl): string
{
    $repo = implode('/', array_slice(explode('/', trim(parse_url($repoUrl, PHP_URL_PATH), '/')), 0, 2));
    return str_contains($repoUrl, 'github.com')
        ? "https://opengraph.githubassets.com/1/$repo"
        : "https://cdn-thumbnails.huggingface.co/social-thumbnails/models/$repo.png";
}

function vf_microlink(string $url): ?string
{
    $ctx = stream_context_create(['http' => ['timeout' => 10, 'ignore_errors' => true]]);
    $json = json_decode(@file_get_contents('https://api.microlink.io/?url=' . urlencode($url), false, $ctx) ?: '', true);
    return $json['data']['image']['url'] ?? $json['data']['logo']['url'] ?? null;
}

function vf_norm_url(string $url): string
{
    return rtrim(preg_replace('~^https?://(www\.)?~', '', $url), '/');
}

/** Expérimentations : public/experimentations/<slug>/{meta.json,index.md}, liées à leur Source par URL. */
function vf_experiments(array $sources): array
{
    $bySourceUrl = [];
    foreach ($sources as $s) {
        $bySourceUrl[vf_norm_url($s['url'])] = $s;
        $bySourceUrl[vf_norm_url($s['canonical_url'])] = $s;
    }
    $md = new Parsedown(); // contenu écrit par moi : HTML autorisé
    $out = [];
    foreach (glob(__DIR__ . '/../experimentations/*/meta.json') as $metaFile) {
        $dir = dirname($metaFile);
        $meta = json_decode(file_get_contents($metaFile), true);
        if (!is_array($meta)) continue;
        $slug = basename($dir);
        $out[] = $meta + [
            'slug' => $slug,
            'base' => "experimentations/$slug/",
            'html' => is_file("$dir/index.md") ? $md->text(file_get_contents("$dir/index.md")) : '',
            'source' => $bySourceUrl[vf_norm_url($meta['source_url'] ?? '')] ?? null,
        ];
    }
    usort($out, fn($a, $b) => strcmp($b['date'] ?? '', $a['date'] ?? ''));
    return $out;
}

/** Liste filtrée et paginée pour le relais. */
function vf_list(string $q, string $format, string $keyword, int $offset, int $limit): array
{
    $res = $q === '' ? vf_all_sources() : vf_search($q);
    $slugs = [];
    foreach (vf_experiments([]) as $e) $slugs[vf_norm_url($e['source_url'] ?? '')] = $e['slug'];
    $items = [];
    foreach ($res['data'] as $s) {
        if ($format !== '' && $s['format'] !== $format) continue;
        if ($keyword !== '' && !in_array(mb_strtolower($keyword), array_map('mb_strtolower', $s['keywords']), true)) continue;
        $s['exp'] = $slugs[vf_norm_url($s['url'])] ?? $slugs[vf_norm_url($s['canonical_url'])] ?? null;
        $items[] = $s;
    }
    return [
        'results' => array_slice($items, $offset, $limit),
        'total' => count($items),
        'offset' => $offset,
        'stale_since' => $res['stale'] ? date('d/m/Y H:i', $res['at']) : null,
    ];
}
