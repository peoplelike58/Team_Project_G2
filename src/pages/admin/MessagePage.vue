<template>
  <CrudPage title="留言管理" 
  :columns="columns" 
  :sampleData="rows" 
  :showCreate="false"
  :showUpdate="false"
  @remove="deleteMessage"
  />
</template>

<script setup>
import { ref, onMounted } from "vue";
import CrudPage from '@/components/common/CrudPage.vue'
const columns = [
  { prop: 'MESSAGE_ID', label: '編號' ,disabled: true  },
  { prop: 'MEMBER_ID', label: '會員ID' ,disabled: true  },
  { prop: 'CONTENT', label: '留言內容' },
  { prop: 'CREATED_AT', label: '留言時間' ,type:"datetime"},
  { prop: 'IMAGE', label: '圖片', type: 'file'},
  { prop: 'MOUNTAIN_ID',   label: '山岳'},
]
// const sampleData = [
//   { id: 1, account: 'hiker01', content: '好期待這次嘉明湖活動！', createdAt: '2025-07-30 21:20' }
// ]

const rows =ref([])

const fetchMessage = () =>{
  //改相對路徑
  fetch(import.meta.env.VITE_AJAX_URL + '/MessagePage.php')
    .then(resp => resp.json())
    .then(json =>{
      rows.value = json
    })
}

const deleteMessage = (id) => {
  fetch(import.meta.env.VITE_AJAX_URL +'/MessageDelete.php',{
    method:'POST',
    headers:{
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ id })
  })
    .then(res => res.json())
    .then(data => {
      if (data.success){
        fetchMessage()
      }else{
        alert('刪除失敗')
      }
    })
}
//當頁面載入時執行
onMounted(()=>{
  fetchMessage()
})

</script>
