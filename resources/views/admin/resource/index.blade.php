@extends('admin.layout')

@section('title', $cfg['label'])

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
    <h1 class="display text-5xl">{{ $cfg['label'] }}</h1>
    <a href="{{ route('admin.resource.create', $resource) }}" class="btn-primary !py-2.5">+ Add {{ $cfg['singular'] }}</a>
</div>

@if(! empty($cfg['search']))
    <form method="GET" class="mt-5 flex gap-2">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search..." class="field !py-2.5">
        <button class="btn-dark !py-2.5">Search</button>
        @if(request('q'))<a href="{{ route('admin.resource.index', $resource) }}" class="btn-ghost !py-2.5">Reset</a>@endif
    </form>
@endif

<div class="mt-5 overflow-hidden rounded-3xl border border-line bg-white">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-soft text-xs uppercase tracking-wider text-muted">
                <tr>
                    @foreach($cfg['columns'] as $col)
                        <th class="px-5 py-3 font-semibold">{{ $col['label'] }}</th>
                    @endforeach
                    <th class="px-5 py-3 text-right font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse($items as $item)
                    <tr class="transition-colors hover:bg-soft/60">
                        @foreach($cfg['columns'] as $col)
                            @php $value = $item->{$col['field']}; @endphp
                            <td class="px-5 py-3 align-middle">
                                @switch($col['type'])
                                    @case('image')
                                        @if($value)
                                            <img src="{{ \App\Support\ImageUploader::url($value, true) }}" alt="" class="h-12 w-12 rounded-xl bg-soft object-cover">
                                        @else
                                            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-soft text-muted/50"><x-icon name="image" class="h-5 w-5" /></span>
                                        @endif
                                        @break
                                    @case('title')
                                        <a href="{{ route('admin.resource.edit', [$resource, $item->id]) }}" class="font-semibold text-ink hover:text-brand-600">{{ \Illuminate\Support\Str::limit($value, 60) }}</a>
                                        @if(! empty($col['sub']) && $item->{$col['sub']})
                                            <p class="mt-0.5 text-xs text-muted">{{ \Illuminate\Support\Str::limit($item->{$col['sub']}, 70) }}</p>
                                        @endif
                                        @break
                                    @case('badge')
                                        <span class="chip bg-brand-50 text-brand-700">{{ ucfirst((string) $value) }}</span>
                                        @break
                                    @case('bool')
                                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full {{ $value ? 'bg-green-100 text-green-700' : 'bg-soft text-muted/60' }}">
                                            @if($value)<x-icon name="check" class="h-3.5 w-3.5" stroke="2.6" />@else&ndash;@endif
                                        </span>
                                        @break
                                    @case('date')
                                        <span class="text-muted">{{ $value ? $value->format('d M Y') : '—' }}</span>
                                        @break
                                    @default
                                        <span class="text-muted">{{ \Illuminate\Support\Str::limit((string) $value, 60) ?: '—' }}</span>
                                @endswitch
                            </td>
                        @endforeach
                        <td class="whitespace-nowrap px-5 py-3 text-right">
                            @if(! empty($cfg['view_route']) && isset($item->slug))
                                <a href="{{ route($cfg['view_route'], $item) }}" target="_blank" class="mr-3 text-xs font-semibold text-muted hover:text-brand-600">View</a>
                            @endif
                            <a href="{{ route('admin.resource.edit', [$resource, $item->id]) }}" class="mr-3 text-xs font-semibold text-brand-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.resource.destroy', [$resource, $item->id]) }}" class="inline"
                                  onsubmit="return confirm('Delete this {{ $cfg['singular'] }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button class="text-xs font-semibold text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($cfg['columns']) + 1 }}" class="px-5 py-14 text-center text-muted">Nothing here yet. <a href="{{ route('admin.resource.create', $resource) }}" class="font-semibold text-brand-600 hover:underline">Add the first {{ $cfg['singular'] }}</a>.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $items->links() }}
@endsection
