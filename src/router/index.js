import { createRouter, createWebHistory } from 'vue-router'
import Member from './member'
/*前台 */
import WelcomePage from '@/pages/WelcomePage.vue'
// 首頁
import homePage from '@/pages/homePage.vue'
// 百岳之書
import routesPage from '@/pages/routesPage.vue'
import togetherPage from '@/pages/togetherPage.vue'
// 揪安心
import myChallenge from '@/pages/myChallenge.vue'
import ShopPage from '@/pages/ShopPage/ShopPage.vue'
// 會員登入

/* 各分頁的子頁面 */

// 商品頁
import ProductDetailRoute from '@/pages/ShopPage/ProductDetailRoute.vue'
//商品頁-結賬流程
import Chekout1Cart from '@/pages/ShopPage/Checkout1Cart.vue'   
import Checkout2Info from '@/pages/ShopPage/Checkout2Info.vue'
import Checkout3Success from '@/pages/ShopPage/Checkout3Success.vue'


/*後台*/
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


// 定義 routes 陣列
/* 前台 */
const frontroutes = [
  {
    path: '/',
    component: WelcomePage,
  },
  {
    path: '/homepage',
    component: homePage,
  },
  {
    path: '/routes',
    component: routesPage
  },
  {
    path: '/together',
    component: togetherPage
  },
  {
    path: '/my-challenge',
    component: myChallenge
  },
  {
    path: '/shop',
    component: ShopPage,
      children:[
      {path:'product/:id',name:'ProductDetailRoute' ,component: ProductDetailRoute ,meta: { modal: true }} // ← 子路由}
      //: 開頭的東西叫「動態參數 (Dynamic Segment)」,meta標記這是一個彈窗路由
     ]
  },
  //前台-結賬流程
  { path: '/Shop/cart',name:'Shop-cart', component: Chekout1Cart },
  { path: '/Shop/info',name:'Shop-info', component: Checkout2Info },
  { path: '/Shop/success', name:'Shop-success',component: Checkout3Success },

  /* 後台 */
  { path: '/login', component: backLogin },
  {
    path: '/',
    component: DefaultLayout,
    children: [
      { path: '', redirect: '/admin/members' }, // 根路徑導向會員管理
      { path: 'admin/home', component: Home },
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

const routes=[
  ...frontroutes,
  ...Member
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// 登入阻擋
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