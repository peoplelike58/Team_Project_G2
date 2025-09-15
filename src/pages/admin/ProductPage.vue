<template>
  <CrudPage title="商品管理" 
            :columns="columns" 
            :sampleData="rows" 
            @create="createProduct" 
            @update="updateProduct" 
            @remove="deleteProduct"
            @refresh="fetchProduct"
            />
<!-- @ 新/刪/修/重整 by YUKI -->

</template>

<script setup>
import { ref, onMounted } from "vue";
import CrudPage from '@/components/common/CrudPage.vue'
const porductType =[
{ label: '睡袋', value: '睡袋' },
{ label: '登山杖', value: '登山杖' },
{ label: '水瓶', value: '水瓶' },
{ label: '望遠鏡', value: '望遠鏡' },
{ label: '登山鞋', value: '登山鞋' },
{ label: '登山服飾', value: '登山服飾' },
{ label: '手電筒', value: '手電筒' },
{ label: '帳篷', value: '帳篷' },
{ label: '廚具', value: '廚具' }
]
const productColor =[
{ label: '橘/軍綠/沙色', value: '橘,軍綠,沙色' },
{ label: '黑/軍綠/沙色', value: '黑,軍綠,沙色' },
{ label: '黑/軍綠/深藍', value: '黑,軍綠,深藍' },
{ label: '黑/灰/咖啡', value: '黑,灰,咖啡' },
{ label: '黑/灰/卡其', value: '黑,灰,卡其' },
{ label: '藍/灰/紅', value: '藍,灰,紅' },
{ label: '鈦灰/銀/黑', value: '鈦灰,銀,黑' },
{ label: '鈦灰/紅/黑', value: '鈦灰,紅,黑' },
{ label: '鈦灰/砂銀/黑', value: '鈦灰,砂銀,黑' },
{ label: '橄欖綠/海軍藍/透明', value: '橄欖綠,海軍藍,透明' }
]

const productSize =[
{ label: '1人/2人/3人', value: '1人,2人,3人' },
{ label: '2人/3人/4人', value: '2人,3人,4人' },
{ label: 'S/M/L', value: 'S,M,L' },
{ label: 'M/L/XL', value: 'M,L,XL' },
{ label: '110cm/120cm/130cm', value: '110cm,120cm,130cm' },
{ label: '500ml/750ml/1L', value: '500ml,750ml,1L' },
{ label: '8x/10x/12x', value: '8x,10x,12x' },
{ label: '40/41/42', value: '40,41,42' },
{ label: '200lm/500lm/1000lm', value: '200lm,500lm,1000lm' },
{ label: '35L/45L/55L', value: '35L,45L,55L' }
]

const columns = [
  { prop: 'PRODUCT_ID', label: '商品編號' ,disabled: true },
  { prop: 'PRODUCT_NAME', label: '名稱' },
  { prop: 'PRICE', label: '售價' ,type: 'number'},
  { prop: 'IMAGE', label: '商品圖片', type: 'file'},
  { prop: 'GENDER', label: '性別', type: 'select', options:[{label:'男女皆宜',value:'unisex'},{label:'男性',value:'male'},{label:'女性',value:'female'}]},
  { prop: 'DESCRIPTION', label: '商品說明' },
  { prop: 'PRODUCT_TYPE', label: '商品類別' ,type:'select', options: porductType },
  { prop: 'COLORS', label: '顏色', type: 'select' ,options: productColor },   
  { prop: 'SIZES', label: '尺寸', type: 'select' , options: productSize },  
  { prop: 'PRODUCT_STATUS', label: '上下架', type: 'select', options:[{label:'上架',value:'上架'},{label:'下架',value:'下架'}] }
]
// const sampleData = [
//   { sku: 'GEAR-001', category: '背包', name: '輕量登山包 30L', price: 2680, stock: 42, status: '上架' },
//   { sku: 'GEAR-002', category: '登山杖', name: '碳纖維登山杖', price: 1890, stock: 20, status: '上架' }
// ]

const rows =ref([])
  
  // onMounted(()=>{
  //   fetch(import.meta.env.VITE_AJAX_URL +'/ProductPage.php')
  //     .then(resp => resp.json())
  //     .then(json =>{
  //       sampleData.value = json
  //       // console.log(sampleData);
  //     }) 

  // })

  const fetchProduct = () =>{
  //改相對路徑
  fetch(import.meta.env.VITE_AJAX_URL + '/ProductPage.php')
    .then(resp => resp.json())
    .then(json =>{
      rows.value = json
    })
}

const deleteProduct = (id) => {
  fetch(import.meta.env.VITE_AJAX_URL +'/ProductDelete.php',{
    method:'POST',
    headers:{
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ id })
  })
    .then(res => res.json())
    .then(data => {
      if (data.success){
        fetchProduct()
      }else{
        alert('刪除失敗')
      }
    })
}
const createProduct = (payload) => {
  fetch(import.meta.env.VITE_AJAX_URL +'/ProductCreate.php',{
    method:'POST',
    headers:{
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(payload)
  })
    .then(res => res.json())
    .then(data => {
      if (data.success){
        fetchProduct()
      }else{
        alert('新增失敗')
      }
    })
}

const updateProduct = (payload) => {
  fetch(import.meta.env.VITE_AJAX_URL +'/ProductUpdate.php', {
    method:'POST',
    headers:{
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(payload)
  })
  .then(res =>res.json())
  .then(data =>{
    if (data.success){
        fetchProduct()
      }else{
        alert('修改失敗')
      }
  })
}

//當頁面載入時執行
onMounted(()=>{
  fetchProduct()
})


</script>
