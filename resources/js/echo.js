import Echo from "laravel-echo";

import Pusher from "pusher-js";
window.Pusher = Pusher;

const echoConfig = JSON.parse(
  document.getElementById("echo-config").textContent
);

if (!echoConfig) {
  console.error("Echo configuration not found");
}

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
