// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'

// 後台系統＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝
import backLogin from '@/pages/backLogin.vue'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import Home from '@/pages/backHome.vue'
import MemberPage from '@/pages/admin/MemberPage.vue'
import OrderPage from '@/pages/admin/OrderPage.vue'
import ProductPage from '@/pages/admin/ProductPage.vue'
import NewsPage from '@/pages/admin/NewsPage.vue'
import EventPage from '@/pages/admin/EventPage.vue'
import CouponPage from '@/pages/admin/CouponPage.vue'
import MessagePage from '@/pages/admin/MessagePage.vue'
import FlagPage from '@/pages/admin/FlagPage.vue'
// ＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝
const routes = [
  { path: '/login', component: backLogin },
  {
    path: '/',
    component: DefaultLayout,
    children: [
      { path: '', redirect: '/home' }, // 將根路徑導向首頁
      { path: 'home', component: Home },
      { path: 'admin/members', component: MemberPage },
      { path: 'admin/orders', component: OrderPage },
      { path: 'admin/products', component: ProductPage },
      { path: 'admin/news', component: NewsPage },
      { path: 'admin/events', component: EventPage },
      { path: 'admin/coupons', component: CouponPage },
      { path: 'admin/messages', component: MessagePage },
      { path: 'admin/flags', component: FlagPage },
    ]
  }
]



const router = createRouter({
  history: createWebHistory(),
  routes
})

// 登入router阻擋
router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem('auth') === 'true'
  const isLoginPage = to.path === '/login'

  if (!isAuthenticated && to.path.startsWith('/admin') && !isLoginPage) {
    next('/login')
  } else {
    next()
  }
})

export default router
