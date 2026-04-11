<!DOCTYPE html>

<html class="light" lang="fr">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Veille Technologique | Louis MOULINET</title>
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
            <a class="nav-link glass-btn" href="index.php">Accueil</a>
            <a class="nav-link glass-btn" href="projects.php">Projets</a>
            <a class="nav-link glass-btn active" href="veille.php">Veille</a>
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
                <span class="text-gradient">Technologique</span>
            </h1>
            <p class="max-w-2xl text-lg text-on-surface-variant font-body leading-relaxed">
                Une collection de solutions, articles, et expérimentations, axée sur l'avancée de l'IA, les nouvelles technologies FrontEnd / BackEnd ainsi que les processus de développement modernes.
            </p>
        </header>
        <!-- Bento Grid Watch Layout -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
            <!-- Articles Section (Main Stream) -->
            <section class="md:col-span-8 space-y-12">
                <div class="section-heading-bar pb-4">
                    <h2 class="font-headline text-2xl font-bold tracking-tight flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">article</span>
                        Articles &amp; Analyses
                    </h2>
                    <span class="font-mono text-xs text-outline">COMPTE : 128_ENTRÉES</span>
                </div>
                <div class="space-y-16">
                    <!-- Article Item -->
                    <article class="group glass-surface p-6 rounded-3xl hover:shadow-xl transition-all duration-500">
                        <div class="flex flex-col md:flex-row gap-8">
                            <div
                                class="w-full md:w-1/3 aspect-4/3 overflow-hidden rounded-2xl shadow-inner bg-surface-container-low">
                                <img alt="Security hardware"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuD59pD3jA5jFBnUH8eUPfuzoE1z5Q_Lk1JeVOFugHqXZFBYgfXdlkSY9XPSvXFKdps8rVZZkbHkojiFNtPHN3a2HnOaGTzkp4oyRHM7yYWuEwu2vYs1EW4MjnkmxZU1jSiHDVddgb7t9DkD8tuHH3B1i1LdUajdmStPhnxNkd9DDrrR0_EPJCcADSN7YsgKTczyavkJ2Likoy3B9PV6jJMDZEXv4R-37ikggHuqKZX1e9luujj1wyt4PNztCWMjYnYxNzryMBSkdY3C" />
                            </div>
                            <div class="flex-1 flex flex-col justify-center">
                                <div class="flex gap-4 mb-3">
                                    <span class="article-category-badge">Cybersécurité</span>
                                    <time class="font-mono text-10px uppercase tracking-widest text-outline">24 Oct,
                                        2024</time>
                                </div>
                                <h3
                                    class="font-headline text-2xl font-bold mb-4 group-hover:text-primary transition-colors">
                                    Le passage à l'architecture Zero-Trust dans les environnements d'entreprise</h3>
                                <p class="text-on-surface-variant font-medium mb-6 leading-relaxed">
                                    Exploration de la transition fondamentale d'une sécurité périmétrique vers des
                                    modèles de vérification centrés sur l'identité. Analyse des défis actuels de mise en
                                    œuvre dans les systèmes hérités.
                                </p>
                                <a class="article-read-link" href="#">
                                    Lire l'analyse <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                </a>
                            </div>
                        </div>
                    </article>
                    <!-- Article Item -->
                    <article class="group">
                        <div class="flex flex-col md:flex-row gap-8">
                            <div class="w-full md:w-1/3 aspect-4/3 overflow-hidden bg-surface-container-low">
                                <img alt="React Code"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuAJ5jReKYnhz3L28x5CH6JAQXEBtiDND9UAJD3ruJgDw3tAONO3mp13_rgY4E6yJ14PYuOtbrPtqXwy7ZLrlbQyAStXzEsYiu9MpazEi_FROPIEtxXS4PeS5wip0NKA0F4npOuqs-APP3_O5jq0pAXHdQ9G8jkzvxt2sFtwvKnC8zx2mBudzSvPRNFQ5Wo4TUjgLgUIi3HRhbPxTGXf1VyM7idbL409eXNZxPW3yFI0byFY3q4kQeiuDrGHzbtKkM_acCvGU-VMgYmD" />
                            </div>
                            <div class="flex-1">
                                <div class="flex gap-4 mb-3">
                                    <span class="article-category-badge article-category-badge--outline">Frontend</span>
                                    <time class="font-mono text-10px uppercase tracking-widest text-outline">18 Oct,
                                        2024</time>
                                </div>
                                <h3
                                    class="font-headline text-2xl font-semibold mb-4 group-hover:text-primary transition-colors">
                                    Server Components et le futur de l'hydratation sélective</h3>
                                <p class="text-on-surface-variant font-body mb-6 leading-relaxed">
                                    Comment les React Server Components redéfinissent le modèle mental pour construire
                                    des applications web performantes en déplaçant les calculs lourds vers l'Edge.
                                </p>
                                <a class="article-read-link" href="#">
                                    Lire l'analyse <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
            </section>
            <!-- Sidebar (Videos & Tools) -->
            <aside class="md:col-span-4 space-y-16">
                <!-- Videos Section -->
                <section>
                    <div class="section-heading-bar pb-4 mb-8">
                        <h2 class="font-headline text-xl font-bold tracking-tight flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">play_circle</span>
                            Masterclasses
                        </h2>
                    </div>
                    <div class="space-y-6">
                        <div class="group cursor-pointer">
                            <div class="relative aspect-video bg-surface-container-high mb-3 overflow-hidden">
                                <img alt="Developer workspace"
                                    class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuDVhiZh5Bu3EiI4fEvhnzXr-f6qvjaw-UrixNMBRH_DETkj6avZQ2hre2_vzoidpKXHq_RW85aIgRBo0VSLLpVt0CUcBxhjcyjCo3isx3nMlAKrn2y62znMQtYA_3nMnMYGpG1Z0HQIHzepGun8-deZwb7yORgJ00P_0BvE333Ddu48_YVaWzcw8dx26hDUFZcnZvElFEMJNOh2BbCzUE-fd6hbvEpCK3ezxh-gQSTzaYs154UCuGKgyKMgAt-jWafXNZz1RZjlHfih" />
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span
                                        class="material-symbols-outlined text-4xl text-white opacity-0 group-hover:opacity-100 transition-opacity">play_arrow</span>
                                </div>
                                <span class="video-timestamp">14:22</span>
                            </div>
                            <h4
                                class="font-headline font-bold text-sm leading-tight group-hover:text-primary transition-colors">
                                Modern CI/CD Pipelines for Scalable Microservices</h4>
                            <p class="font-mono text-10px text-outline mt-1 uppercase">YouTube • Fireship</p>
                        </div>
                        <div class="group cursor-pointer">
                            <div class="relative aspect-video bg-surface-container-high mb-3 overflow-hidden">
                                <img alt="Data Center"
                                    class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuBDCZNDqkwKUmnNWuOBwazv_6UH748oEsZJkt24coSON2ZW9y5Pe7RZc7oQUTKu8eLuxHdhU4Wl-hoUUg-Bc4HCBCmKBTWQIJMr29azSm_QF0n1P05AiD1hWKh4FZ__SHXzh87yfNmeNe4MYSxIq_XwbSdTLUzZuvAo1OlKak671NMrwSXcgewFCXqFDvfXDink-3DvhIcGS_mzb22hO-fMfgNLJIiIIzKawJcHq0FxhMOEbBoY_SM7JPA4_Rr5qYODr97u59S7eFlb" />
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span
                                        class="material-symbols-outlined text-4xl text-white opacity-0 group-hover:opacity-100 transition-opacity">play_arrow</span>
                                </div>
                                <span class="video-timestamp">08:45</span>
                            </div>
                            <h4
                                class="font-headline font-bold text-sm leading-tight group-hover:text-primary transition-colors">
                                AWS Lambda vs. Google Cloud Functions: Benchmark 2024</h4>
                            <p class="font-mono text-10px text-outline mt-1 uppercase">Vimeo • TechTalks</p>
                        </div>
                    </div>
                </section>
                <!-- Tools Stack -->
                <section>
                    <div class="section-heading-bar pb-4 mb-8">
                        <h2 class="font-headline text-xl font-bold tracking-tight flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">construction</span>
                            Boîte à outils
                        </h2>
                    </div>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="secondary-btn">
                            <div class="tool-icon-wrap">
                                <span class="material-symbols-outlined text-primary">terminal</span>
                            </div>
                            <div>
                                <h5 class="font-headline font-bold text-sm">Warp Terminal</h5>
                                <p class="font-mono text-10px text-on-surface-variant uppercase">Le terminal moderne
                                    assisté par IA</p>
                            </div>
                        </div>
                        <div class="secondary-btn">
                            <div class="tool-icon-wrap">
                                <span class="material-symbols-outlined text-primary">database</span>
                            </div>
                            <div>
                                <h5 class="font-headline font-bold text-sm">Supabase</h5>
                                <p class="font-mono text-10px text-on-surface-variant uppercase">Alternative Open Source
                                    à Firebase</p>
                            </div>
                        </div>
                        <div class="secondary-btn">
                            <div class="tool-icon-wrap">
                                <span class="material-symbols-outlined text-primary">monitoring</span>
                            </div>
                            <div>
                                <h5 class="font-headline font-bold text-sm">Grafana Phlare</h5>
                                <p class="font-mono text-10px text-on-surface-variant uppercase">Profilage continu</p>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- Archive Link -->
                <div class="pt-8">
                    <a class="archive-link" href="#">
                        Voir les archives complètes
                    </a>
                </div>
            </aside>
        </div>
    </main>
    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-copy">© 2024 Louis MOULINET | Portfolio BTS SIO</div>
        <div class="footer-links">
            <a class="footer-link" href="#">GitHub</a>
            <a class="footer-link" href="#">LinkedIn</a>
            <a class="footer-link" href="#">Documentation</a>
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
