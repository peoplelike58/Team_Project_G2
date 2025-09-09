import {fileURLToPath, URL} from 'node:url'

import {defineConfig} from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'

export default defineConfig({
    base: process.env.NODE_ENV === 'production' //base: 用來設定「專案打包後的靜態資源的基準路徑」
        ? '/tjd102/g2/' // ← production 模式：部署到 FTP 時的子目錄
        : '/', // ← development 模式：本地開發直接用根目錄
    build: { //build: 控制打包的輸出設定
        outDir: 'dist' // 打包後整個 dist 上傳到 /tjd102/g1/
    },
    plugins: [//plugins: 使用 Vite 插件，這裡啟用 Vue 支援 .vue 檔案
        vue()], // resolve.alias: 設定路徑別名，避免寫一堆 ../../
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./src', import.meta.url)),
            '^': fileURLToPath(new URL('./public', import.meta.url))
        }
    },

})

