import * as tabler from "@tabler/core";
import axios from "axios";
import { visit } from "@hotwired/turbo";

declare global {
  interface Window {
    // app bootstrap
    axios: typeof axios;

    // tabler
    tabler: typeof tabler;

    // turbo drive
    visit: typeof visit;
  }
}
