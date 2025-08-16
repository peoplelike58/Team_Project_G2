<<<<<<< HEAD
import '@/assets/styles/main.scss'
import 'element-plus/dist/index.css'//element-plus
import ElementPlus from 'element-plus'

=======
>>>>>>> dev
import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'

<<<<<<< HEAD
=======
// Element Plus
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'

//全域 SCSS
import '@/assets/styles/main.scss'
>>>>>>> dev

const app = createApp(App)

app.use(createPinia())
app.use(router)
app.use(ElementPlus)

app.mount('#app')
