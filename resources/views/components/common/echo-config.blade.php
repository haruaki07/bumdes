@once
  @php
    $pusherConfig = config('broadcasting.connections.pusher');
    $config = [
        'key' => $pusherConfig['key'] ?? null,
        'cluster' => $pusherConfig['options']['cluster'] ?? null,
        'host' => 'ws-' . $pusherConfig['options']['cluster'] . '.pusher.com',
        'port' => $pusherConfig['options']['port'] ?? null,
    ];
  @endphp
  <script type="application/json" id="echo-config">{!! json_encode($config) !!}</script>
@endonce
