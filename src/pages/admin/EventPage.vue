<template>
  <CrudPage title="活動管理" :columns="columns" :sampleData="sampleData" />
</template>

<script setup>
import CrudPage from '@/components/common/CrudPage.vue'
const columns = [
  { prop: 'eventId', label: '活動編號' },
  { prop: 'name', label: '活動名稱' },
  { prop: 'signupCount', label: '報名人數' },
  { prop: 'eventDate', label: '活動日期' ,type:"date"},
  { prop: 'signupStart', label: '報名開始時間' ,type:"datetime"},
  { prop: 'signupEnd', label: '報名結束時間' ,type:"datetime"},
  { prop: 'status', label: '狀態', type:'select', options:[{label:'報名中',value:'報名中'},{label:'已結束',value:'已結束'}] },
  { prop: 'createdAt', label: '創建時間' ,type:"datetime"}
]
const sampleData = [
  { eventId: 'E-2025-001', name: '陽明山緩步行', signupCount: 18, eventDate: '2025-08-20', signupStart: '2025-08-01 09:00', signupEnd: '2025-08-18 18:00', status: '報名中', createdAt: '2025-07-28 12:10' }
]
</script>
