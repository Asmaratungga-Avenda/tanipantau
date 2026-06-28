@if ($paginator->hasPages())
<style>
    .pagination-modern {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
        gap: 4px;
        align-items: center;
        flex-wrap: wrap;
        justify-content: center;
    }
    .pagination-modern .page-link {
        border: none;
        color: #374151;
        font-weight: 500;
        padding: 10px 16px;
        border-radius: 8px;
        margin: 0;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-block;
        font-size: 14px;
        line-height: 1.5;
        background: transparent;
    }
    .pagination-modern .page-item.active .page-link {
        background: #006F5A;
        color: #fff;
        box-shadow: 0 4px 12px rgba(0, 111, 90, 0.2);
    }
    .pagination-modern .page-link:hover {
        background: #F3F4F6;
        color: #006F5A;
    }
    .pagination-modern .page-item.active .page-link:hover {
        background: #005a49;
        color: #fff;
    }
    .pagination-modern .page-item.disabled .page-link {
        background: transparent;
        color: #9CA3AF;
        opacity: 0.5;
        pointer-events: none;
    }
    .pagination-modern .page-item.disabled .page-link:hover {
        background: transparent;
    }
</style>
<nav>
    <ul class="pagination-modern">
        {{-- Previous --}}
        <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                &lsaquo; Previous
            </a>
        </li>

        {{-- Page Numbers --}}
        @php
            $currentPage = $paginator->currentPage();
            $lastPage = $paginator->lastPage();
            $start = max(1, $currentPage - 2);
            $end = min($lastPage, $currentPage + 2);
        @endphp

        @if($start > 1)
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->url(1) }}">1</a>
            </li>
            @if($start > 2)
                <li class="page-item disabled"><span class="page-link">...</span></li>
            @endif
        @endif

        @for($i = $start; $i <= $end; $i++)
            <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                <a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a>
            </li>
        @endfor

        @if($end < $lastPage)
            @if($end < $lastPage - 1)
                <li class="page-item disabled"><span class="page-link">...</span></li>
            @endif
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->url($lastPage) }}">{{ $lastPage }}</a>
            </li>
        @endif

        {{-- Next --}}
        <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                Next &rsaquo;
            </a>
        </li>
    </ul>
</nav>
@endif
