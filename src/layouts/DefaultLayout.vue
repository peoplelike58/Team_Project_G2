<!-- src/layouts/DefaultLayout.vue -->
<template>
  <div class="layout">
    <aside class="sidebar">
      <BackHeader />
      <el-menu
        :router="true"
        :default-active="$route.path"
        background-color="#24452f"
        text-color="#e6f3ea"
        active-text-color="#ffd04b"
      >
        <el-menu-item index="/admin/home">首頁</el-menu-item>
        <el-menu-item index="/admin/members">會員管理</el-menu-item>
        <el-menu-item index="/admin/orders">訂單管理</el-menu-item>
        <el-menu-item index="/admin/products">商品管理</el-menu-item>
        <el-menu-item index="/admin/news">消息管理</el-menu-item>
        <el-menu-item index="/admin/events">活動管理</el-menu-item>
        <el-menu-item index="/admin/coupons">折價券管理</el-menu-item>
        <el-menu-item index="/admin/messages">留言管理</el-menu-item>
        <el-menu-item index="/admin/flags">旗幟管理</el-menu-item>
      </el-menu>
    </aside>
    <main class="main">
      <router-view />
      <BackFooter />
    </main>
  </div>
</template>

<script setup>
import BackHeader from '@/components/layout/backHeader.vue'
import BackFooter from '@/components/layout/backFooter.vue'
</script>

<style lang="scss" scoped>
.layout {
  display: grid;
  grid-template-columns: 240px 1fr;
  min-height: 100vh;
  background: var(--mp-bg, #f0f4f1);
}

.sidebar {
  background: #24452f;
  color: #fff;
  border-right: 1px solid rgba(255, 255, 255, 0.08);
  display: flex;
  flex-direction: column;
}

.main {
  padding: 20px;
  display: grid;
  grid-template-rows: 1fr auto;
  gap: 16px;
}
</style>
