@if ($paginator->hasPages())
    <div style="display: flex; gap: 4px; list-style: none; padding: 0; margin: 0;">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span style="padding: 4px 8px; font-size: 12px; color: #ccc;">← Prev</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" style="padding: 4px 8px; font-size: 12px; text-decoration: none; color: #0066cc;">← Prev</a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span style="padding: 4px 8px; font-size: 12px; color: #ccc;">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span style="padding: 4px 8px; font-size: 12px; background: #0066cc; color: white;">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" style="padding: 4px 8px; font-size: 12px; text-decoration: none; color: #0066cc;">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" style="padding: 4px 8px; font-size: 12px; text-decoration: none; color: #0066cc;">Next →</a>
        @else
            <span style="padding: 4px 8px; font-size: 12px; color: #ccc;">Next →</span>
        @endif
    </div>
@endif