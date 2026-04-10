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
    $projects = array_filter($projects, function($p) use ($filterMap, $activeFilter) {
        return stripos($p['category'], $filterMap[$activeFilter]) !== false;
    });
}
?>
<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Selected Works | Technical Architect</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;family=Inter:wght@300;400;500;600&amp;family=Manrope:wght@400;500;600&amp;family=JetBrains+Mono:wght@400;500&amp;display=swap"
        rel="stylesheet" />
    <!-- Icons -->
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "on-surface-variant": "#5a5c5c",
                        "surface-container-lowest": "#ffffff",
                        "on-tertiary": "#ffefed",
                        "primary": "#006a2c",
                        "on-secondary-fixed": "#31414f",
                        "on-background": "#2d2f2f",
                        "inverse-primary": "#00fe74",
                        "on-secondary-container": "#445362",
                        "on-primary-fixed": "#00461b",
                        "on-tertiary-fixed": "#3a0002",
                        "tertiary-fixed": "#ff9287",
                        "secondary-fixed": "#d4e4f6",
                        "inverse-on-surface": "#9c9d9d",
                        "outline-variant": "#acadad",
                        "tertiary": "#bb0012",
                        "surface-tint": "#006a2c",
                        "on-error": "#ffefee",
                        "error-container": "#fb5151",
                        "tertiary-dim": "#a4000e",
                        "primary-fixed": "#00fe74",
                        "on-error-container": "#570008",
                        "surface-container-high": "#e1e3e3",
                        "surface-bright": "#f6f6f6",
                        "error-dim": "#9f0519",
                        "background": "#f6f6f6",
                        "error": "#b31b25",
                        "outline": "#767777",
                        "on-primary-container": "#005b25",
                        "secondary-container": "#d4e4f6",
                        "tertiary-fixed-dim": "#ff7b6f",
                        "primary-container": "#00fe74",
                        "on-primary-fixed-variant": "#00662a",
                        "surface-dim": "#d3d5d5",
                        "on-secondary-fixed-variant": "#4e5d6c",
                        "on-tertiary-container": "#690005",
                        "surface-variant": "#dbdddd",
                        "secondary-fixed-dim": "#c6d6e8",
                        "surface-container-low": "#f0f1f1",
                        "primary-dim": "#005d26",
                        "secondary": "#4e5d6c",
                        "surface-container": "#e7e8e8",
                        "on-secondary": "#eaf4ff",
                        "surface": "#f6f6f6",
                        "on-surface": "#2d2f2f",
                        "surface-container-highest": "#dbdddd",
                        "tertiary-container": "#ff9287",
                        "inverse-surface": "#0c0f0f",
                        "on-primary": "#ceffd0",
                        "secondary-dim": "#425160",
                        "on-tertiary-fixed-variant": "#7a0008",
                        "primary-fixed-dim": "#00ee6d"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.125rem",
                        "lg": "0.25rem",
                        "xl": "0.5rem",
                        "full": "0.75rem"
                    },
                    "fontFamily": {
                        "headline": ["Space Grotesk"],
                        "body": ["Inter"],
                        "label": ["Manrope"]
                    }
                },
            },
        }
    </script>

</head>

<body class="bg-surface text-on-surface font-body selection:bg-primary-container selection:text-on-primary-container">
    <!-- TopNavBar -->
    <nav
        class="fixed top-0 w-full z-50 bg-slate-50/70 backdrop-blur-md flex justify-between items-center px-8 py-4 max-w-full tonal-shift bg-slate-100/50">
        <div class="text-xl font-bold tracking-tighter text-slate-900 font-headline">Technical Architect</div>
        <div class="hidden md:flex items-center gap-8">
            <a class="font-headline font-medium uppercase tracking-tight text-slate-600 hover:text-blue-500 transition-all duration-300"
                href="index.html">Home</a>
            <a class="font-headline font-medium uppercase tracking-tight text-blue-500 border-b-2 border-blue-400 pb-1"
                href="projects.php">Projects</a>
            <a class="font-headline font-medium uppercase tracking-tight text-slate-600 hover:text-blue-500 transition-all duration-300"
                href="veille.html">Research</a>
        </div>
        <button
            class="bg-primary text-on-tertiary px-6 py-2 rounded-none font-label font-semibold tracking-wide hover:opacity-90 transition-opacity">
            Contact
        </button>
    </nav>
    <main class="pt-32 pb-24 px-8 max-w-7xl mx-auto">
        <!-- Hero Section -->
        <header class="mb-24 flex flex-col md:flex-row md:items-end justify-between gap-8">
            <div class="max-w-2xl">
                <span class="font-['JetBrains_Mono'] text-primary text-sm tracking-widest uppercase mb-4 block">Archive
                    / Portfolio</span>
                <h1 class="font-headline font-bold text-6xl md:text-8xl tracking-tighter leading-none mb-6">Selected
                    Works</h1>
                <p class="text-on-surface-variant text-lg md:text-xl font-body leading-relaxed">
                    A curated collection of technical solutions, focusing on systems architecture, cloud infrastructure,
                    and modern development workflows during the BTS SIO journey.
                </p>
            </div>
            <div class="flex gap-4">
                <div class="flex flex-col items-end">
                    <span class="text-xs font-label uppercase text-outline mb-2">Build Status</span>
                    <div class="flex items-center gap-2 bg-surface-container-low px-4 py-2">
                        <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                        <span class="font-['JetBrains_Mono'] text-sm">PROD_READY</span>
                    </div>
                </div>
            </div>
        </header>
        <!-- Filters -->
        <section class="mb-16">
            <div class="flex flex-wrap gap-4 items-center">
                <a href="projects.php"
                    class="px-6 py-2 font-label text-sm rounded-full transition-colors <?= $activeFilter === 'all' ? 'bg-primary text-on-tertiary' : 'bg-surface-container-high text-on-surface-variant hover:bg-surface-variant' ?>">
                    All Projects</a>
                <a href="projects.php?filter=web"
                    class="px-6 py-2 font-label text-sm rounded-full transition-colors <?= $activeFilter === 'web' ? 'bg-primary text-on-tertiary' : 'bg-surface-container-high text-on-surface-variant hover:bg-surface-variant' ?>">
                    Web Development</a>
                <a href="projects.php?filter=software"
                    class="px-6 py-2 font-label text-sm rounded-full transition-colors <?= $activeFilter === 'software' ? 'bg-primary text-on-tertiary' : 'bg-surface-container-high text-on-surface-variant hover:bg-surface-variant' ?>">
                    Software Engineering</a>
                <a href="projects.php?filter=devops"
                    class="px-6 py-2 font-label text-sm rounded-full transition-colors <?= $activeFilter === 'devops' ? 'bg-primary text-on-tertiary' : 'bg-surface-container-high text-on-surface-variant hover:bg-surface-variant' ?>">
                    DevOps &amp; Cloud</a>
            </div>
        </section>
        <!-- Projects Grid (dynamic) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-y-32 gap-x-16">
            <?php foreach ($projects as $i => $project): ?>
            <article class="group <?= ($i % 2 === 1) ? 'md:mt-24' : '' ?>">
                <div class="mb-8 overflow-hidden bg-surface-container-low aspect-[16/10] relative">
                    <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-700"
                        src="<?= htmlspecialchars($project['image']) ?>"
                        alt="<?= htmlspecialchars($project['image_alt']) ?>" />
                    <div
                        class="absolute top-4 left-4 bg-surface/90 backdrop-blur px-3 py-1 text-[10px] font-['JetBrains_Mono'] uppercase tracking-widest">
                        <?= htmlspecialchars($project['index']) ?> / <?= htmlspecialchars($project['category']) ?>
                    </div>
                </div>
                <div class="flex flex-col">
                    <div class="flex justify-between items-start mb-4">
                        <h2 class="font-headline font-bold text-3xl tracking-tight"><?= htmlspecialchars($project['title']) ?></h2>
                        <a href="project_detail.php?id=<?= $project['id'] ?>">
                            <span
                                class="material-symbols-outlined text-primary group-hover:translate-x-1 transition-transform">arrow_outward</span>
                        </a>
                    </div>
                    <div class="flex gap-2 mb-6 flex-wrap">
                        <?php foreach ($project['tags'] as $tag): ?>
                        <span
                            class="px-3 py-1 bg-surface-variant text-on-surface-variant font-['JetBrains_Mono'] text-[10px] rounded-full uppercase"><?= htmlspecialchars($tag) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <p class="text-on-surface-variant leading-relaxed mb-6 font-body">
                        <?= htmlspecialchars($project['description']) ?>
                    </p>
                    <div class="h-px bg-outline-variant/20 w-full mb-6"></div>
                    <code class="text-xs text-primary font-['JetBrains_Mono']"><?= htmlspecialchars($project['code_snippet']) ?></code>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <!-- Tech Stack Highlight -->
        <section class="mt-48 pt-24 border-t border-outline-variant/10">
            <h3 class="font-headline font-bold text-4xl mb-12">System Core</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-12">
                <div class="flex flex-col gap-2">
                    <span class="text-primary font-['JetBrains_Mono'] text-xs">01. Architectures</span>
                    <p class="font-label font-medium">RESTful APIs, Microservices, Monolithic Systems</p>
                </div>
                <div class="flex flex-col gap-2">
                    <span class="text-primary font-['JetBrains_Mono'] text-xs">02. Databases</span>
                    <p class="font-label font-medium">PostgreSQL, MongoDB, Redis, SQLite</p>
                </div>
                <div class="flex flex-col gap-2">
                    <span class="text-primary font-['JetBrains_Mono'] text-xs">03. Platforms</span>
                    <p class="font-label font-medium">AWS, Vercel, DigitalOcean, Heroku</p>
                </div>
                <div class="flex flex-col gap-2">
                    <span class="text-primary font-['JetBrains_Mono'] text-xs">04. Automation</span>
                    <p class="font-label font-medium">GitHub Actions, Jenkins, Ansible</p>
                </div>
            </div>
        </section>
    </main>
    <!-- Footer -->
    <footer
        class="w-full py-12 px-8 border-t border-slate-200 bg-slate-50 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="font-['Space_Grotesk'] font-bold text-slate-900">Technical Architect</div>
        <div class="text-slate-500 font-body text-sm tracking-wide">
            © 2024 Technical Architect | BTS SIO Portfolio
        </div>
        <div class="flex gap-8 font-body text-sm tracking-wide">
            <a class="text-slate-500 hover:text-blue-400 transition-colors opacity-80 hover:opacity-100"
                href="#">GitHub</a>
            <a class="text-slate-500 hover:text-blue-400 transition-colors opacity-80 hover:opacity-100"
                href="#">LinkedIn</a>
            <a class="text-slate-500 hover:text-blue-400 transition-colors opacity-80 hover:opacity-100"
                href="#">Documentation</a>
        </div>
    </footer>
</body>

</html>
