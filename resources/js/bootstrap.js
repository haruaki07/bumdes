import axios from "axios";

import { tabler, TomSelect } from "./tabler-init";
import { Datatable, Dropzone, AddressModal } from "./components";

window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

window.tabler = tabler;
window.bootstrap = tabler.bootstrap;

window.Datatable = Datatable;
window.Dropzone = Dropzone;
window.TomSelect = TomSelect;
window.AddressModal = AddressModal;
