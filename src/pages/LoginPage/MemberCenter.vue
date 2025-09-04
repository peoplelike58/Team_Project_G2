<template>
<div class="wrapper">
  <NavMenu/>
  <main>
    <div class="member-layout">
      <LoginPage_nav class="member-nav" />
      <section class="member-content">
        <RouterView />
        <div class="logout">
          <button @click="handlelogout">登出</button>
        </div>
      </section>
    </div>
  </main>
  <brandFooter/>
</div>
</template>

<script setup>
import LoginPage_nav from '@/components/Irene/LoginPage/LoginPage_nav.vue';
import NavMenu from '@/components/An/navMenu.vue';
import brandFooter from '@/components/An/footer.vue'


import { useRouter, onBeforeRouteLeave } from 'vue-router'
import { useUserStore } from '@/stores/user'

const router = useRouter()
const user = useUserStore()



// 組件內的守門員 - 離開前確認,這裡不一定要
// onBeforeRouteLeave((to, from, next) => {
//   const answer = window.confirm('確定要離開個人資料頁嗎？')
//   if (answer) {
//     next()
//   } else {
//     next(false)
//   }
// })

// 一般會員登出後，移除 storage
const handlelogout = () => {
  const answer = window.confirm('確定要登出嗎？')
  if (answer) {
    // localStorage.removeItem('email')
    // localStorage.removeItem('password')
   fetch('/tjd102/g2/PHP/LoginPage_fontLogout.php', {  //http://localhost/teamproject/LoginPage_fontLogout.php（local端測試）
    method: 'POST',
    credentials: 'include'
  })
    user.logout()
    alert('已登出')
    router.push({name:'loginregister-fontrelogin'})
  }
}

</script>


<style scoped lang="scss">
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';

.wrapper{
  width: 100%;
  background-color: $bg-gray;
}
main{
  padding: 10vh 2vh;
  .member-layout{
  max-width: 1200px;
  margin: auto;
  display: grid;
  grid-template-columns: 0.15fr 0.85fr;//第一欄：固定 200px 寬度（放左側導航欄),第二欄：1fr 表示佔滿剩餘空間（放主內容）
  min-height: 100vh;
  }
  .logout{
  max-width: 1000px;
  box-sizing: border-box;
  padding: 40px 0;
  position: fixed;
  bottom:0;
  right: 40px;
  button{
    @include btn(8px);
    padding: 8px 16px;
    color: #fff;
    background-color: $black-14;
    display: block;
    margin: auto;
    font-size: $pcFont-p-s;
    font-weight: $medium;
    }}
}

@include mq(430px) {
  main{
    .member-layout{
      grid-template-columns: 100px 1fr;
    }
  }
}
</style>