{{-- One collaborating brand in the "Trusted by" row: its logo (white) or, without a logo, its name. Links to its website when it has one. --}}
@php $logo = $client->logoUrl(); @endphp
<a @if($client->url) href="{{ $client->url }}" target="_blank" rel="noopener" @endif
   @if(! empty($hidden)) aria-hidden="true" tabindex="-1" @endif
   title="{{ $client->name }}"
   class="flex shrink-0 items-center transition-all duration-200 {{ $client->url ? 'cursor-pointer hover:-translate-y-0.5 active:scale-95' : 'cursor-default' }}">
    @if($logo)
        <img src="{{ $logo }}" alt="{{ $client->name }}" loading="lazy" class="h-8 w-auto opacity-70 brightness-0 invert transition-opacity duration-200 hover:opacity-100">
    @else
        <span class="display whitespace-nowrap text-xl text-white/70 lg:text-2xl transition-colors duration-200 hover:text-white">{{ $client->name }}</span>
    @endif
</a>
