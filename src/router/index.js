import { createRouter, createWebHistory } from 'vue-router'
import { useUserStore } from '@/stores/user'
import Member from './member' //會員中心


/* 前台 */
import WelcomePage from '@/pages/WelcomePage.vue'
import homePage from '@/pages/homePage.vue'                     //前台首頁
import allNewsPage from '@/pages/allNewsPage.vue'               //全部消息
import PeakGuide from '@/pages/PeakGuide.vue'                   //百岳之書
import togetherPage from '@/pages/togetherPage.vue'             //揪上山
import routesPage from '@/pages/routesPage.vue'                 //路線規劃
import trailDetail from '@/pages/trailDetail.vue'
import peacePage from '@/pages/peacePage.vue'                   //揪安心
import myChallenge from '@/pages/myChallenge.vue'               //百岳挑戰
import ShopPage from '@/pages/ShopPage/ShopPage.vue'            //山腳雜貨店
import LoginRegister from '@/pages/LoginPage/LoginRegister.vue' //會員登入

/* 各分頁的子頁面 */

// 商品頁
import ProductDetailRoute from '@/pages/ShopPage/ProductDetailRoute.vue'
// 商品頁-結賬流程
import Chekout1Cart from '@/pages/ShopPage/Checkout1Cart.vue'   
import Checkout2Info from '@/pages/ShopPage/Checkout2Info.vue'
import Checkout3Success from '@/pages/ShopPage/Checkout3Success.vue'
// 揪上山活動卡片
import eventCardInfo from '@/components/togetherItem/eventCardInfo.vue'

// 會員登入
import LoginPage_login from '@/components/Irene/LoginPage/LoginPage_login.vue'
import LoginPage_register from '@/components/Irene/LoginPage/LoginPage_register.vue'
import LoginPage_forget from '@/components/Irene/LoginPage/LoginPage_forget.vue'
import LoginPage_registercoupon from '@/components/Irene/LoginPage/LoginPage_registercoupon.vue'
import LoginPage_forgetsend from '@/components/Irene/LoginPage/LoginPage_forgetsend.vue'
import LoginPage_resetpassword from '@/components/Irene/LoginPage/LoginPage_resetpassword.vue'

/* 後台 */
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
    path: '/allnewspage',
    component: allNewsPage,
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
    path:'/routes/:MOUNTAIN_ID',
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
    path: '/shop/product',
    redirect: '/shop',//讓/shop/product輸入這個路徑導到商品頁
  },
  {
    path: '/shop',
    alias: '/Shop',      // 兩個都算進來
    component: ShopPage,
    children:[
      {path:'product/:id',name:'ProductDetailRoute' ,component: ProductDetailRoute ,meta: { modal: true },props: true},
      // （可選）把 params 直接變成元件的 props, // ← 子路由}
      //: 開頭的東西叫「動態參數 (Dynamic Segment)」,meta標記這是一個彈窗路由
    ]
  },

  //前台-結賬流程
  { path: '/Shop/cart',name:'Shop-cart', component: Chekout1Cart ,meta: { requiresAuth: true }},
  { path: '/Shop/info',name:'Shop-info', component: Checkout2Info ,meta: { requiresAuth: true }},
  { path: '/Shop/success', name:'Shop-success',component: Checkout3Success ,meta: { requiresAuth: true }},

  // //前台-會員登入 
  {
    path: '/loginregister',
    name: 'loginregister',
    component:LoginRegister,
    meta: { requiresGuest: true },  //  requiresGuest，用「訪客頁」標記
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
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,

  //切頁後，頁面回到置頂
  scrollBehavior(to, from, savedPosition) {
    // 1) 瀏覽器返回/前進：恢復先前滾動位置
    if (savedPosition) return savedPosition

    // 2) 有錨點：捲到對應元素
    if (to.hash) return { el: to.hash, top: 0 }

    // 3) 一般導頁：回到最上方
    return { left: 0, top: 0 }
  }
})

// 後台登入阻擋
router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem('auth') === 'true'
  const isLoginPage = to.path === '/login'

  if (!isAuthenticated && to.path.startsWith('/admin') && !isLoginPage) {
    next('/login')
  } else {
    next()
  }
})

// 前置守門員(這邊要修改isLoggedIn的條件和async 函數-因為 hydrateFromSession() 會去呼叫後端的 CheckLogin.php 裡面有非同步操作)
router.beforeEach((to, from, next) => {
  const user = useUserStore()
  // user.hydrateFromSession()
  // hydrateFromSession() 需在 store 裡實作，呼叫 CheckLogin.php 後把 email/name 寫回 state
  const isLoggedIn = user.isLoggedIn// 取得最新登入狀態（回傳 boolean）
  // const isLoggedIn = localStorage.getItem('email') //判讀是否有email值,改成從使用pinia作為登入的條件
  console.log(`從 ${from.path} 跳轉到 ${to.path}`)

  
  if (to.meta.requiresAuth && !isLoggedIn) {//登入判斷:若頁面標記 requiresAuth，但沒有 email，就導去 /login。
    alert('請先登入！')
    next('/loginregister')
    return
  }
  
  if ((to.path === '/Member' || to.path.startsWith('/loginregister')) && isLoggedIn) {//防止已登入再進登入頁,→ 登入狀態下去 /member，會自動導回會員中心。
    next({ name: 'member-profile' })
    return
  }
  next()
})


export default router


