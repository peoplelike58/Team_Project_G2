<template>
  <CrudPage title="留言管理" :columns="columns" :sampleData="sampleData" />
</template>

<script setup>
import CrudPage from '@/components/common/CrudPage.vue'
const columns = [
  { prop: 'id', label: '編號' },
  { prop: 'account', label: '帳號' },
  { prop: 'content', label: '內容' },
  { prop: 'createdAt', label: '創建時間' ,type:"datetime"}
]
const sampleData = [
  { id: 1, account: 'hiker01', content: '好期待這次嘉明湖活動！', createdAt: '2025-07-30 21:20' }
]
</script>
