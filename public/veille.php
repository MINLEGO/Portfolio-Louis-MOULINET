<?php
// Page Veille. Sert aussi l'archive (articles.php définit $mode = 'archive').
require_once __DIR__ . '/lib/veille.php';
$mode = $mode ?? 'home';
$isHome = $mode === 'home';
$experiments = vf_experiments(vf_all_sources()['data']);
$pageData = ['mode' => $mode, 'q' => mb_substr((string) ($_GET['q'] ?? ''), 0, 200), 'experiments' => $experiments];
?>
<!DOCTYPE html>
<html class="light" lang="fr">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title><?= $isHome ? 'Veille Technologique' : 'Archive de la veille' ?> | Louis MOULINET</title>
    <?php $canonical = 'https://louismoulinet.com/' . ($isHome ? 'veille.php' : 'articles.php'); ?>
    <meta name="description" content="<?= $isHome
        ? "Explorez ma veille technologique sur l'IA générative : sources analysées, méthode de veille et expérimentations, par Louis MOULINET."
        : "Archive complète de ma veille technologique sur l'IA générative, avec recherche sémantique." ?>" />
    <link rel="canonical" href="<?= $canonical ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?= $canonical ?>" />
    <meta property="og:title" content="<?= $isHome ? 'Veille Technologique' : 'Archive de la veille' ?> | Louis MOULINET" />
    <meta property="og:description" content="Suivez mes découvertes et analyses sur les dernières tendances tech." />
    <meta property="og:image" content="https://louismoulinet.com/media/og-image-veille.png" />
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&family=Manrope:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="veille.css">
</head>

<body class="bg-surface font-body text-on-surface">
    <nav class="nav-container fixed glass-surface z-50">
        <div class="nav-logo">Louis MOULINET</div>
        <button class="mobile-menu-toggle" aria-label="Menu" onclick="let menu=document.querySelector('.nav-links'); menu.classList.toggle('active-mobile'); this.querySelector('.material-symbols-outlined').textContent = menu.classList.contains('active-mobile') ? 'close' : 'menu';">
            <span class="material-symbols-outlined text-3xl">menu</span>
        </button>
        <div class="nav-links">
            <a class="nav-link glass-btn" href="index.php">Accueil</a>
            <a class="nav-link glass-btn" style="font-size:1.3rem" href="projects.php">Projets</a>
            <a class="nav-link glass-btn active" href="veille.php">Veille</a>
            <a class="nav-link glass-btn nav-link-contact" href="mailto:moulinet.l03@gmail.com">Contact</a>
        </div>
        <button class="btn-contact glass-btn">
            <script>
                document.write('<a href="mailto:' + 'm' + 'o' + 'u' + 'l' + 'i' + 'n' + 'e' + 't' + '.' + 'l' + '0' + '3' + '@' + 'g' + 'm' + 'a' + 'i' + 'l' + '.' + 'c' + 'o' + 'm' + '">contact</a>');
            </script>
        </button>
    </nav>

    <main class="pt-32 pb-20 px-8 md:px-12 max-w-7xl vs-main">
        <header class="mb-12">
            <div class="veille-hero-tag">
                <span class="hero-tag">/execute as lab run <?= $isHome ? 'VEILLE' : 'ARCHIVE' ?></span>
                <div class="veille-divider"></div>
            </div>
            <h1 class="font-headline text-8xl font-bold tracking-tighter text-on-background mb-6">
                <?= $isHome ? 'Veille' : 'Archive' ?> <span class="text-gradient shine"><?= $isHome ? 'Technologique' : 'des Sources' ?></span>
            </h1>
            <p class="max-w-2xl text-lg text-on-surface-variant font-body leading-relaxed">
                <?= $isHome
                    ? "L'IA générative appliquée à la productivité : les Sources que je capture, et ce que j'en ai testé."
                    : 'Toutes les Sources capturées dans VeilleFacile, avec recherche sémantique.' ?>
            </p>
        </header>

        <?php if ($isHome): ?>
            <!-- Domaine & Méthode de veille : PREMIER JET, à réécrire -->
            <section class="glass-surface small-glass rounded-1xl vs-section">
                <div class="section-heading-bar pb-4">
                    <h2 class="font-headline text-2xl font-bold tracking-tight flex items-center gap-2 px-4">
                        <span class="material-symbols-outlined text-primary">explore</span> Domaine &amp; méthode
                    </h2>
                </div>
                <div class="vs-method">
                    <div class="vs-method-block">
                        <h3>Sujet</h3>
                        <p>L'IA générative et son usage pour améliorer la productivité. Le domaine change chaque semaine : je le suis pour toujours utiliser les outils les plus efficaces.</p>
                    </div>
                    <div class="vs-method-block">
                        <h3>Sources suivies</h3>
                        <ul>
                            <li>Blogs et annonces des labs (Anthropic, Google DeepMind…)</li>
                            <li>Chaînes YouTube de vulgarisation et d'actualité</li>
                            <li>Dépôts GitHub et modèles Hugging Face en tendance</li>
                            <li>Benchmarks (Artificial Analysis, ARC Prize)</li>
                        </ul>
                    </div>
                    <div class="vs-method-block">
                        <h3>Outil</h3>
                        <p><a class="vs-link" href="https://github.com/MINLEGO/VeilleFacile" target="_blank" rel="noopener">VeilleFacile</a>, mon propre outil : il capture une page, la résume avec un LLM, en extrait des mots-clés et permet une recherche sémantique. C'est lui qui alimente cette page.</p>
                    </div>
                    <div class="vs-method-block">
                        <h3>Processus</h3>
                        <ol class="vs-steps">
                            <li><strong>Capturer</strong> la Source depuis le navigateur</li>
                            <li><strong>Résumer</strong> et indexer automatiquement</li>
                            <li><strong>Trier</strong> par mots-clés, retrouver par recherche</li>
                            <li><strong>Expérimenter</strong> les nouveautés les plus prometteuses</li>
                        </ol>
                    </div>
                </div>
            </section>

            <section class="glass-surface small-glass rounded-1xl vs-section" id="vs-exp-section">
                <div class="section-heading-bar pb-4">
                    <h2 class="font-headline text-2xl font-bold tracking-tight flex items-center gap-2 px-4">
                        <span class="material-symbols-outlined text-primary">science</span> Expérimentations
                    </h2>
                </div>
                <div class="vs-grid" id="vs-exp-row"></div>
            </section>
        <?php endif; ?>

        <section class="glass-surface small-glass rounded-1xl vs-section">
            <div class="section-heading-bar pb-4">
                <h2 class="font-headline text-2xl font-bold tracking-tight flex items-center gap-2 px-4">
                    <span class="material-symbols-outlined text-primary">article</span> Sources
                </h2>
                <span class="font-mono text-xs text-outline" id="vs-count">COMPTE : …</span>
            </div>
            <div class="vs-filters">
                <label class="vs-search">
                    <span class="material-symbols-outlined">search</span>
                    <input type="search" id="vs-q" placeholder="Recherche sémantique (via VeilleFacile)…" aria-label="Rechercher une Source">
                </label>
                <div class="vs-chips" role="group" aria-label="Filtrer par format">
                    <?php foreach (['' => 'Tous', 'Vidéo' => 'Vidéo', 'Code' => 'Code', 'Modèle' => 'Modèle', 'Article' => 'Article'] as $value => $label): ?>
                        <button class="vs-chip" data-format="<?= $value ?>" aria-pressed="<?= $value === '' ? 'true' : 'false' ?>"><?= $label ?></button>
                    <?php endforeach; ?>
                    <span id="vs-active-kw"></span>
                </div>
            </div>
            <p class="vs-notice" id="vs-notice" role="status"></p>
            <div class="vs-grid" id="vs-grid" aria-live="polite"></div>
            <div id="vs-sentinel"></div>
            <?php if ($isHome): ?>
                <a class="archive-link vs-more" id="vs-more" href="articles.php">Voir l'archive complète</a>
            <?php endif; ?>
        </section>
    </main>

    <footer class="site-footer">
        <div class="footer-copy">© 2026 Louis MOULINET | Portfolio BTS SIO</div>
        <div class="footer-links">
            <a class="footer-link" href="https://github.com/MINLEGO">GitHub</a>
            <a class="footer-link" href="https://www.linkedin.com/in/louis-moulinet-406965384/">LinkedIn</a>
        </div>
    </footer>

    <!-- Détail d'une Source / Expérimentation (fenêtre centrée ou tiroir) -->
    <div class="vs-panel" id="vs-panel" hidden>
        <div class="vs-panel-backdrop" data-close></div>
        <div class="vs-panel-box" role="dialog" aria-modal="true" aria-labelledby="vs-panel-title">
            <button class="vs-close" data-close aria-label="Fermer"><span class="material-symbols-outlined">close</span></button>
            <div id="vs-panel-content"></div>
        </div>
    </div>

    <script>window.VEILLE = <?= json_encode($pageData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;</script>
    <script src="veille.js"></script>
</body>

</html>
