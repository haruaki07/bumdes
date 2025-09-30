const session = JSON.parse(document.getElementById("sessionData").textContent);
const meta = JSON.parse(
  document.getElementById("sessionMeta").textContent || "{}"
);
const action = session?.action || {};
const { type, descriptor, value } = action;

const expiresAt = session?.expires_at ? new Date(session.expires_at) : null;
const countdownEl = document.getElementById("countdown");
let timerId;

// Countdown
function updateCountdown() {
  if (!expiresAt || !countdownEl) return;
  const now = new Date();
  const diff = Math.max(0, Math.floor((expiresAt - now) / 1000));
  const m = Math.floor(diff / 60);
  const s = diff % 60;
  countdownEl.textContent = `(sisa ${m}m ${s}s)`;

  if (diff <= 0) {
    clearInterval(timerId);
    countdownEl.textContent = "(kedaluwarsa)";
    disableButton("btnGoNow");
  }
}
if (
  !(type === "PRESENT_TO_CUSTOMER" && descriptor === "BANK_TRANSFER_DETAILS")
) {
  updateCountdown();
  timerId = setInterval(updateCountdown, 1000);
}

// Polling status
const statusUrl = meta?.status_url;
async function pollStatus() {
  if (!statusUrl) return;
  // Do not poll for manual bank transfer; admin will confirm manually
  if (type === "PRESENT_TO_CUSTOMER" && descriptor === "BANK_TRANSFER_DETAILS")
    return;
  try {
    const res = await fetch(statusUrl, {
      headers: {
        Accept: "application/json",
      },
    });
    if (!res.ok) return;
    if (res.headers.get("Content-Type")?.includes("text/html")) {
      const html = await res.text();
      document.body.querySelector(".page").innerHTML = html;
      clearInterval(timerId);
      return;
    }
    setTimeout(pollStatus, 5000);
  } catch {
    setTimeout(pollStatus, 7000);
  }
}
setTimeout(pollStatus, 5000);

// QR Rendering
if (type === "PRESENT_TO_CUSTOMER" && value) {
  if (descriptor === "QR_STRING") {
    const qrEl = document.getElementById("qr");
    if (qrEl && window.QRCode) {
      QRCode.toCanvas(
        value,
        {
          width: 250,
          margin: 2,
        },
        (err, canvas) => {
          if (!err) {
            qrEl.innerHTML = "";
            qrEl.appendChild(canvas);
            document
              .getElementById("btnDownloadQR")
              .addEventListener("click", () => {
                const link = document.createElement("a");
                link.href = canvas.toDataURL("image/png");
                link.download = "qris.png";
                link.click();
              });
          }
        }
      );
    }
    bindCopy("btnCopyQR", value);
  } else if (descriptor === "PAYMENT_CODE") {
    JsBarcode("#barcode", value, {
      format: "CODE128",
      displayValue: true,
    });
  }
}

// Copy Code
bindCopy(
  "btnCopyCode",
  document.getElementById("codeBox")?.textContent?.trim()
);

// Redirect
if (type === "REDIRECT_CUSTOMER" && descriptor === "WEB_URL" && value) {
  const redirEl = document.getElementById("redirSec");
  const btnGo = document.getElementById("btnGoNow");
  let left = 5;
  const tick = () => {
    if (redirEl) redirEl.textContent = left;
    if (left <= 0) {
      btnGo?.click();
      return;
    }
    left--;
    setTimeout(tick, 1000);
  };
  tick();
}

// Payment Instructions
(function loadPaymentInstructions() {
  const methodCode = session?.payment_method?.code;
  const url = meta?.instructions_url;

  // Determine full payment code placeholder value
  const fullPaymentCode =
    descriptor === "VIRTUAL_ACCOUNT_NUMBER" || descriptor === "PAYMENT_CODE"
      ? value || document.getElementById("codeBox")?.textContent?.trim() || ""
      : document.getElementById("codeBox")?.textContent?.trim() || "";

  const accountNumber =
    descriptor === "BANK_TRANSFER_DETAILS"
      ? value?.account_number ||
        document.getElementById("codeBox")?.textContent?.trim() ||
        ""
      : "";
  const accountName =
    descriptor === "BANK_TRANSFER_DETAILS" ? value?.account_name || "" : "";
  const bankName =
    descriptor === "BANK_TRANSFER_DETAILS" ? value?.bank || "" : "";

  const vars = {
    fullPaymentCode,
    iBankingSource: getIbankingUrl(methodCode),
    merchantName: "E-Billing", // TODO: for qris, should be using global config
    accountNumber,
    accountName,
    bankName,
    invoiceNumber: session?.reference_id || "",
  };

  fetch(url, {
    headers: {
      Accept: "application/json",
    },
  })
    .then((res) => (res.ok ? res.json() : null))
    .then((data) => {
      if (!data || !data.instructions) return;
      renderInstructions(data.instructions, vars);
    })
    .catch(() => {});
})();

function getIbankingUrl(code) {
  // Minimal mapping; extend as needed
  const map = {
    BCA_VIRTUAL_ACCOUNT: "https://ibank.klikbca.com",
    BNI_VIRTUAL_ACCOUNT: "https://ibank.bni.co.id",
    BSI_VIRTUAL_ACCOUNT: "https://bsinet.bankbsi.co.id",
    PERMATA_VIRTUAL_ACCOUNT: "https://www.permatanet.com",
  };
  return map[code] || "#";
}

function applyVars(str, vars) {
  if (typeof str !== "string") return "";
  return str.replace(/\{\{\s*(\w+)\s*\}\}/g, (_, k) => vars[k] ?? "");
}

function sanitizeHtml(input) {
  // allow only a, strong, em, br and text
  const wrapper = document.createElement("div");
  wrapper.innerHTML = input;
  const allowed = new Set(["A", "STRONG", "EM", "BR"]);

  (function walk(node) {
    const children = Array.from(node.childNodes);
    for (const child of children) {
      if (child.nodeType === Node.TEXT_NODE) continue;
      if (child.nodeType === Node.ELEMENT_NODE) {
        if (!allowed.has(child.tagName)) {
          // Replace disallowed element with its text content
          const text = document.createTextNode(child.textContent || "");
          node.replaceChild(text, child);
          continue;
        }
        if (child.tagName === "A") {
          const href = child.getAttribute("href") || "";
          if (!/^https?:\/\//i.test(href) && href !== "#") {
            child.removeAttribute("href");
          }
          child.setAttribute("rel", "noopener");
          child.setAttribute("target", "_blank");
        }
        walk(child);
      } else {
        node.removeChild(child);
      }
    }
  })(wrapper);

  return wrapper.innerHTML;
}

function renderInstructions(instructions, vars) {
  // Support array-form (no categories) and object-form (with categories)
  if (Array.isArray(instructions)) {
    return renderInstructionList(instructions, vars);
  }
  return renderInstructionsTabs(instructions, vars);
}

function renderInstructionList(blocks, vars) {
  const section = document.getElementById("instructionsSection");
  const tabsHost = document.getElementById("instructionsTabs");
  if (!section || !tabsHost) return;

  const container = document.createElement("div");
  const steps = Array.isArray(blocks) ? blocks : [];
  if (!steps.length) return;

  for (const block of steps) {
    const h = document.createElement("h4");
    h.textContent = block.title || "Langkah";
    container.appendChild(h);

    const ol = document.createElement("ol");
    const stepsArr = Array.isArray(block.steps) ? block.steps : [];
    for (const s of stepsArr) {
      const li = document.createElement("li");
      li.className = "mb-1";
      const applied = applyVars(String(s), vars);
      li.innerHTML = sanitizeHtml(applied);
      ol.appendChild(li);
    }
    container.appendChild(ol);
  }

  tabsHost.innerHTML = "";
  tabsHost.appendChild(container);
  section.classList.remove("d-none");
}

function renderInstructionsTabs(instructions, vars) {
  const section = document.getElementById("instructionsSection");
  const tabsHost = document.getElementById("instructionsTabs");
  if (!section || !tabsHost) return;

  const categories = Object.keys(instructions || {});
  if (!categories.length) return;

  const nav = document.createElement("ul");
  nav.className = "nav nav-underline gap-0 border-bottom";

  const content = document.createElement("div");
  content.className = "tab-content mt-3";

  const makeId = (k) =>
    `ins-${k.replace(/[^a-z0-9]/gi, "")}-${Math.random()
      .toString(36)
      .slice(2, 7)}`;
  let first = true;

  for (const key of categories) {
    const pretty = key.toUpperCase();
    const paneId = makeId(key);

    // Tab button
    const li = document.createElement("li");
    li.className = "nav-item";
    const a = document.createElement("a");
    a.className = "nav-link px-4" + (first ? " active" : "");
    a.dataset.bsToggle = "tab";
    a.href = `#${paneId}`;
    a.textContent = pretty;
    li.appendChild(a);
    nav.appendChild(li);

    // Pane
    const pane = document.createElement("div");
    pane.className = "tab-pane fade" + (first ? " show active" : "");
    pane.id = paneId;

    const steps = Array.isArray(instructions[key]) ? instructions[key] : [];
    if (!steps.length) {
      const empty = document.createElement("div");
      empty.className = "text-secondary";
      empty.textContent = "Tidak ada petunjuk untuk kanal ini.";
      pane.appendChild(empty);
    } else {
      for (const block of steps) {
        const h = document.createElement("h4");
        h.textContent = block.title || "Langkah";
        pane.appendChild(h);

        const ol = document.createElement("ol");
        const stepsArr = Array.isArray(block.steps) ? block.steps : [];
        for (const s of stepsArr) {
          const li = document.createElement("li");
          li.className = "mb-1 fs-4";
          const applied = applyVars(String(s), vars);
          li.innerHTML = sanitizeHtml(applied);
          ol.appendChild(li);
        }
        pane.appendChild(ol);
      }
    }

    content.appendChild(pane);
    first = false;
  }

  tabsHost.innerHTML = "";
  tabsHost.appendChild(nav);
  tabsHost.appendChild(content);
  section.classList.remove("d-none");
}

// Helpers
// Upload transfer receipt
(function bindUpload() {
  if (
    !(type === "PRESENT_TO_CUSTOMER" && descriptor === "BANK_TRANSFER_DETAILS")
  )
    return;
  const form = document.getElementById("receiptForm");
  if (!form) return;
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const file = document.getElementById("receiptFile")?.files?.[0];
    if (!file) return;
    const btn = document.getElementById("btnUploadReceipt");
    const statusEl = document.getElementById("uploadStatus");
    const url = meta?.transfer_receipt_url;
    const formData = new FormData(form);
    btn.setAttribute("disabled", "disabled");
    statusEl.textContent = "Mengunggah…";
    try {
      const res = await fetch(url, {
        method: "POST",
        headers: {
          Accept: "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        body: formData,
      });
      const data = await res.json().catch(() => null);
      if (!res.ok) throw new Error(data?.message || "Gagal mengunggah");
      statusEl.textContent = "Bukti terkirim. Menunggu verifikasi admin.";
    } catch (err) {
      statusEl.textContent = err?.message || "Gagal mengunggah.";
    } finally {
      btn.removeAttribute("disabled");
    }
  });
})();

function bindCopy(btnId, text) {
  const btn = document.getElementById(btnId);
  if (btn && text && navigator.clipboard) {
    btn.addEventListener("click", async () => {
      try {
        await navigator.clipboard.writeText(text);
        copyFeedback(btn);
      } catch (_) {
        alert("Gagal menyalin.");
      }
    });
  }
}

function copyFeedback(btn) {
  const tooltip = tabler.Tooltip.getInstance(btn);
  const originalHtml = btn.innerHTML;
  const originalTitle = btn.dataset.bsOriginalTitle || "Salin";
  btn.innerHTML = '<i class="icon ti ti-check"></i>';
  tooltip.setContent({ ".tooltip-inner": "Tersalin" });
  setTimeout(() => {
    btn.innerHTML = originalHtml;
    tooltip.setContent({ ".tooltip-inner": originalTitle });
  }, 2500);
}

function disableButton(id) {
  const el = document.getElementById(id);
  if (el) el.setAttribute("disabled", "disabled");
}

// Inject fee & total summary if exists
(function renderFeeSummary() {
  const container = document.querySelector(".card-body");
  if (!container) return;
  const fee = meta.fee || 0;
  if (fee <= 0) return;
  const base = meta.amount || 0;
  const total = meta.total_amount || base + fee;
  const box = document.createElement("div");
  box.className = "alert alert-info mt-3";
  box.innerHTML = `<div class="d-flex flex-column">
            <div><strong>Rincian Pembayaran</strong></div>
            <div class="small">Tagihan: <span class="fw-semibold">Rp${base.toLocaleString(
              "id-ID"
            )}</span></div>
            <div class="small">Biaya Metode: <span class="fw-semibold">Rp${fee.toLocaleString(
              "id-ID"
            )}</span></div>
            <div class="mt-1">Total Dibayar: <span class="fw-bold">Rp${total.toLocaleString(
              "id-ID"
            )}</span></div>
        </div>`;
  container.insertBefore(box, container.firstChild.nextSibling);
})();

// Simulation (sandbox/test mode only)
(function bindSimulation() {
  const bar = document.getElementById("simulateBar");
  if (!bar) return;
  const url = meta?.simulate_url;
  if (!url) return;
  let busy = false;
  bar.addEventListener("click", async () => {
    if (busy) return;
    busy = true;
    const original = bar.textContent;
    bar.textContent = "Memproses simulasi pembayaran…";
    try {
      const res = await fetch(url, {
        method: "POST",
        headers: {
          Accept: "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        body: new URLSearchParams({}),
      });
      // handle tf bank
      if (session.payment_method.type === "BANK_TRANSFER") {
        const text = await res.text();
        document.body.querySelector(".page").innerHTML = text;
      } else {
        const data = await res.json().catch(() => null);
        if (!res.ok) throw new Error(data?.message || "Gagal!");
        bar.style.background = "#d1e7dd";
        bar.style.color = "#0f5132";
        bar.textContent = `Status pembayaran sekarang: ${data.payment_status}`;
      }
    } catch (e) {
      bar.style.background = "#f8d7da";
      bar.style.color = "#842029";
      bar.textContent = e?.message || "Gagal!";
      setTimeout(
        () => (
          (bar.textContent = original),
          (bar.style.background = "#fff3cd"),
          (bar.style.color = "#856404")
        ),
        3000
      );
    } finally {
      busy = false;
    }
  });
})();
