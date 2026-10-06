<!DOCTYPE html>
@php
    use App\Models\Setting;
    use App\Support\WhatsApp;

    $siteName = Setting::get('site_name', config('app.name'));
    $tagline = Setting::t('tagline', __('site.default_tagline'));
    $pageTitle = trim($__env->yieldContent('title'));
    $fullTitle = $pageTitle ? $pageTitle . ' — ' . $siteName : $siteName . ' — ' . $tagline;
    $metaDescription = trim($__env->yieldContent('meta_description')) ?: $tagline;
    $metaImage = trim($__env->yieldContent('og_image')) ?: Setting::image('hero_photo_1') ?: Setting::image('logo');
    // The studio's own wordmark ships with the site; a logo uploaded in the admin replaces it.
    $logo = Setting::image('logo') ?: asset('images/logo.png');
    $whatsappUrl = WhatsApp::chatUrl();
    $navItems = [
        ['works.index', 'works*', 'site.nav.works', 'works'],
        ['services', 'services', 'site.nav.services', 'services'],
        ['process', 'process', 'site.nav.process', 'process'],
        ['space', 'space', 'site.nav.space', 'space'],
        ['thoughts.index', 'thoughts*', 'site.nav.thoughts', 'thoughts'],
    ];
@endphp
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $fullTitle }}</title>
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($metaDescription), 180) }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $fullTitle }}">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($metaDescription), 180) }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($metaImage)
        <meta property="og:image" content="{{ $metaImage }}">
        <meta name="twitter:card" content="summary_large_image">
    @endif
    <meta name="theme-color" content="#2e59bf">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-white text-ink antialiased">

    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[200] focus:rounded-full focus:bg-ink focus:px-4 focus:py-2 focus:text-white">Skip to content</a>

    {{-- Dims the page while a menu panel is open --}}
    <div id="mega-overlay" class="pointer-events-none fixed inset-0 z-40 bg-ink/45 opacity-0 backdrop-blur-[2px] transition-opacity duration-200" aria-hidden="true"></div>

    {{-- Header --}}
    <header id="site-header" data-over-hero="{{ request()->routeIs('home', 'about', 'works.index', 'services', 'process', 'space', 'thoughts.index', 'contact', 'privacy', 'terms') ? 'true' : 'false' }}"
            class="group/header sticky top-0 z-50 border-b border-line bg-white/85 backdrop-blur-md transition-all duration-300 data-[over-hero=true]:border-white/15 data-[over-hero=true]:bg-transparent data-[over-hero=true]:backdrop-blur-none">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-6 px-5 sm:px-8">
            <a href="{{ route('home') }}" class="group flex shrink-0 items-center" aria-label="{{ $siteName }}">
                @if($logo)
                    <img src="{{ $logo }}" alt="{{ $siteName }}" class="h-7 w-auto transition-all duration-300 group-hover:scale-105 group-data-[over-hero=true]/header:brightness-0 group-data-[over-hero=true]/header:invert sm:h-8">
                @else
                    <span class="display inline-flex text-[1.7rem] transition-transform duration-200 group-active:scale-95"><span class="logo-word">{{ $siteName }}</span><span class="logo-dot">.</span></span>
                @endif
            </a>

            <nav class="hidden items-center gap-1 md:flex" aria-label="Main">
                @foreach($navItems as [$route, $pattern, $label, $key])
                    <a href="{{ route($route) }}" data-mega="{{ $key }}" aria-haspopup="true" aria-expanded="false"
                       class="group/trigger inline-flex items-center gap-1 rounded-full px-3 py-2 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-brand-200 lg:px-4 text-sm font-medium transition-all duration-200 active:scale-95 aria-expanded:bg-brand-500 aria-expanded:text-white group-data-[over-hero=true]/header:text-white/90 group-data-[over-hero=true]/header:hover:bg-white/15 group-data-[over-hero=true]/header:hover:text-white group-data-[over-hero=true]/header:aria-expanded:bg-white group-data-[over-hero=true]/header:aria-expanded:text-brand-600 {{ request()->routeIs($pattern) ? 'bg-brand-50 text-brand-600 group-data-[over-hero=true]/header:bg-white/15' : 'text-ink/70 hover:bg-soft hover:text-ink' }}">{{ __($label) }}<x-icon name="chevron" class="h-3.5 w-3.5 opacity-60 transition-transform duration-200 group-aria-expanded/trigger:rotate-180 group-aria-expanded/trigger:opacity-100" stroke="2.4" /></a>
                @endforeach
                <a href="{{ route('about') }}"
                   class="inline-flex items-center rounded-full px-3 py-2 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-brand-200 lg:px-4 text-sm font-medium transition-all duration-200 active:scale-95 group-data-[over-hero=true]/header:text-white/90 group-data-[over-hero=true]/header:hover:bg-white/15 group-data-[over-hero=true]/header:hover:text-white {{ request()->routeIs('about') ? 'bg-brand-50 text-brand-600 group-data-[over-hero=true]/header:bg-white/15' : 'text-ink/70 hover:bg-soft hover:text-ink' }}">{{ __('site.nav.about') }}</a>
            </nav>

            <div class="flex items-center gap-3">
                <div class="hidden items-center gap-1 text-xs font-semibold sm:flex" aria-label="{{ __('site.footer.language') }}">
                    <a href="{{ route('locale.switch', 'id') }}" class="rounded-full px-2 py-1 transition-colors {{ app()->getLocale() === 'id' ? 'bg-ink text-white group-data-[over-hero=true]/header:bg-white group-data-[over-hero=true]/header:text-brand-600' : 'text-muted hover:text-ink group-data-[over-hero=true]/header:text-white/80 group-data-[over-hero=true]/header:hover:text-white' }}">ID</a>
                    <a href="{{ route('locale.switch', 'en') }}" class="rounded-full px-2 py-1 transition-colors {{ app()->getLocale() === 'en' ? 'bg-ink text-white group-data-[over-hero=true]/header:bg-white group-data-[over-hero=true]/header:text-brand-600' : 'text-muted hover:text-ink group-data-[over-hero=true]/header:text-white/80 group-data-[over-hero=true]/header:hover:text-white' }}">EN</a>
                </div>

                @if($phone = \App\Support\WhatsApp::normalize(Setting::get('whatsapp')))
                    <a href="tel:+{{ $phone }}" class="hidden items-center gap-2 text-sm font-semibold text-ink transition-colors hover:text-brand-600 group-data-[over-hero=true]/header:text-white group-data-[over-hero=true]/header:hover:text-lime xl:inline-flex" aria-label="{{ __('site.footer.call') }}">
                        <x-icon name="phone" class="h-4 w-4" /> +{{ $phone }}
                    </a>
                @endif

                <a href="{{ route('contact') }}" class="btn-primary hidden !px-5 !py-2.5 group-data-[over-hero=true]/header:border group-data-[over-hero=true]/header:border-white/60 group-data-[over-hero=true]/header:!bg-transparent group-data-[over-hero=true]/header:hover:!bg-white/15 group-data-[over-hero=true]/header:hover:shadow-none sm:inline-flex">{{ __('site.nav.lets_talk') }}</a>

                <button id="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" aria-label="{{ __('site.nav.menu') }}"
                        class="flex h-10 w-10 items-center justify-center rounded-full border border-line text-ink transition-all hover:border-brand-300 active:scale-90 group-data-[over-hero=true]/header:border-white/40 group-data-[over-hero=true]/header:text-white md:hidden">
                    <x-icon name="menu" class="h-5 w-5" />
                </button>
            </div>
        </div>

        @include('layouts._mega')

        <div id="mobile-menu" class="hidden border-t border-line bg-white md:hidden">
            @include('layouts._mobile-nav')
        </div>
    </header>

    <main id="main">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="mt-24 rounded-t-[2.5rem] bg-ink text-white">
        <div class="mx-auto max-w-7xl px-5 pb-8 pt-16 sm:px-8">
            @unless(request()->routeIs('contact'))
                <div id="footer-contact" class="mb-16 grid gap-8 rounded-[2rem] bg-white/5 p-6 sm:p-10 lg:grid-cols-12 lg:gap-12">
                    <div class="lg:col-span-4">
                        <h2 class="display text-4xl sm:text-5xl">{{ __('site.footer.form_title') }}</h2>
                        <p class="mt-3 text-sm text-white/60">{{ __('site.footer.form_text') }}</p>
                    </div>
                    <form method="POST" action="{{ route('contact.store') }}" class="space-y-4 lg:col-span-8">
                        @csrf
                        <div class="absolute -left-[9999px] h-0 w-0 overflow-hidden" aria-hidden="true">
                            <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                        </div>
                        @php $ff = 'w-full rounded-xl border border-white/15 bg-white/5 px-4 py-3 text-sm text-white placeholder-white/40 outline-none transition-colors focus:border-lime focus:bg-white/10'; @endphp
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="f-name" class="sr-only">{{ __('site.footer.form_name') }}</label>
                                <input id="f-name" name="name" value="{{ old('name') }}" required maxlength="120" autocomplete="name" placeholder="{{ __('site.footer.form_name') }}" class="{{ $ff }}">
                                @error('name')<p class="mt-1.5 text-xs text-red-300">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="f-email" class="sr-only">{{ __('site.footer.form_email') }}</label>
                                <input id="f-email" type="email" name="email" value="{{ old('email') }}" required maxlength="190" autocomplete="email" placeholder="{{ __('site.footer.form_email') }}" class="{{ $ff }}">
                                @error('email')<p class="mt-1.5 text-xs text-red-300">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div>
                            <label for="f-message" class="sr-only">{{ __('site.footer.form_message') }}</label>
                            <textarea id="f-message" name="message" rows="3" required minlength="10" maxlength="3000" placeholder="{{ __('site.footer.form_message') }}" class="{{ $ff }} resize-y">{{ old('message') }}</textarea>
                            @error('message')<p class="mt-1.5 text-xs text-red-300">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit" class="btn-light">{{ __('site.footer.form_send') }} <x-icon name="arrow" class="h-4 w-4" /></button>
                    </form>
                </div>
                @if($errors->any())
                    <script>document.getElementById('footer-contact')?.scrollIntoView({block: 'center'});</script>
                @endif
            @endunless

            <div class="grid gap-12 md:grid-cols-12">
                <div class="md:col-span-4">
                    @if($logo)
                        <img src="{{ $logo }}" alt="{{ $siteName }}" class="h-9 w-auto brightness-0 invert">
                    @else
                        <a href="{{ route('home') }}" class="group display inline-flex text-5xl" aria-label="{{ $siteName }}"><span class="logo-word on-dark">{{ $siteName }}</span><span class="logo-dot on-dark">.</span></a>
                    @endif
                    <p class="mt-4 max-w-sm text-sm leading-relaxed text-white/60">{{ Setting::t('footer_note', __('site.footer.default_note')) }}</p>
                    <div class="mt-6">
                        @include('partials.socials', ['tone' => 'dark'])
                    </div>
                </div>

                <div class="md:col-span-2 md:col-start-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/40">{{ __('site.footer.explore') }}</p>
                    <ul class="mt-4 space-y-2.5 text-sm">
                        @foreach($navItems as [$route, $pattern, $label])
                            <li><a href="{{ route($route) }}" class="text-white/75 transition-colors hover:text-white">{{ __($label) }}</a></li>
                        @endforeach
                        <li><a href="{{ route('about') }}" class="text-white/75 transition-colors hover:text-white">{{ __('site.nav.about') }}</a></li>
                        <li><a href="{{ route('contact') }}" class="text-white/75 transition-colors hover:text-white">{{ __('site.nav.contact') }}</a></li>
                    </ul>
                </div>

                @if($mega['services']->isNotEmpty())
                    <div class="md:col-span-3">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/40">{{ __('site.footer.services') }}</p>
                        <ul class="mt-4 space-y-2.5 text-sm">
                            @foreach($mega['services'] as $svc)
                                <li><a href="{{ route('services') }}#{{ $svc->slug }}" class="text-white/75 transition-colors hover:text-white">{{ $svc->t('title') }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="md:col-span-2">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-white/40">{{ __('site.footer.contact') }}</p>
                    <ul class="mt-4 space-y-2.5 text-sm text-white/75">
                        @if($email = Setting::get('email'))
                            <li><a href="mailto:{{ $email }}" class="transition-colors hover:text-white">{{ $email }}</a></li>
                        @endif
                        @if($whatsappUrl)
                            <li><a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" class="transition-colors hover:text-white">WhatsApp +{{ \App\Support\WhatsApp::normalize(Setting::get('whatsapp')) }}</a></li>
                        @endif
                        @if($address = Setting::t('address'))
                            <li class="text-white/60">{{ $address }}</li>
                        @endif
                        @if($hours = Setting::t('hours'))
                            <li class="text-white/60">{{ $hours }}</li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="mt-14 flex flex-col items-start justify-between gap-4 border-t border-white/10 pt-6 text-xs text-white/45 sm:flex-row sm:items-center">
                <p>&copy; {{ date('Y') }} {{ $siteName }}. {{ __('site.footer.rights') }}
                    <span class="ml-2 inline-flex gap-3">
                        <a href="{{ route('privacy') }}" class="underline-offset-4 transition-colors hover:text-white hover:underline">{{ __('site.footer.privacy') }}</a>
                        <a href="{{ route('terms') }}" class="underline-offset-4 transition-colors hover:text-white hover:underline">{{ __('site.footer.terms') }}</a>
                    </span>
                </p>
                <div class="flex items-center gap-1 font-semibold">
                    <a href="{{ route('locale.switch', 'id') }}" class="rounded-full px-2 py-1 {{ app()->getLocale() === 'id' ? 'bg-white text-ink' : 'hover:text-white' }}">ID</a>
                    <a href="{{ route('locale.switch', 'en') }}" class="rounded-full px-2 py-1 {{ app()->getLocale() === 'en' ? 'bg-white text-ink' : 'hover:text-white' }}">EN</a>
                </div>
            </div>
        </div>
    </footer>

    @if($whatsappUrl)
        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" aria-label="WhatsApp"
           class="fixed bottom-5 right-5 z-40 flex h-14 w-14 items-center justify-center rounded-full bg-[#25d366] text-white shadow-lg shadow-black/20 transition-all duration-200 hover:scale-110 hover:shadow-xl active:scale-90 print:hidden" data-ripple>
            <x-icon name="whatsapp" class="h-7 w-7" />
        </a>
    @endif

    @stack('scripts')
</body>
</html>
