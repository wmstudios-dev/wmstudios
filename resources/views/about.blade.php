@extends('layouts.app')

@php
    $siteName = \App\Models\Setting::get('site_name', config('app.name'));
    $valueColors = ['bg-lilac', 'bg-butter', 'bg-mint', 'bg-lime'];
@endphp

@section('title', __('about.title'))
@section('meta_description', __('about.agency_p1', ['name' => $siteName]))

@section('content')
@include('partials.page-header', ['title' => __('about.title'), 'subtitle' => __('about.subtitle')])

{{-- More than an agency --}}
<section class="mx-auto max-w-7xl px-5 sm:px-8">
    <div class="grid gap-10 lg:grid-cols-12">
        <h2 class="display reveal text-5xl text-ink sm:text-7xl lg:col-span-5">{{ __('about.agency_title') }}</h2>
        <div class="space-y-6 lg:col-span-6 lg:col-start-7">
            <p class="reveal text-lg leading-relaxed text-ink" style="--d: 60ms">{{ __('about.agency_p1', ['name' => $siteName]) }}</p>
            <p class="reveal leading-relaxed text-muted" style="--d: 120ms">{{ __('about.agency_p2', ['name' => $siteName]) }}</p>
        </div>
    </div>
</section>

{{-- Our direction --}}
<section class="mx-auto mt-24 max-w-7xl px-5 sm:px-8">
    <p class="eyebrow reveal">{{ __('about.direction_eyebrow') }}</p>
    <h2 class="display reveal mt-2 text-5xl text-ink sm:text-7xl" style="--d: 60ms">{{ __('about.direction_title') }}</h2>

    <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach(__('about.values') as [$valueTitle, $valueText])
            <div class="reveal flex flex-col rounded-3xl {{ $valueColors[$loop->index % 4] }} p-7 transition-transform duration-300 hover:-translate-y-1" style="--d: {{ $loop->index * 70 }}ms">
                <div class="flex items-start justify-between">
                    <p class="display text-6xl text-ink">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</p>
                    <x-icon name="arrow-up-right" class="h-7 w-7 text-ink" />
                </div>
                <h3 class="mt-10 text-lg font-bold text-ink">{{ $valueTitle }}</h3>
                <p class="mt-2 text-sm leading-relaxed text-ink/75">{{ $valueText }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- Why us + collaboration options --}}
<section class="mx-auto mt-24 max-w-7xl px-5 sm:px-8">
    <div class="grid gap-10 lg:grid-cols-12 lg:gap-16">
        <div class="lg:col-span-6">
            <p class="eyebrow reveal">{{ __('about.why_eyebrow') }}</p>
            <h2 class="display reveal mt-2 text-5xl text-ink sm:text-7xl" style="--d: 60ms">{{ __('about.why_title', ['name' => $siteName]) }}</h2>
            <ul class="mt-8 divide-y divide-line border-y border-line">
                @foreach(__('about.why_points') as $point)
                    <li class="reveal flex items-center gap-4 py-4 text-lg font-semibold text-ink" style="--d: {{ $loop->index * 50 }}ms">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-500 text-white"><x-icon name="check" class="h-4 w-4" stroke="2.4" /></span>
                        {{ $point }}
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="reveal relative overflow-hidden rounded-[2rem] bg-brand-500 p-8 text-white sm:p-10 lg:col-span-5 lg:col-start-8 lg:self-start" style="--d: 80ms">
            <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-brand-400/40 blur-3xl"></div>
            <h3 class="display relative text-4xl sm:text-5xl">{{ __('about.collab_title') }}</h3>
            <ul class="relative mt-6 space-y-3">
                @foreach(__('about.collab_points') as $point)
                    <li class="flex items-center gap-3 text-base font-medium">
                        <x-icon name="spark" class="h-5 w-5 shrink-0 text-lime" /> {{ $point }}
                    </li>
                @endforeach
            </ul>
            <a href="{{ route('contact') }}" class="btn-light relative mt-8">{{ __('about.collab_cta') }} <x-icon name="arrow" class="h-4 w-4" /></a>
        </div>
    </div>
</section>

@include('partials.cta-band')
@endsection
