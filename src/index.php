<?php
$projects_json = file_get_contents('../projects.json');
$projects_data = json_decode($projects_json, true);
$projects = $projects_data['projects'];

// Sort projects by ID descending
usort($projects, function ($a, $b) {
    return $a['id'] - $b['id'];
});

// Take the top 2
$recent_projects = array_slice($projects, 0, 2);
?>
<!DOCTYPE html>

<html class="light" lang="fr">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Louis MOULINET | Portfolio</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&family=Manrope:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="style.css">
</head>

<body class="bg-surface font-body text-on-surface">
    <!-- TopNavBar Shell -->
    <nav class="nav-container fixed glass-surface z-50">
        <div class="nav-logo">Louis MOULINET</div>
        <div class="nav-links">
            <a class="nav-link glass-btn active" href="index.php">Accueil</a>
            <a class="nav-link glass-btn" href="projects.php">Projets</a>
            <a class="nav-link glass-btn" href="veille.php">Veille</a>
        </div>
        <button class="btn-contact glass-btn">
            <script>
                document.write('<a href="mailto:' + 'm' + 'o' + 'u' + 'l' + 'i' + 'n' + 'e' + 't' + '.' + 'l' + '0' + '3' + '@' + 'g' + 'm' + 'a' + 'i' + 'l' + '.' + 'c' + 'o' + 'm' + '">contact</a>');
            </script>
        </button>
    </nav>
    <main class="pt-24 min-h-screen">
        <!-- Hero Section -->
        <section class="hero-section blueprint-grid">
            <div class="max-w-5xl">
                <div class="hero-tag">
                    <span class="material-symbols-outlined text-sm">terminal</span>
                    <span>root@bts_sio:~/portfolio</span>
                </div>
                <h1 class="hero-title">
                    Developpement <br><span class="text-gradient shine">Full Stack</span>
                    et gestion de projet.
                </h1>
                <div class="hero-badges">
                    <div class="hero-badge glass-surface small-glass rounded-full">
                        <span class="w-2\.5 h-2\.5 rounded-full bg-primary animate-pulse"></span>
                        <span class="hero-badge-label">En
                            recherche de stage</span>
                    </div>
                    <div class="hero-badge glass-surface small-glass rounded-full">
                        <span class="material-symbols-outlined text-sm">location_on</span>
                        <span class="hero-badge-label">Nantes,
                            FR</span>
                    </div>
                </div>
            </div>
        </section>
        <!-- Comptétences - Bento Grid -->
        <section class="arsenal-section">
            <div class="arsenal-grid">
                <div class="arsenal-header md:col-span-12">
                    <h2 class="font-headline text-4xl font-bold tracking-tight mb-2">Compétences<span
                            class="text-gradient">...</span></h2>
                    <div class="h-1 max-w-xl bg-primary-gradient"></div>
                </div>
                <!-- Formtion card -->
                <div
                    class="md:col-span-12 glass-surface big-glass p-10 justify-between group transition-all duration-500 hover:shadow-2xl rounded-3xl">
                    <div>
                        <div class="font-mono text-primary text-sm mb-4 font-bold">01 / Formation</div>
                        <h3 class="font-headline text-3xl font-bold mb-6">BTS SIO SLAM</h3>
                        <p class="text-on-surface-variant max-w-4xl leading-relaxed mb-8 font-medium">
                            Le BTS SIO (Service Informatique aux organisations) forme à la mise en place de solutions
                            informatiques au sein des entreprises et propose 2 options : SISR « Solutions
                            d’Infrastructures, Systèmes et Réseaux » et SLAM « Solutions Logicielles et Applications
                            Métiers ».
                            <br><br>
                            Ce site a donc été créé dans le cadre de mon BTS, en vue de regrouper l’ensemble des activités
                            effectuées et des compétences acquises durant mes deux années de formation ainsi que mes deux
                            stages.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <span class="skill-tag">Html5</span>
                        <span class="skill-tag">PHP</span>
                        <span class="skill-tag">CSS</span>
                    </div>
                </div>
                <!-- Full Stack Card -->
                <div class="md:col-span-4 bg-primary p-10 justify-between transition-all duration-300 rounded-3xl glass-surface small-glass shadow-lg max-w-xl"
                    style="transform-origin: center; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                    <span class="material-symbols-outlined text-8xl opacity-30">computer</span>
                    <div>
                        <div class="font-mono text-xs mb-2 opacity-80 font-bold">02 / WEB</div>
                        <h3 class="font-headline text-2xl font-bold mb-4">Développement Full Stack</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center border-b border-on-primary/10 pb-2">
                                <span class="font-headline font-semibold">HTML5</span>
                                <span class="material-symbols-outlined">coffee</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-on-primary/10 pb-2">
                                <span class="font-headline font-semibold">CSS</span>
                                <span class="material-symbols-outlined">rebase_edit</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-on-primary/10 pb-2">
                                <span class="font-headline font-semibold">PHP</span>
                                <span class="material-symbols-outlined">code</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Java/Python Card -->
                <div class="md:col-span-4 bg-primary p-10 justify-between transition-all duration-300 rounded-3xl glass-surface small-glass shadow-lg max-w-xl"
                    style="transform-origin: center; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                    <span class="material-symbols-outlined text-8xl opacity-30">code_blocks</span>
                    <div>
                        <div class="font-mono text-xs mb-2 opacity-80 font-bold">03 / LOGIC</div>
                        <h3 class="font-headline text-2xl font-bold mb-4">Systèmes Cœurs</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center border-b border-on-primary/10 pb-2">
                                <span class="font-headline font-semibold">Java</span>
                                <span class="material-symbols-outlined">coffee</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-on-primary/10 pb-2">
                                <span class="font-headline font-semibold">Python</span>
                                <span class="material-symbols-outlined">rebase_edit</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- SQL / Data Card -->
                <div
                    class="md:col-span-4 glass-surface small-glass p-10 flex flex-col items-center text-center justify-center border-t-4 border-tertiary">
                    <span class="material-symbols-outlined text-5xl text-tertiary mb-6"
                        data-weight="fill">database</span>
                    <h3 class="font-headline text-2xl font-bold mb-2">Intégrité des Données</h3>
                    <p class="text-sm font-label text-on-surface-variant mb-6 uppercase tracking-widest font-semibold">
                        MariaDB &amp; SQLite</p>
                    <div class="w-full bg-surface-variant h-px"></div>
                </div>
                <!--                    
                 DevOps Card --
                <div
                    class="md:col-span-8 glass-surface rounded-1xl bg-surface-container-lowest p-10 flex flex-col md:flex-row gap-10 items-center justify-between border-l-8 border-primary">
                    <div class="flex-1">
                        <div class="font-mono text-primary text-sm mb-4">03 / INFRA</div>
                        <h3 class="font-headline text-3xl font-bold mb-4">Pipeline DevOps</h3>
                        <p class="text-on-surface-variant leading-relaxed">
                            Stratégies de conteneurisation et de déploiement continu. S'assurer que l'infrastructure est
                            aussi agile que le code qu'elle héberge.
                        </p>
                    </div>
                    <div class="devops-mini-grid">
                        <div class="devops-mini-card">
                            <span class="material-symbols-outlined text-4xl block mb-2">grid_view</span>
                            <span class="font-mono text-xs">DOCKER</span>
                        </div>
                        <div class="devops-mini-card">
                            <span class="material-symbols-outlined text-4xl block mb-2">settings_input_component</span>
                            <span class="font-mono text-xs">CI/CD</span>
                        </div>
                    </div>
                </div> -->
            </div>
        </section>
        <!-- Projects - Featured Split -->
        <section class="projects-section glass-surface big-glass">
            <div class="max-w-7xl">
                <div class="projects-header">
                    <div class="max-w-xl">
                        <h2 class="font-headline text-5xl font-bold tracking-tighter mb-6">Projets récents</h2>
                        <p class="text-on-surface-variant font-body text-lg leading-relaxed">
                            Une sélection de projets que j'ai été amené a réaliser pour mon intéret personel ou pour ma
                            formation
                        </p>
                    </div>
                    <a class="group flex items-center gap-4 font-headline font-bold text-lg uppercase tracking-tight"
                        href="projects.php">
                        Voir Tous Les Projets
                        <span
                            class="material-symbols-outlined group-hover:translate-x-2 transition-transform">arrow_forward</span>
                    </a>
                </div>
                <div class="grid grid-cols-1 gap-16">
                    <?php foreach ($recent_projects as $index => $project): ?>
                        <?php if ($index % 2 === 0): ?>
                            <!-- Project <?= $index + 1 ?> (Even index: Image Left) -->
                            <a class="project-item glass-btn group" href="project_detail.php?id=<?= $project['id'] ?>"
                                style="border-radius: 2rem 0rem 0rem 2rem;">
                                <div class="relative group transition-all ">
                                    <img alt="<?= htmlspecialchars($project['image_alt']) ?>"
                                        class=" grayscale group-hover:grayscale-0 w-full aspect-video object-cover transition-transform  "

                                        src="<?= htmlspecialchars($project['image']) ?>" />
                                    <div class="project-overlay   " style="cursor:pointer"></div>
                                </div>
                                <div>
                                    <div class="font-mono text-primary font-bold mb-4">
                                        <?= htmlspecialchars($project['detail']['subtitle']) ?>
                                    </div>
                                    <h3 class="font-headline text-4xl font-bold mb-6">
                                        <?= htmlspecialchars($project['title']) ?>
                                    </h3>
                                    <p class="text-on-surface-variant text-lg leading-relaxed mb-8">
                                        <?= htmlspecialchars($project['description']) ?>
                                    </p>
                                    <div class="flex gap-6 items-center">
                                        <span class="project-tag secondary-btn">Détails du projet</span>
                                        <span
                                            class="material-symbols-outlined text-3xl group-hover:translate-x-2 transition-transform">open_in_new</span>
                                    </div>
                                </div>
                            </a>
                        <?php else: ?>
                            <!-- Project <?= $index + 1 ?> (Odd index: Image Right) -->
                            <a class="project-item glass-btn group" href="project_detail.php?id=<?= $project['id'] ?>"
                                style="border-radius: 0rem 2rem 2rem 0rem;padding-left: 1rem;">
                                <div class="md:order-1">
                                    <div class="font-mono text-primary font-bold mb-4">
                                        <?= htmlspecialchars($project['detail']['subtitle']) ?>
                                    </div>
                                    <h3 class="font-headline text-4xl font-bold mb-6">
                                        <?= htmlspecialchars($project['title']) ?>
                                    </h3>
                                    <p class="text-on-surface-variant text-lg leading-relaxed mb-8">
                                        <?= htmlspecialchars($project['description']) ?>
                                    </p>
                                    <div class="flex gap-6 items-center">
                                        <span class="project-tag secondary-btn">Détails du projet</span>
                                        <span
                                            class="material-symbols-outlined text-3xl group-hover:text-primary transition-colors">open_in_new</span>
                                    </div>
                                </div>
                                <div class="relative md:order-2 group transition-all ">
                                    <img alt="<?= htmlspecialchars($project['image_alt']) ?>"
                                        class="grayscale group-hover:grayscale-0 w-full aspect-video object-cover transition-transform"

                                        src="<?= htmlspecialchars($project['image']) ?>" />
                                    <div class="project-overlay" style="cursor:pointer"></div>
                                </div>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <!-- CTA Section -->
        <section class="cta-section">
            <div class="cta-bg"></div>
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="font-headline text-5xl font-bold tracking-tighter mb-12">Construire le prochain
                    standard numérique.</h2>
                <div class="cta-buttons">
                    <a href="media/Recherche de stage BTS SIO SLAM MOULINET--PRIMA Louis-8.pdf" download
                        class="btn-primary glass-btn shadow-xl">
                        Télécharger le CV
                    </a>
                    <button class="btn-secondary glass-surface glass-btn border-2 border-primary/20">
                        <script>
                            document.write('<a href="mailto:' + 'm' + 'o' + 'u' + 'l' + 'i' + 'n' + 'e' + 't' + '.' + 'l' + '0' + '3' + '@' + 'g' + 'm' + 'a' + 'i' + 'l' + '.' + 'c' + 'o' + 'm' + '">Prendre contact</a>');
                        </script>
                    </button>
                </div>
            </div>
        </section>
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
            <feGaussianBlur in="turbulence" stdDeviation="3" result="softMap" />
            <feSpecularLighting in="softMap" surfaceScale="5" specularConstant="1" specularExponent="100"
                lighting-color="white" result="specLight">
                <fePointLight x="-200" y="-200" z="300" />
            </feSpecularLighting>
            <feDisplacementMap in="SourceGraphic" in2="softMap" scale="150" xChannelSelector="R" yChannelSelector="G" />
        </filter>
    </svg>
</body>

</html>