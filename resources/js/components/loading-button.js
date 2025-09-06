class LoadingButton {
  constructor(element, config = {}) {
    this._element = element;
    this._config = {
      spinnerType: element.dataset.bsSpinnerType || "border",
      disabledOnLoading:
        element.dataset.bsDisabledOnLoading === "true" || false,
      timeout: element.dataset.bsTimeout
        ? parseInt(element.dataset.bsTimeout, 10)
        : false,
      ...config,
    };
    this._isLoading = false;
  }

  start() {
    if (this._isLoading) return;
    this._isLoading = true;

    if (
      this._config.spinnerType === "border" ||
      this._config.spinnerType === "grow"
    ) {
      const spinner = document.createElement("span");
      spinner.classList.add(
        "btn-loading-spinner",
        `spinner-${this._config.spinnerType}`,
        `spinner-${this._config.spinnerType}-sm`,
        "me-2"
      );
      spinner.setAttribute("role", "status");
      spinner.setAttribute("aria-hidden", "true");
      this._element.prepend(spinner);
    } else if (this._config.spinnerType === "dots") {
      const spinner = document.createElement("span");
      spinner.classList.add("btn-loading-spinner", "animated-dots");
      this._element.appendChild(spinner);
    }

    if (this._config.disabledOnLoading) {
      this._element.disabled = true;
    }

    this._element.dispatchEvent(new Event("start.bs.loading-button"));

    if (this._config.timeout) {
      setTimeout(() => this.stop(), this._config.timeout);
    }
  }

  stop() {
    if (!this._isLoading) return;
    this._isLoading = false;

    const spinner = this._element.querySelector(".btn-loading-spinner");
    if (spinner) {
      spinner.remove();
    }

    if (this._config.disabledOnLoading) {
      this._element.disabled = false;
    }

    this._element.dispatchEvent(new Event("stop.bs.loading-button"));
  }

  dispose() {
    this.stop();
    this._element = null;
    this._config = null;
  }

  static getInstance(element) {
    return element._loadingButtonInstance || null;
  }
}

export { LoadingButton };
