<link rel="stylesheet" href="{{ asset('css/pagination.css') }}"> <!-- Подключение стилей пагинации -->

<div class="pagination">
    @if ($allAdverts->onFirstPage())
        <!-- Не отображаем кнопку "предыдущая страница" на первой странице -->
    @else
        <a href="{{ $allAdverts->previousPageUrl() }}">&laquo;</a>
    @endif

    @php
        $currentPage = $allAdverts->currentPage();
        $lastPage = $allAdverts->lastPage();
        $start = max(1, $currentPage - 3);
        $end = min($lastPage, $currentPage + 3);
    @endphp

    @if ($start > 1)
        <a href="{{ $allAdverts->appends(request()->input())->url(1) }}">1</a>
        @if ($start > 2)
            <span>...</span>
        @endif
    @endif

    @for ($i = $start; $i <= $end; $i++)
        @if ($i == $currentPage)
            <span class="active">{{ $i }}</span>
        @else
            <a href="{{ $allAdverts->appends(request()->input())->url($i) }}">{{ $i }}</a>
        @endif
    @endfor

    @if ($end < $lastPage)
        @if ($end < $lastPage - 1)
            <span>...</span>
        @endif
        <a href="{{ $allAdverts->appends(request()->input())->url($lastPage) }}">{{ $lastPage }}</a>
    @endif

    @if ($allAdverts->hasMorePages())
        <a href="{{ $allAdverts->nextPageUrl() }}">&raquo;</a>
    @else
        <!-- Не отображаем кнопку "следующая страница" на последней странице -->
    @endif
</div>