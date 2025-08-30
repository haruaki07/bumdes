@if (session('message'))
  <div class="alert alert-info" role="alert">
    <div class="d-flex gap-2">
      <div class="alert-icon">
        <i class="ti ti-info-circle"></i>
      </div>
      <div class="alert-description">{{ session('message') }}</div>
    </div>
  </div>
@elseif(session('success'))
  <div class="alert alert-important alert-success alert-dismissible" role="alert" data-bs-timeout="5000">
    <div class="d-flex gap-2">
      <div class="alert-icon">
        <i class="ti ti-check"></i>
      </div>
      <div class="alert-description">{{ session('success') }}</div>
    </div>
    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
  </div>
@elseif(session('warning'))
  <div class="alert alert-important alert-warning alert-dismissible" role="alert"data-bs-timeout="5000">
    <div class="d-flex gap-2">
      <div class="alert-icon">
        <i class="ti ti-alert-triangle"></i>
      </div>
      <div class="alert-description">{{ session('warning') }}</div>
    </div>
    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
  </div>
@elseif(session('error'))
  <div class="alert alert-important alert-danger alert-dismissible" role="alert"data-bs-timeout="5000">
    <div class="d-flex gap-2">
      <div class="alert-icon">
        <i class="ti ti-alert-circle"></i>
      </div>
      <div class="alert-description">{{ session('error') }}</div>
    </div>
    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
  </div>
@endif
@if ($errors->any())
  @foreach ($errors->all() as $error)
    <div class="alert alert-important alert-danger alert-dismissible" role="alert"data-bs-timeout="5000">
      <div class="d-flex gap-2">
        <div class="alert-icon">
          <i class="ti ti-alert-circle"></i>
        </div>
        <div class="alert-description">{{ $error }}</div>
      </div>
      <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
  @endforeach
@endif


@pushIf($errors->any() || session('message') || session('success') || session('warning') || session('error'), 'js')
<script>
  document.addEventListener("DOMContentLoaded", function() {
    const alertList = document.querySelectorAll('.alert.alert-dismissible[role="alert"][data-bs-timeout]');
    alertList.forEach(function(alert) {
      const timeout = parseInt(alert.getAttribute('data-bs-timeout')) || 5000;
      setTimeout(function() {
        const bsAlert = tabler.Alert.getOrCreateInstance(alert);
        anime.waapi.animate(alert, {
          duration: 500,
          opacity: 0,
          onComplete: () => bsAlert.close()
        });
      }, timeout);
    });
  });
</script>
@endPushIf
