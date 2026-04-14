<?php
// Load projects from JSON file (one level up from src/)
$jsonPath = __DIR__ . '/../projects.json';
$data = json_decode(file_get_contents($jsonPath), true);
$projects = $data['projects'];

// Filter logic
$activeFilter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$filterMap = [
    'web'      => 'Web',
    'software' => 'Software',
    'devops'   => 'Dev_Ops',
];
if ($activeFilter !== 'all' && isset($filterMap[$activeFilter])) {
    $projects = array_filter($projects, function ($p) use ($filterMap, $activeFilter) {
        return stripos($p['category'], $filterMap[$activeFilter]) !== false;
    });
}
?>
<!DOCTYPE html>

<html class="light" lang="fr">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Projets</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&family=Manrope:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet" />
    <!-- Icons -->
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="style.css">
</head>

<body class="bg-surface text-on-surface font-body">
    <!-- TopNavBar Shell -->
    <nav class="nav-container fixed glass-surface z-50">
        <div class="nav-logo">Louis MOULINET</div>
        <div class="nav-links">
            <a class="nav-link glass-btn" href="index.php">Accueil</a>
            <a class="nav-link glass-btn active" href="projects.php">Projets</a>
            <a class="nav-link glass-btn" href="veille.php">Veille</a>
        </div>
        <button class="btn-contact glass-btn">
            <script>
                document.write('<a href="mailto:' + 'm' + 'o' + 'u' + 'l' + 'i' + 'n' + 'e' + 't' + '.' + 'l' + '0' + '3' + '@' + 'g' + 'm' + 'a' + 'i' + 'l' + '.' + 'c' + 'o' + 'm' + '">contact</a>');
            </script>
        </button>
    </nav>
    <main class="pt-32 pb-24 px-8 max-w-7xl">
        <!-- Hero Section -->
        <header class="projects-hero">
            <div class="max-w-2xl">
                <span class="hero-tag">C:/archive/portfolio</span>
                <h1 class="font-headline font-bold text-6xl hero-title tracking-tighter leading-none mb-6">Projets</h1>
                <p class="text-on-surface-variant text-lg font-body leading-relaxed font-medium">
                    Une collection de solutions, articles, et expérimentations, axée sur l'avancée de l'IA, les nouvelles technologies FrontEnd / BackEnd ainsi que les processus de développement modernes.
                </p>
            </div>
        </header>
        <!-- Filters -->
        <section class="filter-bar">
            <div class="filter-list font-medium">
                <a href="projects.php"
                    class="filter-btn glass-btn <?= $activeFilter === 'all' ? 'filter-btn--active' : 'filter-btn--inactive' ?>">
                    Tous les Projets</a>
                <a href="projects.php?filter=web"
                    class="filter-btn glass-btn <?= $activeFilter === 'web' ? 'filter-btn--active' : 'filter-btn--inactive' ?>">
                    Développement Web</a>
                <a href="projects.php?filter=software"
                    class="filter-btn glass-btn <?= $activeFilter === 'software' ? 'filter-btn--active' : 'filter-btn--inactive' ?>">
                    Ingénierie Logicielle</a>
                <a href="projects.php?filter=devops"
                    class="filter-btn glass-btn <?= $activeFilter === 'devops' ? 'filter-btn--active' : 'filter-btn--inactive' ?>">
                    DevOps &amp; Cloud</a>
            </div>
        </section>
        <!-- Projects Grid (dynamic) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-8 px-8">
            <?php $l_i = -1; ?>
            <?php foreach ($projects as $i => $project): ?>
                <?php if ($project['id'] == null) continue; ?>
                <?php $l_i++; ?>
                <article class="group glass-btn p-3 rounded-3xl transition-all duration-500" style="margin: <?= [0, 0, 0][$l_i % 3]  ?>rem 0 <?= 2 - [0, 0, 0][$l_i % 3]  ?>rem; cursor: pointer; display: block">
                    <a href="project_detail.php?id=<?= $project['id'] ?>">
                        <div class="project-card-image-wrap">
                            <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-700"
                                src="<?= htmlspecialchars($project['image']) ?>"
                                alt="<?= htmlspecialchars($project['image_alt']) ?>" />
                            <div class="project-card-badge">
                                <?= htmlspecialchars($project['index']) ?> / <?= htmlspecialchars($project['category']) ?>
                            </div>
                        </div>
                        <div class="project-card">
                            <div class="flex justify-between items-start mb-4">
                                <h2 class="font-headline font-bold text-3xl tracking-tight"><?= htmlspecialchars($project['title']) ?></h2>
                                <div class="glass-btn p-2 rounded-full">
                                    <span class="material-symbols-outlined text-primary group-hover:translate-x-1 transition-transform">arrow_outward</span>
                                </div>
                            </div>

                            <div class="flex gap-2 mb-6 flex-wrap">
                                <?php foreach ($project['tags'] as $tag): ?>
                                    <span class="project-card-tag glass-surface small-glass"><?= htmlspecialchars($tag) ?></span>
                                <?php endforeach; ?>
                            </div>
                            <p class="text-on-surface-variant leading-relaxed mb-6 font-medium">
                                <?= htmlspecialchars($project['description']) ?>
                            </p>
                        </div>
                    </a>

                </article>
            <?php endforeach; ?>
        </div>

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