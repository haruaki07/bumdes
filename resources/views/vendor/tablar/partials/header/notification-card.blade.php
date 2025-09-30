<div class="card">
  <div class="card-header">
    <h3 class="card-title">
      Notifikasi
    </h3>
    @if ($user->unreadNotifications->count() > 0)
      <a href="{{ route('e-billing.notifications.read-all') }}" class="ms-auto">Tandai semua telah dibaca</a>
    @endif
  </div>
  <div class="list-group list-group-flush list-group-hoverable">
    @forelse ($user->notifications->take(5) as $notification)
      <a href="{{ route('e-billing.notifications.read', ['id' => $notification->id]) }}"
        class="list-group-item text-body text-decoration-none @if ($notification->read_at) opacity-50 @endif">
        <div class="row align-items-center">
          <div class="col">
            <div class="mb-1">
            </div>
            <div class="d-block">
              {!! \Modules\EBilling\Formatters\NotificationFormatter::format($notification) !!}
            </div>
            <small class="col-auto @if (!$notification->read_at) text-primary @endif mt-1">
              {{ $notification->created_at->locale('id')->diffForHumans() }}
            </small>
          </div>
        </div>
      </a>
    @empty
      <div class="list-group-item">
        <div class="row align-items-center">
          <div class="col text-truncate">
            <div class="d-block text-muted text-truncate mt-n1 text-center">
              Tidak ada notifikasi
            </div>
          </div>
        </div>
      </div>
    @endforelse
  </div>
  <div class="card-body border-top">
    <a href="{{ route('e-billing.notifications.index') }}" class="ms-auto d-block text-center">Lihat semua</a>
  </div>
</div>
