import "./bootstrap";
import { Datatable } from "./components";

Datatable.init("[data-datatable-id]");

document.addEventListener("turbo:load", () => {
  Datatable.init("[data-datatable-id]");
});

document.addEventListener("turbo:before-render", () => {
  Datatable.initialized.forEach((datatableId) => {
    const dtInstance = document.querySelector(
      `[data-datatable-id="${datatableId}"]`
    )?.dt;
    if (dtInstance) {
      dtInstance.destroy();
    }
  });
});
