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
            <div>
                <label class="mb-2 block text-sm font-semibold">{{ $label }}</label>
                @if($editing && $item->{$f['relation']}->isNotEmpty())
                    <div class="mb-3 grid grid-cols-3 gap-3 sm:grid-cols-5">
                        @foreach($item->{$f['relation']} as $photo)
                            <label class="group relative block cursor-pointer overflow-hidden rounded-2xl bg-soft">
                                <img src="{{ $photo->url(true) }}" alt="" class="aspect-square w-full object-cover">
                                <span class="absolute inset-x-0 bottom-0 flex items-center gap-1.5 bg-ink/70 px-2 py-1.5 text-[11px] font-medium text-white">
                                    <input type="checkbox" name="{{ $name }}_remove[]" value="{{ $photo->id }}" class="rounded border-white/50"> Remove
                                </span>
                            </label>
                            @if(! empty($f['kinds']))
                                <select name="{{ $name }}_kind[{{ $photo->id }}]" aria-label="Type" class="-mt-1 w-full rounded-xl border border-line bg-white px-2 py-1.5 text-xs">
                                    @foreach(\App\Models\WorkPhoto::KINDS as $kindValue => $kindLabel)
                                        <option value="{{ $kindValue }}" @selected(($photo->kind ?? 'other') === $kindValue)>{{ $kindLabel }}</option>
                                    @endforeach
                                </select>
                            @endif
                        @endforeach
                    </div>
                @endif
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
                    <p class="mt-1 text-xs text-muted">Pick the type first, then choose the files. To upload another type, save and repeat. The work page shows each type in its own section.</p>
                @endif
                <p class="mt-1.5 text-xs text-muted">Up to {{ $f['max'] ?? 12 }} photos at a time. They are resized automatically.</p>
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
