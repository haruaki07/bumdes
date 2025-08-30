import axios from "axios";
import * as anime from "./anime";
import * as QRCode from "qrcode";

import { tabler, TomSelect } from "./tabler-init";
import { Datatable, Dropzone, AddressModal, LoadingButton } from "./components";

window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

window.anime = anime;
window.QRCode = QRCode;

window.tabler = tabler;
window.bootstrap = tabler.bootstrap;

window.Datatable = Datatable;
window.Dropzone = Dropzone;
window.TomSelect = TomSelect;
window.AddressModal = AddressModal;
window.LoadingButton = LoadingButton;
