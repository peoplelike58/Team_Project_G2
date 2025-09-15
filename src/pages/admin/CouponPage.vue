<template>
  <CrudPage title="折價券管理" 
  :columns="columns" 
  :sampleData="rows"
  @create="createCoupon" 
  @update="updateCoupon" 
  @remove="deleteCoupon"
  @refresh="fetchCoupon" />
</template>

<script setup>
import { ref, onMounted } from "vue";
import CrudPage from '@/components/common/CrudPage.vue'
const columns = [
  { prop: 'COUPON_ID', label: '折價券編號'  ,disabled: true},
  { prop: 'UPLOAD_DATE', label: '上架日期' ,type:"date"},
  { prop: 'COUPON_TITLE', label: '標題' },
  { prop: 'END_AT', label: '到期日' ,type:"date"},
  { prop: 'STATUS', label: '上下架', type: 'select', options:[{label:'上架',value:'上架'},{label:'下架',value:'下架'}] }

]
// const sampleData = [
//   { couponId: 'CPN-100', publishDate: '2025-08-01', title: '滿 2000 折 200', expireAt: '2025-09-30' }
// ]

const rows =ref([])

const fetchCoupon = () =>{
  //改相對路徑
  fetch(import.meta.env.VITE_AJAX_URL + '/CouponPage.php')
    .then(resp => resp.json())
    .then(json =>{
      rows.value = json
    })
}
const deleteCoupon = (id) => {
  fetch(import.meta.env.VITE_AJAX_URL +'/CouponDelete.php',{
    method:'POST',
    headers:{
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ id })
  })
    .then(res => res.json())
    .then(data => {
      if (data.success){
        fetchCoupon()
      }else{
        alert('刪除失敗')
      }
    })
}
const createCoupon = (payload) => {
  fetch(import.meta.env.VITE_AJAX_URL +'/CouponCreate.php',{
    method:'POST',
    headers:{
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(payload)
  })
    .then(res => res.json())
    .then(data => {
      if (data.success){
        fetchCoupon()
      }else{
        alert('新增失敗')
      }
    })
}

const updateCoupon = (payload) => {
  fetch(import.meta.env.VITE_AJAX_URL +'/CouponUpdate.php', {
    method:'POST',
    headers:{
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(payload)
  })
  .then(res =>res.json())
  .then(data =>{
    if (data.success){
      fetchCoupon()
      }else{
        alert('修改失敗')
      }
  })
}

//當頁面載入時執行
onMounted(()=>{
  fetchCoupon()
})

  


</script>
