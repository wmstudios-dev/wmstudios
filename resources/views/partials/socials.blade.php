{{-- Social icons for every network that has a link in the site settings. $tone: 'dark' (on dark bg) or 'light'. --}}
@php
    $links = collect(\App\Support\SettingsSchema::socials())
        ->mapWithKeys(fn ($network) => [$network => \App\Models\Setting::get($network)])
        ->filter();
    $dark = ($tone ?? 'light') === 'dark';
@endphp
@if($links->isNotEmpty())
    <div class="flex flex-wrap items-center gap-2">
        @foreach($links as $network => $url)
            <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($network) }}" title="{{ ucfirst($network) }}"
               class="flex h-10 w-10 items-center justify-center rounded-full border transition-all duration-200 hover:-translate-y-0.5 active:scale-90
                      {{ $dark ? 'border-white/15 text-white/80 hover:border-white hover:bg-white hover:text-ink' : 'border-line text-ink hover:border-brand-500 hover:bg-brand-500 hover:text-white' }}">
                <x-icon :name="$network" class="h-[18px] w-[18px]" />
            </a>
        @endforeach
    </div>
@endif
