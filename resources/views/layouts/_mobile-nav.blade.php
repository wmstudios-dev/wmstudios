{{--
    Mobile menu: every item has a link plus a chevron that opens a short submenu, the phone version of the
    desktop mega menu. Needs $navItems and $mega (see layouts/app.blade.php). Toggle logic lives in resources/js/app.js.
--}}
@php
    $chip = 'rounded-full border border-line px-4 py-1.5 text-sm font-semibold text-ink transition-colors active:bg-brand-50';
    $more = 'mt-3 inline-flex items-center gap-2 text-sm font-semibold text-brand-600';
@endphp

<nav class="mx-auto flex max-h-[calc(100dvh-4rem)] max-w-7xl flex-col overflow-y-auto px-5 py-4" aria-label="Mobile">
    @foreach($navItems as [$route, $pattern, $label, $key])
        <div class="border-b border-line">
            <div class="flex items-center justify-between gap-2">
                <a href="{{ route($route) }}" class="display flex-1 py-4 text-3xl {{ request()->routeIs($pattern) ? 'text-brand-500' : 'text-ink' }}">{{ __($label) }}</a>
                <button type="button" data-sub-toggle aria-expanded="false" aria-controls="sub-{{ $key }}" aria-label="{{ __($label) }}"
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-line text-ink transition-all active:scale-90 aria-expanded:rotate-180 aria-expanded:border-brand-500 aria-expanded:bg-brand-500 aria-expanded:text-white">
                    <x-icon name="chevron" class="h-5 w-5" />
                </button>
            </div>

            <div id="sub-{{ $key }}" hidden class="pb-5">
                @switch($key)
                    @case('works')
                        <div class="flex flex-wrap gap-2">
                            @foreach($mega['categories'] as $cat)
                                <a href="{{ route('works.index', ['c' => $cat]) }}" class="{{ $chip }}">{{ __('site.categories.' . $cat) }}</a>
                            @endforeach
                        </div>
                        <a href="{{ route('works.index') }}" class="{{ $more }}">{{ __('site.mega.works_all') }}@if($mega['workCount']) ({{ $mega['workCount'] }})@endif <x-icon name="arrow" class="h-4 w-4" /></a>
                        @break

                    @case('services')
                        <ul class="space-y-1">
                            @foreach($mega['services'] as $service)
                                <li><a href="{{ route('services') }}#{{ $service->slug }}" class="flex items-center gap-3 rounded-xl px-2 py-2.5 text-sm font-semibold text-ink active:bg-brand-50">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-soft text-brand-500"><x-icon :name="$service->icon" class="h-4 w-4" /></span>
                                    {{ $service->t('title') }}
                                </a></li>
                            @endforeach
                        </ul>
                        <a href="{{ route('services') }}" class="{{ $more }}">{{ __('site.home.services_all') }} <x-icon name="arrow" class="h-4 w-4" /></a>
                        @break

                    @case('process')
                        <ol class="space-y-1">
                            @foreach($mega['steps'] as $step)
                                <li><a href="{{ route('process') }}" class="flex items-center gap-3 rounded-xl px-2 py-2.5 text-sm font-semibold text-ink active:bg-brand-50">
                                    <span class="display w-8 text-2xl text-brand-500">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    {{ $step->t('title') }}
                                </a></li>
                            @endforeach
                        </ol>
                        <a href="{{ route('process') }}" class="{{ $more }}">{{ __('site.home.process_all') }} <x-icon name="arrow" class="h-4 w-4" /></a>
                        @break

                    @case('space')
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('space') }}" class="{{ $chip }}">{{ __('site.categories.all') }}</a>
                            @foreach($mega['spaceTags'] as $tag)
                                <a href="{{ route('space', ['tag' => $tag]) }}" class="{{ $chip }}">{{ __('site.space.tags.' . $tag) }}</a>
                            @endforeach
                        </div>
                        <a href="{{ route('space') }}" class="{{ $more }}">{{ __('site.home.space_all') }} <x-icon name="arrow" class="h-4 w-4" /></a>
                        @break

                    @case('thoughts')
                        @if($mega['thoughts']->isNotEmpty())
                            <ul class="space-y-1">
                                @foreach($mega['thoughts'] as $thought)
                                    <li><a href="{{ route('thoughts.show', $thought) }}" class="block rounded-xl px-2 py-2.5 active:bg-brand-50">
                                        <span class="block text-xs text-muted">{{ $thought->published_at?->locale(app()->getLocale())->translatedFormat('d F Y') }}</span>
                                        <span class="block text-sm font-semibold text-ink">{{ $thought->t('title') }}</span>
                                    </a></li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-muted">{{ __('site.thoughts.empty') }}</p>
                        @endif
                        <a href="{{ route('thoughts.index') }}" class="{{ $more }}">{{ __('site.home.thoughts_all') }} <x-icon name="arrow" class="h-4 w-4" /></a>
                        @break
                @endswitch
            </div>
        </div>
    @endforeach

    <a href="{{ route('contact') }}" class="display py-4 text-3xl text-brand-500">{{ __('site.nav.contact') }}</a>

    <div class="mt-2 flex items-center gap-2 text-sm font-semibold">
        <a href="{{ route('locale.switch', 'id') }}" class="rounded-full px-3 py-1.5 {{ app()->getLocale() === 'id' ? 'bg-ink text-white' : 'border border-line text-muted' }}">Indonesia</a>
        <a href="{{ route('locale.switch', 'en') }}" class="rounded-full px-3 py-1.5 {{ app()->getLocale() === 'en' ? 'bg-ink text-white' : 'border border-line text-muted' }}">English</a>
    </div>
</nav>
