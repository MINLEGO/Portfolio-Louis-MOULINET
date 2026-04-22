<?php
// Load projects from JSON file
$jsonPath = file_exists(__DIR__ . '/../projects.json') ? __DIR__ . '/../projects.json' : __DIR__ . '/projects.json';
$data = json_decode(file_get_contents($jsonPath), true);
$projects = $data['projects'];

// Get project ID from URL, e.g. project_detail.php?id=1
$requestedId = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Find the matching project
$project = null;
foreach ($projects as $p) {
    if ($p['id'] === $requestedId) {
        $project = $p;
        break;
    }
}

// Fallback: if project not found, use first one
if ($project === null) {
    $project = $projects[0];
}

$d = $project['detail']; // shorthand for detail fields
?>
<!DOCTYPE html>

<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title><?= htmlspecialchars($project['title']) ?> | Projets - Louis MOULINET</title>
    <meta name="description" content="<?= htmlspecialchars(substr($project['description'], 0, 160)) ?>" />
    <link rel="canonical" href="https://louismoulinet.com/project_detail.php?id=<?= $project['id'] ?>" />

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="article" />
    <meta property="og:url" content="https://louismoulinet.com/project_detail.php?id=<?= $project['id'] ?>" />
    <meta property="og:title" content="<?= htmlspecialchars($project['title']) ?> | Louis MOULINET" />
    <meta property="og:description" content="<?= htmlspecialchars($project['description']) ?>" />
    <meta property="og:image" content="https://louismoulinet.com/<?= htmlspecialchars($project['image']) ?>" />

    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&family=Manrope:wght@400;500;600&family=JetBrains+Mono&display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="style.css">
</head>

<body class="bg-surface text-on-surface font-body">
    <!-- TopNavBar Shell -->
    <nav class="nav-container fixed glass-surface z-50">
        <div class="nav-logo">Louis MOULINET</div>
        <button class="mobile-menu-toggle" aria-label="Menu" onclick="let menu=document.querySelector('.nav-links'); menu.classList.toggle('active-mobile'); let icon=this.querySelector('.material-symbols-outlined'); icon.textContent = menu.classList.contains('active-mobile') ? 'close' : 'menu';">
            <span class="material-symbols-outlined text-3xl">menu</span>
        </button>
        <div class="nav-links">
            <a class="nav-link glass-btn" href="index.php">Accueil</a>
            <a class="nav-link glass-btn active" style="font-size:1.3rem" href="projects.php">Projets</a>
            <a class="nav-link glass-btn" href="veille.php">Veille</a>
            <a class="nav-link glass-btn nav-link-contact" href="mailto:moulinet.l03@gmail.com">Contact</a>
        </div>
        <button class="btn-contact glass-btn">
            <script>
                document.write('<a href="mailto:' + 'm' + 'o' + 'u' + 'l' + 'i' + 'n' + 'e' + 't' + '.' + 'l' + '0' + '3' + '@' + 'g' + 'm' + 'a' + 'i' + 'l' + '.' + 'c' + 'o' + 'm' + '">contact</a>');
            </script>
        </button>
    </nav>
    <main class="pt-24 px-8 max-w-7xl">
        <!-- Hero Section -->
        <header class="detail-hero mb-20">
            <div class="md:col-span-8">
                <span class="font-label text-primary font-semibold tracking-widest uppercase text-xs mb-4 block">
                    <?= str_pad(htmlspecialchars($project['id']), 2, '0', STR_PAD_LEFT) ?> / BTS-SIO / <?= htmlspecialchars($project['category']) ?>
                </span>
                <h1 class="font-headline text-6xl font-bold tracking-tighter leading-none mb-6">
                    <?= htmlspecialchars($project['title']) ?>
                </h1>
                <p class="font-body text-xl text-on-surface-variant max-w-xl leading-relaxed">
                    <?= htmlspecialchars($project['description']) ?>
                </p>
            </div>
            <div class="md:col-span-4 flex justify-end">
                <div class="detail-hero-meta">
                    <span class="detail-hero-meta-item">Statut : <?= htmlspecialchars($d['status']) ?></span>
                    <span class="detail-hero-meta-item">Stack : <?= htmlspecialchars($d['stack']) ?></span>
                    <span class="detail-hero-meta-item">Année : <?= htmlspecialchars($d['year']) ?></span>
                </div>
            </div>
        </header>
        <!-- High-Res Project Image -->
        <section>
            <div class="detail-image-banner glass-surface big-glass">
                <img alt="<?= htmlspecialchars($d['detail_image_alt']) ?>"
                    class="w-full h-full object-cover transition-all duration-700"
                    style="transition: filter 0.7s ease;"
                    onmouseover="this.style.filter='grayscale(0)'"
                    onmouseout="this.style.filter='grayscale(100%)'"
                    src="<?= htmlspecialchars($d['detail_image']) ?>" />
                <div class="detail-image-overlay"></div>
            </div>
        </section>
        <!-- Project Narrative Grid -->
        <div class="detail-narrative">
            <!-- Left Column: The Challenge -->
            <?php $IncludeFeature = $d['feature1_desc'] || $d['feature2_desc'] || $d['feature3_desc']; ?>
            <?php if ($d['challenge'] || $d['challenge2']) : ?>
                <div class="<?= $IncludeFeature ? 'md:col-span-5' : 'md:col-span-10' ?>">
                    <h2 class="font-headline text-3xl font-bold mb-8 flex items-center gap-3">
                        <span class="w-8 h-px bg-primary"></span> Le Défi
                    </h2>
                    <div class="space-y-6 text-on-surface-variant leading-loose font-light">
                        <p><?= nl2br(htmlspecialchars($d['challenge'])) ?></p>
                        <p><?= nl2br(htmlspecialchars($d['challenge2'])) ?></p>
                    </div>
                </div>
            <?php endif; ?>
            <!-- Right Column: Key Features (Bento Style) -->
            <?php if ($IncludeFeature): ?>
                <div class="md:col-span-7 feature-grid">
                    <?php if ($d['feature1_desc']): ?>
                        <div class="glass-surface small-glass feature-card feature-card--span-2">
                            <span class="material-symbols-outlined text-primary mb-4"><?= htmlspecialchars($d['feature1_icon']) ?></span>
                            <h3 class="font-headline font-bold text-lg mb-2"><?= htmlspecialchars($d['feature1_title']) ?></h3>
                            <p class="text-sm text-on-surface-variant font-medium"><?= nl2br(htmlspecialchars($d['feature1_desc'])) ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if ($d['feature2_desc']): ?>
                        <div class="glass-surface small-glass feature-card">
                            <span class="material-symbols-outlined text-primary mb-4"><?= htmlspecialchars($d['feature2_icon']) ?></span>
                            <h3 class="font-headline font-bold text-lg mb-2"><?= htmlspecialchars($d['feature2_title']) ?></h3>
                            <p class="text-sm text-on-surface-variant font-medium"><?= nl2br(htmlspecialchars($d['feature2_desc'])) ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if ($d['feature3_desc']): ?>
                        <div class="feature-card feature-card--primary glass-btn shadow-lg" style="display:inline">
                            <span class="material-symbols-outlined mb-4 opacity-70"><?= htmlspecialchars($d['feature3_icon']) ?></span>
                            <h3 class="font-headline font-bold text-lg mb-2"><?= htmlspecialchars($d['feature3_title']) ?></h3>
                            <p class="text-sm font-medium opacity-90"><?= nl2br(htmlspecialchars($d['feature3_desc'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        <!-- Technical Specification / Code Block -->
        <?php if ($d['code_block']): ?>
            <section class="mb-32">
                <div class="tech-spec-section">
                    <div class="tech-spec-sidebar">
                        <h2 class="font-headline text-3xl font-bold mb-6">Architecture Technique</h2>
                        <p class="text-on-surface-variant mb-8 font-body">
                            Extrait de code principal illustrant le modèle d'implémentation clé de ce projet.
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($d['tech_tags'] as $tag): ?>
                                <span class="tech-tag-pill"><?= htmlspecialchars($tag) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="tech-spec-code">
                        <div class="glass-surface shadow-lg code-block-wrap">
                            <div class="code-block-header">
                                <span><?= htmlspecialchars($d['code_file']) ?></span>
                                <span><?= htmlspecialchars($d['code_version']) ?></span>
                            </div>
                            <pre class="leading-relaxed text-on-surface-variant font-bold"><?= htmlspecialchars($d['code_block']) ?></pre>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>
        <!-- Secondary Imagery / Detail View -->
        <?php if ($d['detail_note']) : ?>
            <section class="detail-secondary-images">
                <div class="bg-surface-container-low aspect-square overflow-hidden">
                    <img alt="<?= htmlspecialchars($d['detail_image2_alt']) ?>"
                        class="w-full h-full object-cover mix-blend-multiply opacity-80"
                        src="<?= htmlspecialchars($d['detail_image2']) ?>" />
                </div>
                <div class="detail-number-card">
                    <span class="font-headline text-5xl font-bold mb-4"><?= str_pad($project['id'], 2, '0', STR_PAD_LEFT) ?></span>
                    <p class="font-body text-xl text-on-surface-variant" style="font-weight:500;">
                        <?= nl2br(htmlspecialchars($d['detail_note'])) ?>
                    </p>
                </div>
            </section>
        <?php endif; ?>
        <!-- Navigation between projects -->
        <nav class="project-nav">
            <?php
            $prevProject = null;
            $nextProject = null;
            foreach ($projects as $idx => $p) {
                if ($p['id'] === $project['id']) {
                    if ($idx > 0) $prevProject = $projects[$idx - 1];
                    if ($idx < count($projects) - 1) $nextProject = $projects[$idx + 1];
                    break;
                }
            }
            ?>
            <div>
                <?php if ($prevProject): ?>
                    <a href="project_detail.php?id=<?= $prevProject['id'] ?>"
                        class="group flex items-center gap-3 font-headline font-bold uppercase tracking-tight hover:text-primary transition-colors">
                        <span class="material-symbols-outlined group-hover:-translate-x-1 transition-transform">arrow_back</span>
                        <span class="text-sm"><?= htmlspecialchars($prevProject['title']) ?></span>
                    </a>
                <?php endif; ?>
            </div>
            <a href="projects.php" class="font-mono text-xs text-outline uppercase tracking-widest hover:text-primary transition-colors">
                Tous les Projets
            </a>
            <div>
                <?php if ($nextProject): ?>
                    <a href="project_detail.php?id=<?= $nextProject['id'] ?>"
                        class="group flex items-center gap-3 font-headline font-bold uppercase tracking-tight hover:text-primary transition-colors">
                        <span class="text-sm"><?= htmlspecialchars($nextProject['title']) ?></span>
                        <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
                    </a>
                <?php endif; ?>
            </div>
        </nav>
    </main>
    <!-- Footer Shell -->
    <footer class="site-footer">
        <div class="footer-copy">© 2026 Louis MOULINET | Portfolio BTS SIO</div>
        <div class="footer-links">
            <a class="footer-link" href="https://github.com/MINLEGO">GitHub</a>
            <a class="footer-link" href="https://www.linkedin.com/in/louis-moulinet-406965384/">LinkedIn</a>
        </div>
    </footer>
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
</body>

</html>