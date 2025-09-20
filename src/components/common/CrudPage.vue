<template>
  <div class="panel">
    <div class="toolbar">
      <div class="left">
        <h2 class="title">{{ title }}</h2>
      </div>
      <div class="right">
        <el-input v-model="keyword" placeholder="關鍵字搜尋" clearable style="max-width: 240px" />
        <el-button v-if="props.showCreate" type="primary" @click="openCreate">新增</el-button>
        <el-button @click="resetData">重置</el-button>
      </div>
    </div>

    <el-table :data="pagedData" border stripe>
      <el-table-column
        v-for="col in columns"
        :key="col.prop"
        :prop="col.prop"
        :label="col.label"
        :min-width="col.minWidth || 120"
      />
      <el-table-column label="操作" fixed="right" width="180">
        <template #default="{ row, $index }">
          <!-- <el-button size="small" @click="openEdit(row, indexOf($index))">編輯</el-button> -->
          <el-button v-if="props.showUpdate" size="small" type="success" @click="openEdit(row)">編輯</el-button>
          <!-- <el-button size="small" type="danger" @click="remove(indexOf($index))">刪除</el-button> -->  
          <el-button v-if="props.showDelete" size="small" type="danger" @click="removeClick(row)">刪除</el-button>   
        </template>
      </el-table-column>
    </el-table>

    <el-pagination
      v-if="total > pageSize"
      style="margin-top: 12px"
      layout="prev, pager, next"
      :total="total"
      :page-size="pageSize"
      @current-change="(p)=> currentPage = p"
    />

    <el-dialog v-model="dialogVisible" :title="dialogMode==='create' ? `新增${title}` : `編輯${title}`" width="600">
      <el-form :model="form" label-width="120px">
        <template v-for="col in columns" :key="col.prop">
          <!-- 新增圖片上傳欄位的判斷式（YUKI） -->
          <el-form-item :label="col.label">
            <!-- 圖片上傳欄位 -->
             <template v-if="col.type ==='file'">
              <el-upload
                  class="upload-img"
                  :action="uploadUrl"
                  name="file"
                  :show-file-list="false"
                  :on-success="(res) => { 
                    if(res?.success){ 
                      form[col.prop] = res.filename  //只存檔名res.filename 
                      console.log('上傳成功，檔名：', res.filename);
                      // const imgUrl = getImageUrl();
                    } else {
                       console.log(res?.message || '上傳失敗');
                        }}"
                  >
           
                  <el-button type="primary">上傳圖片</el-button>
                  <!-- 預覽縮圖 -->
                </el-upload>
                <img v-if="form[col.prop]" :src="getImageUrl() + '/images/Products/products/' + form[col.prop]"style="max-width:100px; margin-top:5px;" @error="(e) => console.log('載入錯誤詳情:', e.target.src, e)" />
                <!-- <img v-if="form[col.prop]" :src="getImageUrl()  + form[col.prop]" style="max-width:100px; margin-top:5px;" @error="(e) => console.log('載入錯誤詳情:', e.target.src, e)" /> -->
             </template>

             <template v-else>
                <component
                :is="resolveInput(col)"
                v-model="form[col.prop]"
                :type="col.type === 'datetime' ? 'datetime' : col.type === 'date' ? 'date' : undefined"
                :show-password="col.type === 'password'"
                :placeholder="`請輸入${col.label}`"
                :disabled="col.disabled"  
                :options="col.options"
                :value-format="col.valueFormat || (col.type === 'datetime' ? 'YYYY-MM-DD HH:mm' : col.type === 'date' ? 'YYYY-MM-DD' : undefined)"
                :format="col.format || (col.type === 'datetime' ? 'YYYY-MM-DD HH:mm' : col.type === 'date' ? 'YYYY-MM-DD' : undefined)"
                style="width:100%"
              />
             </template>
          </el-form-item>


          <!-- <el-form-item :label="col.label">
            <component
              :is="resolveInput(col)"
              v-model="form[col.prop]"
              :type="col.type === 'datetime' ? 'datetime' : col.type === 'date' ? 'date' : undefined"
              :placeholder="`請輸入${col.label}`"
              :options="col.options"
              :value-format="col.valueFormat || (col.type === 'datetime' ? 'YYYY-MM-DD HH:mm' : col.type === 'date' ? 'YYYY-MM-DD' : undefined)"
              :format="col.format || (col.type === 'datetime' ? 'YYYY-MM-DD HH:mm' : col.type === 'date' ? 'YYYY-MM-DD' : undefined)"
              style="width:100%"
            />
          </el-form-item> -->
        </template>
      </el-form>
      <template #footer>
        <el-button @click="dialogVisible=false">取消</el-button>
        <el-button type="primary" @click="submit">確認</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'


// const imagePreviewUrl = computed(()=>{
//   if(form.IMAGE){
//     const imgUrl = this.getImageUrl();
//       return `${imgUrl}/images/Products/${this.form.IMAGE}`;
//   }
//   return null;
// })


// const imagePreview = (uploadFile, propName) => {
//   if (uploadFile && uploadFile.raw) {
//     const reader = new FileReader()
//     reader.onload = (e) => {
//       form[propName] = e.target.result
//     }
//     reader.readAsDataURL(uploadFile.raw)
//   }
// }

const props = defineProps({
  title: { type: String, required: true },
  columns: { type: Array, required: true }, // [{prop,label,type?,options?}]
  sampleData: { type: Array, default: () => [] },

  //控制 新增 & 刪除 & 編輯 Button
  showCreate: {type: Boolean, default: true},
  showDelete: {type: Boolean, default: true},
  showUpdate: {type: Boolean, default: true}

})

//改成用computed更新時自動刷新(YUKI)
const data = computed(() => props.sampleData)
//==========================================

// const data = ref([...props.sampleData])
const keyword = ref('')
const dialogVisible = ref(false)
const dialogMode = ref('create')
// const editIndex = ref(-1)    //(不抓index , 改成通知父層處理，用emit傳遞 抓ID)/YUKI
const form = ref({})
const pageSize = 10
let currentPage = ref(1)

//========================= 
//新增 emit： 通知父層處理(YUKI)
const emit = defineEmits(['create', 'update','refresh' ,'remove'])

//預設抓第一個欄位"id"
const idKey = computed(() => props.columns[0]?.prop ||'id')

//=========================

const uploadUrl = import.meta.env.VITE_AJAX_URL + '/uploadimg.php'

const getImageUrl = () => {
  console.log('當前端口:', window.location.port);
  if (import.meta.env.MODE === 'development') {
    // console.log('開發環境，返回後端路徑');  
    return 'http://localhost/TeamProject/public';
  } else {
    // console.log('正式環境，使用當前域名');
    return `${window.location.protocol}//${window.location.host}/TeamProject`;
  }
}

const filtered = computed(() => {
  if (!keyword.value) return data.value
  return data.value.filter(r => Object.values(r).some(v => String(v ?? '').includes(keyword.value)))
})
const total = computed(() => filtered.value.length)
const pagedData = computed(() => {
  const start = (currentPage.value - 1) * pageSize
  return filtered.value.slice(start, start + pageSize)
})

const resolveInput = (col) => {
  if (col.type === 'select') return 'el-select'
  if (col.type === 'date' || col.type === 'datetime') return 'el-date-picker'
  if (col.type === 'password') return 'el-input'   // 密碼用 
  return 'el-input'
}

const openCreate = () => {
  dialogMode.value = 'create'
  form.value = Object.fromEntries(props.columns.map(c => [c.prop, '']))
  dialogVisible.value = true
}
//不抓index,  改抓id(YUKI)
//========================= 
const openEdit = (row) => {
//=========================

// const openEdit = (row, index) => {
  dialogMode.value = 'edit'
  // editIndex.value = index
  form.value = JSON.parse(JSON.stringify(row))
  dialogVisible.value = true
}
const submit = () => {
  if (dialogMode.value === 'create') {
    emit('create', {...form.value})  //YUKI
    // data.value.unshift({ ...form.value, _id: Date.now() })
  } else {
    emit('update', {...form.value})  //YUKI
    // data.value.splice(editIndex.value, 1, { ...form.value })
  }
  dialogVisible.value = false
}
// const indexOf = ($index) => (currentPage.value - 1) * pageSize + $index
// const remove = (absIndex) => data.value.splice(absIndex, 1)
// const resetData = () => { data.value = [...props.sampleData]; keyword.value = ''; currentPage.value = 1 }


//========================= 
const removeClick = (row) => {
  emit('remove', row[idKey.value])  //告訴父層要刪除的這一個欄位
}
const resetData = () => {
  keyword.value=''
  currentPage.value = 1
  emit('refresh')
}
//=========================



//========================= 監聽對話框關閉
watch(dialogVisible, (isOpen)=>{
  if(!isOpen){
    //對話框關閉時，清空圖片
    props.columns.forEach(col=>{
      if(col.type === 'file'){
        form[col.prop] = ''
      }
    })
  }
})
//=========================


</script>

<style scoped lang="scss">
.panel {
  background: #ffffff;
  border: 1px solid rgba(0,0,0,.06);
  border-radius: 16px;
  padding: 16px;
}
.toolbar {
  display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 12px; flex-wrap: wrap;
}
.title { margin: 0; }
.right { display: flex; gap: 8px; }

//限制每一個欄位的寬高，多的隱藏（YUKI）
:deep(.el-table .cell) {
  max-width: 150px;
  max-height: 40px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

</style>
