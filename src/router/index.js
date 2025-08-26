import { createRouter, createWebHistory } from 'vue-router'
import Member from './member'//會員中心


/*前台 */
import WelcomePage from '@/pages/WelcomePage.vue'
import homePage from '@/pages/homePage.vue'//前台首頁
import PeakGuide from '@/pages/PeakGuide.vue'// 百岳之書
import togetherPage from '@/pages/togetherPage.vue'//揪上山
import routesPage from '@/pages/routesPage.vue'//路線規劃
import trailDetail from '@/pages/trailDetail.vue'
import peacePage from '@/pages/peacePage.vue'// 揪安心
import myChallenge from '@/pages/myChallenge.vue'//百岳挑戰
import ShopPage from '@/pages/ShopPage/ShopPage.vue'//山腳雜貨店
import LoginRegister from '@/pages/LoginPage/LoginRegister.vue'//會員登入

/* 各分頁的子頁面 */

// 商品頁
import ProductDetailRoute from '@/pages/ShopPage/ProductDetailRoute.vue'
//商品頁-結賬流程
import Chekout1Cart from '@/pages/ShopPage/Checkout1Cart.vue'   
import Checkout2Info from '@/pages/ShopPage/Checkout2Info.vue'
import Checkout3Success from '@/pages/ShopPage/Checkout3Success.vue'
// 揪上山活動卡片
import eventCardInfo from '@/components/togetherItem/eventCardInfo.vue'

//會員登入
import LoginPage_login from '@/components/Irene/LoginPage/LoginPage_login.vue'
import LoginPage_register from '@/components/Irene/LoginPage/LoginPage_register.vue'
import LoginPage_forget from '@/components/Irene/LoginPage/LoginPage_forget.vue'
import LoginPage_registercoupon from '@/components/Irene/LoginPage/LoginPage_registercoupon.vue'
import LoginPage_forgetsend from '@/components/Irene/LoginPage/LoginPage_forgetsend.vue'
import LoginPage_resetpassword from '@/components/Irene/LoginPage/LoginPage_resetpassword.vue'

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
    path: '/peaks',
    component: PeakGuide,
  },
  {
    path: '/routes',
    component: routesPage
  },
//------- 詳細頁面 -----------
  {
    path:'/routes/:id',
    name:'trailDetail',
    component: trailDetail,
    props: true
  },
//----------------------------
  {
    path: '/together',
    component: togetherPage,
  },
  {
    path: '/peace',
    component: peacePage,
  },
  {
    path: '/together/activities/:id',
    name:'eventCard', 
    component: eventCardInfo,
  },
  {
    path: '/mychallenge',
    component: myChallenge,
  },
  {
    path: '/shop',
    alias: '/Shop',          // 兩個都算進來
    component: ShopPage,
    children:[
      {path:'product/:id',name:'ProductDetailRoute' ,component: ProductDetailRoute ,meta: { modal: true }}, // ← 子路由}
      //: 開頭的東西叫「動態參數 (Dynamic Segment)」,meta標記這是一個彈窗路由
    ]
  },

  //前台-結賬流程
  { path: '/Shop/cart',name:'Shop-cart', component: Chekout1Cart },
  { path: '/Shop/info',name:'Shop-info', component: Checkout2Info },
  { path: '/Shop/success', name:'Shop-success',component: Checkout3Success },

  // //前台-會員登入 
  {
    path: '/loginregister',
    name: 'loginregister',
    component:LoginRegister,
    children: [
      { path: '', redirect: { name: 'loginregister-fontrelogin' } },
      { path: 'fontrelogin',name:'loginregister-fontrelogin', component: LoginPage_login },
      { path: 'fontregister',name:'loginregister-fontregister', component: LoginPage_register },
      { path: 'forgetpassword',name:'loginregister-forgetpassword', component: LoginPage_forget},
      { path: 'forgetsend',name:'loginregister-forgetsend', component: LoginPage_forgetsend },
      { path: 'registercoupon',name:'loginregister-registercoupon', component: LoginPage_registercoupon },
      { path: 'resetpassword',name:'loginregister-resetpassword', component: LoginPage_resetpassword }
     
    ]
  },
  // { path: '/forgetpassword',name:'loginregister-forgetpassword', component: LoginPage_forget },

  /* 後台 */
  { path: '/backlogin', component: backLogin },
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