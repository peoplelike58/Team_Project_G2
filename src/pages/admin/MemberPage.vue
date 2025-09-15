<template>
  <CrudPage title="會員管理" 
  :columns="columns" 
  :sampleData="rows" 
  @update="updateMember"
  
  />
</template>

<script setup>
import { ref, onMounted } from "vue";
import CrudPage from '@/components/common/CrudPage.vue'
const columns = [
  { prop: 'MEMBER_ID', label: '會員ID',disabled: true  },
  { prop: 'EMAIL', label: '會員帳號' ,disabled: true},
  { prop: 'PW', label: '密碼' , type: 'password',disabled: true},
  { prop: 'NICKNAME', label: '暱稱' ,disabled: true},
  { prop: 'NAME', label: '姓名' ,disabled: true},
  { prop: 'BIRTHDAY', label: '生日' ,disabled: true},
  { prop: 'PHONE', label: '電話',disabled: true },
  { prop: 'ADDRESS', label: '地址' ,disabled: true},
  { prop: 'CREATED_AT', label: '創建時間' ,type:"date",disabled: true},
  { prop: 'STATUS', label: '帳號狀態', type: 'select', options: [{label:'啟用',value:'啟用'},{label:'停用',value:'停用'}] },
]
// const sampleData = [
//   { memberNo: 'M0001', account: 'hiker01', name: '王小山', phone: '0912-345-678', status: '啟用', createdAt: '2025-07-01 10:20' },
//   { memberNo: 'M0002', account: 'trail_fox', name: '李步道', phone: '0922-111-222', status: '啟用', createdAt: '2025-07-05 09:00' }
// ]

const rows =ref([])


const fetchMember = () =>{
  fetch(import.meta.env.VITE_AJAX_URL + '/MemberPage.php')
    .then(resp => resp.json())
    .then(json =>{
      rows.value = json
    })
}

const updateMember = (payload) => {
  fetch(import.meta.env.VITE_AJAX_URL +'/MemberUpdate.php', {
    method:'POST',
    headers:{
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(payload)
  })
  .then(res =>res.json())
  .then(data =>{
    if (data.success){
      fetchMember()
      }else{
        alert('修改失敗')
      }
  })
}

//當頁面載入時執行
onMounted(()=>{
  fetchMember()
})

</script>
