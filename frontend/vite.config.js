import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [vue()],
  server: {
    historyApiFallback: true,
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'), // Ensures correct resolution of files
    },
  },
  css: {
    preprocessorOptions: {
      scss: {
        additionalData: `@import "@/assets/styles/global.scss";`, // Optional for global SCSS styles
      },
    },
  },
});
