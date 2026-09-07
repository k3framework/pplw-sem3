@props(['items'])
@if($items->hasPages())
<nav class="pagination" aria-label="Halaman data">
    @if($items->onFirstPage())<span class="muted">Sebelumnya</span>
    @else<a href="{{ $items->previousPageUrl() }}">Sebelumnya</a>@endif
    <span>Halaman {{ $items->currentPage() }} dari {{ $items->lastPage() }}</span>
    @if($items->hasMorePages())<a href="{{ $items->nextPageUrl() }}">Berikutnya</a>
    @else<span class="muted">Berikutnya</span>@endif
</nav>
@endif

