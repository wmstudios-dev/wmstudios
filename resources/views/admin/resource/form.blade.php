@extends('admin.layout')

@php
    $editing = $item->exists;
    $title = ($editing ? 'Edit ' : 'Add ') . $cfg['singular'];
    $old = session()->hasOldInput();

    // Value of a field column: what the user typed if the form came back with errors, otherwise the stored value.
    $val = fn (string $column) => $old ? old($column) : $item->{$column};
@endphp

@section('title', $title)

@section('content')
<a href="{{ route('admin.resource.index', $resource) }}" class="text-sm font-medium text-muted hover:text-brand-600">&larr; {{ $cfg['label'] }}</a>
<h1 class="display mt-2 text-5xl">{{ $title }}</h1>

<form method="POST" enctype="multipart/form-data"
      action="{{ $editing ? route('admin.resource.update', [$resource, $item->id]) : route('admin.resource.store', $resource) }}"
      class="mt-6 space-y-6 rounded-3xl border border-line bg-white p-6 sm:p-8">
    @csrf
    @if($editing) @method('PUT') @endif

    @foreach($cfg['fields'] as $f)
        @php
            $name = $f['name'];
            $label = $f['label'] . (! empty($f['required']) ? ' *' : '');
            $columns = ! empty($f['bilingual'])
                ? [$name . '_id' => 'Indonesian', $name . '_en' => 'English']
                : [$name => null];
            $inputClass = 'field';
        @endphp

        @if($f['type'] === 'checkbox')
            <label class="flex cursor-pointer items-center gap-3 text-sm font-semibold">
                <input type="checkbox" name="{{ $name }}" value="1" @checked((bool) $val($name)) class="h-5 w-5 rounded border-line text-brand-500 focus:ring-brand-200">
                {{ $f['label'] }}
            </label>

        @elseif($f['type'] === 'multicheck')
            @php $picked = (array) old($name, array_filter(explode(',', (string) ($item->{$name} ?? '')))); @endphp
            <div>
                <label class="mb-2 block text-sm font-semibold">{{ $label }}</label>
                <div class="flex flex-wrap gap-3">
                    @foreach($f['options'] as $optValue => $optLabel)
                        <label class="flex cursor-pointer items-center gap-2 rounded-full border border-line px-4 py-1.5 text-sm font-medium">
                            <input type="checkbox" name="{{ $name }}[]" value="{{ $optValue }}" @checked(in_array($optValue, $picked, true)) class="rounded border-line text-brand-500 focus:ring-brand-200"> {{ $optLabel }}
                        </label>
                    @endforeach
                </div>
            </div>

        @elseif($f['type'] === 'image')
            <div>
                <label class="mb-2 block text-sm font-semibold">{{ $label }}</label>
                @if($editing && $item->{$name})
                    <div class="mb-3 flex items-center gap-4">
                        <img src="{{ \App\Support\ImageUploader::url($item->{$name}, true) }}" alt="" class="h-24 w-24 rounded-2xl bg-soft object-cover">
                        <label class="flex items-center gap-2 text-sm text-muted"><input type="checkbox" name="{{ $name }}_remove" value="1" class="rounded border-line text-red-500"> Remove this photo</label>
                    </div>
                @endif
                <input type="file" name="{{ $name }}" accept="image/jpeg,image/png,image/webp,image/gif"
                       class="block w-full text-sm text-muted file:mr-4 file:cursor-pointer file:rounded-full file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
                @if(! empty($f['hint']))<p class="mt-1.5 text-xs text-muted">{{ $f['hint'] }}</p>@endif
            </div>

        @elseif($f['type'] === 'gallery')
            @php
                $kinds = ! empty($f['kinds']) ? \App\Models\WorkPhoto::KINDS : ['other' => 'Photos'];
                $galleryPhotos = $editing ? $item->{$f['relation']} : collect();
                $byKind = $galleryPhotos->groupBy(fn ($p) => array_key_exists($p->kind ?? 'other', $kinds) ? ($p->kind ?? 'other') : 'other');
            @endphp
            <div data-gallery data-field="{{ $name }}">
                <label class="mb-1 block text-sm font-semibold">{{ $label }}</label>

                @if($editing)
                    <p class="mb-4 text-xs text-muted">
                        Drag a photo to re-order it, or drop it into another group to change its type. On a phone, hold a photo a moment before dragging, or use "Move to". Changes are saved with the button at the bottom.
                    </p>

                    @if(! empty($f['cover_field']))
                        <input type="hidden" name="{{ $name }}_cover" value="" data-cover-input>
                    @endif

                    <div class="space-y-4">
                        @foreach($kinds as $kindValue => $kindLabel)
                            <section data-group data-kind="{{ $kindValue }}" class="gal-group rounded-2xl border border-line bg-soft/60 p-3">
                                <header class="mb-2 flex items-center justify-between px-1">
                                    <h3 class="text-sm font-bold text-ink">{{ $kindLabel }}</h3>
                                    <span class="rounded-full bg-white px-2.5 py-0.5 text-xs font-semibold text-muted" data-count>{{ ($byKind[$kindValue] ?? collect())->count() }}</span>
                                </header>

                                <ul data-list class="gal-list grid min-h-[3.5rem] grid-cols-3 gap-3 sm:grid-cols-4 lg:grid-cols-6">
                                    @foreach($byKind[$kindValue] ?? [] as $photo)
                                        <li data-tile data-id="{{ $photo->id }}" class="gal-tile group relative cursor-grab overflow-hidden rounded-xl border border-line bg-white active:cursor-grabbing">
                                            <input type="hidden" name="{{ $name }}_order[]" value="{{ $photo->id }}">
                                            @if(! empty($f['kinds']))
                                                <input type="hidden" name="{{ $name }}_kind[{{ $photo->id }}]" value="{{ $kindValue }}" data-kind-input>
                                            @endif
                                            <img src="{{ $photo->url(true) }}" alt="" draggable="false" loading="lazy" class="aspect-square w-full object-cover">

                                            <div class="space-y-1.5 p-2 text-[11px]">
                                                @if(! empty($f['cover_field']))
                                                    <button type="button" data-make-cover="{{ $photo->id }}" aria-pressed="false" class="gal-cover w-full rounded-lg border border-line px-2 py-1 font-semibold text-ink hover:border-brand-500">Make main photo</button>
                                                @endif
                                                @if(! empty($f['kinds']))
                                                    <select data-move aria-label="Move to" class="w-full rounded-lg border border-line bg-white px-1.5 py-1">
                                                        @foreach($kinds as $moveValue => $moveLabel)
                                                            <option value="{{ $moveValue }}" @selected($moveValue === $kindValue)>Move to: {{ $moveLabel }}</option>
                                                        @endforeach
                                                    </select>
                                                    {{-- only visible while the photo sits in the Carousel group (see app.css) --}}
                                                    <input type="text" name="{{ $name }}_caption[{{ $photo->id }}]" value="{{ $photo->caption }}" maxlength="120" placeholder="Set name, e.g. Carousel 1" aria-label="Carousel set name" class="gal-caption w-full rounded-lg border border-line px-2 py-1">
                                                @endif
                                                <label class="flex cursor-pointer items-center gap-1.5 font-medium text-muted">
                                                    <input type="checkbox" name="{{ $name }}_remove[]" value="{{ $photo->id }}" data-remove class="rounded border-line text-red-500"> Remove
                                                </label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </section>
                        @endforeach
                    </div>
                @endif

                <div class="mt-5 rounded-2xl border border-dashed border-line p-4">
                    <p class="mb-2 text-sm font-semibold">Add photos</p>
                    <input type="file" name="{{ $name }}_new[]" multiple accept="image/jpeg,image/png,image/webp,image/gif"
                           class="block w-full text-sm text-muted file:mr-4 file:cursor-pointer file:rounded-full file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
                    @if(! empty($f['kinds']))
                        <div class="mt-3 flex flex-wrap items-center gap-2 text-sm">
                            <label for="{{ $name }}_new_kind" class="font-semibold">Type of the new photos</label>
                            <select id="{{ $name }}_new_kind" name="{{ $name }}_new_kind" class="rounded-xl border border-line bg-white px-3 py-1.5 text-sm">
                                @foreach(\App\Models\WorkPhoto::KINDS as $kindValue => $kindLabel)
                                    <option value="{{ $kindValue }}" @selected($kindValue === 'other')>{{ $kindLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <p class="mt-1 text-xs text-muted">Pick the type first, then choose the files. To upload another type, save and repeat. New photos go to the end of their group; drag them afterwards.</p>
                    @endif
                    <p class="mt-1.5 text-xs text-muted">Up to {{ $f['max'] ?? 12 }} photos at a time. They are resized automatically.</p>
                </div>
            </div>
        @else
            <div>
                <label class="mb-2 block text-sm font-semibold">{{ $label }}</label>
                <div class="grid gap-3 {{ count($columns) > 1 ? 'md:grid-cols-2' : '' }}">
                    @foreach($columns as $column => $lang)
                        <div>
                            @if($lang)<span class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-muted">{{ $lang }}</span>@endif

                            @if(in_array($f['type'], ['textarea', 'lines'], true))
                                <textarea name="{{ $column }}" rows="{{ $f['rows'] ?? 4 }}" class="{{ $inputClass }} resize-y">{{ $val($column) }}</textarea>

                            @elseif(in_array($f['type'], ['select', 'icon'], true))
                                <select name="{{ $column }}" class="{{ $inputClass }}">
                                    @unless(! empty($f['required']))<option value="">—</option>@endunless
                                    @foreach($f['options'] as $optValue => $optLabel)
                                        <option value="{{ $optValue }}" @selected((string) $val($column) === (string) $optValue)>{{ $optLabel }}</option>
                                    @endforeach
                                </select>

                            @elseif($f['type'] === 'number')
                                <input type="number" name="{{ $column }}" value="{{ $val($column) }}" class="{{ $inputClass }}">

                            @elseif($f['type'] === 'datetime')
                                @php $dt = $val($column); @endphp
                                <input type="datetime-local" name="{{ $column }}"
                                       value="{{ $dt instanceof \Carbon\CarbonInterface ? $dt->format('Y-m-d\TH:i') : $dt }}" class="{{ $inputClass }}">

                            @else
                                <input type="{{ $f['type'] === 'url' ? 'url' : 'text' }}" name="{{ $column }}" value="{{ $val($column) }}" maxlength="255" class="{{ $inputClass }}"
                                       @if($f['type'] === 'url') placeholder="https://" @endif>
                            @endif
                        </div>
                    @endforeach
                </div>
                @if(! empty($f['hint']))<p class="mt-1.5 text-xs text-muted">{{ $f['hint'] }}</p>@endif
            </div>
        @endif
    @endforeach

    <div class="flex items-center gap-3 border-t border-line pt-6">
        <button type="submit" class="btn-primary">{{ $editing ? 'Save changes' : 'Create' }}</button>
        <a href="{{ route('admin.resource.index', $resource) }}" class="btn-ghost">Cancel</a>
    </div>
</form>
@endsection
