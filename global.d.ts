import * as tabler from "@tabler/core";
import axios from "axios";

declare global {
  interface Window {
    // app bootstrap
    axios: typeof axios;

    // tabler
    tabler: typeof tabler;
  }
}
