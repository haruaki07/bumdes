import * as tabler from "@tabler/core";
import axios from "axios";
import TomSelect from "tom-select";

declare global {
  interface Window {
    // app bootstrap
    axios: typeof axios;

    // tabler
    tabler: typeof tabler;
    TomSelect: typeof TomSelect;
  }
}
