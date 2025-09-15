<!-- 優惠券 -->
<template>
  <button class="coupon_btn" @click="ToRegister">
    <img src="/images/Products/ProductPage_coupon_pc.png" alt="">
  </button>
</template>
<script setup>
import { useRouter } from 'vue-router';


const router = useRouter()
const ToRegister = () => {
  router.push({name:'loginregister-fontregister'})
}

</script>

<style lang="scss" scoped>
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';

.coupon_btn {
  background-color: #fff;
  border: none;
  cursor: pointer;

  img {
    display: block;
    max-width: 100%;
    
  }
}

    
</style>