window.deleteConfirm = async function (url, method = "DELETE", opts = {}) {
  const res = await new Promise((resolve) => {
    bootbox.confirm({
      title: "Hapus Data?",
      message: "Data yang dihapus tidak dapat dikembalikan.",
      buttons: {
        cancel: { label: "Batal" },
        confirm: { label: "Ya", className: "btn-danger" },
      },
      callback: (result) => resolve(result),
      ...opts,
    });
  });

  if (!res) return;

  const form = document.createElement("form");
  form.method = "POST";
  form.action = url;

  const csrf = document.createElement("input");
  csrf.hidden = true;
  csrf.name = "_token";
  csrf.value = document.querySelector("meta[name=csrf-token]").content;

  const meth = document.createElement("input");
  meth.hidden = true;
  meth.name = "_method";
  meth.value = method;

  form.append(csrf, meth);
  document.body.append(form);
  form.submit();
};

window.formatRupiah = function (angka) {
  let rupiah = "";
  let angkarev = angka.toString().split("").reverse().join("");
  for (let i = 0; i < angkarev.length; i++)
    if (i % 3 == 0) rupiah += angkarev.substr(i, 3) + ".";
  return (
    "Rp" +
    rupiah
      .split("", rupiah.length - 1)
      .reverse()
      .join("")
  );
};

window.throttle = (func, delay) => {
  let lastCall = 0;
  return (...args) => {
    const now = new Date().getTime();
    if (now - lastCall < delay) {
      return;
    }
    lastCall = now;
    return func(...args);
  };
};
