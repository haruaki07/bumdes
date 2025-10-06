@php
  $authenticated = false;
  $guard = 'web';
  if (Auth::guard('ebil')->check()) {
      $authenticated = true;
      $guard = 'ebil';
  }

  if (Auth::check()) {
      $authenticated = true;
  }

  $user = $authenticated ? auth($guard)->user() : null;
@endphp

@if ($authenticated && $guard === 'ebil')
  <div class="nav-item dropdown d-none d-md-flex me-3">
    <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" data-bs-auto-close="outside" tabindex="-1"
      aria-label="Show notifications" hx-get="{{ route('e-billing.notifications.dropdown') }}"
      hx-target="#notificationContent" hx-trigger="click" hx-indicator="#loadingIndicator" hx-swap="innerHTML"
      hx-on::before-request="document.querySelector('#notificationContent').innerHTML = ''">
      <i class="ti ti-bell icon" id="bellIcon"></i>
      @if ($user->unreadNotifications->count() > 0)
        <span class="badge bg-red" id="notificationCount"></span>
      @endif
    </a>
    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card"
      style="width: 28rem;user-select: auto;" id="loadingIndicator">
      <div class="htmx-display-indicator">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              Notifikasi
            </h3>
          </div>
          <div class="card-body">
            <div class="text-center">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="notificationContent"></div>
    </div>
  </div>

  @push('js')
    @include('components.common.echo-config')
    <script src="https://cdn.jsdelivr.net/npm/htmx.org@2.0.7/dist/htmx.min.js"
      integrity="sha384-ZBXiYtYQ6hJ2Y0ZNoYuI+Nq5MqWBr+chMrS/RkXpNzQCApHEhOt2aY8EJgqwHLkJ" crossorigin="anonymous"></script>
    <script type="module">
      const bell = document.getElementById("bellIcon");

      let ringAnimation;

      const cancelAnimation = () => {
        document.getElementById("notificationCount")?.remove();
        if (ringAnimation) {
          ringAnimation.cancel();
          ringAnimation = null;
          bell.removeEventListener("click", cancelAnimation);
        }
      };

      const animateBell = () => {
        const keyframes = [{
            transform: 'scale(1) rotate(0deg)'
          },
          {
            transform: 'scale(1.1) rotate(-15deg)'
          },
          {
            transform: 'scale(1.1) rotate(15deg)'
          },
          {
            transform: 'scale(1.1) rotate(-15deg)'
          },
          {
            transform: 'scale(1.1) rotate(15deg)'
          },
          {
            transform: 'scale(1) rotate(0deg)'
          }
        ];

        ringAnimation = bell.animate(
          keyframes, {
            duration: 1000,
            iterations: Infinity,
            easing: 'ease-in-out'
          }
        );

        bell.addEventListener("click", cancelAnimation);
      }

      if ({{ $user->unreadNotifications->count() > 0 ? 'true' : 'false' }}) {
        animateBell();
      }

      Echo.private('Modules.EBilling.Models.User.{{ $user->id }}')
        .notification((notification) => {
          new Toast({
            body: notification.formatted,
            className: 'border-0 bg-primary text-white',
            btnCloseWhite: true,
            placement: "bottom-right"
          }).show();

          if (!ringAnimation) {
            const notificationCount = document.getElementById("notificationCount");
            if (!notificationCount) {
              const badge = document.createElement("span");
              badge.className = "badge bg-red";
              badge.id = "notificationCount";
              bell.parentNode.appendChild(badge);
            }
            animateBell();
          }
        });
    </script>
  @endpush
@endif
