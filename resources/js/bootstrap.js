import axios from "axios";
import { visit } from "@hotwired/turbo";
import { tabler } from "./tabler-init";

window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

window.tabler = tabler;
window.visit = visit;
