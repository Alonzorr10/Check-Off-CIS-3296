import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/js/events.js",
                "resources/js/contributions.js",
                "resources/js/contributions-logged.js",
            ],
            refresh: true,
        }),
    ],
});
