import * as tabler from "@tabler/core/dist/js/tabler.esm";
import "@tabler/core/dist/js/tabler-theme.esm";

// re-initialize tooltips, popovers, and dropdowns on turbo:load
document.addEventListener("turbo:load", () => {
  const tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  );
  tooltipTriggerList.forEach((tooltipTriggerEl) => {
    new tabler.Tooltip(tooltipTriggerEl);
  });

  const dropdownTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="dropdown"]')
  );
  dropdownTriggerList.forEach((dropdownTriggerEl) => {
    new tabler.Dropdown(dropdownTriggerEl);
  });

  const popoverTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="popover"]')
  );
  popoverTriggerList.forEach((popoverTriggerEl) => {
    new tabler.Popover(popoverTriggerEl);
  });
});

export { tabler };
