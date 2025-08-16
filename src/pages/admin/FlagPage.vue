<template>
  <CrudPage title="旗幟管理" :columns="columns" :sampleData="sampleData" />
</template>

<script setup>
import CrudPage from '@/components/common/CrudPage.vue'
const columns = [
  { prop: 'id', label: '編號' },
  { prop: 'name', label: '名稱' },
  { prop: 'content', label: '內容' },
  { prop: 'visible', label: '顯示狀態', type:'select', options:[{label:'顯示',value:'顯示'},{label:'隱藏',value:'隱藏'}] },
  { prop: 'createdAt', label: '創建時間' ,type:"datetime"}
]
const sampleData = [
  { id: 'F-01', name: '首頁公告', content: '高溫注意補水', visible: '顯示', createdAt: '2025-07-15 08:00' }
]
</script>
