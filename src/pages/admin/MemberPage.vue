<template>
  <CrudPage title="會員管理" :columns="columns" :sampleData="sampleData" />
</template>

<script setup>
import CrudPage from '@/components/common/CrudPage.vue'
const columns = [
  { prop: 'memberNo', label: '會員編號' },
  { prop: 'account', label: '帳號' },
  { prop: 'name', label: '姓名' },
  { prop: 'phone', label: '手機' },
  { prop: 'status', label: '帳號狀態', type: 'select', options: [{label:'啟用',value:'啟用'},{label:'停用',value:'停用'}] },
  { prop: 'createdAt', label: '創建時間' ,type:"datetime"}
]
const sampleData = [
  { memberNo: 'M0001', account: 'hiker01', name: '王小山', phone: '0912-345-678', status: '啟用', createdAt: '2025-07-01 10:20' },
  { memberNo: 'M0002', account: 'trail_fox', name: '李步道', phone: '0922-111-222', status: '啟用', createdAt: '2025-07-05 09:00' }
]
</script>
