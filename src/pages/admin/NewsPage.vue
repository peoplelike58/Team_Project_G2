<template>
  <CrudPage title="消息管理" 
            :columns="columns" 
            :sampleData="rows" 
            @create="createNews" 
            @update="updateNews" 
            @remove="deleteNews"
            @refresh="fetchList"
            />
<!-- @ 新/刪/修/重整 by YUKI -->
</template>

<script setup>
import { ref, onMounted } from "vue";
import CrudPage from '@/components/common/CrudPage.vue'
const columns = [
  { prop: 'NEWS_ID', label: '文章編號' },
  { prop: 'UPLOAD_AT', label: '上架日期' ,type:"date"},
  { prop: 'TYPE', label: '分類' },
  { prop: 'TITLE', label: '標題' },
  { prop: 'STATUS', label: '上下架', type:'select', options:[{label:'上架',value:'上架'},{label:'下架',value:'下架'}] },
  { prop: 'UPDATED_AT', label: '最後更新日期' ,type:"date"}
]
// const sampleData = [
//   { postId: 'N-001', publishDate: '2025-07-10', category: '安全小知識', title: '夏季補水要點', status: '上架', updatedAt: '2025-07-12' }
// ]

  const rows =ref([])
  // const sampleData =ref([])
  
  // onMounted(()=>{
  //   fetch('http://localhost/Mountain_Peak/NewsPage.php')
  //     .then(resp => resp.json())
  //     .then(json =>{
  //       sampleData.value = json
  //       // console.log(sampleData);
  //     }) 

  // })

const fetchList = () =>{
  //改相對路徑
  fetch(import.meta.env.VITE_AJAX_URL + '/NewsPage.php')
  // fetch('http://localhost/Mountain_Peak/NewsPage.php')
    .then(resp => resp.json())
    .then(json =>{
      rows.value = json
    })
}

const deleteNews = (id) => {
  fetch(import.meta.env.VITE_AJAX_URL +'/NewsDelete.php',{
    method:'POST',
    headers:{
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ id })
  })
    .then(res => res.json())
    .then(data => {
      if (data.success){
        rows.value = rows.value.filter(item=>item.NEWS_ID !==id)
      }else{
        alert('刪除失敗')
      }
    })
}
const createNews = (payload) => {
  fetch(import.meta.env.VITE_AJAX_URL +'/NewsCreat.php',{
    method:'POST',
    headers:{
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(payload)
  })
    .then(res => res.json())
    .then(data => {
      if (data.success){
        fetchList()
      }else{
        alert('新增失敗')
      }
    })
}

//當頁面載入時執行
onMounted(()=>{
  fetchList()
})


</script>
