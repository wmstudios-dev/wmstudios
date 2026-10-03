@extends('layouts.app')

@section('title', __('site.thoughts.title'))
@section('meta_description', __('site.thoughts.subtitle'))

@section('content')
@include('partials.page-header', ['title' => __('site.thoughts.title'), 'subtitle' => __('site.thoughts.subtitle')])

<section class="mx-auto max-w-7xl px-5 sm:px-8">
    @if($thoughts->isEmpty())
        <p class="py-24 text-center text-muted">{{ __('site.thoughts.empty') }}</p>
    @else
        <div class="grid gap-x-8 gap-y-14 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($thoughts as $thought)
                @include('partials.thought-card', ['thought' => $thought, 'delay' => ($loop->index % 3) * 80])
            @endforeach
        </div>

        {{ $thoughts->links() }}
    @endif
</section>

@include('partials.cta-band')
@endsection
