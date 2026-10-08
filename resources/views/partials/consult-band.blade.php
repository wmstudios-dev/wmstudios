{{-- Two easy ways to start: a free consultation and a free audit of a social media account. --}}
@php
    $consultWa = \App\Support\WhatsApp::chatUrl(__('site.consult.consult_wa_message'));
    $auditWa = \App\Support\WhatsApp::chatUrl(__('site.consult.audit_message'));
@endphp
<section class="mx-auto mt-24 max-w-7xl px-5 sm:px-8">
    <div class="max-w-2xl">
        <p class="eyebrow reveal">{{ __('site.consult.eyebrow') }}</p>
        <h2 class="display reveal mt-2 text-5xl text-ink sm:text-7xl" style="--d: 60ms">{{ __('site.consult.title') }}</h2>
        <p class="reveal mt-4 text-muted" style="--d: 100ms">{{ __('site.consult.text') }}</p>
    </div>

    <div class="mt-10 grid gap-5 lg:grid-cols-2">
        {{-- Free consultation --}}
        <article class="reveal relative flex flex-col overflow-hidden rounded-[2rem] bg-brand-500 p-8 text-white sm:p-10">
            <div class="pointer-events-none absolute -right-16 -top-16 h-56 w-56 rounded-full bg-white/10 blur-2xl"></div>
            <span class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15"><x-icon name="users" class="h-6 w-6" /></span>
            <h3 class="display relative mt-6 text-4xl sm:text-5xl">{{ __('site.consult.consult_title') }}</h3>
            <p class="relative mt-3 max-w-md text-white/85">{{ __('site.consult.consult_text') }}</p>
            <div class="relative mt-auto flex flex-wrap gap-3 pt-8">
                @if($consultWa)
                    <a href="{{ $consultWa }}" target="_blank" rel="noopener" class="btn-light"><x-icon name="whatsapp" class="h-4 w-4" /> {{ __('site.consult.consult_wa') }}</a>
                @endif
                <a href="{{ route('contact') }}" class="btn border border-white/40 text-white hover:bg-white/10">{{ __('site.consult.consult_form') }} <x-icon name="arrow" class="h-4 w-4" /></a>
            </div>
        </article>

        {{-- Free social media audit --}}
        <article class="reveal relative flex flex-col overflow-hidden rounded-[2rem] bg-lilac p-8 text-ink sm:p-10" style="--d: 80ms">
            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/70"><x-icon name="chart" class="h-6 w-6" /></span>
            <h3 class="display mt-6 text-4xl sm:text-5xl">{{ __('site.consult.audit_title') }}</h3>
            <p class="mt-3 max-w-md text-ink/80">{{ __('site.consult.audit_text') }}</p>
            <ul class="mt-5 space-y-2 text-sm font-medium">
                @foreach(trans('site.consult.audit_points') as $point)
                    <li class="flex items-start gap-2.5"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-brand-600" stroke="2.4" /> <span>{{ $point }}</span></li>
                @endforeach
            </ul>
            <div class="mt-auto flex flex-wrap gap-3 pt-8">
                <a href="{{ route('contact', ['service' => 'social-media', 'topic' => 'audit']) }}" class="btn-dark">{{ __('site.consult.audit_cta') }} <x-icon name="arrow" class="h-4 w-4" /></a>
                @if($auditWa)
                    <a href="{{ $auditWa }}" target="_blank" rel="noopener" class="btn border border-ink/20 text-ink hover:bg-white/50"><x-icon name="whatsapp" class="h-4 w-4" /> WhatsApp</a>
                @endif
            </div>
        </article>
    </div>
</section>
