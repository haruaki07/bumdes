function humanBytes(bytes) {
  const sizes = ["Bytes", "KB", "MB", "GB", "TB"];
  let i = 0;
  while (bytes >= 1024 && i < sizes.length - 1) {
    bytes /= 1024;
    i++;
  }
  return `${parseFloat(bytes.toFixed(2))} ${sizes[i]}`;
}

class Dropzone {
  constructor(selector, options = {}) {
    this.selector = selector;
    this.options = {
      maxFileSize: options.maxFileSize || Infinity,
      ...options,
    };
    this.files = [];
    this.init();
  }

  init() {
    const inputElement = document.querySelector(this.selector);
    if (!inputElement) {
      console.error(`Element with selector ${this.selector} not found.`);
      return;
    }
    this.inputElement = inputElement;
    this.files = Array.from(inputElement.files || []);

    this.options.multiple = inputElement.multiple || false;

    const dropzoneElement = document.createElement("div");
    dropzoneElement.classList.add("dropzone");
    if (inputElement.classList.contains("is-invalid")) {
      dropzoneElement.classList.add("is-invalid");
    }
    dropzoneElement.innerHTML = `
      <div class="dropzone-area" tabindex="0" role="button">
        <span>Drag and drop files here or click to select</span>
      </div>
      <ul class="dropzone-file-list"></ul>
    `;

    inputElement.style.display = "none";
    inputElement.parentNode.insertBefore(dropzoneElement, inputElement);

    this.bindEvents(dropzoneElement, inputElement);
    this.handleFiles(
      this.files,
      dropzoneElement.querySelector(".dropzone-file-list")
    );
  }

  bindEvents(dropzoneElement, inputElement) {
    const dropzoneArea = dropzoneElement.querySelector(".dropzone-area");
    const fileList = dropzoneElement.querySelector(".dropzone-file-list");

    dropzoneArea.addEventListener("click", () => inputElement.click());

    dropzoneArea.addEventListener("dragover", (e) => {
      e.preventDefault();
      dropzoneArea.classList.add("dropzone-hover");
    });

    dropzoneArea.addEventListener("dragleave", () => {
      dropzoneArea.classList.remove("dropzone-hover");
    });

    dropzoneArea.addEventListener("drop", (e) => {
      e.preventDefault();
      dropzoneArea.classList.remove("dropzone-hover");
      this.handleFiles(e.dataTransfer.files, fileList);
    });

    inputElement.addEventListener("change", (e) => {
      this.handleFiles(e.target.files, fileList);
    });
  }

  handleFiles(files, fileList) {
    if (!files || files.length === 0) {
      // if no files selected, the input value is empty
      // restore the files into the input element
      this.syncInputFiles(this.files);
      return;
    }

    if (!this.options.multiple) {
      // If not multiple, clear existing files
      this.files = [];
      fileList.innerHTML = "";
    }

    Array.from(files).forEach((file) => {
      if (file.size > this.options.maxFileSize) {
        alert(
          `File size exceeds the maximum limit of ${humanBytes(
            this.options.maxFileSize
          )}.`
        );
        // remove the invalid file from the input element
        const inputFiles = Array.from(this.inputElement.files || []).filter(
          (f) => f !== file
        );
        this.syncInputFiles(inputFiles);
        return;
      }

      this.files.push(file);
      const listItem = document.createElement("li");
      listItem.classList.add("dropzone-file-item");
      listItem.innerHTML = `
        <div class="dropzone-file-info">
          <span class="dropzone-file-name">${file.name}</span>
          <span class="dropzone-file-size">${humanBytes(file.size)}</span>
        </div>
        <button type="button" class="dropzone-delete-btn" tab-index="-1">
          <i class="icon ti ti-x"></i>
        </button>
      `;

      listItem
        .querySelector(".dropzone-delete-btn")
        .addEventListener("click", () => {
          this.files = this.files.filter((f) => f !== file);
          this.syncInputFiles(this.files);
          fileList.removeChild(listItem);
        });

      fileList.appendChild(listItem);
    });
  }

  syncInputFiles(files = []) {
    const dataTransfer = new DataTransfer();
    files.forEach((f) => dataTransfer.items.add(f));
    this.inputElement.files = dataTransfer.files;
  }

  getFiles() {
    return this.files;
  }
}

export { Dropzone };
