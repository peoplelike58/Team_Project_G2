<template>
  <div class="header">
    <div class="brand">
      <!-- <img class="logo" src="/images/logo.svg" alt="logo" /> -->
      <span>MountainPeak 後台</span>
    </div>
    <div class="actions">
      <el-button type="danger" @click="logout">登出</el-button>
    </div>
  </div>
</template>

<script setup>
// 登出按鈕動作
import { useRouter } from 'vue-router'
const router = useRouter()

function logout() {
  localStorage.removeItem('auth')
  router.push('/login')
}
</script>

<style scoped lang="scss">
.header {
  height: 56px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 12px;
  color: #e6f3ea;
  background: linear-gradient(90deg, #24452f, #2e5a3a);
}
.brand {
  display: flex; align-items: center; gap: 8px;
  font-weight: 800;
}
.logo { width: 20px; height: 20px; }
</style>
