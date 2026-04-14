<?php
// ─── Data Handling ─────────────────────────────────────────────────────────
$jsonPath = __DIR__ . '/projects.json';
$data     = json_decode(file_get_contents($jsonPath), true);
$projects = $data['projects'];

// ─── Save Handler ──────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'save') {
        $id = (int)$_POST['id'];

        // Build tag arrays from comma-separated strings
        $tags      = array_map('trim', explode(',', $_POST['tags']      ?? ''));
        $tech_tags = array_map('trim', explode(',', $_POST['tech_tags'] ?? ''));

        // Find and update project
        foreach ($data['projects'] as &$p) {
            if ($p['id'] === $id) {
                $p['title']       = $_POST['title']       ?? $p['title'];
                $p['category']    = $_POST['category']    ?? $p['category'];
                $p['description'] = $_POST['description'] ?? $p['description'];
                $p['image']       = $_POST['image']       ?? $p['image'];
                $p['image_alt']   = $_POST['image_alt']   ?? $p['image_alt'];
                $p['tags']        = $tags;

                $p['detail']['subtitle']          = $_POST['subtitle']          ?? '';
                $p['detail']['status']            = $_POST['status']            ?? '';
                $p['detail']['stack']             = $_POST['stack']             ?? '';
                $p['detail']['year']              = $_POST['year']              ?? '';
                $p['detail']['challenge']         = $_POST['challenge']         ?? '';
                $p['detail']['challenge2']        = $_POST['challenge2']        ?? '';
                $p['detail']['feature1_icon']     = $_POST['feature1_icon']     ?? '';
                $p['detail']['feature1_title']    = $_POST['feature1_title']    ?? '';
                $p['detail']['feature1_desc']     = $_POST['feature1_desc']     ?? '';
                $p['detail']['feature2_icon']     = $_POST['feature2_icon']     ?? '';
                $p['detail']['feature2_title']    = $_POST['feature2_title']    ?? '';
                $p['detail']['feature2_desc']     = $_POST['feature2_desc']     ?? '';
                $p['detail']['feature3_icon']     = $_POST['feature3_icon']     ?? '';
                $p['detail']['feature3_title']    = $_POST['feature3_title']    ?? '';
                $p['detail']['feature3_desc']     = $_POST['feature3_desc']     ?? '';
                $p['detail']['detail_image']      = $_POST['detail_image']      ?? '';
                $p['detail']['detail_image_alt']  = $_POST['detail_image_alt']  ?? '';
                $p['detail']['tech_tags']         = $tech_tags;
                $p['detail']['code_file']         = $_POST['code_file']         ?? '';
                $p['detail']['code_version']      = $_POST['code_version']      ?? '';
                $p['detail']['code_block']        = $_POST['code_block']        ?? '';
                $p['detail']['detail_image2']     = $_POST['detail_image2']     ?? '';
                $p['detail']['detail_image2_alt'] = $_POST['detail_image2_alt'] ?? '';
                $p['detail']['detail_note']       = $_POST['detail_note']       ?? '';
                break;
            }
        }
        unset($p);
        file_put_contents($jsonPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        header('Location: editor.php?saved=1&id=' . $id);
        exit;
    } elseif ($_POST['action'] === 'new') {
        $maxId = 0;
        foreach ($data['projects'] as $p) {
            if ($p['id'] > $maxId) $maxId = $p['id'];
        }
        $newId = $maxId + 1;
        $data['projects'][] = [
            'id'        => $newId,
            'index'     => str_pad($newId, 2, '0', STR_PAD_LEFT),
            'category'  => 'Nouveau',
            'title'     => 'Nouveau Projet',
            'tags'      => [],
            'description' => '',
            'image'     => '',
            'image_alt' => '',
            'detail'    => [
                'status'   => '',
                'stack' => '',
                'year' => '',
                'challenge' => '',
                'challenge2' => '',
                'feature1_icon' => '',
                'feature1_title' => '',
                'feature1_desc' => '',
                'feature2_icon' => '',
                'feature2_title' => '',
                'feature2_desc' => '',
                'feature3_icon' => '',
                'feature3_title' => '',
                'feature3_desc' => '',
                'detail_image' => '',
                'detail_image_alt' => '',
                'tech_tags'    => [],
                'code_file'    => '',
                'code_version' => '',
                'code_block' => '',
                'detail_image2' => '',
                'detail_image2_alt' => '',
                'detail_note' => '',
            ]
        ];
        file_put_contents($jsonPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        header('Location: editor.php?id=' . $newId);
        exit;
    } elseif ($_POST['action'] === 'delete') {
        $id = (int)$_POST['id'];
        $data['projects'] = array_values(array_filter($data['projects'], fn($p) => $p['id'] !== $id));
        file_put_contents($jsonPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        header('Location: editor.php?deleted=1');
        exit;
    }
}

// ─── Determine View ────────────────────────────────────────────────────────
$editId  = isset($_GET['id']) ? (int)$_GET['id'] : null;
$project = null;
$d       = null;

if ($editId !== null) {
    foreach ($projects as $p) {
        if ($p['id'] === $editId) {
            $project = $p;
            break;
        }
    }
    if (!$project) {
        $project = $projects[0];
        $editId = $project['id'];
    }
    $d = $project['detail'];
}

$saved = isset($_GET['saved']);

// Helper: render an editable text input inline
function ei($name, $value, $class = '')
{
    $v = htmlspecialchars($value ?? '');
    return "<input type=\"text\" name=\"{$name}\" value=\"{$v}\" class=\"ed-input {$class}\" autocomplete=\"off\">";
}
// Helper: render a textarea inline
function eta($name, $value, $rows = 3, $class = '')
{
    $v = htmlspecialchars($value ?? '');
    return "<textarea name=\"{$name}\" rows=\"{$rows}\" class=\"ed-textarea {$class}\">{$v}</textarea>";
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Éditeur de Projets | Portfolio</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&family=Manrope:wght@400;500;600&family=JetBrains+Mono&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../style.css">
    <style>
        /* ── Editor-specific overlay styles ── */
        .ed-toolbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            height: 56px;
            background: rgba(10, 10, 15, 0.92);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            font-family: 'Space Grotesk', sans-serif;
        }

        .ed-toolbar-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .ed-toolbar-title {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: rgba(255, 255, 255, 0.4);
        }

        .ed-selector {
            background: rgba(241, 241, 241, 0.83);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #000000ff;
            border-radius: 6px;
            padding: 4px 10px;
            font-size: 0.85rem;
            font-family: inherit;
            cursor: pointer;
        }

        .ed-selector:focus {
            outline: none;
            border-color: var(--color-primary, #7c6dff);
        }

        .ed-btn {
            padding: 6px 16px;
            border-radius: 6px;
            border: none;
            font-family: inherit;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            transition: opacity .15s;
        }

        .ed-btn:hover {
            opacity: .85;
        }

        .ed-btn-save {
            background: var(--color-primary, #7c6dff);
            color: #fff;
        }

        .ed-btn-new {
            background: rgba(255, 255, 255, 0.08);
            color: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .ed-btn-delete {
            background: rgba(220, 50, 50, 0.18);
            color: #f87171;
            border: 1px solid rgba(220, 50, 50, 0.3);
        }

        .ed-saved-badge {
            font-size: 0.78rem;
            color: #4ade80;
            background: rgba(74, 222, 128, 0.12);
            border: 1px solid rgba(74, 222, 128, 0.3);
            padding: 3px 10px;
            border-radius: 100px;
        }

        /* ── Editable field styles — sit in the exact visual positions ── */
        .ed-input,
        .ed-textarea {
            display: block;
            width: 100%;
            box-sizing: border-box;
            background: rgba(255, 255, 255, 0.04);
            border: 1px dashed rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            color: inherit;
            font: inherit;
            padding: 2px 6px;
            resize: vertical;
            transition: border-color .15s, background .15s;
        }

        .ed-input:focus,
        .ed-textarea:focus {
            outline: none;
            border-color: var(--color-primary, #7c6dff);
            background: rgba(255, 255, 255, 0.07);
        }

        /* Override font sizes so inputs inherit their context */
        .ed-input.font-label {
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .ed-input.font-headline {
            font-size: inherit;
            font-weight: bold;
        }

        .ed-textarea.font-body {
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .ed-textarea.code-ta {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.85rem;
            background: transparent;
        }

        .ed-inline-group {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .ed-inline-group .ed-input {
            flex: 1;
            min-width: 80px;
        }

        /* Prevent the fixed nav from covering toolbar (toolbar is now on top) */
        body {
            padding-top: 56px;
        }

        nav.nav-container {
            top: 56px !important;
        }

        main {
            padding-top: 6rem !important;
        }

        /* ── Project list (shown when no project is selected) ── */
        .ed-list-wrap {
            max-width: 720px;
            margin: 6rem auto 4rem;
            padding: 0 2rem;
        }

        .ed-list-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 2rem;
        }

        .ed-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: .75rem;
        }

        .ed-list-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.25rem;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 10px;
        }

        .ed-list-item-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .ed-list-id {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.3);
            width: 28px;
        }

        .ed-list-name {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 600;
        }

        .ed-list-cat {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.4);
            margin-left: 4px;
        }

        /* Icon helper label for icon fields */
        .ed-icon-field {
            position: relative;
        }

        .ed-icon-hint {
            position: absolute;
            right: 6px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.7rem;
            color: rgba(255, 0, 0, 0.3);
            pointer-events: none;
        }
    </style>
</head>

<body class="bg-surface text-on-surface font-body">

    <!-- ═══════════════ TOOLBAR ═══════════════ -->
    <div class="ed-toolbar">
        <div class="ed-toolbar-left">
            <span class="ed-toolbar-title">✏️ Éditeur</span>
            <?php if ($project): ?>
                <form method="get" style="margin:0">
                    <select name="id" class="ed-selector" onchange="this.form.submit()">
                        <?php foreach ($projects as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= $p['id'] === $editId ? 'selected' : '' ?>>
                                #<?= str_pad($p['id'], 2, '0', STR_PAD_LEFT) ?> — <?= htmlspecialchars($p['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            <?php endif; ?>
            <?php if ($saved): ?>
                <span class="ed-saved-badge">✓ Enregistré</span>
            <?php endif; ?>
        </div>
        <div style="display:flex; gap:.6rem; align-items:center;">
            <?php if ($project): ?>
                <button type="submit" form="edit-form" class="ed-btn ed-btn-save">💾 Enregistrer</button>
                <button class="ed-btn ed-btn-delete" onclick="if(confirm('Supprimer ce projet ?')) document.getElementById('del-form').submit()">🗑 Supprimer</button>
            <?php endif; ?>
            <form method="post" style="margin:0">
                <input type="hidden" name="action" value="new">
                <button type="submit" class="ed-btn ed-btn-new">+ Nouveau projet</button>
            </form>
        </div>
    </div>

    <?php if ($project === null): ?>
        <!-- ═══════════════ LIST VIEW ═══════════════ -->
        <div class="ed-list-wrap">
            <p class="ed-list-title">Sélectionner un projet à éditer</p>
            <ul class="ed-list">
                <?php foreach ($projects as $p): ?>
                    <li class="ed-list-item">
                        <div class="ed-list-item-info">
                            <span class="ed-list-id"><?= $p['index'] ?></span>
                            <span class="ed-list-name"><?= htmlspecialchars($p['title']) ?></span>
                            <span class="ed-list-cat"><?= htmlspecialchars($p['category']) ?></span>
                        </div>
                        <a href="editor.php?id=<?= $p['id'] ?>" class="ed-btn ed-btn-save" style="text-decoration:none">Modifier</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

    <?php else: ?>
        <!-- ═══════════════ EDIT VIEW (mirrors project_detail.php) ═══════════════ -->

        <!-- Hidden delete form -->
        <form id="del-form" method="post" style="display:none">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= $project['id'] ?>">
        </form>

        <form id="edit-form" method="post">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= $project['id'] ?>">

            <!-- TopNavBar Shell (display only, not editable) -->
            <nav class="nav-container fixed glass-surface z-50">
                <div class="nav-logo">Louis MOULINET</div>
                <div class="nav-links">
                    <a class="nav-link glass-btn" href="#">Accueil</a>
                    <a class="nav-link glass-btn active" href="#">Projets</a>
                    <a class="nav-link glass-btn" href="#">Veille</a>
                </div>
                <button class="btn-contact glass-btn" type="button">contact</button>
            </nav>

            <main class="pt-24 pb-20 px-8 max-w-7xl">
                <!-- Hero Section -->
                <header class="detail-hero mb-20">
                    <div class="md:col-span-8">
                        <span class="font-label text-primary font-semibold tracking-widest uppercase text-xs mb-4 block">
                            <?= ei('subtitle', $d['subtitle'], 'font-label') ?>
                        </span>
                        <h1 class="font-headline text-6xl font-bold tracking-tighter leading-none mb-6">
                            <?= ei('title', $project['title'], 'font-headline') ?>
                        </h1>
                        <p class="font-body text-xl text-on-surface-variant max-w-xl leading-relaxed">
                            <?= eta('description', $project['description'], 3, 'font-body') ?>
                        </p>
                    </div>
                    <div class="md:col-span-4 flex justify-end">
                        <div class="detail-hero-meta">
                            <span class="detail-hero-meta-item">Statut : <?= ei('status', $d['status']) ?></span>
                            <span class="detail-hero-meta-item">Stack : <?= ei('stack', $d['stack']) ?></span>
                            <span class="detail-hero-meta-item">Année : <?= ei('year', $d['year']) ?></span>
                        </div>
                    </div>
                </header>

                <!-- High-Res Project Image -->
                <section class="mb-32">
                    <div class="detail-image-banner">
                        <?php $imgSrc = htmlspecialchars($d['detail_image']); ?>
                        <img alt="<?= htmlspecialchars($d['detail_image_alt']) ?>"
                            class="w-full h-full object-cover transition-all duration-700"
                            style="transition: filter 0.7s ease;"
                            onmouseover="this.style.filter='grayscale(0)'"
                            onmouseout="this.style.filter='grayscale(100%)'"
                            src="<?= $imgSrc ?>" id="preview-detail-image" />
                        <div class="detail-image-overlay"></div>
                    </div>
                    <!-- Image URL fields (shown below the banner) -->
                    <div style="margin-top:.75rem; display:flex; flex-direction:column; gap:.4rem; padding: 0 .5rem;">
                        <label style="font-size:.7rem;color:rgba(255, 0, 0, 0.35);text-transform:uppercase;letter-spacing:.08em;">Image principale (URL)</label>
                        <input type="text" name="detail_image" value="<?= $imgSrc ?>" class="ed-input"
                            oninput="document.getElementById('preview-detail-image').src=this.value">
                        <input type="text" name="detail_image_alt" value="<?= htmlspecialchars($d['detail_image_alt']) ?>" class="ed-input" placeholder="Texte alternatif">
                    </div>
                </section>

                <!-- Project Narrative Grid -->
                <div class="detail-narrative">
                    <!-- Left Column: The Challenge -->
                    <div class="md:col-span-5">
                        <h2 class="font-headline text-3xl font-bold mb-8 flex items-center gap-3">
                            <span class="w-8 h-px bg-primary"></span> Le Défi
                        </h2>
                        <div class="space-y-12 text-on-surface-variant leading-loose font-light">
                            <p><?= eta('challenge', $d['challenge'], 4) ?></p>
                        </div>
                    </div>
                    <!-- Right Column: Key Features (Bento Style) -->
                    <div class="md:col-span-7 feature-grid">
                        <!-- Feature 1 (span-2) -->
                        <div class="glass-surface feature-card feature-card--span-2">
                            <span class="material-symbols-outlined text-primary mb-4"><?= htmlspecialchars($d['feature1_icon']) ?></span>
                            <div class="ed-icon-field" style="margin-bottom:.5rem">
                                <?= ei('feature1_icon', $d['feature1_icon']) ?>
                                <span class="ed-icon-hint">icône material</span>
                            </div>
                            <h3 class="font-headline font-bold text-lg mb-2"><?= ei('feature1_title', $d['feature1_title']) ?></h3>
                            <p class="text-sm text-on-surface-variant font-medium"><?= eta('feature1_desc', $d['feature1_desc'], 2) ?></p>
                        </div>
                        <!-- Feature 2 -->
                        <div class="glass-surface feature-card">
                            <span class="material-symbols-outlined text-primary mb-4"><?= htmlspecialchars($d['feature2_icon']) ?></span>
                            <div class="ed-icon-field" style="margin-bottom:.5rem">
                                <?= ei('feature2_icon', $d['feature2_icon']) ?>
                                <span class="ed-icon-hint">icône material</span>
                            </div>
                            <h3 class="font-headline font-bold text-lg mb-2"><?= ei('feature2_title', $d['feature2_title']) ?></h3>
                            <p class="text-sm text-on-surface-variant font-medium"><?= eta('feature2_desc', $d['feature2_desc'], 2) ?></p>
                        </div>
                        <!-- Feature 3 -->
                        <div class="feature-card feature-card--primary glass-btn shadow-lg" style="display:inline">
                            <span class="material-symbols-outlined mb-4 opacity-70"><?= htmlspecialchars($d['feature3_icon']) ?></span>
                            <div class="ed-icon-field" style="margin-bottom:.5rem">
                                <?= ei('feature3_icon', $d['feature3_icon']) ?>
                                <span class="ed-icon-hint">icône material</span>
                            </div>
                            <h3 class="font-headline font-bold text-lg mb-2"><?= ei('feature3_title', $d['feature3_title']) ?></h3>
                            <p class="text-sm font-medium opacity-90"><?= eta('feature3_desc', $d['feature3_desc'], 2) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Technical Specification / Code Block -->
                <section class="mb-32">
                    <div class="tech-spec-section">
                        <div class="tech-spec-sidebar">
                            <h2 class="font-headline text-3xl font-bold mb-6">Architecture Technique</h2>
                            <p class="text-on-surface-variant mb-8 font-body">
                                Extrait de code principal illustrant le modèle d'implémentation clé de ce projet.
                            </p>
                            <div style="margin-bottom:.5rem">
                                <label style="font-size:.7rem;color:rgba(255, 0, 0, 0.35);text-transform:uppercase;letter-spacing:.08em;display:block;margin-bottom:4px;">Tags (séparés par des virgules)</label>
                                <?= ei('tags', implode(', ', $project['tags'] ?? [])) ?>
                            </div>
                            <div style="margin-top:1rem">
                                <label style="font-size:.7rem;color:rgba(255, 0, 0, 0.35);text-transform:uppercase;letter-spacing:.08em;display:block;margin-bottom:4px;">Tech tags (séparés par des virgules)</label>
                                <?= ei('tech_tags', implode(', ', $d['tech_tags'] ?? [])) ?>
                            </div>
                            <div style="margin-top:1rem">
                                <label style="font-size:.7rem;color:rgba(255, 0, 0, 0.35);text-transform:uppercase;letter-spacing:.08em;display:block;margin-bottom:4px;">Catégorie</label>
                                <?= ei('category', $project['category']) ?>
                            </div>
                        </div>
                        <div class="tech-spec-code">
                            <div class="glass-surface code-block-wrap">
                                <div class="code-block-header">
                                    <?= ei('code_file', $d['code_file']) ?>
                                    <?= ei('code_version', $d['code_version']) ?>
                                </div>
                                <?= eta('code_block', $d['code_block'], 10, 'code-ta') ?>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Secondary Imagery / Detail View -->
                <section class="detail-secondary-images">
                    <div class="bg-surface-container-low aspect-square overflow-hidden" style="position:relative">
                        <?php $img2Src = htmlspecialchars($d['detail_image2']); ?>
                        <img alt="<?= htmlspecialchars($d['detail_image2_alt']) ?>"
                            class="w-full h-full object-cover mix-blend-multiply opacity-80"
                            src="<?= $img2Src ?>" id="preview-detail-image2" />
                        <div style="position:absolute;bottom:0;left:0;right:0;padding:.5rem;background:rgba(0,0,0,.55);">
                            <input type="text" name="detail_image2" value="<?= $img2Src ?>" class="ed-input"
                                oninput="document.getElementById('preview-detail-image2').src=this.value"
                                placeholder="URL image secondaire">
                            <input type="text" name="detail_image2_alt" value="<?= htmlspecialchars($d['detail_image2_alt']) ?>" class="ed-input" placeholder="Texte alternatif" style="margin-top:4px">
                        </div>
                    </div>
                    <div class="detail-number-card">
                        <span class="font-headline text-5xl font-bold mb-4"><?= str_pad($project['id'], 2, '0', STR_PAD_LEFT) ?></span>
                        <p class="font-body text-xl text-on-surface-variant">
                            <?= eta('detail_note', $d['detail_note'], 4) ?>
                        </p>
                    </div>
                </section>

                <!-- Card image (thumbnail shown on projects list) -->
                <section style="margin-top:3rem; padding:0 .5rem;">
                    <label style="font-size:.7rem;color:rgba(255, 0, 0, 0.35);text-transform:uppercase;letter-spacing:.08em;display:block;margin-bottom:.5rem">Image miniature (projets.php)</label>
                    <div style="display:flex;gap:.75rem;align-items:flex-start">
                        <img id="preview-thumb" src="<?= htmlspecialchars($project['image']) ?>" alt="" style="height:80px;width:120px;object-fit:cover;border-radius:6px;border:1px solid rgba(255,255,255,.1)">
                        <div style="flex:1;display:flex;flex-direction:column;gap:4px">
                            <input type="text" name="image" value="<?= htmlspecialchars($project['image']) ?>" class="ed-input"
                                oninput="document.getElementById('preview-thumb').src=this.value"
                                placeholder="URL image miniature">
                            <input type="text" name="image_alt" value="<?= htmlspecialchars($project['image_alt']) ?>" class="ed-input" placeholder="Texte alternatif">
                        </div>
                    </div>
                </section>

                <!-- Navigation between projects (display only) -->
                <nav class="project-nav" style="margin-top:3rem">
                    <div></div>
                    <a href="projects.php" class="font-mono text-xs text-outline uppercase tracking-widest">Tous les Projets</a>
                    <div></div>
                </nav>
            </main>

            <!-- Footer -->
            <footer class="site-footer">
                <p class="footer-copy">© 2024 Louis MOULINET | Portfolio BTS SIO</p>
            </footer>

        </form><!-- end edit-form -->

        <!-- Liquid Glass SVG Distortion Filter -->
        <svg style="display: none">
            <filter id="glass-distortion" x="0%" y="0%" width="100%" height="100%" filterUnits="objectBoundingBox">
                <feTurbulence type="fractalNoise" baseFrequency="0.01 0.01" numOctaves="1" seed="5" result="turbulence" />
                <feComponentTransfer in="turbulence" result="mapped">
                    <feFuncR type="gamma" amplitude="1" exponent="10" offset="0.5" />
                    <feFuncG type="gamma" amplitude="0" exponent="1" offset="0" />
                    <feFuncB type="gamma" amplitude="0" exponent="1" offset="0.5" />
                </feComponentTransfer>
                <feGaussianBlur in="turbulence" stdDeviation="3" result="softMap" />
                <feSpecularLighting in="softMap" surfaceScale="5" specularConstant="1" specularExponent="100"
                    lighting-color="white" result="specLight">
                    <fePointLight x="-200" y="-200" z="300" />
                </feSpecularLighting>
                <feComposite in="specLight" operator="arithmetic" k1="0" k2="1" k3="1" k4="0" result="litImage" />
                <feDisplacementMap in="SourceGraphic" in2="softMap" scale="150" xChannelSelector="R" yChannelSelector="G" />
            </filter>
        </svg>
    <?php endif; ?>
</body>

</html>