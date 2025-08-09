<!-- # MountainPeak

This template should help get you started developing with Vue 3 in Vite.

## Recommended IDE Setup

[VSCode](https://code.visualstudio.com/) + [Volar](https://marketplace.visualstudio.com/items?itemName=Vue.volar) (and disable Vetur).

## Customize configuration

See [Vite Configuration Reference](https://vite.dev/config/).

## Project Setup

```sh
npm install
```

### Compile and Hot-Reload for Development

```sh
npm run dev
```

### Compile and Minify for Production

```sh
npm run build
``` -->

專案vue主架構
MountainPeak/
│
├── public/                      # 靜態資源（不經過打包直接輸出）
│   ├── favicon.ico
│   └── images/                  # 公用圖片（logo 等）
│
├── src/                         # 專案核心程式碼
│   ├── assets/                  # 會被 Vite 打包的資源
│   │   ├── images/               # 圖片
│   │   ├── icons/                # SVG 或 icon
│   │   └── styles/               # Sass 全域樣式
│   │       ├── _variables.scss   # 全域變數
│   │       ├── _mixins.scss      # Sass mixin
│   │       ├── _reset.scss       # CSS reset
│   │       └── main.scss         # 全域匯入樣式
│   │
│   ├── components/               # 可重用元件
│   │   ├── common/               # 基礎元件（Button、Modal、Card...）
│   │   └── layout/               # 佈局元件（Header、Footer...）
│   │
│   ├── layouts/                  # 頁面模板（有 header/footer 的版型）
│   │   └── DefaultLayout.vue
│   │
│   ├── pages/                    # 各頁面
│   │   ├── Home.vue
│   │   ├── About.vue
│   │   └── NotFound.vue
│   │
│   ├── router/                   # Vue Router 設定
│   │   └── index.js
│   │
│   ├── stores/                   # Pinia 狀態管理
│   │   └── userStore.js
│   │
│   ├── utils/                    # 公用工具方法
│   │   └── formatDate.js
│   │
│   ├── App.vue                   # 根元件
│   └── main.js                   # 專案進入點
│
├── .gitignore
├── index.html
├── package.json
├── vite.config.js
└── README.md

Sass文件架構
src/
├── assets/
│   └── styles/
│       ├── abstracts/        # 抽象層（不產生 CSS）
│       │   ├── _variables.scss   # 變數
│       │   ├── _mixins.scss      # Mixin
│       │   ├── _functions.scss   # Sass 自訂函數
│       │
│       ├── base/             # 基礎層（全域設定）
│       │   ├── _reset.scss       # Reset / Normalize
│       │   ├── _typography.scss  # 字體樣式
│       │   └── _base.scss        # HTML / body 等基本樣式
│       │
│       ├── components/       # 元件樣式（Button、Card 等）
│       │   └── _button.scss
│       │
│       ├── layout/           # 版型排版（Header、Footer、Grid）
│       │   ├── _header.scss
│       │   ├── _footer.scss
│       │   └── _grid.scss
│       │
│       ├── pages/            # 頁面專用樣式
│       │   ├── _home.scss
│       │   └── _about.scss
│       │
│       ├── themes/           # 主題與顏色方案
│       │   ├── _light.scss
│       │   └── _dark.scss
│       │
│       ├── vendors/          # 第三方套件覆蓋
│       │   └── _bootstrap.scss
│       │
│       └── main.scss         # 入口樣式檔，統一引入所有上面檔案
