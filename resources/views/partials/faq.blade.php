{{-- FAQ: heading on the left, accordion on the right. Needs $faqs; optional $more (link to the full list). --}}
@if($faqs->isNotEmpty())
<section id="faq" class="mx-auto mt-24 max-w-7xl scroll-mt-28 px-5 sm:px-8">
    <div class="grid gap-10 lg:grid-cols-12 lg:gap-16">
        <div class="lg:col-span-4">
            <div class="lg:sticky lg:top-28">
                <p class="eyebrow reveal">{{ __('site.services.faq_eyebrow') }}</p>
                <h2 class="display reveal mt-2 text-5xl text-ink sm:text-7xl" style="--d: 60ms">{{ __('site.services.faq_title') }}</h2>
                <p class="reveal mt-5 max-w-xs text-muted" style="--d: 100ms">{{ __('site.services.faq_text') }}</p>
                <a href="{{ route('contact') }}" class="btn-dark reveal mt-6" style="--d: 140ms">{{ __('site.services.faq_ask') }} <x-icon name="arrow" class="h-4 w-4" /></a>
            </div>
        </div>

        <div class="lg:col-span-8">
            <div class="divide-y divide-line border-y border-line">
                @foreach($faqs as $faq)
                    <details class="group py-5">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-6 text-lg font-semibold text-ink transition-colors hover:text-brand-600">
                            {{ $faq->t('question') }}
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-line transition-all duration-300 group-open:rotate-45 group-open:border-brand-500 group-open:bg-brand-500 group-open:text-white">
                                <x-icon name="plus" class="h-4 w-4" />
                            </span>
                        </summary>
                        <p class="mt-3 max-w-2xl leading-relaxed text-muted">{{ $faq->t('answer') }}</p>
                    </details>
                @endforeach
            </div>
            @if($more ?? false)
                <a href="{{ route('services') }}#faq" class="btn-ghost reveal mt-8">{{ __('site.home.faq_more') }} <x-icon name="arrow" class="h-4 w-4" /></a>
            @endif
        </div>
    </div>
</section>
@endif
