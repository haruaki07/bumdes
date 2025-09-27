import Cookie from "js-cookie";

const themeConfig = {
  theme: "light",
  "theme-base": "gray",
  "theme-font": "sans-serif",
  "theme-primary": "blue",
  "theme-radius": "1",
};

const params = new Proxy(new URLSearchParams(window.location.search), {
  get: (searchParams, prop) => searchParams.get(prop),
});

let storedConfig = {};
try {
  storedConfig = JSON.parse(Cookie.get("theme-config") || "{}");
} catch {
  storedConfig = {};
}

let updatedConfig = { ...themeConfig, ...storedConfig };

let shouldUpdateCookie = false;

for (const key in themeConfig) {
  const param = params[key];
  if (param) {
    updatedConfig[key] = param;
    shouldUpdateCookie = true;
  }

  const selectedValue = updatedConfig[key];

  if (selectedValue !== themeConfig[key]) {
    document.documentElement.setAttribute("data-bs-" + key, selectedValue);
  } else {
    document.documentElement.removeAttribute("data-bs-" + key);
  }
}

if (shouldUpdateCookie) {
  Cookie.set("theme-config", JSON.stringify(updatedConfig), {
    expires: 365,
    path: "/",
  });
}
