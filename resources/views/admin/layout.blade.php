<!DOCTYPE html>
@php
    $siteName = \App\Models\Setting::get('site_name', config('app.name'));
    $newMessages = \App\Models\ContactMessage::where('status', 'new')->count();
    $content = collect(\App\Support\AdminResources::all())->map(fn ($c, $key) => ['key' => $key, 'label' => $c['nav']]);
@endphp
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Admin') — {{ $siteName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-soft text-ink antialiased">
<div class="flex min-h-screen flex-col lg:flex-row">

    <aside class="shrink-0 border-b border-line bg-white lg:sticky lg:top-0 lg:h-screen lg:w-64 lg:overflow-y-auto lg:border-b-0 lg:border-r">
        <div class="flex items-center justify-between px-5 py-4 lg:block lg:py-6">
            <a href="{{ route('admin.dashboard') }}" class="display text-3xl">{{ $siteName }}<span class="text-brand-500">.</span> <span class="ml-1 align-middle text-[0.65rem] font-sans font-semibold normal-case tracking-wider text-muted">ADMIN</span></a>
            <a href="{{ route('home') }}" target="_blank" class="text-xs font-medium text-brand-600 hover:underline lg:mt-2 lg:block">View site &nearr;</a>
        </div>

        <nav class="flex gap-1 overflow-x-auto px-3 pb-3 lg:block lg:space-y-0.5 lg:px-3 lg:pb-6">
            @php
                $link = fn ($href, $label, $active, $badge = null) => '<a href="' . $href . '" class="flex shrink-0 items-center justify-between gap-2 rounded-xl px-3 py-2 text-sm font-medium transition-colors whitespace-nowrap '
                    . ($active ? 'bg-brand-50 text-brand-700' : 'text-ink/70 hover:bg-soft hover:text-ink') . '">' . e($label)
                    . ($badge ? '<span class="rounded-full bg-brand-500 px-1.5 py-0.5 text-[10px] font-bold leading-none text-white">' . $badge . '</span>' : '') . '</a>';
            @endphp

            {!! $link(route('admin.dashboard'), 'Overview', request()->routeIs('admin.dashboard')) !!}
            {!! $link(route('admin.messages.index'), 'Messages', request()->routeIs('admin.messages.*'), $newMessages ?: null) !!}

            <p class="hidden px-3 pb-1 pt-4 text-[0.65rem] font-semibold uppercase tracking-[0.18em] text-muted lg:block">Content</p>
            @foreach($content as $item)
                {!! $link(route('admin.resource.index', $item['key']), $item['label'], request()->route('resource') === $item['key']) !!}
            @endforeach

            <p class="hidden px-3 pb-1 pt-4 text-[0.65rem] font-semibold uppercase tracking-[0.18em] text-muted lg:block">Site</p>
            {!! $link(route('admin.settings.edit'), 'Site settings', request()->routeIs('admin.settings.*')) !!}
        </nav>

        <form method="POST" action="{{ route('admin.logout') }}" class="hidden border-t border-line p-3 lg:block">
            @csrf
            <button type="submit" class="w-full rounded-xl px-3 py-2 text-left text-sm font-medium text-red-600 transition-colors hover:bg-red-50">Log out ({{ auth()->user()->name }})</button>
        </form>
    </aside>

    <div class="min-w-0 flex-1">
        <main class="mx-auto max-w-5xl px-5 py-8 sm:px-8">
            @if(session('success'))
                <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <p class="font-semibold">Please check the form:</p>
                    <ul class="mt-1 list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            @yield('content')

            <form method="POST" action="{{ route('admin.logout') }}" class="mt-10 lg:hidden">
                @csrf
                <button type="submit" class="text-sm font-medium text-red-600">Log out</button>
            </form>
        </main>
    </div>
</div>
</body>
</html>
