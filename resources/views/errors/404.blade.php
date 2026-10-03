@extends('layouts.app')

@section('title', __('site.errors.not_found_title'))

@section('content')
<section class="mx-auto flex min-h-[60vh] max-w-3xl flex-col items-start justify-center px-5 py-20 sm:px-8">
    <p class="eyebrow">404</p>
    <h1 class="display mt-3 text-[4.5rem] text-ink sm:text-[8rem]">{{ __('site.errors.not_found_title') }}</h1>
    <p class="mt-5 max-w-md text-lg text-muted">{{ __('site.errors.not_found_text') }}</p>
    <a href="{{ route('home') }}" class="btn-primary mt-8">{{ __('site.errors.back_home') }} <x-icon name="arrow" class="h-4 w-4" /></a>
</section>
@endsection
