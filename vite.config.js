import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";

export default defineConfig({
  plugins: [
    laravel({
      input: [
        "resources/css/app.css",
        "resources/js/app.js",
        "resources/sass/tabler.scss",
        "resources/sass/tabler-icons.scss",
        "resources/js/leaflet.js",
        "resources/js/hugerte.js",
        "app-modules/e-billing/resources/css/e-billing.css",
        "app-modules/e-billing/resources/js/pages/pay.js",
        "app-modules/e-billing/resources/js/main.js",
      ],
      refresh: [
        "resources/views/**/*.blade.php",
        "resources/js/**/*.js",
        "resources/css/**/*.css",
        "app-modules/*/resources/views/**/*.blade.php",
        "app-modules/*/resources/js/**/*.js",
        "app-modules/*/resources/css/**/*.css",
      ],
    }),
  ],
});
