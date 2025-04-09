@if ($paginator->hasPages())
    <nav>
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link" aria-hidden="true">&lsaquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                </li>
            @endif

            {{-- First Page Link --}}
            <li class="page-item {{ $paginator->currentPage() == 1 ? 'active' : '' }}">
                <a class="page-link" href="{{ $paginator->url(1) }}">1</a>
            </li>

            {{-- Current Page with Ellipsis --}}
            @if($paginator->currentPage() > 1 && $paginator->currentPage() < $paginator->lastPage())
                @if($paginator->currentPage() > 2)
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link">...</span></li>
                @endif
                <li class="page-item active" aria-current="page"><span class="page-link">{{ $paginator->currentPage() }}</span></li>
            @endif

            {{-- Ellipsis and Last Page Link --}}
            @if($paginator->lastPage() > 1)
                @if($paginator->currentPage() < $paginator->lastPage() - 1)
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link">...</span></li>
                @endif
                @if($paginator->currentPage() == $paginator->lastPage() || $paginator->currentPage() != $paginator->lastPage())
                    <li class="page-item">
                        <a class="page-link {{ ($paginator->currentPage() == $paginator->lastPage()) ? 'active' : '' }}" href="{{ $paginator->url($paginator->lastPage()) }}">{{ $paginator->lastPage() }}</a>
                    </li>
                @endif
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link" aria-hidden="true">&rsaquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
