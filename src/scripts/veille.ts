// Page Veille et archive (window.VEILLE fourni par components/VeillePage.astro).
import type { Demo, Experiment, VeillePageData } from '../lib/experiments';
import { skeletons } from '../lib/skeletons';
import type { Source } from '../lib/veille';

declare global {
    interface Window { VEILLE: VeillePageData }
}

/** Source telle que renvoyée par le relais. */
type ListedSource = Source & { exp: string | null };
type View = 'exp' | 'src';

(() => {
    const V = window.VEILLE;
    const isHome = V.mode === 'home';
    const PAGE = isHome ? 12 : 24;
    const ICONS: Record<string, string> = { 'Vidéo': 'play_circle', 'Code': 'code', 'Modèle': 'neurology', 'Article': 'article' };
    const $ = <T extends Element = HTMLElement>(sel: string, el: ParentNode = document) => el.querySelector<T>(sel)!;
    const ENTITIES: Record<string, string> = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };
    const esc = (s: unknown) => String(s ?? '').replace(/[&<>"']/g, c => ENTITIES[c]!);
    const safeUrl = (u: string) => /^https?:\/\//i.test(u || '') ? u : '#';
    const fmtDate = (iso?: string) => iso ? new Date(iso).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' }) : '';
    const ytId = (url: string) => (String(url).match(/(?:[?&]v=|youtu\.be\/|\/shorts\/|\/embed\/)([\w-]{11})/) || [])[1];

    const state = { q: V.q || '', format: '', kw: '', offset: 0, total: 0, loading: false, token: 0, items: new Map<string, ListedSource>() };
    const thumbs = new Map<number, string | null>();

    const grid = $('#vs-grid'), count = $('#vs-count'), notice = $('#vs-notice');
    const more = document.querySelector<HTMLAnchorElement>('#vs-more'); // absent de l'archive
    const panel = $('#vs-panel'), content = $('#vs-panel-content');

    // ---------- Cartes ----------
    const thumbHtml = (url: string | null | undefined, format?: string) => url
        ? `<img src="${esc(url)}" alt="" loading="lazy" class="${/\.svg(\?|$)/.test(url) ? 'contain' : ''}">`
        : `<span class="material-symbols-outlined vs-icon">${ICONS[format ?? ''] || 'article'}</span>`;

    const kwButtons = (kws: string[]) => kws.map(k => `<span class="vs-kw" role="button" tabindex="0" data-kw="${esc(k)}">#${esc(k)}</span>`).join('');

    function sourceCard(s: ListedSource) {
        return `<article class="vs-card glass-btn" data-id="${s.id}" tabindex="0" role="button" aria-haspopup="dialog">
            <div class="vs-thumb">
                ${thumbHtml(s.thumb || thumbs.get(s.id), s.format)}
                <span class="vs-badge">${esc(s.format)}</span>
                ${s.exp ? '<span class="vs-badge vs-badge--exp">Expérimenté</span>' : ''}
            </div>
            <div class="vs-body">
                <p class="vs-meta">${esc(s.site)} · <time datetime="${esc(s.date)}" title="Date de ${s.date_kind}">${fmtDate(s.date)}</time></p>
                <h3 class="vs-title">${esc(s.title)}</h3>
                <div class="vs-kws">${kwButtons(s.keywords)}</div>
            </div>
        </article>`;
    }

    function expCard(e: Experiment) {
        const gallery = (e.demos || []).find(d => d.type === 'galerie');
        const first = gallery?.type === 'galerie' ? gallery.images?.[0] : undefined;
        const img = first ? e.base + first : e.source?.thumb;
        return `<article class="vs-card glass-btn" data-exp="${esc(e.slug)}" tabindex="0" role="button" aria-haspopup="dialog">
            <div class="vs-thumb">${thumbHtml(img, 'Article')}<span class="vs-badge vs-badge--exp">Expérimenté</span></div>
            <div class="vs-body">
                <p class="vs-meta">Expérimentation · ${fmtDate(e.date)}</p>
                <h3 class="vs-title">${esc(e.titre)}</h3>
                ${e.source ? `<p class="vs-meta">Source : ${esc(e.source.title)}</p>` : ''}
            </div>
        </article>`;
    }

    // ---------- Chargement ----------
    async function load(reset: boolean) {
        if (reset) {
            state.offset = 0; state.total = 0; state.items.clear();
            grid.innerHTML = skeletons(6);
        } else {
            if (state.loading || state.offset >= state.total) return;
            grid.insertAdjacentHTML('beforeend', skeletons(6));
        }
        const token = ++state.token;
        state.loading = true;
        const params = new URLSearchParams({ q: state.q, format: state.format, kw: state.kw, offset: String(state.offset), limit: String(PAGE) });
        let data: { results: ListedSource[]; total: number; stale_since: string | null };
        try {
            const res = await fetch('/veille_api.php?' + params);
            const body = await res.json();
            if (!res.ok) throw new Error(body.error);
            data = body;
        } catch (err) {
            if (token !== state.token) return;
            state.loading = false;
            grid.querySelectorAll('.vs-skeleton').forEach(n => n.remove());
            notice.textContent = 'Impossible de charger les Sources : ' + ((err as Error).message || 'erreur réseau');
            // Un lien direct vers une Expérimentation s'ouvre même quand les Sources sont indisponibles.
            if (reset) openFromHash();
            return;
        }
        if (token !== state.token) return; // une recherche plus récente a été lancée
        state.loading = false;
        grid.querySelectorAll('.vs-skeleton').forEach(n => n.remove());
        data.results.forEach(s => state.items.set(String(s.id), s));
        grid.insertAdjacentHTML('beforeend', data.results.map(sourceCard).join(''));
        fitKeywords();
        if (!data.total) grid.innerHTML = '<p class="vs-empty">Aucune Source ne correspond.</p>';
        state.offset += data.results.length;
        state.total = data.total;
        count.textContent = `COMPTE : ${data.total}_ENTRÉES`;
        notice.textContent = data.stale_since ? `VeilleFacile est injoignable : données du ${data.stale_since}.` : '';
        data.results.filter(s => !s.thumb && !thumbs.has(s.id)).forEach(loadThumb);
        updateMore();
        if (!isHome && sentinelVisible) load(false);
        if (reset) openFromHash();
    }

    async function loadThumb(s: ListedSource) {
        thumbs.set(s.id, null);
        try {
            const { thumb } = await (await fetch('/veille_api.php?thumb=' + s.id)).json();
            if (!thumb) return;
            thumbs.set(s.id, thumb);
            document.querySelectorAll(`.vs-card[data-id="${s.id}"] .vs-thumb`).forEach(el => {
                el.querySelector('.vs-icon')?.remove();
                el.insertAdjacentHTML('afterbegin', thumbHtml(thumb));
            });
        } catch { /* l'icône du format reste affichée */ }
    }

    // Carte : une ligne de mots-clés ; le premier qui dépasse est raccourci (… dans son propre fond), les suivants masqués.
    function fitKeywords() {
        document.querySelectorAll('.vs-card .vs-kws').forEach(box => {
            const kws = [...box.children] as HTMLElement[];
            kws.forEach(k => { k.hidden = false; k.style.maxWidth = ''; });
            const right = box.getBoundingClientRect().right;
            let cut = false;
            for (const k of kws) {
                if (cut) { k.hidden = true; continue; }
                const r = k.getBoundingClientRect();
                if (r.right <= right) continue;
                cut = true;
                if (right - r.left < 48) k.hidden = true; // trop peu de place pour un mot lisible
                else k.style.maxWidth = (right - r.left) + 'px';
            }
        });
    }
    let resizeTimer: number | undefined;
    addEventListener('resize', () => { clearTimeout(resizeTimer); resizeTimer = window.setTimeout(fitKeywords, 150); });
    document.fonts?.ready.then(fitKeywords);

    // Image cassée -> icône du format
    document.addEventListener('error', e => {
        const img = e.target as Element;
        if (img.matches?.('.vs-thumb img')) img.outerHTML = `<span class="material-symbols-outlined vs-icon">image</span>`;
    }, true);

    function updateMore() {
        if (!more) return;
        more.href = '/veille/archive' + (state.q ? '?q=' + encodeURIComponent(state.q) : '');
        more.textContent = state.q ? `Voir tous les résultats (${state.total})` : "Voir l'archive complète";
    }

    // Défilement infini (archive)
    let sentinelVisible = false;
    if (!isHome) {
        new IntersectionObserver(([entry]) => {
            sentinelVisible = entry!.isIntersecting;
            if (sentinelVisible) load(false);
        }, { rootMargin: '600px' }).observe($('#vs-sentinel'));
    }

    // ---------- Recherche et filtres ----------
    const input = $<HTMLInputElement>('#vs-q');
    input.value = state.q;
    let debounce: number | undefined;
    input.addEventListener('input', () => {
        clearTimeout(debounce);
        debounce = window.setTimeout(() => {
            if (input.value.trim() === state.q) return;
            state.q = input.value.trim();
            if (!isHome) history.replaceState(null, '', state.q ? '?q=' + encodeURIComponent(state.q) : location.pathname);
            load(true);
        }, 400);
    });

    document.querySelectorAll<HTMLElement>('.vs-chip[data-format]').forEach(chip => chip.addEventListener('click', () => {
        document.querySelectorAll('.vs-chip[data-format]').forEach(c => c.setAttribute('aria-pressed', String(c === chip)));
        state.format = chip.dataset.format!;
        load(true);
    }));

    function setKeyword(kw: string) {
        state.kw = kw;
        $('#vs-active-kw').innerHTML = kw ? `<button class="vs-chip" aria-pressed="true" id="vs-kw-clear" aria-label="Retirer le filtre ${esc(kw)}">#${esc(kw)} <span class="material-symbols-outlined">close</span></button>` : '';
        document.querySelector('#vs-kw-clear')?.addEventListener('click', () => setKeyword(''));
        load(true);
    }

    // ---------- Clics sur les cartes ----------
    document.addEventListener('click', e => {
        const target = e.target as Element;
        const kw = target.closest<HTMLElement>('.vs-kw');
        if (kw) {
            e.stopPropagation();
            closePanel();
            setKeyword(kw.dataset.kw!);
            grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
            return;
        }
        const card = target.closest<HTMLElement>('.vs-card[data-id], .vs-card[data-exp]');
        if (card) openCard(card);
    });
    document.addEventListener('keydown', e => {
        const target = e.target as HTMLElement;
        const card = target.closest?.<HTMLElement>('.vs-card[data-id], .vs-card[data-exp]');
        if (e.key !== 'Enter' && e.key !== ' ') return;
        if (target.matches?.('.vs-kw')) { e.preventDefault(); target.click(); }
        else if (card && target === card) { e.preventDefault(); openCard(card); }
    });

    function openCard(card: HTMLElement) {
        if (card.dataset.exp) {
            const exp = V.experiments.find(x => x.slug === card.dataset.exp)!;
            return openPanel(exp.source, exp, 'exp');
        }
        const s = state.items.get(card.dataset.id!)!;
        const exp = s.exp && V.experiments.find(x => x.slug === s.exp);
        openPanel(s, exp || null, exp ? 'exp' : 'src');
    }

    // ---------- Panneau de détail ----------
    let lastFocus: HTMLElement | null = null;

    // Une Source ou une Expérimentation est toujours fournie ; la Source peut manquer à une Expérimentation.
    function openPanel(source: Source | null, exp: Experiment | null, view: View) {
        lastFocus = document.activeElement as HTMLElement | null;
        panel.hidden = false;
        document.body.style.overflow = 'hidden';
        renderPanel(source, exp, view);
        history.replaceState(null, '', location.pathname + location.search + '#' + (exp ? 'exp-' + exp.slug : 'src-' + source!.id));
        $('.vs-close', panel).focus();
    }

    function closePanel() {
        if (panel.hidden) return;
        panel.hidden = true;
        content.innerHTML = ''; // stoppe vidéos et démos
        document.body.style.overflow = '';
        history.replaceState(null, '', location.pathname + location.search);
        lastFocus?.focus();
    }

    function renderPanel(source: Source | null, exp: Experiment | null, view: View) {
        const tab = (v: View, label: string) => `<button role="tab" class="vs-tab" data-view="${v}" aria-selected="${view === v}">${label}</button>`;
        const tabs = exp && source ? `<div class="vs-tabs" role="tablist">${tab('exp', 'Expérimentation')}${tab('src', 'Source')}</div>` : '';
        const v = view === 'exp' ? expView(exp!) : srcView(source!);
        content.innerHTML = `<div class="vs-panel-head">${tabs}<div><p class="vs-meta">${v.meta}</p>
            <h2 class="vs-panel-title" id="vs-panel-title">${esc(v.title)}</h2></div></div>` + v.body;
        content.querySelectorAll<HTMLElement>('[data-view]').forEach(b => b.addEventListener('click', () => renderPanel(source, exp, b.dataset.view as View)));
        $('.vs-panel-box', panel).scrollTop = 0;
    }

    function srcView(s: Source) {
        return { meta: `${esc(s.format)} · ${esc(s.site)} · ${s.date_kind === 'parution' ? 'publié le' : 'capturé le'} ${fmtDate(s.date)}`, title: s.title, body: `
            <div class="vs-kws">${kwButtons(s.keywords)}</div>
            <div class="vs-md">${s.summary_html}</div>
            <a class="vs-open glass-btn" href="${esc(safeUrl(s.url))}" target="_blank" rel="noopener">Ouvrir la source <span class="material-symbols-outlined">open_in_new</span></a>` };
    }

    // Médias : "entete": N les place sous le titre, {{demo:N}} dans le texte les insère sur place,
    // les autres s'affichent à la fin. N commence à 1.
    function expView(e: Experiment) {
        const demos = e.demos || [], used = new Set<number>();
        const take = (n: number) => { const d = demos[n - 1]; if (!d) return ''; used.add(n - 1); return demoHtml(d, e.base); };
        const header = e.entete ? `<div class="vs-exp-header">${take(e.entete)}</div>` : '';
        const body = e.html.replace(/\{\{demo:(\d+)\}\}/g, (m, n: string) => take(+n) || m);
        return { meta: `Expérimentation · ${fmtDate(e.date)}`, title: e.titre, body: `${header}
            <div class="vs-md">${body}</div>
            ${demos.map((d, i) => used.has(i) ? '' : demoHtml(d, e.base)).join('')}` };
    }

    function demoHtml(d: Demo, base: string) {
        if (d.type === 'galerie') {
            const imgs = d.images || [], caps = d.legendes || [];
            return `<figure class="vs-gallery">
                <img class="vs-gallery-main" src="${esc(base + imgs[0])}" alt="${esc(caps[0] || '')}">
                <figcaption>${esc(caps[0] || '')}</figcaption>
                <div class="vs-gallery-thumbs">${imgs.map((src, i) =>
                    `<button data-src="${esc(base + src)}" data-cap="${esc(caps[i] || '')}" aria-label="Image ${i + 1}"><img src="${esc(base + src)}" alt=""></button>`).join('')}</div>
            </figure>`;
        }
        if (d.type === 'video') {
            const id = ytId(d.url);
            return id
                ? `<div class="vs-video"><iframe src="https://www.youtube-nocookie.com/embed/${id}" title="Vidéo de démonstration" allowfullscreen loading="lazy"></iframe></div>`
                : `<video class="vs-video" controls preload="metadata" src="${esc(d.url.startsWith('http') ? d.url : base + d.url)}"></video>`;
        }
        if (d.type === 'html') {
            // Sans allow-same-origin : la démo ne peut pas toucher au site.
            return `<iframe class="vs-demo-html" sandbox="allow-scripts" src="${esc(base + d.fichier)}" style="height:${parseInt(String(d.hauteur), 10) || 400}px" title="Démonstration interactive"></iframe>`;
        }
        return '';
    }

    content.addEventListener('click', e => {
        const thumb = (e.target as Element).closest<HTMLElement>('.vs-gallery-thumbs button');
        if (!thumb) return;
        const fig = thumb.closest('.vs-gallery')!;
        const main = $<HTMLImageElement>('.vs-gallery-main', fig);
        main.src = thumb.dataset.src!;
        main.alt = thumb.dataset.cap!;
        $('figcaption', fig).textContent = thumb.dataset.cap!;
    });

    panel.addEventListener('click', e => { if ((e.target as Element).closest('[data-close]')) closePanel(); });
    document.addEventListener('keydown', e => {
        if (panel.hidden) return;
        if (e.key === 'Escape') return closePanel();
        if (e.key !== 'Tab') return;
        // Garde le focus dans le panneau
        const f = [...panel.querySelectorAll<HTMLElement>('button, a[href], iframe, video, [tabindex="0"]')];
        if (!f.length) return;
        if (e.shiftKey && document.activeElement === f[0]) { e.preventDefault(); f.at(-1)!.focus(); }
        else if (!e.shiftKey && document.activeElement === f.at(-1)) { e.preventDefault(); f[0]!.focus(); }
    });

    function openFromHash() {
        const [, kind, key] = location.hash.match(/^#(exp|src)-(.+)$/) || [];
        if (!kind || !panel.hidden) return;
        if (kind === 'exp') {
            const exp = V.experiments.find(x => x.slug === key);
            if (exp) openPanel(exp.source, exp, 'exp');
        } else {
            const s = state.items.get(key!);
            if (s) openPanel(s, null, 'src');
        }
    }

    // ---------- Expérimentations ----------
    function renderExperiments() {
        const section = $('#vs-exp-section');
        if (!section) return;
        section.hidden = !V.experiments.length;
        $('#vs-exp-row').innerHTML = V.experiments.map(expCard).join('');
    }

    renderExperiments();
    load(true);
})();
