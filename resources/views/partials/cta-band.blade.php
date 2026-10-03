{{-- Big blue call-to-action block. Optional $title and $text. --}}
@php $wa = \App\Support\WhatsApp::chatUrl(); @endphp
<section class="mx-auto mt-24 max-w-7xl px-5 sm:px-8">
    <div class="reveal relative overflow-hidden rounded-[2rem] bg-brand-500 px-7 py-14 text-white sm:px-14 sm:py-20">
        <div class="pointer-events-none absolute -right-24 -top-24 h-80 w-80 rounded-full bg-brand-400/40 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-32 -left-16 h-80 w-80 rounded-full bg-brand-700/60 blur-3xl"></div>

        <div class="relative max-w-3xl">
            <h2 class="display text-5xl sm:text-7xl">{{ $title ?? __('site.home.cta_title') }}</h2>
            <p class="mt-5 max-w-xl text-lg text-white/80">{{ $text ?? __('site.home.cta_text') }}</p>
            <div class="mt-9 flex flex-wrap gap-3">
                <a href="{{ route('contact') }}" class="btn-light">{{ __('site.hero.cta_primary') }} <x-icon name="arrow" class="h-4 w-4" /></a>
                @if($wa)
                    <a href="{{ $wa }}" target="_blank" rel="noopener" class="btn border border-white/30 text-white hover:bg-white/10"><x-icon name="whatsapp" class="h-4 w-4" /> WhatsApp</a>
                @endif
            </div>
        </div>
    </div>
</section>
