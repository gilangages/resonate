import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import tailwindcss from "@tailwindcss/vite";

// https://vite.dev/config/
export default defineConfig(({ mode }) => {
  return {
    plugins: [vue(), tailwindcss()],
    esbuild: {
      pure: mode === "production" ? ["console.log", "console.debug"] : [],
    },
    test: {
      environment: "jsdom",
      globals: true,
    },
  };
});
