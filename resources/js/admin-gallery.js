// Admin gallery editor: tiles are grouped by type, can be dragged to re-order, and dropping a tile into another group
// changes its type. The form posts the photo ids in DOM order (_order[]) and each photo's group (_kind[id]).
import Sortable from 'sortablejs';

document.querySelectorAll('[data-gallery]').forEach((root) => {
    const field = root.dataset.field;
    const groups = [...root.querySelectorAll('[data-group]')];
    const coverInput = root.querySelector('[data-cover-input]');

    const sync = () => {
        groups.forEach((group) => {
            const list = group.querySelector('[data-list]');
            const tiles = list.querySelectorAll('[data-tile]');

            group.querySelector('[data-count]').textContent = tiles.length;
            group.classList.toggle('is-empty', tiles.length === 0);

            tiles.forEach((tile) => {
                const kind = tile.querySelector('[data-kind-input]');
                if (kind) kind.value = group.dataset.kind;

                const move = tile.querySelector('[data-move]');
                if (move) move.value = group.dataset.kind;
            });
        });
    };

    groups.forEach((group) => {
        new Sortable(group.querySelector('[data-list]'), {
            group: field,
            animation: 160,
            ghostClass: 'gal-ghost',
            chosenClass: 'gal-chosen',
            filter: 'input, button, select, label',
            preventOnFilter: false,
            delay: 120,
            delayOnTouchOnly: true,
            onSort: sync,
        });
    });

    // The "Move to" select does the same as dragging, for keyboards and phones.
    root.addEventListener('change', (event) => {
        const select = event.target.closest('[data-move]');
        if (!select) return;

        const target = groups.find((g) => g.dataset.kind === select.value);
        if (target) {
            target.querySelector('[data-list]').appendChild(select.closest('[data-tile]'));
            sync();
        }
    });

    root.addEventListener('click', (event) => {
        // mark / unmark a photo as the work's main photo (applied when the form is saved)
        const coverBtn = event.target.closest('[data-make-cover]');
        if (coverBtn && coverInput) {
            const id = coverBtn.dataset.makeCover;
            const on = coverInput.value !== id;
            coverInput.value = on ? id : '';

            root.querySelectorAll('[data-make-cover]').forEach((b) => {
                const active = on && b.dataset.makeCover === id;
                b.setAttribute('aria-pressed', active ? 'true' : 'false');
                b.classList.toggle('is-cover', active);
                b.textContent = active ? 'Will be main photo' : 'Make main photo';
            });
        }
    });

    // Tiles ticked for removal fade out
    root.addEventListener('change', (event) => {
        const box = event.target.closest('[data-remove]');
        if (box) box.closest('[data-tile]').classList.toggle('is-removing', box.checked);
    });

    sync();
});
