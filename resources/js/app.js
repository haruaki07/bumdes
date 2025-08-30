import "./bootstrap";
import "./bootbox";
import "./helpers";
import { LoadingButton } from "./components";

document.querySelectorAll('[data-bs-toggle="loading-button"]').forEach((el) => {
  el._loadingButtonInstance = new LoadingButton(el);
});
