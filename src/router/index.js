import { createRouter, createWebHistory } from 'vue-router'
//後台
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
