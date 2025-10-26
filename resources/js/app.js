import "./bootstrap";
import "./bootbox";
import "./helpers";
import { LoadingButton } from "./components";

document.querySelectorAll('[data-bs-toggle="loading-button"]').forEach((el) => {
  el._loadingButtonInstance = new LoadingButton(el);
});

document.querySelectorAll("input[data-mask-phone]").forEach((input) => {
  if (input._imask) return;

  input._imask = window.IMask(input, {
    mask: [
      { mask: "0000-0000-0000", startsWith: "08", lazy: true },
      { mask: "0000-000-000", startsWith: "08", lazy: true },
      { mask: "+62 000-0000-0000", startsWith: "+62", lazy: true },
    ],
    dispatch: (appended, dynamicMasked) => {
      const value = (dynamicMasked.value + appended).replace(/\D/g, "");
      if (value.startsWith("62")) return dynamicMasked.compiledMasks[2];
      if (value.startsWith("08")) {
        return value.length <= 10
          ? dynamicMasked.compiledMasks[1]
          : dynamicMasked.compiledMasks[0];
      }
      return dynamicMasked.compiledMasks[0];
    },
  });
});

document.addEventListener("wheel", function (event) {
  if (document.activeElement.type === "number") {
    document.activeElement.blur();
  }
});

document.querySelectorAll("input[data-mask-currency]").forEach((input) => {
  if (input._imask) return;

  input._imask = window.IMask(input, {
    mask: Number,
    thousandsSeparator: ".",
    radix: ",", // decimal separator
    scale: 0, // no decimals
  });
});

// auto dismissable alerts
document
  .querySelectorAll('.alert.alert-dismissible[role="alert"][data-bs-timeout]')
  .forEach(function (alert) {
    const timeout = parseInt(alert.getAttribute("data-bs-timeout")) || 5000;
    setTimeout(function () {
      const bsAlert = tabler.Alert.getOrCreateInstance(alert);
      anime.waapi.animate(alert, {
        duration: 500,
        opacity: 0,
        onComplete: () => bsAlert.close(),
      });
    }, timeout);
  });
