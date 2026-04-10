<?php
// Load projects from JSON file
$jsonPath = __DIR__ . '/../projects.json';
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

<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title><?= htmlspecialchars($project['title']) ?> | Technical Architect</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;family=Inter:wght@300;400;500;600&amp;family=Manrope:wght@400;500;600&amp;family=JetBrains+Mono&amp;display=swap"
        rel="stylesheet" />
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
                        "label": ["Manrope"],
                        "mono": ["JetBrains Mono"]
                    }
                },
            },
        }
    </script>

</head>

<body class="bg-surface text-on-surface font-body selection:bg-primary-container selection:text-on-primary-container">
    <!-- Top Navigation Bar -->
    <nav
        class="fixed top-0 w-full z-50 bg-slate-50/70 backdrop-blur-md flex justify-between items-center px-8 py-4 max-w-full">
        <div class="text-xl font-bold tracking-tighter text-slate-900 font-headline uppercase">Technical Architect</div>
        <div class="hidden md:flex items-center gap-8">
            <a class="font-headline font-medium uppercase tracking-tight text-slate-600 hover:text-blue-500 transition-all duration-300"
                href="index.html">Home</a>
            <a class="font-headline font-medium uppercase tracking-tight text-blue-500 border-b-2 border-blue-400 pb-1"
                href="projects.php">Projects</a>
            <a class="font-headline font-medium uppercase tracking-tight text-slate-600 hover:text-blue-500 transition-all duration-300"
                href="veille.html">Research</a>
        </div>
        <button
            class="bg-primary text-on-primary px-6 py-2 font-headline font-bold uppercase tracking-widest text-xs scale-95 transition-transform duration-200 hover:opacity-90">Contact</button>
    </nav>
    <main class="pt-24 pb-20 px-8 max-w-7xl mx-auto">
        <!-- Hero Section -->
        <header class="mb-20 grid grid-cols-1 md:grid-cols-12 gap-8 items-end">
            <div class="md:col-span-8">
                <span class="font-label text-primary font-semibold tracking-widest uppercase text-xs mb-4 block">
                    <?= htmlspecialchars($d['subtitle']) ?>
                </span>
                <h1 class="font-headline text-6xl md:text-8xl font-bold tracking-tighter leading-none mb-6">
                    <?= htmlspecialchars($project['title']) ?>
                </h1>
                <p class="font-body text-xl text-on-surface-variant max-w-xl leading-relaxed">
                    <?= htmlspecialchars($project['description']) ?>
                </p>
            </div>
            <div class="md:col-span-4 flex justify-end">
                <div class="flex flex-col gap-2 text-right">
                    <span class="font-mono text-xs text-outline uppercase">Status: <?= htmlspecialchars($d['status']) ?></span>
                    <span class="font-mono text-xs text-outline uppercase">Stack: <?= htmlspecialchars($d['stack']) ?></span>
                    <span class="font-mono text-xs text-outline uppercase">Year: <?= htmlspecialchars($d['year']) ?></span>
                </div>
            </div>
        </header>
        <!-- High-Res Project Image -->
        <section class="mb-32">
            <div class="w-full aspect-[21/9] bg-surface-container-high relative overflow-hidden group">
                <img alt="<?= htmlspecialchars($d['detail_image_alt']) ?>"
                    class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all duration-700"
                    src="<?= htmlspecialchars($d['detail_image']) ?>" />
                <div class="absolute inset-0 bg-primary/5 mix-blend-overlay"></div>
            </div>
        </section>
        <!-- Project Narrative Grid -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-16 mb-32">
            <!-- Left Column: The Challenge -->
            <div class="md:col-span-5">
                <h2 class="font-headline text-3xl font-bold mb-8 flex items-center gap-3">
                    <span class="w-8 h-px bg-primary"></span> The Challenge
                </h2>
                <div class="space-y-6 text-on-surface-variant leading-loose font-light">
                    <p><?= htmlspecialchars($d['challenge']) ?></p>
                    <p><?= htmlspecialchars($d['challenge2']) ?></p>
                </div>
            </div>
            <!-- Right Column: Key Features (Bento Style) -->
            <div class="md:col-span-7 grid grid-cols-2 gap-4">
                <div class="bg-surface-container-low p-8 col-span-2">
                    <span class="material-symbols-outlined text-primary mb-4"><?= htmlspecialchars($d['feature1_icon']) ?></span>
                    <h3 class="font-headline font-bold text-lg mb-2"><?= htmlspecialchars($d['feature1_title']) ?></h3>
                    <p class="text-sm text-on-surface-variant font-body"><?= htmlspecialchars($d['feature1_desc']) ?></p>
                </div>
                <div class="bg-surface-container-high p-8">
                    <span class="material-symbols-outlined text-primary mb-4"><?= htmlspecialchars($d['feature2_icon']) ?></span>
                    <h3 class="font-headline font-bold text-lg mb-2"><?= htmlspecialchars($d['feature2_title']) ?></h3>
                    <p class="text-sm text-on-surface-variant font-body"><?= htmlspecialchars($d['feature2_desc']) ?></p>
                </div>
                <div class="bg-primary text-on-primary p-8">
                    <span class="material-symbols-outlined mb-4"><?= htmlspecialchars($d['feature3_icon']) ?></span>
                    <h3 class="font-headline font-bold text-lg mb-2"><?= htmlspecialchars($d['feature3_title']) ?></h3>
                    <p class="text-sm font-body opacity-90"><?= htmlspecialchars($d['feature3_desc']) ?></p>
                </div>
            </div>
        </div>
        <!-- Technical Specification / Code Block -->
        <section class="mb-32">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
                <div class="lg:col-span-4 sticky top-32">
                    <h2 class="font-headline text-3xl font-bold mb-6">Technical Architecture</h2>
                    <p class="text-on-surface-variant mb-8 font-body">
                        Core code snippet illustrating the key implementation pattern for this project.
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($d['tech_tags'] as $tag): ?>
                        <span
                            class="bg-surface-variant px-3 py-1 rounded-full text-[10px] font-mono uppercase tracking-widest text-on-surface-variant"><?= htmlspecialchars($tag) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="lg:col-span-8">
                    <div
                        class="bg-surface-container-lowest border-l-2 border-tertiary p-8 font-mono text-sm overflow-x-auto">
                        <div
                            class="flex justify-between items-center mb-6 text-outline text-[10px] uppercase tracking-tighter">
                            <span><?= htmlspecialchars($d['code_file']) ?></span>
                            <span><?= htmlspecialchars($d['code_version']) ?></span>
                        </div>
                        <pre class="leading-relaxed text-on-surface-variant"><?= htmlspecialchars($d['code_block']) ?></pre>
                    </div>
                </div>
            </div>
        </section>
        <!-- Secondary Imagery / Detail View -->
        <section class="mb-32 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-surface-container-low aspect-square overflow-hidden">
                <img alt="<?= htmlspecialchars($d['detail_image2_alt']) ?>"
                    class="w-full h-full object-cover mix-blend-multiply opacity-80"
                    src="<?= htmlspecialchars($d['detail_image2']) ?>" />
            </div>
            <div class="bg-surface-container-highest aspect-square flex flex-col justify-center p-16">
                <span class="font-headline text-5xl font-bold mb-4"><?= str_pad($project['id'], 2, '0', STR_PAD_LEFT) ?></span>
                <p class="font-body text-xl text-on-surface-variant">
                    <?= htmlspecialchars($d['detail_note']) ?>
                </p>
            </div>
        </section>
        <!-- Navigation between projects -->
        <nav class="flex justify-between items-center pt-12 border-t border-outline-variant/20">
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
                All Projects
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
    <!-- Footer -->
    <footer
        class="w-full py-12 px-8 border-t border-slate-200 bg-slate-50 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="font-['Space_Grotesk'] font-bold text-slate-900 uppercase">Technical Architect</div>
        <p class="font-['Inter'] text-sm tracking-wide text-slate-500">© 2024 Technical Architect | BTS SIO Portfolio
        </p>
        <div class="flex gap-6">
            <a class="text-slate-500 hover:text-blue-400 transition-colors opacity-80 hover:opacity-100 font-['Inter'] text-sm tracking-wide"
                href="#">GitHub</a>
            <a class="text-slate-500 hover:text-blue-400 transition-colors opacity-80 hover:opacity-100 font-['Inter'] text-sm tracking-wide"
                href="#">LinkedIn</a>
            <a class="text-slate-500 hover:text-blue-400 transition-colors opacity-80 hover:opacity-100 font-['Inter'] text-sm tracking-wide"
                href="#">Documentation</a>
        </div>
    </footer>
</body>

</html>
