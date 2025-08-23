<template>
  <main class="member-layout">
    <LoginPage_nav class="member-nav" />
    <section class="member-content">
      <RouterView />
    </section>
  </main>
</template>

<script setup>
import LoginPage_nav from '@/components/Irene/LoginPage/LoginPage_nav.vue';




</script>


<style scoped lang="scss">
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';

main{
  max-width: 1200px;
  margin: auto;
  display: grid;
  grid-template-columns: 200px 1fr;//第一欄：固定 250px 寬度（放左側導航欄),第二欄：1fr 表示佔滿剩餘空間（放主內容）
  min-height: 100vh;
  padding: 20vh 0;
}
</style>