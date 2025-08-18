class Bootbox {
  constructor() {
    this.VERSION = "0.0.1";
    this.locales = {
      en: {
        OK: "OK",
        CANCEL: "Cancel",
        CONFIRM: "OK",
      },
    };

    this.defaults = {
      locale: "en",
      backdrop: "static",
      animate: true,
      className: null,
      closeButton: true,
      show: true,
      container: "body",
      value: "",
      inputType: "text",
      errorMessage: null,
      swapButtonOrder: false,
      centerVertical: false,
      multiple: false,
      scrollable: false,
      reusable: false,
      relatedTarget: null,
      size: null,
      id: null,
    };

    this.templates = {
      dialog:
        '<div class="bootbox modal" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-body"><div class="bootbox-body"></div></div></div></div></div>',
      header: '<div class="modal-header"><h5 class="modal-title"></h5></div>',
      footer: '<div class="modal-footer"></div>',
      closeButton:
        '<button type="button" class="bootbox-close-button close btn-close" aria-hidden="true" aria-label="Close"></button>',
      form: '<form class="bootbox-form"></form>',
      button: '<button type="button" class="btn"></button>',
      option: '<option value=""></option>',
      promptMessage: '<div class="bootbox-prompt-message"></div>',
      inputs: {
        text: '<input class="bootbox-input bootbox-input-text form-control" autocomplete="off" type="text" />',
        textarea:
          '<textarea class="bootbox-input bootbox-input-textarea form-control"></textarea>',
        email:
          '<input class="bootbox-input bootbox-input-email form-control" autocomplete="off" type="email" />',
        select:
          '<select class="bootbox-input bootbox-input-select form-select"></select>',
        checkbox:
          '<div class="form-check checkbox"><label class="form-check-label"><input class="form-check-input bootbox-input bootbox-input-checkbox" type="checkbox" /></label></div>',
        radio:
          '<div class="form-check radio"><label class="form-check-label"><input class="form-check-input bootbox-input bootbox-input-radio" type="radio" name="bootbox-radio" /></label></div>',
        date: '<input class="bootbox-input bootbox-input-date form-control" autocomplete="off" type="date" />',
        time: '<input class="bootbox-input bootbox-input-time form-control" autocomplete="off" type="time" />',
        number:
          '<input class="bootbox-input bootbox-input-number form-control" autocomplete="off" type="number" />',
        password:
          '<input class="bootbox-input bootbox-input-password form-control" autocomplete="off" type="password" />',
        range:
          '<input class="bootbox-input bootbox-input-range form-range" autocomplete="off" type="range" />',
      },
    };

    this.init();
  }

  init() {
    // Initialize the global bootbox instance
    if (typeof window !== "undefined") {
      window.bootbox = this;
    }
  }

  // Utility functions
  createElement(html) {
    const template = document.createElement("template");
    template.innerHTML = html.trim();
    return template.content.firstElementChild ?? template.content.firstChild;
  }

  extend(target, ...sources) {
    sources.forEach((source) => {
      if (source) {
        Object.keys(source).forEach((key) => {
          if (source[key] !== undefined) {
            target[key] = source[key];
          }
        });
      }
    });
    return target;
  }

  deepExtend() {
    let extended = {};
    let deep = false;
    let i = 0;
    let length = arguments.length;

    // check if a deep merge
    if (Object.prototype.toString.call(arguments[0]) === "[object Boolean]") {
      deep = arguments[0];
      i++;
    }

    // merge the object into the extended object
    let merge = function (obj) {
      for (let prop in obj)
        if (Object.prototype.hasOwnProperty.call(obj, prop)) {
          // if deep merge and property is an object, merge properties
          if (
            deep &&
            Object.prototype.toString.call(obj[prop]) === "[object Object]"
          )
            extended[prop] = extend(true, extended[prop], obj[prop]);
          else extended[prop] = obj[prop];
        }
    };

    // loop through each object and conduct a merge
    for (; i < length; i++) {
      let obj = arguments[i];
      merge(obj);
    }

    return extended;
  }

  each(collection, iterator) {
    if (Array.isArray(collection)) {
      collection.forEach((value, index) => iterator(index, value, index));
    } else {
      Object.keys(collection).forEach((key, index) =>
        iterator(key, collection[key], index)
      );
    }
  }

  getKeyLength(obj) {
    return Object.keys(obj).length;
  }

  getText(key, locale) {
    const labels = this.locales[locale];
    return labels ? labels[key] : this.locales.en[key];
  }

  createLabels(labels, locale) {
    const buttons = {};
    labels.forEach((argument) => {
      const key = argument.toLowerCase();
      const value = argument.toUpperCase();
      buttons[key] = {
        label: this.getText(value, locale),
      };
    });
    return buttons;
  }

  // Public API methods
  locales(name) {
    return name ? this.locales[name] : this.locales;
  }

  addLocale(name, values) {
    ["OK", "CANCEL", "CONFIRM"].forEach((v) => {
      if (!values[v]) {
        throw new Error(`Please supply a translation for "${v}"`);
      }
    });

    this.locales[name] = {
      OK: values.OK,
      CANCEL: values.CANCEL,
      CONFIRM: values.CONFIRM,
    };

    return this;
  }

  removeLocale(name) {
    if (name !== "en") {
      delete this.locales[name];
    } else {
      throw new Error(
        '"en" is used as the default and fallback locale and cannot be removed.'
      );
    }
    return this;
  }

  setLocale(name) {
    return this.setDefaults("locale", name);
  }

  setDefaults() {
    const values = {};
    if (arguments.length === 2) {
      values[arguments[0]] = arguments[1];
    } else {
      Object.assign(values, arguments[0]);
    }
    this.extend(this.defaults, values);
    return this;
  }

  hideAll() {
    document.querySelectorAll(".bootbox").forEach((modal) => {
      this.hideModal(modal);
    });
    return this;
  }

  // Core dialog functionality
  dialog(options) {
    if (typeof bootstrap === "undefined" || !bootstrap.Modal) {
      throw new Error(
        "Bootstrap Modal is not defined. Please include Bootstrap JavaScript library."
      );
    }

    options = this.sanitize(options);

    const dialog = this.createElement(this.templates.dialog);
    const innerDialog = dialog.querySelector(".modal-dialog");
    const body = dialog.querySelector(".modal-body");
    const header = this.createElement(this.templates.header);
    const footer = this.createElement(this.templates.footer);
    const buttons = options.buttons;

    const callbacks = {
      onEscape: options.onEscape,
    };

    body
      .querySelector(".bootbox-body")
      .append(
        typeof options.message === "string"
          ? this.createElement(options.message)
          : options.message
      );

    // Create buttons
    if (this.getKeyLength(options.buttons) > 0) {
      this.each(buttons, (key, b) => {
        const button = this.createElement(this.templates.button);
        button.dataset.bbHandler = key;
        button.className = `btn ${b.className || ""}`;

        switch (key) {
          case "ok":
          case "confirm":
            button.classList.add("bootbox-accept");
            break;
          case "cancel":
            button.classList.add("bootbox-cancel");
            break;
        }

        button.innerHTML = b.label;

        if (b.id) {
          button.id = b.id;
        }

        if (b.disabled === true) {
          button.disabled = true;
        }

        footer.appendChild(button);
        callbacks[key] = b.callback;
      });

      body.after(footer);
    }

    // Apply options
    if (options.animate === true) {
      dialog.classList.add("fade");
    }

    if (options.className) {
      dialog.classList.add(options.className);
    }

    if (options.id) {
      dialog.id = options.id;
    }

    if (options.size) {
      switch (options.size) {
        case "small":
        case "sm":
          innerDialog.classList.add("modal-sm");
          break;
        case "large":
        case "lg":
          innerDialog.classList.add("modal-lg");
          break;
        case "extra-large":
        case "xl":
          innerDialog.classList.add("modal-xl");
          break;
      }
    }

    if (options.scrollable) {
      innerDialog.classList.add("modal-dialog-scrollable");
    }

    if (options.title || options.closeButton) {
      if (options.title) {
        header.querySelector(".modal-title").innerHTML = options.title;
      } else {
        header.classList.add("border-0");
      }

      if (options.closeButton) {
        const closeButton = this.createElement(this.templates.closeButton);
        header.appendChild(closeButton);
      }

      body.before(header);
    }

    if (options.centerVertical) {
      innerDialog.classList.add("modal-dialog-centered");
    }

    // Add event listeners
    this.bindEvents(dialog, callbacks, options);

    // Add to DOM
    const container =
      options.container === "body"
        ? document.body
        : document.querySelector(options.container);
    container.appendChild(dialog);

    // Initialize Bootstrap modal
    const modal = new bootstrap.Modal(dialog, {
      backdrop: options.backdrop,
      keyboard: false,
    });

    if (options.show) {
      modal.show();
    }

    // Store modal instance
    dialog.bootboxModal = modal;
    return dialog;
  }

  bindEvents(dialog, callbacks, options) {
    const self = this;

    // Button click handlers
    dialog.addEventListener("click", (e) => {
      const button = e.target.closest(".modal-footer button:not(.disabled)");
      if (button) {
        const callbackKey = button.dataset.bbHandler;
        if (callbackKey && callbacks[callbackKey]) {
          this.processCallback(e, dialog, callbacks[callbackKey]);
        }
      }

      // Close button handler
      if (e.target.closest(".bootbox-close-button")) {
        this.processCallback(e, dialog, callbacks.onEscape);
      }
    });

    // Escape key handler
    dialog.addEventListener("keyup", (e) => {
      if (e.key === "Escape") {
        this.processCallback(e, dialog, callbacks.onEscape);
      }
    });

    // Bootstrap event handlers
    if (!options.reusable) {
      dialog.addEventListener("hide.bs.modal", () => {
        this.unbindModal(dialog);
      });

      dialog.addEventListener("hidden.bs.modal", () => {
        this.destroyModal(dialog);
      });
    }

    // Custom event handlers
    if (options.onHide && typeof options.onHide === "function") {
      dialog.addEventListener("hide.bs.modal", options.onHide);
    }

    if (options.onHidden && typeof options.onHidden === "function") {
      dialog.addEventListener("hidden.bs.modal", options.onHidden);
    }

    if (options.onShow && typeof options.onShow === "function") {
      dialog.addEventListener("show.bs.modal", options.onShow);
    }

    if (options.onShown && typeof options.onShown === "function") {
      dialog.addEventListener("shown.bs.modal", options.onShown);
    }

    // Focus primary button on shown
    dialog.addEventListener("shown.bs.modal", () => {
      const primaryButton = dialog.querySelector(".bootbox-accept");
      if (primaryButton) {
        primaryButton.focus();
      }
    });
  }

  processCallback(e, dialog, callback) {
    e.stopPropagation();
    e.preventDefault();

    const preserveDialog =
      typeof callback === "function" && callback.call(dialog, e) === false;

    if (!preserveDialog) {
      this.hideModal(dialog);
    }
  }

  hideModal(dialog) {
    if (dialog.bootboxModal) {
      dialog.bootboxModal.hide();
    }
  }

  unbindModal(dialog) {
    // Remove event listeners if needed
  }

  destroyModal(dialog) {
    if (dialog.parentNode) {
      dialog.parentNode.removeChild(dialog);
    }
  }

  sanitize(options) {
    if (typeof options !== "object") {
      throw new Error("Please supply an object of options");
    }

    if (!options.message) {
      throw new Error('"message" option must not be null or an empty string.');
    }

    // Merge with defaults
    options = this.deepExtend({}, this.defaults, options);

    // Validate backdrop
    if (!options.backdrop) {
      options.backdrop =
        options.backdrop === false || options.backdrop === 0 ? false : "static";
    } else {
      options.backdrop =
        typeof options.backdrop === "string" &&
        options.backdrop.toLowerCase() === "static"
          ? "static"
          : true;
    }

    // Ensure buttons object exists
    if (!options.buttons) {
      options.buttons = {};
    }

    const buttons = options.buttons;
    const total = this.getKeyLength(buttons);

    this.each(buttons, (key, button, index) => {
      if (typeof button === "function") {
        button = buttons[key] = { callback: button };
      }

      if (typeof button !== "object") {
        throw new Error(`button with key "${key}" must be an object`);
      }

      if (!button.label) {
        button.label = key;
      }

      if (!button.className) {
        let isPrimary = false;
        if (options.swapButtonOrder) {
          isPrimary = index === 0;
        } else {
          isPrimary = index === total - 1;
        }

        if (total <= 2 && isPrimary) {
          button.className = "btn-primary";
        } else {
          button.className = "btn-secondary btn-default";
        }
      }
    });

    return options;
  }

  // Helper methods for alert, confirm, prompt
  alert() {
    const options = this.mergeDialogOptions(
      "alert",
      ["ok"],
      ["message", "callback"],
      arguments
    );

    if (options.callback && typeof options.callback !== "function") {
      throw new Error(
        'alert requires the "callback" property to be a function when provided'
      );
    }

    options.buttons.ok.callback = options.onEscape = function () {
      if (typeof options.callback === "function") {
        return options.callback.call(this);
      }
      return true;
    };

    return this.dialog(options);
  }

  confirm() {
    const options = this.mergeDialogOptions(
      "confirm",
      ["cancel", "confirm"],
      ["message", "callback"],
      arguments
    );

    if (typeof options.callback !== "function") {
      throw new Error("confirm requires a callback");
    }

    options.buttons.cancel.callback = options.onEscape = function () {
      return options.callback.call(this, false);
    };

    options.buttons.confirm.callback = function () {
      return options.callback.call(this, true);
    };

    return this.dialog(options);
  }

  prompt() {
    let input;

    const form = this.createElement(this.templates.form);
    const options = this.mergeDialogOptions(
      "prompt",
      ["cancel", "confirm"],
      ["title", "callback"],
      arguments
    );

    if (!options.value) {
      options.value = this.defaults.value;
    }

    if (!options.inputType) {
      options.inputType = this.defaults.inputType;
    }

    const shouldShow =
      options.show === undefined ? this.defaults.show : options.show;
    options.show = false;

    // Cancel handler
    options.buttons.cancel.callback = options.onEscape = function () {
      return options.callback.call(this, null);
    };

    // Confirm handler
    options.buttons.confirm.callback = function () {
      let value;

      if (options.inputType === "checkbox") {
        value = Array.from(input.querySelectorAll("input:checked")).map(
          (el) => el.value
        );
        if (value.length === 0 && options.required === true) {
          return false;
        }
      } else if (options.inputType === "radio") {
        const checked = input.querySelector("input:checked");
        value = checked ? checked.value : null;
      } else {
        const el = input;
        if (el.checkValidity && !el.checkValidity()) {
          if (options.errorMessage) {
            el.setCustomValidity(options.errorMessage);
          }
          if (el.reportValidity) {
            el.reportValidity();
          }
          return false;
        } else {
          if (options.inputType === "select" && options.multiple === true) {
            value = Array.from(input.selectedOptions).map((opt) => opt.value);
          } else {
            value = input.value;
          }
        }
      }

      return options.callback.call(this, value);
    };

    // Validation
    if (!options.title) {
      throw new Error("prompt requires a title");
    }

    if (typeof options.callback !== "function") {
      throw new Error("prompt requires a callback");
    }

    if (!this.templates.inputs[options.inputType]) {
      throw new Error("Invalid prompt type");
    }

    // Create input
    input = this.createElement(this.templates.inputs[options.inputType]);
    input = this.setupPromptInput(input, options) || input;
    form.appendChild(input);

    // Form submit handler
    form.addEventListener("submit", (e) => {
      e.preventDefault();
      e.stopPropagation();
      const confirmButton = dialog.querySelector(".bootbox-accept");
      if (confirmButton) {
        confirmButton.click();
      }
    });

    // Set message
    if (options.message && options.message.trim() !== "") {
      const message = this.createElement(this.templates.promptMessage);
      message.innerHTML = options.message;
      form.insertBefore(message, form.firstChild);
      options.message = form;
    } else {
      options.message = form;
    }

    // Generate dialog
    const dialog = this.dialog(options);

    // Focus input on shown
    dialog.addEventListener("shown.bs.modal", () => {
      const inputToFocus = dialog.querySelector(".bootbox-input");
      if (inputToFocus) {
        inputToFocus.focus();
      }
    });

    if (shouldShow === true) {
      dialog.bootboxModal.show();
    }

    return dialog;
  }

  setupPromptInput(input, options) {
    switch (options.inputType) {
      case "text":
      case "textarea":
      case "email":
      case "password":
        input.value = options.value;
        if (options.placeholder) input.placeholder = options.placeholder;
        if (options.pattern) input.pattern = options.pattern;
        if (options.maxlength) input.maxLength = options.maxlength;
        if (options.required) input.required = true;
        if (options.rows && !isNaN(parseInt(options.rows))) {
          if (options.inputType === "textarea") {
            input.rows = options.rows;
          }
        }
        break;

      case "date":
      case "time":
      case "number":
      case "range":
        input.value = options.value;
        if (options.placeholder) input.placeholder = options.placeholder;
        if (options.pattern) input.pattern = options.pattern;
        if (options.required) input.required = true;
        if (options.step) input.step = options.step;
        if (options.min !== undefined) input.min = options.min;
        if (options.max !== undefined) input.max = options.max;
        break;

      case "select":
        const inputOptions = options.inputOptions || [];
        if (!Array.isArray(inputOptions)) {
          throw new Error("Please pass an array of input options");
        }
        if (!inputOptions.length) {
          throw new Error(
            'prompt with "inputType" set to "select" requires at least one option'
          );
        }
        if (options.required) input.required = true;
        if (options.multiple) input.multiple = true;

        const groups = {};

        inputOptions.forEach((option) => {
          if (option.value === undefined || option.text === undefined) {
            throw new Error(
              'each option needs a "value" property and a "text" property'
            );
          }

          let elem = input;

          if (option.group) {
            if (!groups[option.group]) {
              const optGroup = document.createElement("optgroup");
              optGroup.label = option.group;
              groups[option.group] = optGroup;
              input.appendChild(optGroup);
            }
            elem = groups[option.group];
          }

          const optionEl = this.createElement(this.templates.option);
          optionEl.value = option.value;
          optionEl.textContent = option.text;

          if (options.value.includes(option.value)) {
            optionEl.selected = true;
          }

          elem.appendChild(optionEl);
        });

        break;

      case "checkbox":
        const checkboxValues = Array.isArray(options.value)
          ? options.value
          : [options.value];
        const checkboxOptions = options.inputOptions || [];
        if (!checkboxOptions.length) {
          throw new Error(
            'prompt with "inputType" set to "checkbox" requires at least one option'
          );
        }

        const container = document.createElement("div");
        container.className = "bootbox-checkbox-list";

        checkboxOptions.forEach((option) => {
          if (option.value === undefined || option.text === undefined) {
            throw new Error(
              'each option needs a "value" property and a "text" property'
            );
          }

          const checkbox = this.createElement(this.templates.inputs.checkbox);
          checkbox.querySelector("input").value = option.value;
          checkbox
            .querySelector("label")
            .appendChild(document.createTextNode(option.text));

          checkboxValues.forEach((value) => {
            if (value === option.value) {
              checkbox.querySelector("input").checked = true;
            }
          });

          container.appendChild(checkbox);
        });

        return container;

      case "radio":
        if (options.value !== undefined && Array.isArray(options.value)) {
          throw new Error(
            'prompt with "inputType" set to "radio" requires a single, non-array value for "value"'
          );
        }

        const radioOptions = options.inputOptions || [];
        if (!radioOptions.length) {
          throw new Error(
            'prompt with "inputType" set to "radio" requires at least one option'
          );
        }

        // Replace input with container
        const radioContainer = document.createElement("div");
        radioContainer.className = "bootbox-radiobutton-list";

        let checkFirstRadio = true;

        radioOptions.forEach((option) => {
          if (option.value === undefined || option.text === undefined) {
            throw new Error(
              'each option needs a "value" property and a "text" property'
            );
          }

          const radio = this.createElement(this.templates.inputs.radio);
          radio.querySelector("input").value = option.value;
          radio
            .querySelector("label")
            .appendChild(document.createTextNode(option.text));

          if (options.value !== undefined && option.value === options.value) {
            radio.querySelector("input").checked = true;
            checkFirstRadio = false;
          }

          radioContainer.appendChild(radio);
        });

        if (checkFirstRadio) {
          const firstRadio = radioContainer.querySelector(
            'input[type="radio"]'
          );
          if (firstRadio) firstRadio.checked = true;
        }

        return radioContainer;
    }
  }

  mergeDialogOptions(className, labels, properties, args) {
    let locale;
    if (args && args[0]) {
      locale = args[0].locale || this.defaults.locale;
      const swapButtons =
        args[0].swapButtonOrder || this.defaults.swapButtonOrder;
      if (swapButtons) {
        labels = labels.reverse();
      }
    }

    const baseOptions = {
      className: `bootbox-${className}`,
      buttons: this.createLabels(labels, locale),
    };

    return this.validateButtons(
      this.mergeArguments(baseOptions, args, properties),
      labels
    );
  }

  mergeArguments(defaults, args, properties) {
    return this.deepExtend({}, defaults, this.mapArguments(args, properties));
  }

  mapArguments(args, properties) {
    const argsLength = args.length;
    const options = {};

    if (argsLength < 1 || argsLength > 2) {
      throw new Error("Invalid argument length");
    }

    if (argsLength === 2 || typeof args[0] === "string") {
      options[properties[0]] = args[0];
      options[properties[1]] = args[1];
    } else {
      Object.assign(options, args[0]);
    }

    return options;
  }

  validateButtons(options, buttons) {
    const allowedButtons = {};
    buttons.forEach((value) => {
      allowedButtons[value] = true;
    });

    Object.keys(options.buttons).forEach((key) => {
      if (allowedButtons[key] === undefined) {
        throw new Error(
          `button key "${key}" is not allowed (options are ${buttons.join(
            " "
          )})`
        );
      }
    });

    return options;
  }
}

// Create and export the bootbox instance
const bootbox = new Bootbox();

// Export for different module systems
if (typeof define === "function" && define.amd) {
  define([], () => bootbox);
} else if (typeof exports === "object") {
  module.exports = bootbox;
} else if (typeof window !== "undefined") {
  window.bootbox = bootbox;
}

export default bootbox;
