<template>
  <CrudPage title="商品管理" :columns="columns" :sampleData="sampleData" />
</template>

<script setup>
import CrudPage from '@/components/common/CrudPage.vue'
const columns = [
  { prop: 'sku', label: '商品編號' },
  { prop: 'category', label: '商品類別' },
  { prop: 'name', label: '名稱' },
  { prop: 'price', label: '售價' },
  { prop: 'stock', label: '庫存量' },
  { prop: 'status', label: '上下架', type: 'select', options:[{label:'上架',value:'上架'},{label:'下架',value:'下架'}] }
]
const sampleData = [
  { sku: 'GEAR-001', category: '背包', name: '輕量登山包 30L', price: 2680, stock: 42, status: '上架' },
  { sku: 'GEAR-002', category: '登山杖', name: '碳纖維登山杖', price: 1890, stock: 20, status: '上架' }
]
</script>
