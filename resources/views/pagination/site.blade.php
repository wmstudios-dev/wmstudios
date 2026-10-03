@if ($paginator->hasPages())
    <nav class="mt-14 flex items-center justify-center gap-2" role="navigation" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="rounded-full border border-line px-4 py-2 text-sm text-muted/50">&larr; {{ __('site.pagination.prev') }}</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn-ghost !px-4 !py-2">&larr; {{ __('site.pagination.prev') }}</a>
        @endif

        <span class="px-3 text-sm font-medium text-muted">{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn-ghost !px-4 !py-2">{{ __('site.pagination.next') }} &rarr;</a>
        @else
            <span class="rounded-full border border-line px-4 py-2 text-sm text-muted/50">{{ __('site.pagination.next') }} &rarr;</span>
        @endif
    </nav>
@endif
