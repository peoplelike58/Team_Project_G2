import { createRouter, createWebHistory } from 'vue-router'

import MemberPage from '@/pages/admin/MemberPage.vue'
import OrderPage from '@/pages/admin/OrderPage.vue'
import ProductPage from '@/pages/admin/ProductPage.vue'
import NewsPage from '@/pages/admin/NewsPage.vue'
import EventPage from '@/pages/admin/EventPage.vue'
import CouponPage from '@/pages/admin/CouponPage.vue'
import MessagePage from '@/pages/admin/MessagePage.vue'
import FlagPage from '@/pages/admin/FlagPage.vue'

const routes = [
  { path: '/', redirect: '/admin/members' },
  { path: '/admin/members', component: MemberPage },
  { path: '/admin/orders', component: OrderPage },
  { path: '/admin/products', component: ProductPage },
  { path: '/admin/news', component: NewsPage },
  { path: '/admin/events', component: EventPage },
  { path: '/admin/coupons', component: CouponPage },
  { path: '/admin/messages', component: MessagePage },
  { path: '/admin/flags', component: FlagPage }
]

export default createRouter({
  history: createWebHistory(),
  routes
})
