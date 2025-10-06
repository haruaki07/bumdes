class AddressModal extends HTMLElement {
  connectedCallback() {
    if (!this.getAttribute("api-key")) {
      console.error("[address-modal]: attribute api-key is required!");
      return;
    }

    if (
      !this.getAttribute("default-lat") ||
      !this.getAttribute("default-lng")
    ) {
      console.error(
        "[address-modal]: attributes default-lat and default-lng are required!"
      );
      return;
    }

    this.apiKey = this.getAttribute("api-key") || "";
    this.defaultLat = parseFloat(this.getAttribute("default-lat"));
    this.defaultLng = parseFloat(this.getAttribute("default-lng"));
    this.initialAddress = this.getAttribute("address") || "";
    this.initialLat = this.getAttribute("lat");
    this.initialLng = this.getAttribute("lng");

    this.state = {
      map: null,
      marker: null,
      coordsSelected: false,
      searchSelectInitialized: false,
    };

    document.addEventListener("DOMContentLoaded", () => {
      this.render();
      this.cache();
      this.prefill();
      this.bindEvents();
    });
  }

  render() {
    const modalId = this.id || "addressModal";
    this.innerHTML = `
      <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Pilih Lokasi & Alamat</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <div class="d-flex align-items-center gap-3">
                  <span class="me-1">Metode:</span>
                  <div class="form-check form-check-inline m-0">
                    <input class="form-check-input" type="radio" name="addressMode" id="modeSearch" value="search">
                    <label class="form-check-label" for="modeSearch">Cari tempat</label>
                  </div>
                  <div class="form-check form-check-inline m-0">
                    <input class="form-check-input" type="radio" name="addressMode" id="modeManual" value="manual" checked>
                    <label class="form-check-label" for="modeManual">Masukkan manual</label>
                  </div>
                </div>
                <div id="modeHint" class="form-text">Pilih metode untuk mengisi alamat.</div>
              </div>

              <div id="searchModeSection" style="display:none;">
                <div class="mb-3">
                  <label class="form-label">Cari tempat</label>
                  <div class="input-group">
                    <input type="text" id="placeQuery" placeholder="Contoh: Alun-alun, Jl. Merdeka, Cilacap" />
                    <button class="btn btn-outline-secondary" type="button" id="placeSearchBtn">Cari</button>
                  </div>
                </div>

                <div class="mb-3">
                  <div id="addressMap" style="height: 420px; border-radius: 6px; overflow: hidden;"></div>
                </div>

                <div class="small text-muted">Koordinat: <span id="coordsPreview">-</span></div>
              </div>

              <div id="commonAddressInput">
                <div class="mb-2 mt-3">
                  <label class="form-label required">Alamat</label>
                  <textarea class="form-control" id="modalAddressTextarea" rows="3" placeholder="Tulis alamat lengkap..."></textarea>
                </div>

                <div class="mb-3">
                  <label class="form-label">Map URL (opsional)</label>
                  <input type="url" class="form-control" name="map_url" id="mapUrlInput" placeholder="https://maps.app.goo.gl/xx">
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-link" data-bs-dismiss="modal">Batal</button>
              <button type="button" class="btn btn-primary" id="applyAddressBtn">Terapkan</button>
            </div>
          </div>
        </div>
      </div>
    `;
  }

  cache() {
    this.els = {
      modal: this.querySelector(".modal"),
      modeSearch: this.querySelector("#modeSearch"),
      modeManual: this.querySelector("#modeManual"),
      searchSection: this.querySelector("#searchModeSection"),
      modeHint: this.querySelector("#modeHint"),
      placeQuery: this.querySelector("#placeQuery"),
      placeSearchBtn: this.querySelector("#placeSearchBtn"),
      addressTextarea: this.querySelector("#modalAddressTextarea"),
      coordsPreview: this.querySelector("#coordsPreview"),
      mapContainer: this.querySelector("#addressMap"),
      applyBtn: this.querySelector("#applyAddressBtn"),
    };
  }

  prefill() {
    if (this.initialAddress)
      this.els.addressTextarea.value = this.initialAddress;
    const lat = parseFloat(this.initialLat);
    const lng = parseFloat(this.initialLng);
    if (!Number.isNaN(lat) && !Number.isNaN(lng)) {
      this.defaultLat = lat;
      this.defaultLng = lng;
      this.state.coordsSelected = true;
      if (this.els.coordsPreview)
        this.els.coordsPreview.textContent = `${lat.toFixed(6)}, ${lng.toFixed(
          6
        )}`;
      // default to search mode when coords exist
      this.els.modeSearch.checked = true;
      this.setMode("search");
      setTimeout(() => this.ensureMapAndMarker(lat, lng, false), 0);
    } else if (this.initialAddress) {
      this.els.modeManual.checked = true;
      this.setMode("manual");
    } else {
      this.setMode(null);
    }
  }

  bindEvents() {
    this.els.modeSearch.addEventListener("change", () => {
      if (this.els.modeSearch.checked) {
        this.setMode("search");
        this.initSearchSelect();
      }
    });
    this.els.modeManual.addEventListener("change", () => {
      if (this.els.modeManual.checked) this.setMode("manual");
    });

    this.els.applyBtn.addEventListener("click", () => this.apply());

    this.els.modal.addEventListener("shown.bs.modal", () => {
      // Re-evaluate map size and show existing marker if any
      if (this.els.modeSearch.checked) {
        this.initSearchSelect();
        this.ensureMap();
        if (this.state.marker) {
          const ll = this.state.marker.getLatLng();
          this.state.map.setView([ll.lat, ll.lng], 15);
        }
      }
    });
  }

  setMode(mode) {
    switch (mode) {
      case "search":
        this.els.searchSection.style.display = "";
        this.els.modeHint.style.display = "none";
        this.ensureMap();
        break;
      case "manual":
        this.els.searchSection.style.display = "none";
        this.els.modeHint.style.display = "none";
        this.state.coordsSelected = false;
        break;
      default:
        this.els.searchSection.style.display = "none";
        this.els.modeHint.style.display = "";
    }
  }

  ensureMap() {
    if (this.state.map) {
      setTimeout(() => this.state.map.invalidateSize(), 0);
      return;
    }

    if (!window.L) {
      console.error("Leaflet is not available on window.L");
      return;
    }

    L.Icon.Default.imagePath = "/assets/js/leaflet/images/";

    this.state.map = L.map(this.els.mapContainer).setView(
      [this.defaultLat, this.defaultLng],
      11
    );

    if (window.google && L.gridLayer && L.gridLayer.googleMutant) {
      L.gridLayer
        .googleMutant({ type: "roadmap", maxZoom: 20 })
        .addTo(this.state.map);
    } else {
      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 19,
        attribution: "&copy; OpenStreetMap contributors",
      }).addTo(this.state.map);
    }

    this.state.map.on("click", (e) => {
      this.ensureMapAndMarker(e.latlng.lat, e.latlng.lng, true);
      this.reverseGeocodeAndFill(e.latlng.lat, e.latlng.lng);
    });

    // If prefilled coords exist, show them
    if (this.state.coordsSelected) {
      this.ensureMapAndMarker(this.defaultLat, this.defaultLng, false);
    } else if (this.els.coordsPreview) {
      this.els.coordsPreview.textContent = "-";
    }
  }

  ensureMapAndMarker(lat, lng, userSelected) {
    this.ensureMap();
    if (!this.state.marker) {
      this.state.marker = L.marker([lat, lng], { draggable: true }).addTo(
        this.state.map
      );
      this.state.marker.on("dragend", () => {
        const ll = this.state.marker.getLatLng();
        this.updatePreview(ll);
        this.state.coordsSelected = true;
      });
      this.state.marker.on("click", () => {
        this.state.coordsSelected = false;
        if (this.els.coordsPreview) this.els.coordsPreview.textContent = "-";
        this.state.map.removeLayer(this.state.marker);
        this.state.marker = null;
      });
    } else {
      this.state.marker.setLatLng([lat, lng]);
    }
    this.updatePreview(this.state.marker.getLatLng());
    if (userSelected) this.state.coordsSelected = true;
  }

  updatePreview(latlng) {
    if (this.els.coordsPreview && latlng) {
      this.els.coordsPreview.textContent = `${latlng.lat.toFixed(
        6
      )}, ${latlng.lng.toFixed(6)}`;
    }
  }

  async reverseGeocodeAndFill(lat, lng) {
    try {
      if (!window.axios) {
        console.error("axios not found on window");
        return;
      }
      const res = await axios.get(
        "https://maps.googleapis.com/maps/api/geocode/json",
        {
          params: { latlng: `${lat},${lng}`, key: this.apiKey, language: "id" },
          transformRequest: (data, headers) => {
            // this header may cause CORS issues when calling Google APIs directly
            delete headers["X-Requested-With"];
            return data;
          },
        }
      );
      const formatted = res?.data?.results?.[0]?.formatted_address;
      if (formatted) this.els.addressTextarea.value = formatted;
    } catch (e) {
      console.error("Error reverse geocoding:", e);
    }
  }

  initSearchSelect() {
    if (this.state.searchSelectInitialized) return;
    this.state.searchSelectInitialized = true;

    if (!window.TomSelect) return;

    // eslint-disable-next-line no-new
    new window.TomSelect(this.els.placeQuery, {
      maxItems: 1,
      load: async (query, callback) => {
        try {
          if (!query) return callback();
          const res = await axios.post(
            "https://places.googleapis.com/v1/places:autocomplete?languageCode=id",
            {
              input: query,
              locationBias: {
                circle: {
                  center: {
                    latitude: this.defaultLat,
                    longitude: this.defaultLng,
                  },
                  radius: 45000, // 45km
                },
              },
            },
            { headers: { "X-Goog-Api-Key": this.apiKey } }
          );
          const items = (res.data.suggestions || []).map((item) => ({
            text: item.placePrediction?.text?.text,
            value: item.placePrediction?.placeId,
          }));
          callback(items);
        } catch (e) {
          console.error("Error fetching places:", e);
          callback();
        }
      },
      onChange: async (value) => {
        try {
          if (!value) return;
          const res = await axios.get(
            `https://places.googleapis.com/v1/places/${value}?languageCode=id`,
            {
              headers: {
                "X-Goog-Api-Key": this.apiKey,
                "X-Goog-FieldMask": "id,displayName,formattedAddress,location",
              },
            }
          );
          const loc = res.data.location;
          if (
            loc &&
            typeof loc.latitude === "number" &&
            typeof loc.longitude === "number"
          ) {
            const lat = loc.latitude;
            const lng = loc.longitude;
            this.ensureMapAndMarker(lat, lng, true);
            this.state.map.setView([lat, lng], 15);
          }
          const name = res.data.displayName?.text
            ? res.data.displayName.text + ", "
            : "";
          const formatted = res.data.formattedAddress || "";
          this.els.addressTextarea.value = name + formatted;
        } catch (e) {
          console.error("Error fetching place details:", e);
        }
      },
    });

    if (this.els.placeSearchBtn) {
      this.els.placeSearchBtn.addEventListener("click", () => {
        this.els.placeQuery?.focus();
      });
    }
  }

  apply() {
    const isManual = this.els.modeManual.checked;
    const address = (this.els.addressTextarea.value || "").trim();
    if (!address) {
      this.els.addressTextarea.focus();
      return;
    }

    let lat = "";
    let lng = "";
    if (!isManual && this.state.coordsSelected && this.state.marker) {
      const ll = this.state.marker.getLatLng();
      lat = ll.lat;
      lng = ll.lng;
    }

    const detail = { address, lat, lng, mode: isManual ? "manual" : "search" };
    // Callback property support
    if (typeof this.onapply === "function") {
      try {
        this.onapply(detail);
      } catch (e) {
        console.error(e);
      }
    }
    // Event dispatch for general usage
    this.dispatchEvent(
      new CustomEvent("address-apply", { detail, bubbles: true })
    );

    // Close modal
    const bs = window.bootstrap || (window.tabler && window.tabler.bootstrap);
    const instance = bs ? bs.Modal.getInstance(this.els.modal) : null;
    if (instance) instance.hide();
  }
}

// Safe define to avoid double registration in hot-reload or double imports
if (!customElements.get("address-modal")) {
  customElements.define("address-modal", AddressModal);
}

export { AddressModal };
