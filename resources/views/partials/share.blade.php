{{-- Share buttons: WhatsApp, copy link, and the phone's share sheet where supported. $title optional. --}}
@php
    $shareUrl = url()->current();
    $shareTitle = $title ?? \App\Models\Setting::get('site_name', config('app.name'));
@endphp
<div class="share-box flex flex-wrap items-center gap-2" data-url="{{ $shareUrl }}" data-title="{{ $shareTitle }}"
     data-copied="{{ __('site.thoughts.copied') }}" data-copy="{{ __('site.thoughts.copy_link') }}">
    <span class="mr-1 text-xs font-semibold uppercase tracking-wider text-muted">{{ __('site.thoughts.share') }}</span>

    <a href="https://wa.me/?text={{ rawurlencode($shareTitle . ' — ' . $shareUrl) }}" target="_blank" rel="noopener"
       class="btn-ghost !gap-1.5 !px-4 !py-2 !text-xs"><x-icon name="whatsapp" class="h-4 w-4" /> WhatsApp</a>

    <button type="button" class="share-copy btn-ghost !gap-1.5 !px-4 !py-2 !text-xs">
        <x-icon name="check" class="h-4 w-4" /> <span class="share-copy-label">{{ __('site.thoughts.copy_link') }}</span>
    </button>
</div>

@once
@push('scripts')
<script>
document.querySelectorAll('.share-box').forEach(function (box) {
    var btn = box.querySelector('.share-copy');
    var label = box.querySelector('.share-copy-label');

    btn.addEventListener('click', async function () {
        var ok = false;
        try {
            await navigator.clipboard.writeText(box.dataset.url);
            ok = true;
        } catch (e) {
            var t = document.createElement('textarea');
            t.value = box.dataset.url; t.style.position = 'fixed'; t.style.opacity = '0';
            document.body.appendChild(t); t.select();
            try { ok = document.execCommand('copy'); } catch (e2) {}
            t.remove();
        }
        if (ok) {
            label.textContent = box.dataset.copied;
            setTimeout(function () { label.textContent = box.dataset.copy; }, 2000);
        }
    });
});
</script>
@endpush
@endonce
