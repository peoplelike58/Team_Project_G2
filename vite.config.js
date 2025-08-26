import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'

export default defineConfig({
  base: process.env.NODE_ENV === 'production'
      ? '/tjd102/g2/'
      : '/',
  build: { 
      outDir: 'dist'
  },
  plugins: [
      vue()],
  resolve: {
      alias: {
          '@': fileURLToPath(new URL('./src', import.meta.url)),
          '^': fileURLToPath(new URL('./public', import.meta.url))
      }
  }
})