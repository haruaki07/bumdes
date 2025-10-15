<x-e-billing::layouts.blank>
  <div class="container h-100 d-flex align-items-center justify-content-center">
    <div class="d-flex gap-5 justify-content-center">
      <a class="d-flex flex-column rounded-3 justify-content-start align-items-center w-100 p-1 p-md-2 app text-decoration-none text-dark"
        href="{{ route('e-billing.login') }}">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14 14" class="app-icon rounded-3">
          <g fill="none" fill-rule="evenodd" clip-rule="evenodd">
            <path fill="#a9d3ff"
              d="M1.5 0A1.5 1.5 0 0 0 0 1.5v11A1.5 1.5 0 0 0 1.5 14h7a1.5 1.5 0 0 0 1.5-1.5V9.25h-.516a2 2 0 0 1-3.398 1.164l-2-2a2 2 0 0 1 0-2.828l2-2A2 2 0 0 1 9.484 4.75H10V1.5A1.5 1.5 0 0 0 8.5 0z" />
            <path fill="#066fd1"
              d="M8.25 5a.75.75 0 0 0-1.28-.53l-2 2a.75.75 0 0 0 0 1.06l2 2A.75.75 0 0 0 8.25 9V8H13a1 1 0 1 0 0-2H8.25z" />
          </g>
        </svg>
        <div class="w-100 text-center text-truncate mt-3">Login</div>
      </a>
      <a class="d-flex flex-column rounded-3 justify-content-start align-items-center w-100 p-1 p-md-2 app text-decoration-none text-dark"
        href="{{ route('e-billing.invoice.customer-search') }}">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="app-icon rounded-3">
          <g fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="4">
            <path fill="#a9d3ff" stroke="#a9d3ff" d="M10 6a2 2 0 0 1 2-2h24a2 2 0 0 1 2 2v38l-7-5l-7 5l-7-5l-7 5z" />
            <path stroke="#066fd1" d="M18 22h12m-12 8h12M18 14h12" />
          </g>
        </svg>
        <div class="w-100 text-center text-truncate mt-3">Bayar Tagihan</div>
      </a>
    </div>
  </div>

  <div class="container d-flex justify-content-center py-4">
    <ul class="list-inline list-inline-dots mb-0">
      <li class="list-inline-item text-center">
        Copyright &copy; 2025
        {{ config('tablar.bottom_title', 'TabLar') }}.
        All rights reserved.
      </li>
    </ul>
  </div>
</x-e-billing::layouts.blank>
