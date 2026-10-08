@extends('layouts.app')

@php
    use App\Models\Setting;

    $whatsapp = \App\Support\WhatsApp::chatUrl();
    $email = Setting::get('email');
    $address = Setting::t('address');
    $currentService = old('service', $selectedService);
@endphp

@section('title', __('site.contact.title'))
@section('meta_description', __('site.contact.subtitle'))

@section('content')
@include('partials.page-header', ['title' => __('site.contact.title'), 'subtitle' => __('site.contact.subtitle')])

<section class="mx-auto grid max-w-7xl gap-10 px-5 sm:px-8 lg:grid-cols-12">
    {{-- Direct contact --}}
    <aside class="reveal space-y-8 lg:col-span-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted">{{ __('site.contact.direct') }}</p>
            <ul class="mt-4 space-y-3">
                @if($whatsapp)
                    <li><a href="{{ $whatsapp }}" target="_blank" rel="noopener" class="group flex items-center gap-4 rounded-2xl border border-line p-4 transition-all duration-200 hover:border-brand-300 hover:bg-brand-50/50">
                        <span class="flex h-11 w-11 items-center justify-center rounded-full bg-[#25d366]/15 text-[#128c7e]"><x-icon name="whatsapp" class="h-5 w-5" /></span>
                        <span><span class="block text-sm font-bold text-ink">{{ __('site.contact.chat_whatsapp') }}</span><span class="block text-xs text-muted">+{{ \App\Support\WhatsApp::normalize(Setting::get('whatsapp')) }}</span></span>
                    </a></li>
                @endif
                @if($email)
                    <li><a href="mailto:{{ $email }}" class="group flex items-center gap-4 rounded-2xl border border-line p-4 transition-all duration-200 hover:border-brand-300 hover:bg-brand-50/50">
                        <span class="flex h-11 w-11 items-center justify-center rounded-full bg-brand-50 text-brand-500"><x-icon name="mail" class="h-5 w-5" /></span>
                        <span><span class="block text-sm font-bold text-ink">Email</span><span class="block text-xs text-muted">{{ $email }}</span></span>
                    </a></li>
                @endif
                @if($address)
                    <li class="flex items-center gap-4 rounded-2xl border border-line p-4">
                        <span class="flex h-11 w-11 items-center justify-center rounded-full bg-brand-50 text-brand-500"><x-icon name="pin" class="h-5 w-5" /></span>
                        <span class="text-sm text-ink">{{ $address }}</span>
                    </li>
                @endif
            </ul>
        </div>

        @if(collect(\App\Support\SettingsSchema::socials())->contains(fn ($n) => Setting::get($n)))
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted">{{ __('site.contact.follow') }}</p>
                <div class="mt-4">@include('partials.socials')</div>
            </div>
        @endif
    </aside>

    {{-- Form --}}
    <div class="reveal lg:col-span-8" style="--d: 80ms">
        @if(session('sent'))
            <div class="rounded-[2rem] bg-brand-50 p-10 text-center sm:p-14">
                <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-brand-500 text-white"><x-icon name="check" class="h-8 w-8" stroke="2.4" /></span>
                <h2 class="display mt-6 text-5xl text-ink">{{ __('site.contact.sent_title') }}</h2>
                <p class="mx-auto mt-3 max-w-md text-muted">{{ __('site.contact.sent_text') }}</p>
                @if(session('whatsapp'))
                    <a href="{{ session('whatsapp') }}" target="_blank" rel="noopener" class="btn-primary mt-8"><x-icon name="whatsapp" class="h-4 w-4" /> {{ __('site.contact.sent_whatsapp') }}</a>
                @endif
            </div>
        @else
            <form method="POST" action="{{ route('contact.store') }}" class="space-y-5 rounded-[2rem] border border-line p-6 sm:p-10">
                @csrf
                {{-- Honeypot: hidden from people, bots fill it in --}}
                <div class="absolute -left-[9999px] h-0 w-0 overflow-hidden" aria-hidden="true">
                    <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="name" class="mb-2 block text-sm font-semibold">{{ __('site.contact.name') }}</label>
                        <input id="name" name="name" value="{{ old('name') }}" required maxlength="120" autocomplete="name" class="field">
                        @error('name')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="email" class="mb-2 block text-sm font-semibold">{{ __('site.contact.email') }}</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required maxlength="190" autocomplete="email" class="field">
                        @error('email')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="phone" class="mb-2 block text-sm font-semibold">{{ __('site.contact.phone') }} <span class="font-normal text-muted">{{ __('site.contact.phone_optional') }}</span></label>
                    <input id="phone" name="phone" value="{{ old('phone') }}" maxlength="40" inputmode="tel" autocomplete="tel" class="field">
                    @error('phone')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="service" class="mb-2 block text-sm font-semibold">{{ __('site.contact.service') }}</label>
                        <select id="service" name="service" class="field">
                            <option value="">{{ __('site.contact.service_placeholder') }}</option>
                            @foreach($services as $service)
                                <option value="{{ $service->t('title') }}" @selected($currentService === $service->t('title') || $currentService === $service->slug)>{{ $service->t('title') }}</option>
                            @endforeach
                            <option value="{{ __('site.contact.service_other') }}" @selected($currentService === __('site.contact.service_other'))>{{ __('site.contact.service_other') }}</option>
                        </select>
                    </div>
                    <div>
                        <label for="budget" class="mb-2 block text-sm font-semibold">{{ __('site.contact.budget') }}</label>
                        <select id="budget" name="budget" class="field">
                            <option value="">{{ __('site.contact.budget_placeholder') }}</option>
                            @foreach($budgets as $b)
                                <option value="{{ $b }}" @selected(old('budget') === $b)>{{ __('site.contact.budgets.' . $b) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="message" class="mb-2 block text-sm font-semibold">{{ __('site.contact.message') }}</label>
                    <textarea id="message" name="message" rows="6" required minlength="10" maxlength="3000" placeholder="{{ __('site.contact.message_placeholder') }}" class="field resize-y">{{ old('message', $prefill ?? '') }}</textarea>
                    @error('message')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="btn-primary w-full sm:w-auto">{{ __('site.contact.send') }} <x-icon name="arrow" class="h-4 w-4" /></button>
            </form>
        @endif
    </div>
</section>
@endsection
