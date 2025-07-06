class Datatable {
  constructor(id) {
    this.id = id;
    this.tableEl = document.querySelector(`[data-datatable-id="${id}"]`);
    if (!this.tableEl) throw new Error(`Table with id "${id}" not found`);

    this.limitSelectEl = document.querySelector(
      `[data-datatable-limit="${id}"]`
    );
    this.searchInputEl = document.querySelector(
      `[data-datatable-search="${id}"]`
    );
    this.sortFieldsEl = document.querySelectorAll(
      `[data-datatable-sort="${id}"]`
    );

    try {
      this.req = JSON.parse(this.tableEl.dataset.datatableRequest);
    } catch (e) {
      throw new Error('Invalid JSON in data attribute "datatableRequest"');
    }

    this.handleLimitChange = this.handleLimitChange.bind(this);
    this.handleSearchKeyUp = this.handleSearchKeyUp.bind(this);
    this.handleSortClick = this.handleSortClick.bind(this);

    if (this.limitSelectEl) {
      this.limitSelectEl.addEventListener("input", this.handleLimitChange);
    }

    if (this.searchInputEl) {
      this.searchInputEl.addEventListener("keyup", this.handleSearchKeyUp);
    }

    this.sortFieldsEl.forEach((el) => {
      el.addEventListener("click", this.handleSortClick);
    });
  }

  handleLimitChange(e) {
    this.req.limit = e.target.value;
    if (this.req.page) this.req.page = 1;
    this.submit();
  }

  handleSearchKeyUp(e) {
    if (e.key === "Enter") {
      this.req.search = e.target.value;
      this.submit();
    }
  }

  handleSortClick(e) {
    const nextDirection = e.target.dataset.datatableSortNextDirection;
    const sortField = e.target.dataset.datatableSortField;
    if (nextDirection && sortField) {
      this.req.sort = `${sortField}:${nextDirection}`;
    } else {
      delete this.req.sort;
    }
    this.submit();
  }

  destroy() {
    if (this.limitSelectEl) {
      this.limitSelectEl.removeEventListener("input", this.handleLimitChange);
    }

    if (this.searchInputEl) {
      this.searchInputEl.removeEventListener("keyup", this.handleSearchKeyUp);
    }

    this.sortFieldsEl.forEach((el) => {
      el.removeEventListener("click", this.handleSortClick);
    });

    Datatable.initialized.delete(this.id);
  }

  submit() {
    const url = new URL(window.location.href);

    // remove datatable preserved search params
    for (const key of url.searchParams.keys()) {
      if (key === "search" || key === "sort" || key === "limit") {
        url.searchParams.delete(key);
      }
    }

    for (const [key, value] of Object.entries(this.req)) {
      if (!value) continue;

      url.searchParams.set(key, value);
    }

    window.location.href = this.tableEl.dataset.datatableUrl + url.search;
  }

  static initialized = new Set();

  static init(selector) {
    document.querySelectorAll(selector).forEach((el) => {
      const id = el.dataset.datatableId;
      if (!Datatable.initialized.has(id)) {
        let dt = new Datatable(id);
        el.dt = dt;
        Datatable.initialized.add(id);
      }
    });
  }
}

export { Datatable };
