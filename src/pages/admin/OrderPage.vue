<template>
  <CrudPage title="訂單管理" :columns="columns" :sampleData="sampleData" />
</template>

<script setup>
import CrudPage from '@/components/common/CrudPage.vue'
const columns = [
  { prop: 'id', label: '編號' },
  { prop: 'orderNo', label: '訂單編號' },
  { prop: 'account', label: '帳號' },
  { prop: 'payStatus', label: '付款狀態', type:'select', options:[{label:'未付款',value:'未付款'},{label:'已付款',value:'已付款'},{label:'退款',value:'退款'}] },
  { prop: 'shipStatus', label: '貨運狀態', type:'select', options:[{label:'備貨中',value:'備貨中'},{label:'已出貨',value:'已出貨'},{label:'已送達',value:'已送達'}] },
  { prop: 'orderStatus', label: '訂單狀態', type:'select', options:[{label:'處理中',value:'處理中'},{label:'完成',value:'完成'},{label:'取消',value:'取消'}] },
  { prop: 'orderDate', label: '訂購日期' ,type:"datetime"}
]
const sampleData = [
  { id: 1, orderNo: 'OD20250701001', account: 'hiker01', payStatus: '已付款', shipStatus: '已送達', orderStatus: '完成', orderDate: '2025-07-02-11:11' },
  { id: 2, orderNo: 'OD20250702011', account: 'trail_fox', payStatus: '未付款', shipStatus: '備貨中', orderStatus: '處理中', orderDate: '2025-07-03-22:22' }
]
</script>
