<template>
  <div class="header">
    <div class="brand">
      <!-- <img class="logo" src="/images/logo.svg" alt="logo" /> -->
      <span>MountainPeak 後台</span>
    </div>
    <!-- <div class="actions">
      <el-button size="small" type="primary" @click="$router.push('/admin/events')">活動</el-button>
    </div> -->
  </div>
</template>

<script setup></script>

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
