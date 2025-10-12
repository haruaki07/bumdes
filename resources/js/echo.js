import Echo from "laravel-echo";

import Pusher from "pusher-js";
window.Pusher = Pusher;

const echoConfigElement = document.getElementById("echo-config");
if (echoConfigElement) {
  const echoConfig = JSON.parse(echoConfigElement.textContent);
  window.Echo = new Echo({
    broadcaster: "pusher",
    key: echoConfig.key,
    cluster: echoConfig.cluster,
    forceTLS: true,
    wsHost: echoConfig.host,
    wsPort: echoConfig.port,
    wssPort: echoConfig.port,
    enabledTransports: ["ws", "wss"],
  });
}
