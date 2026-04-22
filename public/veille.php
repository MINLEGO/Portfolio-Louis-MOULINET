<?php
$jsonPath = file_exists(__DIR__ . '/../veille.json') ? __DIR__ . '/../veille.json' : __DIR__ . '/veille.json';
$veilleJson = file_get_contents($jsonPath);
$veilleData = json_decode($veilleJson, true);
$themesMap = [];
if (!empty($veilleData['Themes'])) {
    foreach ($veilleData['Themes'] as $theme) {
        $themesMap[$theme['id']] = $theme['tag'];
    }
}
$technologies = $veilleData['Technologies'] ?? [];
$articlesData = $veilleData['articles'] ?? [];
?>
<!DOCTYPE html>

<html class="light" lang="fr">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Veille Technologique</title>
    <meta name="description" content="Explorez ma veille technologique sur l'IA, le développement web et les nouvelles technologies. Analyse d'articles et découvertes techniques par Louis MOULINET." />
    <link rel="canonical" href="https://louismoulinet.com/veille.php" />

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://louismoulinet.com/veille.php" />
    <meta property="og:title" content="Veille Technologique | Louis MOULINET" />
    <meta property="og:description" content="Suivez mes découvertes et analyses sur les dernières tendances tech." />
    <meta property="og:image" content="https://louismoulinet.com/media/og-image-veille.png" />

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
        <button class="mobile-menu-toggle" aria-label="Menu" onclick="let menu=document.querySelector('.nav-links'); menu.classList.toggle('active-mobile'); let icon=this.querySelector('.material-symbols-outlined'); icon.textContent = menu.classList.contains('active-mobile') ? 'close' : 'menu';">
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
    <main class="pt-32 pb-20 px-8 md:px-12 max-w-7xl">
        <!-- Hero Header -->
        <header class="mb-20">
            <div class="veille-hero-tag">
                <span class="hero-tag">/execute as lab run VEILLE</span>
                <div class="veille-divider"></div>
            </div>
            <h1 class="font-headline text-8xl font-bold  tracking-tighter text-on-background mb-6">
                Veille
                <span class="text-gradient shine">Technologique</span>
            </h1>
            <p class="max-w-2xl text-lg text-on-surface-variant font-body leading-relaxed">
                Une collection de solutions, articles, et expérimentations, axée sur l'avancée de l'IA.<!--, les nouvelles technologies FrontEnd / BackEnd ainsi que les processus de développement modernes.-->
            </p>
        </header>
        <!-- Bento Grid Watch Layout -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 ">
            <!-- Theme Section  -->
            <section class="md:col-span-8 space-y-12 rounded-1xl glass-surface p-2 pt-4 small-glass">
                <div class="section-heading-bar pb-4">
                    <h2 class="font-headline text-2xl font-bold tracking-tight flex items-center px-4">
                        <span class="material-symbols-outlined text-primary">article</span>
                        Domaines
                    </h2>
                </div>
                <div class="space-y-16" id="domaines-container">
                    <!-- Populated by JS -->
                </div>
            </section>
            <!-- Sidebar (Articles & Tools) -->
            <aside class="md:col-span-4 space-y-12 ">
                <!-- Articles -->
                <section class="rounded-1xl glass-surface p-3 pt-4 small-glass">
                    <div class="section-heading-bar pb-4 mb-8">
                        <h2 class="font-headline text-xl font-bold tracking-tight flex items-center gap-2 px-4">
                            <span class="material-symbols-outlined text-primary">play_circle</span>
                            Articles
                        </h2>
                        <span class="font-mono text-xs text-outline" id="articles-count">COMPTE : 0_ENTRÉES</span>
                    </div>
                    <div class="space-y-6 rounded-1xl" id="articles-container">
                        <!-- Populated by JS -->
                    </div>
                    <div style="margin-top:2rem">
                        <a class="archive-link" href="articles.php">
                            Voir les archives complètes
                        </a>
                    </div>

                </section>
                <!-- Archive Link -->

                <!-- Tools Stack -->
                <section class="rounded-1xl glass-surface p-3 small-glass">
                    <div class="section-heading-bar pb-4">
                        <h2 class="font-headline text-xl font-bold tracking-tight flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">construction</span>
                            Nouvelles technologies
                        </h2>
                    </div>
                    <div class="grid grid-cols-1 gap-4 ">
                        <?php foreach ($technologies as $tech): ?>
                            <div class="secondary-btn">
                                <div class="tool-icon-wrap">
                                    <span class="material-symbols-outlined text-primary"><?php echo htmlspecialchars($tech['icon'] ?? ''); ?></span>
                                </div>
                                <div>
                                    <h5 class="font-headline font-bold text-sm"><?php echo htmlspecialchars($tech['title'] ?? ''); ?></h5>
                                    <p class="font-mono text-10px text-on-surface-variant uppercase"><?php echo htmlspecialchars($tech['description'] ?? ''); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </aside>
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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const veilleData = <?php echo json_encode($articlesData); ?>;
            const themesMap = <?php echo json_encode($themesMap); ?>;

            const domainesContainer = document.getElementById('domaines-container');
            const articlesContainer = document.getElementById('articles-container');
            const articlesCount = document.getElementById('articles-count');

            async function fetchMicrolink(url) {
                const cacheKey = url;
                const cached = localStorage.getItem(cacheKey);
                if (cached) {
                    try {
                        return JSON.parse(cached);
                    } catch (e) {}
                }
                if (url.includes('youtube.com')) {
                    try {
                        const response = await fetch(`https://www.youtube.com/oembed?url=${encodeURIComponent(url)}&format=json`);

                        //test for 400 bad request
                        if (response.status === 200) {
                            const json = await response.json();
                            console.log(json);

                            localStorage.setItem(cacheKey, JSON.stringify(json));
                            return json;
                        }
                    } catch (e) {
                        console.warn('Youtube oembed fetch failed for', url, e);
                    }
                } else {
                    try {
                        const response = await fetch('https://api.microlink.io?url=' + encodeURIComponent(url));
                        const json = await response.json();
                        console.log(json);
                        if (json.status === 'success' && json.data) {
                            localStorage.setItem(cacheKey, JSON.stringify(json.data));
                            return json.data;
                        }
                    } catch (e) {
                        console.warn('Microlink fetch failed for', url, e);
                    }
                    return null;
                }
            }

            function getThemeName(themeId) {
                // themesMap keys are strings after JSON serialization
                return themesMap[String(themeId)] || 'Autre';
            }

            function buildDomaineArticle(article, meta) {
                const title = (meta && meta.title) || article.title || 'Sans titre';
                const description = (meta && meta.description) || article.description || '';
                const image = (meta && meta.image && meta.image.url) ||
                    (meta && meta.logo && meta.logo.url) ||
                    article.image || 'https://placehold.co/400x300/1a1a2e/ffffff?text=Article';
                const date = article.date || (meta && meta.date) || '';
                const themeName = getThemeName(article.theme_id);
                const href = article.link || '#';

                return `<article class="group glass-btn p-3 rounded-2xl hover:shadow-xl transition-all duration-500">
                    <div class="flex flex-col md:flex-row gap-8">
                        <div class="glass-surface w-full md:w-1/3 aspect-4/3 overflow-hidden rounded-2xl shadow-inner bg-surface-container-low relative">
                            <span class="article-category-badge" style="position: absolute; top: 0.5rem; left: 0.5rem; z-index: 10; background-color: rgba(255,255,255,0.9); backdrop-filter: blur(4px);">${themeName}</span>
                            <img alt="${title}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" src="${image}" />
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            <div class="flex gap-4 mb-3">
                                ${date ? `<time class="font-mono text-10px uppercase tracking-widest text-outline">${date}</time>` : ''}
                            </div>
                            <h3 class="font-headline text-2xl font-bold mb-4 group-hover:text-primary transition-colors">${title}</h3>
                            <p class="text-on-surface-variant font-medium mb-6 leading-relaxed">${description}</p>
                            <!--<a class="article-read-link rounded-1xl glass-btn p-1" href="${href}" target="${href !== '#' ? '_blank' : '_self'}">
                              Lire l'analyse <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </a> -->
                        </div>
                    </div>
                </article>`;
            }

            function buildSidebarArticle(article, link, meta) {
                const title = (meta && meta.title) || article.title || 'Sans titre';
                const description = (meta && meta.description) || article.description || '';
                const image =
                    meta?.image?.url || meta?.thumbnail_url ||
                    meta?.logo?.url ||
                    article.image ||
                    'https://placehold.co/400x225/1a1a2e/ffffff?text=Article';
                const publisher = (meta && (meta.author || meta.author_name)) || article.author || '';
                const themeName = getThemeName(article.theme_id);
                const isVideo = link.includes('youtube.com') || link.includes('youtu.be') || link.includes('vimeo.com');
                const icon = isVideo ? 'play_arrow' : 'open_in_new';
                let host = link;
                try {
                    host = new URL(link).hostname.replace('www.', '');
                } catch (e) {}

                return `<div class="group cursor-pointer" onclick="window.open('${link}', '_blank')" title="${title}">
                    <div class="rounded-1xl glass-surface relative aspect-video bg-surface-container-high mb-3 overflow-hidden">
                        <span class="article-category-badge" 
                        style="position: absolute; top: 0.2rem; left: 0.2rem; z-index: 10; background-color: rgba(255,255,255,0.9); backdrop-filter: blur(4px);">${themeName}</span>
                        <img alt="${title}" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity" src="${image}" />
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="material-symbols-outlined text-4xl text-white opacity-0 group-hover:opacity-100 transition-opacity">${icon}</span>
                        </div>
                        ${(isVideo&&(meta?.duration ||false)) ? `<span class="video-timestamp">${meta.duration}</span>` : ''}
                    </div>
                    <h4 class="font-headline font-bold text-sm leading-tight group-hover:text-primary transition-colors">${title}</h4>
                    <p class="font-mono text-10px text-outline mt-1 uppercase">${publisher ? publisher + ' - ' : ''}${host}</p>
                </div>`;
            }

            // Render all articles immediately using JSON data, then enrich with Microlink
            let sidebarCount = 0;
            const microlinkPromises = [];

            veilleData.forEach((article, index) => {
                const link = article.link ? article.link.replace(/\s+/g, '') : null;

                if (!link) {
                    // Domaine article: render immediately with JSON data
                    domainesContainer.insertAdjacentHTML('beforeend', buildDomaineArticle(article, null));
                } else {
                    sidebarCount++;
                    if (sidebarCount >= 4) return;
                    // Sidebar article: render placeholder immediately, then enrich

                    const placeholder = buildSidebarArticle(article, link, null);
                    articlesContainer.insertAdjacentHTML('beforeend', placeholder);
                    const cardEl = articlesContainer.lastElementChild;

                    // Enrich with Microlink asynchronously
                    const promise = fetchMicrolink(link).then(meta => {
                        if (meta) {
                            cardEl.outerHTML = buildSidebarArticle(article, link, meta);
                        }
                    });
                    microlinkPromises.push(promise);
                }
            });

            if (articlesCount) {
                articlesCount.textContent = 'COMPTE : ' + sidebarCount + '_ENTRÉES';
            }
        });
    </script>
</body>

</html>