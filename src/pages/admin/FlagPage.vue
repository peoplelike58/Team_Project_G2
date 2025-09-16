<template>
  <CrudPage title="旗幟管理" 
  :columns="columns" 
  :sampleData="rows" 
  :showCreate="false"
  :showUpdate="false"
  />
</template>

<script setup>
import { ref, onMounted } from "vue";
import CrudPage from '@/components/common/CrudPage.vue'
const columns = [
  { prop: 'MEMBER_ID', label: '會員ID' },
  { prop: 'BIG_TARGET', label: '大百岳目標' },
  { prop: 'SMALL_TARGET', label: '小百岳目標' },
  { prop: 'CREATED_AT', label: '創建時間' ,type:"datetime"}
]
// const sampleData = [
//   { id: 'F-01', name: '首頁公告', content: '高溫注意補水', visible: '顯示', createdAt: '2025-07-15 08:00' }
// ]

const rows =ref([])

const fetchFlag = () =>{
  //改相對路徑
  fetch(import.meta.env.VITE_AJAX_URL + '/FlagPage.php')
    .then(resp => resp.json())
    .then(json =>{
      rows.value = json
    })
}

//當頁面載入時執行
onMounted(()=>{
  fetchFlag()
})


</script>
