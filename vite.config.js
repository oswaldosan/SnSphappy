import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
  // Build output goes to dist/ inside the theme
  build: {
    outDir: resolve(__dirname, 'dist'),
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'src/css/main.css'),
        app:  resolve(__dirname, 'src/js/app.js'),
      },
    },
  },

  // Dev server proxies to WordPress
  server: {
    port: 3000,
    strictPort: true,
    origin: 'http://localhost:3000',
  },
});
