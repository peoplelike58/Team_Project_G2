<template>
  <CrudPage title="折價券管理" :columns="columns" :sampleData="sampleData" />
</template>

<script setup>
import CrudPage from '@/components/common/CrudPage.vue'
const columns = [
  { prop: 'couponId', label: '折價券編號' },
  { prop: 'publishDate', label: '上架日期' ,type:"date"},
  { prop: 'title', label: '標題' },
  { prop: 'expireAt', label: '到期日' ,type:"date"}
]
const sampleData = [
  { couponId: 'CPN-100', publishDate: '2025-08-01', title: '滿 2000 折 200', expireAt: '2025-09-30' }
]
</script>
