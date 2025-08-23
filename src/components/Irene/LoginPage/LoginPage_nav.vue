<script setup>

</script>

<template>
    <nav class="menu" >
        <!-- 🟡 改為 ul/li 結構 -->
        <ul class="menu__list">
          <li class="menu__item"><RouterLink to="/Member/profile">個人資料</RouterLink></li>
          <li class="menu__item"><RouterLink to="/Member/orders">我的訂單</RouterLink></li>
          <li class="menu__item"><RouterLink to="/Member/collections">商品收藏</RouterLink></li>
          <li class="menu__item"><RouterLink to="/Member/messages">我的留言</RouterLink></li>
          <li class="menu__item"><RouterLink to="/Member/activitys">我的活動</RouterLink></li>
          <li class="menu__item"><RouterLink to="/Member/coupons">我的優惠</RouterLink></li>
          <li class="menu__item"><RouterLink to="/Member/challenges">百岳挑戰</RouterLink></li>
        </ul>
    </nav>
</template>

<style lang="scss" scoped>
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';

.menu__list { 
  @include flexcenter(16px,column);
  :deep(a){ //:deep(a) 的意思是「穿透 scoped 的邊界，往子元件裡router自動產生的深層的 <a> 套樣式」。所以這一條能確保連 <RouterLink> 產生的 <a> 也會吃到字色、粗細、去底線等設定
  color:$black-14; 
  text-decoration:none; 
  font-weight:$semiBold; 
  }
  /* 啟用中的路由高亮標示 */
  :deep(.router-link-active){ 
    color:$black-14; 
    position:relative; 
  }
  :deep(.router-link-active)::before{//在這個位置時掛在active的屬性，產生偽元素的亮標
    content:''; 
    position:absolute; 
    left:-12px; 
    top:6px; 
    width:4px; 
    height:12px; 
    background:$black-14; 
    border-radius:2px;
  }
}
</style>