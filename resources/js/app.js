import './bootstrap';

document.documentElement.classList.add('js');

// Scroll reveal
(() => {
    const items = document.querySelectorAll('.reveal');
    if (!items.length) return;

    if (!('IntersectionObserver' in window)) {
        items.forEach((el) => el.classList.add('in'));
        return;
    }

    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const el = entry.target;
                el.classList.add('in');
                io.unobserve(el);

                // Once it has faded in, drop the reveal classes so hover and press effects animate normally.
                const delay = parseInt(getComputedStyle(el).getPropertyValue('--d'), 10) || 0;
                setTimeout(() => el.classList.remove('reveal', 'in'), 760 + delay);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    items.forEach((el) => io.observe(el));
})();

// Header: shadow after scrolling + mobile menu
(() => {
    const header = document.getElementById('site-header');
    const toggle = document.getElementById('menu-toggle');
    const menu = document.getElementById('mobile-menu');

    if (header) {
        const onScroll = () => header.classList.toggle('shadow-sm', window.scrollY > 8);
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    }

    if (toggle && menu) {
        toggle.addEventListener('click', () => {
            const open = menu.classList.toggle('hidden') === false;
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            document.body.classList.toggle('overflow-hidden', open);
        });

        menu.querySelectorAll('a').forEach((a) => a.addEventListener('click', () => {
            menu.classList.add('hidden');
            toggle.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('overflow-hidden');
        }));
    }
})();

// Lightbox for photo groups: <a href="big.webp" data-lightbox="group"><img ...></a>
(() => {
    const links = Array.from(document.querySelectorAll('[data-lightbox]'));
    if (!links.length) return;

    const overlay = document.createElement('div');
    overlay.className = 'overlay';
    overlay.setAttribute('role', 'dialog');
    overlay.setAttribute('aria-modal', 'true');
    overlay.innerHTML = `
        <button type="button" data-close aria-label="Close" class="absolute right-4 top-4 flex h-11 w-11 items-center justify-center rounded-full bg-white/10 text-2xl text-white hover:bg-white/20">&times;</button>
        <button type="button" data-prev aria-label="Previous" class="absolute left-3 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-2xl text-white hover:bg-white/20">&#8249;</button>
        <button type="button" data-next aria-label="Next" class="absolute right-3 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-2xl text-white hover:bg-white/20">&#8250;</button>
        <figure class="flex max-h-full max-w-6xl flex-col items-center gap-3">
            <img alt="" class="max-h-[82vh] max-w-full rounded-2xl object-contain">
            <figcaption class="text-center text-sm text-white/80"></figcaption>
        </figure>`;
    document.body.appendChild(overlay);

    const img = overlay.querySelector('img');
    const caption = overlay.querySelector('figcaption');
    let group = [];
    let index = 0;

    const show = (i) => {
        index = (i + group.length) % group.length;
        const a = group[index];
        img.src = a.getAttribute('href');
        img.alt = a.dataset.caption || '';
        caption.textContent = a.dataset.caption || '';
        overlay.querySelectorAll('[data-prev],[data-next]').forEach((b) => b.classList.toggle('hidden', group.length < 2));
    };

    const close = () => {
        overlay.classList.remove('open');
        document.body.classList.remove('overflow-hidden');
        img.src = '';
    };

    links.forEach((a) => a.addEventListener('click', (e) => {
        e.preventDefault();
        group = links.filter((l) => l.dataset.lightbox === a.dataset.lightbox);
        overlay.classList.add('open');
        document.body.classList.add('overflow-hidden');
        show(group.indexOf(a));
    }));

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay || e.target.closest('[data-close]')) close();
        else if (e.target.closest('[data-prev]')) show(index - 1);
        else if (e.target.closest('[data-next]')) show(index + 1);
    });

    document.addEventListener('keydown', (e) => {
        if (!overlay.classList.contains('open')) return;
        if (e.key === 'Escape') close();
        if (e.key === 'ArrowLeft') show(index - 1);
        if (e.key === 'ArrowRight') show(index + 1);
    });
})();

// Video modal: <button data-video="https://embed..." data-type="youtube|vimeo|instagram|file" data-vertical="1">
(() => {
    const buttons = document.querySelectorAll('[data-video]');
    if (!buttons.length) return;

    const overlay = document.createElement('div');
    overlay.className = 'overlay';
    overlay.setAttribute('role', 'dialog');
    overlay.setAttribute('aria-modal', 'true');
    overlay.innerHTML = `
        <button type="button" data-close aria-label="Close" class="absolute right-4 top-4 flex h-11 w-11 items-center justify-center rounded-full bg-white/10 text-2xl text-white hover:bg-white/20">&times;</button>
        <div data-stage class="w-full max-w-5xl"></div>`;
    document.body.appendChild(overlay);
    const stage = overlay.querySelector('[data-stage]');

    const close = () => {
        overlay.classList.remove('open');
        document.body.classList.remove('overflow-hidden');
        stage.innerHTML = ''; // removing the player also stops the sound
    };

    buttons.forEach((b) => b.addEventListener('click', () => {
        const vertical = b.dataset.vertical === '1';
        const box = document.createElement('div');
        box.className = vertical
            ? 'mx-auto aspect-[9/16] max-h-[88vh] overflow-hidden rounded-2xl bg-black'
            : 'aspect-video w-full overflow-hidden rounded-2xl bg-black';

        if (b.dataset.type === 'file') {
            const v = document.createElement('video');
            v.src = b.dataset.video;
            v.controls = true;
            v.autoplay = true;
            v.playsInline = true;
            v.className = 'h-full w-full';
            box.appendChild(v);
        } else {
            const f = document.createElement('iframe');
            f.src = b.dataset.video;
            f.allow = 'autoplay; fullscreen; picture-in-picture; encrypted-media';
            f.allowFullscreen = true;
            f.className = 'h-full w-full border-0';
            box.appendChild(f);
        }

        stage.innerHTML = '';
        stage.appendChild(box);
        overlay.classList.add('open');
        document.body.classList.add('overflow-hidden');
    }));

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay || e.target.closest('[data-close]')) close();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && overlay.classList.contains('open')) close();
    });
})();

// Before / after slider
document.querySelectorAll('.ba').forEach((root) => {
    const range = root.querySelector('input[type=range]');
    if (!range) return;
    const set = () => root.style.setProperty('--pos', range.value + '%');
    range.addEventListener('input', set);
    set();
});

// Click feedback --------------------------------------------------------------------------

// Ripple from the pointer on buttons and on anything marked data-ripple.
(() => {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    document.addEventListener('pointerdown', (e) => {
        if (e.button !== 0) return;

        const el = e.target.closest('.btn, .btn-primary, .btn-dark, .btn-ghost, .btn-light, [data-ripple]');
        if (!el || el.disabled) return;

        const rect = el.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height) * 2.2;
        const dot = document.createElement('span');

        dot.className = 'ripple';
        dot.style.width = dot.style.height = size + 'px';
        dot.style.left = (e.clientX - rect.left - size / 2) + 'px';
        dot.style.top = (e.clientY - rect.top - size / 2) + 'px';

        if (getComputedStyle(el).position === 'static') el.style.position = 'relative';
        el.style.overflow = 'hidden';
        el.appendChild(dot);
        dot.addEventListener('animationend', () => dot.remove());
        setTimeout(() => dot.remove(), 900); // safety net if the animation never reports back (hidden tab)
    });
})();

// Submit buttons: spinner + disabled while the form is being sent (stops double submits).
document.addEventListener('submit', (e) => {
    const form = e.target;

    // A confirm() on the form (delete buttons) or validation may have cancelled it already.
    if (e.defaultPrevented || form.hasAttribute('data-no-loading')) return;

    const button = form.querySelector('button[type=submit], button:not([type])');
    if (!button || button.dataset.loading === '1') return;

    button.dataset.loading = '1';

    // Disable on the next tick so the browser still submits the form normally.
    setTimeout(() => {
        const spinner = document.createElement('span');
        spinner.className = 'spinner';
        button.prepend(spinner);
        button.classList.add('opacity-80', 'pointer-events-none');
        button.setAttribute('aria-busy', 'true');
    }, 0);

    // If the page does not change (e.g. the browser blocks the request), allow another try.
    setTimeout(() => {
        button.dataset.loading = '';
        button.classList.remove('opacity-80', 'pointer-events-none');
        button.removeAttribute('aria-busy');
        button.querySelector('.spinner')?.remove();
    }, 15000);
});

// Thin progress bar while the next page loads.
(() => {
    const bar = document.createElement('div');
    bar.id = 'nav-progress';
    document.body.appendChild(bar);

    const reset = () => {
        bar.classList.remove('run');
        bar.style.transition = 'none';
        bar.style.width = '0';
        bar.offsetWidth; // restart the transition next time
        bar.style.transition = '';
    };

    window.addEventListener('pageshow', reset);

    document.addEventListener('click', (e) => {
        if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

        const a = e.target.closest('a[href]');
        if (!a || a.target === '_blank' || a.hasAttribute('download') || a.hasAttribute('data-lightbox')) return;

        const url = new URL(a.href, location.href);
        if (url.origin !== location.origin || (url.pathname === location.pathname && url.search === location.search)) return;

        bar.classList.add('run');
    });

    document.addEventListener('submit', (e) => {
        if (!e.defaultPrevented) bar.classList.add('run');
    });
})();

// Header menu panels ----------------------------------------------------------------------
// Hover (or click) a menu item: the page dims and a panel with that item's content opens.
// Hover opens it for a moment; a click pins it open until you click outside, press Esc or click the item again.
(() => {
    const triggers = Array.from(document.querySelectorAll('[data-mega]'));
    const overlay = document.getElementById('mega-overlay');
    if (!triggers.length || !overlay) return;

    const panels = {};
    document.querySelectorAll('[data-panel]').forEach((p) => { panels[p.dataset.panel] = p; });

    const desktop = window.matchMedia('(min-width: 768px)');
    let current = null;
    let pinned = false;
    let openTimer;
    let closeTimer;

    const show = (name) => {
        if (!panels[name]) return;
        clearTimeout(closeTimer);
        current = name;

        Object.entries(panels).forEach(([key, el]) => el.classList.toggle('open', key === name));
        triggers.forEach((t) => t.setAttribute('aria-expanded', t.dataset.mega === name ? 'true' : 'false'));
        overlay.classList.add('open');
    };

    const hide = () => {
        clearTimeout(openTimer);
        clearTimeout(closeTimer);
        current = null;
        pinned = false;

        Object.values(panels).forEach((el) => el.classList.remove('open'));
        triggers.forEach((t) => t.setAttribute('aria-expanded', 'false'));
        overlay.classList.remove('open');
    };

    const scheduleHide = () => {
        if (pinned) return;
        clearTimeout(openTimer);
        clearTimeout(closeTimer);
        closeTimer = setTimeout(hide, 160); // lets the pointer travel from the item down into the panel
    };

    triggers.forEach((t) => {
        t.addEventListener('mouseenter', () => {
            if (!desktop.matches || pinned) return;
            clearTimeout(closeTimer);
            clearTimeout(openTimer);
            openTimer = setTimeout(() => show(t.dataset.mega), current ? 0 : 90);
        });

        t.addEventListener('mouseleave', scheduleHide);

        t.addEventListener('click', (e) => {
            if (!desktop.matches) return; // on phones the item is a normal link
            e.preventDefault();

            if (current === t.dataset.mega && pinned) {
                hide();
            } else {
                pinned = true;
                show(t.dataset.mega);
            }
        });
    });

    Object.values(panels).forEach((el) => {
        el.addEventListener('mouseenter', () => clearTimeout(closeTimer));
        el.addEventListener('mouseleave', scheduleHide);
        el.addEventListener('click', (e) => { if (e.target.closest('a')) hide(); });
    });

    overlay.addEventListener('click', hide);
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && current) hide(); });
    desktop.addEventListener('change', hide);
    window.addEventListener('pageshow', hide);
})();
