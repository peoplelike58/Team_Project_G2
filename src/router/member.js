

//會員中心
import MemberCenter from '@/pages/LoginPage/MemberCenter.vue'
import LoginPage_hundredpeakschallenge from '@/components/Irene/LoginPage/LoginPage_hundredpeakschallenge.vue'
import LoginPage_myactivity from '@/components/Irene/LoginPage/LoginPage_myactivity.vue'
import LoginPage_mycollection from '@/components/Irene/LoginPage/LoginPage_mycollection.vue'
import LoginPage_mycoupon from '@/components/Irene/LoginPage/LoginPage_mycoupon.vue'
import LoginPage_mymessage from '@/components/Irene/LoginPage/LoginPage_mymessage.vue'
import LoginPage_myorder from '@/components/Irene/LoginPage/LoginPage_myorder.vue'
import LoginPage_myprofile from '@/components/Irene/LoginPage/LoginPage_myprofile.vue'

export default[

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
    ]}
]

