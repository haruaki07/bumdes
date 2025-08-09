<p class="m-0 text-muted">Menampilkan <span>{{ $paginator->perPage() }}</span>
  dari <span>{{ $paginator->total() }}</span> hasil
</p>
<ul class="pagination m-0 ms-auto">
  @if ($paginator->onFirstPage())
    <li class="page-item disabled">
      <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
        <i class="icon ti ti-chevron-left" style="font-size: 16px;"></i>
        Sebelumnya
      </a>
    </li>
  @else
    <li class="page-item">
      <a class="page-link" href="{{ $paginator->previousPageUrl() }}" tabindex="-1" aria-disabled="true">
        <i class="icon ti ti-chevron-left" style="font-size: 16px;"></i>
        Sebelumnya
      </a>
    </li>
  @endif

  @foreach ($elements as $element)
    @if (is_string($element))
      <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
    @endif
    @if (is_array($element))
      @foreach ($element as $page => $url)
        @if ($page == $paginator->currentPage())
          <li class="page-item active"><a class="page-link" href="#">{{ $page }}</a></li>
        @else
          <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
        @endif
      @endforeach
    @endif
  @endforeach

  @if ($paginator->hasMorePages())
    <li class="page-item">
      <a class="page-link" href="{{ $paginator->nextPageUrl() }}">
        Selanjutnya
        <i class="icon ti ti-chevron-right" style="font-size: 16px;"></i>
      </a>
    </li>
  @else
    <li class="page-item disabled">
      <a class="page-link" href="#">
        Selanjutnya
        <i class="icon ti ti-chevron-right" style="font-size: 16px;"></i>
      </a>
    </li>
  @endif
</ul>
