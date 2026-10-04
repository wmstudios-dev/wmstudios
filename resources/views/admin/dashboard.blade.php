@extends('admin.layout')

@section('title', 'Overview')

@section('content')
<h1 class="display text-5xl">Overview</h1>
<p class="mt-1 text-sm text-muted">Welcome back, {{ auth()->user()->name }}.</p>

@unless($imageOptimization)
    <div class="mt-6 rounded-2xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
        <p class="font-semibold">Photo optimisation is off</p>
        <p class="mt-0.5">This server has no PHP <code class="rounded bg-amber-100 px-1">gd</code> extension, so uploaded photos are saved exactly as they are (not resized or converted to WebP). Big photos will make pages load slowly. Resize photos to about 1920 px wide before uploading until this is fixed.</p>
    </div>
@endunless

<div class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
    @foreach([
        ['New messages', $stats['new_messages'], route('admin.messages.index', ['status' => 'new']), $stats['new_messages'] > 0],
        ['All messages', $stats['messages'], route('admin.messages.index'), false],
        ['Works', $stats['works'], route('admin.resource.index', 'works'), false],
        ['Articles', $stats['thoughts'], route('admin.resource.index', 'thoughts'), false],
    ] as [$label, $value, $href, $hot])
        <a href="{{ $href }}" class="rounded-3xl border p-5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md {{ $hot ? 'border-brand-500 bg-brand-500 text-white' : 'border-line bg-white' }}">
            <p class="text-xs font-semibold uppercase tracking-wider {{ $hot ? 'text-white/70' : 'text-muted' }}">{{ $label }}</p>
            <p class="display mt-2 text-5xl">{{ $value }}</p>
        </a>
    @endforeach
</div>

<div class="mt-8 grid gap-6 lg:grid-cols-2">
    <section class="rounded-3xl border border-line bg-white p-6">
        <div class="flex items-center justify-between">
            <h2 class="font-bold">Latest messages</h2>
            <a href="{{ route('admin.messages.index') }}" class="text-sm font-medium text-brand-600 hover:underline">View all</a>
        </div>
        <ul class="mt-4 divide-y divide-line">
            @forelse($recentMessages as $m)
                <li>
                    <a href="{{ route('admin.messages.show', $m) }}" class="flex items-start justify-between gap-3 py-3 transition-colors hover:text-brand-600">
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-semibold">{{ $m->name }} @if($m->status === 'new')<span class="ml-1 rounded-full bg-brand-500 px-1.5 py-0.5 align-middle text-[10px] font-bold text-white">NEW</span>@endif</span>
                            <span class="block truncate text-xs text-muted">{{ \Illuminate\Support\Str::limit($m->message, 70) }}</span>
                        </span>
                        <span class="shrink-0 text-xs text-muted">{{ $m->created_at->diffForHumans(null, true) }}</span>
                    </a>
                </li>
            @empty
                <li class="py-6 text-sm text-muted">No messages yet.</li>
            @endforelse
        </ul>
    </section>

    <section class="rounded-3xl border border-line bg-white p-6">
        <h2 class="font-bold">Quick actions</h2>
        <div class="mt-4 grid gap-2 sm:grid-cols-2">
            @foreach([
                ['Add a work', route('admin.resource.create', 'works')],
                ['Write an article', route('admin.resource.create', 'thoughts')],
                ['Add a Space photo', route('admin.resource.create', 'space')],
                ['Add a client logo', route('admin.resource.create', 'clients')],
                ['Edit services', route('admin.resource.index', 'services')],
                ['Site settings', route('admin.settings.edit')],
            ] as [$label, $href])
                <a href="{{ $href }}" class="rounded-2xl border border-line px-4 py-3 text-sm font-semibold transition-all duration-200 hover:border-brand-300 hover:bg-brand-50 hover:text-brand-700">{{ $label }} &rarr;</a>
            @endforeach
        </div>

        <h2 class="mt-8 font-bold">Recent works</h2>
        <ul class="mt-3 space-y-2 text-sm">
            @forelse($recentWorks as $w)
                <li><a href="{{ route('admin.resource.edit', ['works', $w->id]) }}" class="text-brand-600 hover:underline">{{ $w->title_id }}</a> <span class="text-muted">· {{ ucfirst($w->category) }}</span></li>
            @empty
                <li class="text-muted">No works yet.</li>
            @endforelse
        </ul>
    </section>
</div>
@endsection
