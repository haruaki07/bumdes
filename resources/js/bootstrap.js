import axios from "axios";
import * as anime from "./anime";
import * as QRCode from "qrcode";
import JsBarcode from "jsbarcode";
import IMask from "imask";
import ApexCharts from "apexcharts";

import { tabler, TomSelect } from "./tabler-init";
import {
  Datatable,
  Dropzone,
  AddressModal,
  LoadingButton,
  Toast,
} from "./components";

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */
import "./echo";

window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

window.anime = anime;
window.QRCode = QRCode;
window.JsBarcode = JsBarcode;

window.tabler = tabler;
window.bootstrap = tabler.bootstrap;

window.Datatable = Datatable;
window.Dropzone = Dropzone;
window.TomSelect = TomSelect;
window.AddressModal = AddressModal;
window.LoadingButton = LoadingButton;
window.Toast = Toast;
window.IMask = IMask;
window.ApexCharts = ApexCharts;
