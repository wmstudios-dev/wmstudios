@extends('admin.layout')

@section('title', 'Messages')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
    <h1 class="display text-5xl">Messages</h1>
    <a href="{{ route('admin.messages.export') }}" class="btn-ghost !py-2.5">Export CSV</a>
</div>

<form method="GET" class="mt-5 flex flex-wrap gap-2">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Search name, email or message..." class="field !w-auto min-w-64 flex-1 !py-2.5">
    <select name="status" onchange="this.form.submit()" class="field !w-auto !py-2.5">
        <option value="">All statuses</option>
        @foreach(\App\Models\ContactMessage::STATUSES as $s)
            <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
        @endforeach
    </select>
    <button class="btn-dark !py-2.5">Search</button>
</form>

<div class="mt-5 overflow-hidden rounded-3xl border border-line bg-white">
    <ul class="divide-y divide-line">
        @forelse($messages as $m)
            <li>
                <a href="{{ route('admin.messages.show', $m) }}" class="flex items-start gap-4 px-5 py-4 transition-colors hover:bg-soft/60">
                    <span class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full {{ $m->status === 'new' ? 'bg-brand-500' : 'bg-line' }}"></span>
                    <span class="min-w-0 flex-1">
                        <span class="flex flex-wrap items-center gap-x-3 text-sm">
                            <span class="font-semibold {{ $m->status === 'new' ? 'text-ink' : 'text-ink/70' }}">{{ $m->name }}</span>
                            <span class="text-xs text-muted">{{ $m->email }}</span>
                            @if($m->service)<span class="chip bg-brand-50 text-brand-700">{{ $m->service }}</span>@endif
                        </span>
                        <span class="mt-1 block truncate text-sm text-muted">{{ $m->message }}</span>
                    </span>
                    <span class="shrink-0 text-right text-xs text-muted">
                        {{ $m->created_at->format('d M Y') }}<br>{{ $m->created_at->format('H:i') }}
                        @if($m->status === 'replied')<br><span class="font-semibold text-green-700">replied</span>@endif
                    </span>
                </a>
            </li>
        @empty
            <li class="px-5 py-14 text-center text-muted">No messages yet.</li>
        @endforelse
    </ul>
</div>

{{ $messages->links() }}
@endsection
