@extends('layouts.app')

@section('title', __('site.works.title'))
@section('meta_description', __('site.works.subtitle'))

@section('content')
@include('partials.page-header', ['title' => __('site.works.title'), 'subtitle' => __('site.works.subtitle')])

<section class="mx-auto max-w-7xl px-5 sm:px-8">
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('works.index') }}"
           class="rounded-full border px-5 py-2 text-sm font-semibold transition-all duration-200 active:scale-95 {{ ! $category ? 'border-brand-500 bg-brand-500 text-white' : 'border-line text-ink hover:border-brand-300 hover:text-brand-600' }}">{{ __('site.categories.all') }}</a>
        @foreach($categories as $cat)
            <a href="{{ route('works.index', ['c' => $cat]) }}"
               class="rounded-full border px-5 py-2 text-sm font-semibold transition-all duration-200 active:scale-95 {{ $category === $cat ? 'border-brand-500 bg-brand-500 text-white' : 'border-line text-ink hover:border-brand-300 hover:text-brand-600' }}">{{ __('site.categories.' . $cat) }}</a>
        @endforeach
    </div>

    @if($works->isEmpty())
        <p class="py-24 text-center text-muted">{{ __('site.works.empty') }}</p>
    @else
        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($works as $work)
                @include('partials.work-card', ['work' => $work, 'delay' => ($loop->index % 3) * 80])
            @endforeach
        </div>

        {{ $works->links() }}
    @endif
</section>

@include('partials.cta-band')
@endsection
