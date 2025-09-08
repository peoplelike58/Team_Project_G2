<template>
  <CrudPage title="活動管理" :columns="columns" :sampleData="sampleData" />
</template>

<script setup>
import { ref, onMounted } from "vue";
import CrudPage from '@/components/common/CrudPage.vue'
const columns = [
  { prop: 'EVENT_ID', label: '活動編號' },
  { prop: 'EVENT_NAME', label: '活動名稱' },
  { prop: 'JOIN_QTY', label: '報名人數' },
  { prop: 'EVENT_DATE', label: '活動日期' ,type:"date"},
  { prop: 'START_DATE', label: '報名開始時間' ,type:"datetime"},
  { prop: 'END_DATETIME', label: '報名結束時間' ,type:"datetime"},
  { prop: 'STATUS', label: '狀態', type:'select', options:[{label:'報名中',value:'報名中'},{label:'已結束',value:'已結束'}] },
  { prop: 'CREATED_AT', label: '創建時間' ,type:"datetime"}
]
// const sampleData = [
//   { eventId: 'E-2025-001', name: '陽明山緩步行', signupCount: 18, eventDate: '2025-08-20', signupStart: '2025-08-01 09:00', signupEnd: '2025-08-18 18:00', status: '報名中', createdAt: '2025-07-28 12:10' }
// ]

const sampleData =ref([])
  
  onMounted(()=>{
    fetch('http://localhost/Mountain_Peak/EventPage.php')
      .then(resp => resp.json())
      .then(json =>{
        sampleData.value = json
        // console.log(sampleData);
      }) 

  })


</script>
