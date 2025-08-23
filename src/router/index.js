import { createRouter, createWebHistory } from 'vue-router'
//後台
import Home from '@/pages/backHome.vue'
import About from '@/pages/backAbout.vue'
import NotFound from '@/pages/backNotFound.vue'

import MemberPage from '@/pages/admin/MemberPage.vue'
import OrderPage from '@/pages/admin/OrderPage.vue'
import ProductPage from '@/pages/admin/ProductPage.vue'
import NewsPage from '@/pages/admin/NewsPage.vue'
import EventPage from '@/pages/admin/EventPage.vue'
import CouponPage from '@/pages/admin/CouponPage.vue'
import MessagePage from '@/pages/admin/MessagePage.vue'
import FlagPage from '@/pages/admin/FlagPage.vue'

//前台-商品頁
import ShopPage from '@/pages/ShopPage/ShopPage.vue'
import ProductDetailRoute from '@/pages/ShopPage/ProductDetailRoute.vue'

//前台-結賬流程
import Chekout1Cart from '@/pages/ShopPage/Checkout1Cart.vue'   
import Checkout2Info from '@/pages/ShopPage/Checkout2Info.vue'
import Checkout3Success from '@/pages/ShopPage/Checkout3Success.vue'

//會員中心
import MemberCenter from '@/pages/LoginPage/MemberCenter.vue'
import LoginPage_hundredpeakschallenge from '@/components/Irene/LoginPage/LoginPage_hundredpeakschallenge.vue'
import LoginPage_myactivity from '@/components/Irene/LoginPage/LoginPage_myactivity.vue'
import LoginPage_mycollection from '@/components/Irene/LoginPage/LoginPage_mycollection.vue'
import LoginPage_mycoupon from '@/components/Irene/LoginPage/LoginPage_mycoupon.vue'
import LoginPage_mymessage from '@/components/Irene/LoginPage/LoginPage_mymessage.vue'
import LoginPage_myorder from '@/components/Irene/LoginPage/LoginPage_myorder.vue'
import LoginPage_myprofile from '@/components/Irene/LoginPage/LoginPage_myprofile.vue'
const routes = [

  //前台-商品頁
  {path: '/Shop',
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
  
  //會員中心
  {path: '/Member'
    ,name:'Member', 
    component: MemberCenter ,
    children:[
      { path: '', redirect: { name: 'member-profile' } },
      { path: 'profile',name:'member-profile', component: LoginPage_myprofile,},
      { path: 'orders',name:'member-orders', component: LoginPage_myorder,},
      { path: 'collections',name:'member-collections', component: LoginPage_mycollection,},
      { path: 'messages',name:'member-messages', component: LoginPage_mymessage,},
      { path: 'activitys',name:'member-activitys', component: LoginPage_myactivity,},
      { path: 'coupons',name:'member-coupons', component: LoginPage_mycoupon,},
      { path: 'challenges',name:'member-challenges', component: LoginPage_hundredpeakschallenge,},
    ]
  },
  
  


  //後台
  { path: '/', redirect: '/admin/members' },
  { path: '/home', component: Home },
  { path: '/about', component: About },
  { path: '/admin/members', component: MemberPage },
  { path: '/admin/orders', component: OrderPage },
  { path: '/admin/products', component: ProductPage },
  { path: '/admin/news', component: NewsPage },
  { path: '/admin/events', component: EventPage },
  { path: '/admin/coupons', component: CouponPage },
  { path: '/admin/messages', component: MessagePage },
  { path: '/admin/flags', component: FlagPage },
  { path: '/:pathMatch(.*)*', name: 'NotFound', component: NotFound }
]

export default createRouter({
  history: createWebHistory(),
  routes
})
