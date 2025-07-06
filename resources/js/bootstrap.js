import axios from "axios";
import { tabler } from "./tabler-init";
import { Datatable, Dropzone } from "./components";

window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

window.tabler = tabler;

window.Datatable = Datatable;
window.Dropzone = Dropzone;
