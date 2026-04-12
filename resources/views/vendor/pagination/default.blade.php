@if ($paginator->hasPages())
<nav style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-top:1px solid var(--border);flex-direction:row-reverse;">
    {{-- Info --}}
    <div style="font-size:12px;color:var(--text-muted);">
        عرض {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} من {{ $paginator->total() }}
    </div>

    {{-- Page links --}}
    <div style="display:flex;align-items:center;gap:4px;direction:ltr;">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="page-link disabled" style="font-size:16px;">‹</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="page-link" style="font-size:16px;">‹</a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="page-link disabled">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="page-link active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="page-link" style="font-size:16px;">›</a>
        @else
            <span class="page-link disabled" style="font-size:16px;">›</span>
        @endif
    </div>
</nav>
@endif
