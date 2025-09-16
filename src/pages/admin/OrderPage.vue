<template>
  <CrudPage title="訂單管理" 
  :columns="columns" 
  :sampleData="rows"
  :showCreate="false"
  :showDelete="false"
  @update="updateOrder" 
  @refresh="fetchOrder"
  />
</template>

<script setup>
import { ref, onMounted } from "vue";
import CrudPage from '@/components/common/CrudPage.vue'
const columns = [
  { prop: 'ORDER_ID', label: '訂單編號' ,disabled: true},
  { prop: 'MEMBER_ID', label: '會員編號' ,disabled: true},
  { prop: 'ORDER_STATUS', label: '訂單狀態' },
  { prop: 'PAYMENT', label: '付款方式',disabled: true},
  { prop: 'PAY_STATUS', label: '付款狀態', type:'select', options:[{label:'未付款',value:'未付款'},{label:'已付款',value:'已付款'},{label:'付款失敗',value:'付款失敗'},{label:'退款中',value:'退款中'},{label:'已退款',value:'已款款'}] },
  { prop: 'DELIVERY', label: '運送方式',disabled: true },
  { prop: 'DEL_STATUS', label: '運送狀態', type:'select', options:[{label:'待出貨',value:'待出貨'},{label:'已出貨',value:'已出貨'},{label:'已送達',value:'已送達'},{label:'已取貨',value:'已取貨'},{label:'已退貨',value:'已退貨'}] },
  { prop: 'ORDER_AT', label: '訂購日期',type:'datetime',disabled: true},
  { prop: 'SUBTTL', label: '商品金額', type: 'number'},
  { prop: 'DISCOUNT', label: '折扣金額', type: 'number'},
  { prop: 'SHIPPINGFEE', label:'運費', type: 'number'},
  { prop: 'TTL_AMT', label: '訂單金額' ,type:'number'},
  { prop: 'MEMBER_NAME', label: '收件人' ,disabled: true},
  { prop: 'MEMBER_PHONE', label: '收件人電話',disabled: true },
  { prop: 'MEMBER_ADDRESS', label: '收件地址' ,disabled: true},

]
// const sampleData = [
//   { id: 1, orderNo: 'OD20250701001', account: 'hiker01', payStatus: '已付款', shipStatus: '已送達', orderStatus: '完成', orderDate: '2025-07-02-11:11' },
//   { id: 2, orderNo: 'OD20250702011', account: 'trail_fox', payStatus: '未付款', shipStatus: '備貨中', orderStatus: '處理中', orderDate: '2025-07-03-22:22' }
// ]

const rows =ref([])

const fetchOrder = () =>{
  //改相對路徑
  fetch(import.meta.env.VITE_AJAX_URL + '/OrderPage.php')
    .then(resp => resp.json())
    .then(json =>{
      rows.value = json
    })
}

const updateOrder = (payload) => {
  fetch(import.meta.env.VITE_AJAX_URL +'/OrderUpdate.php', {
    method:'POST',
    headers:{
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(payload)
  })
  .then(res =>res.json())
  .then(data =>{
    if (data.success){
        fetchOrder()
      }else{
        alert('修改失敗')
      }
  })
}

//當頁面載入時執行
onMounted(()=>{
  fetchOrder()
})

</script>
