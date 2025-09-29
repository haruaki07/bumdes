<x-e-billing::layouts.panel>
  <div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center col-lg-8">
        <div class="col">
          <div class="page-pretitle">
            Dashboard
          </div>
          <h2 class="page-title">
            Notifikasi
          </h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
          <div class="btn-list">
            @if ($notifications->total() > 0)
              <form action="{{ route('e-billing.notifications.clear') }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger d-none d-sm-inline-block">
                  <i class="ti ti-trash icon"></i>
                  Hapus semua
                </button>
              </form>
            @endif
            @if (request()->user('ebil')->unreadNotifications->count() > 0)
              <a href="{{ route('e-billing.notifications.read-all') }}"
                class="btn btn-primary d-none d-sm-inline-block">
                Tandai semua telah dibaca
              </a>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">
    <div class="container-xl">
      <div class="row">
        <div class="col-lg-8">
          @include('tablar::common.alert')
          <div class="card">
            <div class="card-header">
              <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                <li class="nav-item">
                  <a href="{{ route('e-billing.notifications.index') }}"
                    class="nav-link @if (request()->input('filter') !== 'unread') active @endif">
                    Semua
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('e-billing.notifications.index') }}?filter=unread"
                    class="nav-link @if (request()->input('filter') === 'unread') active @endif">
                    Belum dibaca
                  </a>
                </li>
              </ul>
            </div>
            <div class="card-body">
              <div class="divide-y">
                @forelse ($notifications as $notification)
                  <a href="{{ route('e-billing.notifications.read', ['id' => $notification->id]) }}"
                    class="row text-body text-decoration-none">
                    <div class="col @if ($notification->read_at) opacity-50 @endif">
                      <div>{!! \Modules\EBilling\Formatters\NotificationFormatter::format($notification) !!}</div>
                      <div class="text-secondary">{{ $notification->created_at->locale('id')->diffForHumans() }}</div>
                    </div>
                    <div class="col-auto align-self-center">
                      @if (!$notification->read_at)
                        <div class="badge bg-primary"></div>
                      @endif
                    </div>
                  </a>
                @empty
                  <div class="text-center">
                    Tidak ada notifikasi
                  </div>
                @endforelse
              </div>

            </div>
            <div class="card-footer d-flex align-items-center">
              {!! $notifications->links('tablar::pagination') !!}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-e-billing::layouts.panel>
