<template>
  <CrudPage title="活動管理" 
  :columns="columns" 
  :sampleData="rows" 
  @create="createEvent" 
  @update="updateEvent" 
  @remove="deleteEvent"
  @refresh="fetchEvent"
  />
  <!-- @ 新/刪/修/重整 by YUKI -->

</template>

<script setup>
import { ref, onMounted } from "vue";
import CrudPage from '@/components/common/CrudPage.vue'

const mountainOptions = [
  { label: '大霸尖山', value: 1 },
  { label: '奇萊主峰', value: 2 },
  { label: '合歡山主峰', value: 3 },
  { label: '能高主峰', value: 4 },
  { label: '雪山主峰', value: 5 },
  { label: '秀姑巒山', value: 6 },
  { label: '南湖大山', value: 7 },
  { label: '品田山', value: 8 },
  { label: '關山', value: 9 },
  { label: '玉山主峰', value: 10 },
  { label: '七星山主峰', value: 11 },
  { label: '烏嘴稜來山國家步道', value: 12 },
  { label: '聖母登山步道（抹茶山）', value: 13 },
  { label: '火炎山', value: 14 },
  { label: '奮起湖大凍山步道', value: 15 },
  { label: '獅頭山', value: 16 },
  { label: '忘憂森林', value: 17 },
  { label: '瓊崖山', value: 18 },
  { label: '六十石山', value: 19 },
  { label: '龍過脈山', value: 20 },
  { label: '象山步道', value: 21 },
  { label: '大坑九號步道', value: 22 },
  { label: '烏山步道', value: 23 },
  { label: '瓦拉米步道', value: 24 },
  { label: '觀霧瀑布步道', value: 25 }
]


const columns = [
  { prop: 'EVENT_ID', label: '活動編號' },
  { prop: 'EVENT_NAME', label: '活動名稱' },
  { prop: 'JOIN_QTY', label: '報名人數' },
  { prop: 'EVENT_DATE', label: '活動日期' ,type:"date"},
  { prop: 'EVENT_TIME', label: '活動時間' ,type:"time"},
  { prop: 'START_DATE', label: '報名開始日期' ,type:"date"},
  { prop: 'START_TIME', label: '報名開始時間' ,type:"time"},
  { prop: 'END_DATETIME', label: '報名結束時間' ,type:"datetime"},
  { prop: 'STATUS', label: '狀態', type:'select', options:[{label:'報名中',value:'報名中'},{label:'已結束',value:'已結束'}] },
  { prop: 'CREATED_AT', label: '創建時間' ,type:"datetime"},
  { prop: 'CONTENT', label: '活動簡介' },
  { prop: 'MEETING_PLACE', label: '集合地點' },
  { prop: 'DISTANCE', label: '路程 ' },
  { prop: 'MOUNTAIN_ID',   label: '山岳', type:'select', options: mountainOptions },

]


// const sampleData = [
//   { eventId: 'E-2025-001', name: '陽明山緩步行', signupCount: 18, eventDate: '2025-08-20', signupStart: '2025-08-01 09:00', signupEnd: '2025-08-18 18:00', status: '報名中', createdAt: '2025-07-28 12:10' }
// ]

const rows =ref([])
  
  // onMounted(()=>{
  //   fetch(import.meta.env.VITE_AJAX_URL +'/EventPage.php')
  //     .then(resp => resp.json())
  //     .then(json =>{
  //       sampleData.value = json
  //       // console.log(sampleData);
  //     }) 

      //  fetch(ajax_url + '/Mountain_Peak/EventPage.php')
      // .then(resp => resp.json())
      // .then(json =>{
      //   sampleData.value = json
      //   // console.log(sampleData);
      // }) 

  // })

  const fetchEvent = () =>{
  //改相對路徑
  fetch(import.meta.env.VITE_AJAX_URL + '/EventPage.php')
    .then(resp => resp.json())
    .then(json =>{
      rows.value = json
    })
}

const deleteEvent = (id) => {
  fetch(import.meta.env.VITE_AJAX_URL +'/EventDelete.php',{
    method:'POST',
    headers:{
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ id })
  })
    .then(res => res.json())
    .then(data => {
      if (data.success){
        fetchEvent()
      }else{
        alert('刪除失敗')
      }
    })
}
const createEvent = (payload) => {
  fetch(import.meta.env.VITE_AJAX_URL +'/EventCreate.php',{
    method:'POST',
    headers:{
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(payload)
  })
    .then(res => res.json())
    .then(data => {
      if (data.success){
        fetchEvent()
      }else{
        alert('新增失敗')
      }
    })
}

const updateEvent = (payload) => {
  fetch(import.meta.env.VITE_AJAX_URL +'/EventUpdate.php', {
    method:'POST',
    headers:{
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(payload)
  })
  .then(res =>res.json())
  .then(data =>{
    if (data.success){
      fetchEvent()
      }else{
        alert('修改失敗')
      }
  })
}

//當頁面載入時執行
onMounted(()=>{
  fetchEvent()
})

  

</script>
