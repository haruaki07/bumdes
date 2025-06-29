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
      ],
      refresh: true,
    }),
  ],
});
