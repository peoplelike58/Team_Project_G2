<template>
  <CrudPage title="商品管理" :columns="columns" :sampleData="sampleData" />
</template>

<script setup>
import { ref, onMounted } from "vue";
import CrudPage from '@/components/common/CrudPage.vue'
const columns = [
  { prop: 'PRODUCT_ID', label: '商品編號' },
  { prop: 'PRODUCT_TYPE', label: '商品類別' },
  { prop: 'PRODUCT_NAME', label: '名稱' },
  { prop: 'PRICE', label: '售價' },
  { prop: 'stock', label: '庫存量' },
  { prop: 'PRODUCT_STATUS', label: '上下架', type: 'select', options:[{label:'上架',value:'上架'},{label:'下架',value:'下架'}] }
]
// const sampleData = [
//   { sku: 'GEAR-001', category: '背包', name: '輕量登山包 30L', price: 2680, stock: 42, status: '上架' },
//   { sku: 'GEAR-002', category: '登山杖', name: '碳纖維登山杖', price: 1890, stock: 20, status: '上架' }
// ]

const sampleData =ref([])
  
  onMounted(()=>{
    fetch(import.meta.env.VITE_AJAX_URL +'/ProductPage.php')
      .then(resp => resp.json())
      .then(json =>{
        sampleData.value = json
        // console.log(sampleData);
      }) 

  })



</script>
