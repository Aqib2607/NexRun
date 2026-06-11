import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import { TanStackRouterVite } from "@tanstack/router-plugin/vite";
import tsconfigPaths from "vite-tsconfig-paths";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
  plugins: [tailwindcss(), TanStackRouterVite(), react(), tsconfigPaths()],
  build: {
    outDir: "backend/public/dist",
    emptyOutDir: true,
    chunkSizeWarningLimit: 1000,
    rollupOptions: {
      output: {
        manualChunks(id: string) {
          if (id.includes("node_modules")) {
            if (id.includes("@radix-ui")) return "ui";
            if (id.includes("lucide-react")) return "icons";
            if (id.includes("recharts")) return "charts";
            if (id.includes("framer-motion")) return "animation";
            if (id.includes("@tanstack")) return "router";
            return "vendor";
          }
        },
      },
    },
  },
  base: "/dist/",
});
