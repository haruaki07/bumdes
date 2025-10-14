@extends('tablar::auth.layout')

@section('title', 'Home')

@section('tablar_css')
  <style>
    .app-icon {
      width: 70px;
      aspect-ratio: 1;
      padding: 1rem;
      background-color: white;
      object-fit: cover;
      transform-origin: center bottom;
      transition: box-shadow ease-in 0.1s, transform ease-in 0.1s;
      box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.2), 0 1px 1px rgba(0, 0, 0, 0.02), 0 2px 2px rgba(0, 0, 0, 0.02), 0 4px 4px rgba(0, 0, 0, 0.02), 0 8px 8px rgba(0, 0, 0, 0.02), 0 16px 16px rgba(0, 0, 0, 0.02);
    }

    .app:hover .app-icon {
      box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.2), 0 2px 2px rgba(0, 0, 0, 0.03), 0 4px 4px rgba(0, 0, 0, 0.03), 0 8px 8px rgba(0, 0, 0, 0.03), 0 12px 12px rgba(0, 0, 0, 0.03), 0 24px 24px rgba(0, 0, 0, 0.03);
      transform: translateY(-2px);
    }
  </style>
@endsection

@section('content')

  <div class="container h-100 d-flex align-items-center justify-content-center">
    <div class="d-flex gap-5 justify-content-center">
      <a class="d-flex flex-column rounded-3 justify-content-start align-items-center w-100 p-1 p-md-2 app text-decoration-none text-dark"
        href="/dashboard">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14 14" class="app-icon rounded-3">
          <g fill="none">
            <path fill="#066fd1" d="M7.5 6a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .5.5h6a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.5-.5z" />
            <path fill="#a9d3ff"
              d="M4.521.138a.5.5 0 0 0-.69 0l-3.676 3.5A.5.5 0 0 0 0 4v9.5a.5.5 0 0 0 .5.5h7.353a.5.5 0 0 0 .5-.5V4a.5.5 0 0 0-.156-.362z" />
            <path fill="#066fd1" fill-rule="evenodd"
              d="M5.176 14v-2a1 1 0 1 0-2 0v2zM2.798 4.53a.625.625 0 1 0 0 1.25h2.757a.625.625 0 1 0 0-1.25zm0 2.608a.625.625 0 1 0 0 1.25h2.757a.625.625 0 0 0 0-1.25z"
              clip-rule="evenodd" />
          </g>
        </svg>
        <div class="w-100 text-center text-truncate mt-3">BUM Desa</div>
      </a>
      <a class="d-flex flex-column rounded-3 justify-content-start align-items-center w-100 p-1 p-md-2 app text-decoration-none text-dark"
        href="/e-billing">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14 14" class="app-icon rounded-3">
          <g fill="none" fill-rule="evenodd" clip-rule="evenodd">
            <path fill="#066fd1"
              d="M1.71.606a.75.75 0 0 1 1.144.97a5.56 5.56 0 0 0-1.317 3.427a5.55 5.55 0 0 0 1.317 3.421a.75.75 0 0 1-1.143.971A7.05 7.05 0 0 1 .037 4.983A7.06 7.06 0 0 1 1.71.606m10.58 0a.75.75 0 0 0-1.145.97a5.56 5.56 0 0 1 1.318 3.427a5.55 5.55 0 0 1-1.317 3.421a.75.75 0 0 0 1.143.971a7.05 7.05 0 0 0 1.674-4.412A7.06 7.06 0 0 0 12.289.606Zm-1.637 2.106a.75.75 0 1 0-1.295.757c.28.478.436 1.006.462 1.545a3.4 3.4 0 0 1-.46 1.55a.75.75 0 0 0 1.296.754a4.9 4.9 0 0 0 .664-2.335a4.9 4.9 0 0 0-.667-2.27Zm-6.28-.27a.75.75 0 0 0-1.026.27a4.9 4.9 0 0 0-.668 2.329c.03.8.259 1.58.665 2.277a.75.75 0 0 0 1.296-.755a3.4 3.4 0 0 1-.46-1.55a3.4 3.4 0 0 1 .462-1.544a.75.75 0 0 0-.27-1.026Z" />
            <path fill="#a9d3ff"
              d="M7.75 6.59c.613-.228.947-.771.947-1.574c0-1.087-.611-1.698-1.697-1.698s-1.698.611-1.698 1.698c0 .803.334 1.346.948 1.575V13a.75.75 0 0 0 1.5 0z" />
          </g>
        </svg>
        <div class="w-100 text-center text-truncate mt-3">E-Billing</div>
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

@endsection
