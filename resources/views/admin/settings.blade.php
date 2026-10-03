@extends('admin.layout')

@section('title', 'Site settings')

@section('content')
<h1 class="display text-5xl">Site settings</h1>
<p class="mt-1 text-sm text-muted">Contact details, social links, logo and the home page headline.</p>

<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
    @csrf

    @foreach($groups as $group => $fields)
        <section class="rounded-3xl border border-line bg-white p-6 sm:p-8">
            <h2 class="font-bold">{{ $group }}</h2>

            <div class="mt-5 space-y-5">
                @foreach($fields as $f)
                    @php
                        $keys = ! empty($f['bilingual'])
                            ? [$f['key'] . '_id' => 'Indonesian', $f['key'] . '_en' => 'English']
                            : [$f['key'] => null];
                    @endphp

                    <div>
                        <label class="mb-2 block text-sm font-semibold">{{ $f['label'] }}</label>

                        @if($f['type'] === 'image')
                            @php $current = $values[$f['key']] ?? null; @endphp
                            @if($current)
                                <div class="mb-3 flex items-center gap-4">
                                    <img src="{{ \App\Support\ImageUploader::url($current, true) }}" alt="" class="h-20 w-auto max-w-40 rounded-2xl bg-soft object-contain">
                                    <label class="flex items-center gap-2 text-sm text-muted"><input type="checkbox" name="{{ $f['key'] }}_remove" value="1" class="rounded border-line"> Remove</label>
                                </div>
                            @endif
                            <input type="file" name="{{ $f['key'] }}" accept="image/jpeg,image/png,image/webp,image/gif"
                                   class="block w-full text-sm text-muted file:mr-4 file:cursor-pointer file:rounded-full file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
                        @else
                            <div class="grid gap-3 {{ count($keys) > 1 ? 'md:grid-cols-2' : '' }}">
                                @foreach($keys as $key => $lang)
                                    <div>
                                        @if($lang)<span class="mb-1 block text-[11px] font-semibold uppercase tracking-wider text-muted">{{ $lang }}</span>@endif
                                        @if($f['type'] === 'textarea')
                                            <textarea name="{{ $key }}" rows="3" class="field resize-y">{{ old($key, $values[$key] ?? '') }}</textarea>
                                        @else
                                            <input type="{{ $f['type'] === 'url' ? 'url' : 'text' }}" name="{{ $key }}" value="{{ old($key, $values[$key] ?? '') }}" class="field"
                                                   @if($f['type'] === 'url') placeholder="https://" @endif>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if(! empty($f['hint']))<p class="mt-1.5 text-xs text-muted">{{ $f['hint'] }}</p>@endif
                    </div>
                @endforeach
            </div>
        </section>
    @endforeach

    <div class="sticky bottom-4 flex justify-end">
        <button type="submit" class="btn-primary shadow-lg shadow-brand-500/30">Save settings</button>
    </div>
</form>
@endsection
