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
                entry.target.classList.add('in');
                io.unobserve(entry.target);
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
