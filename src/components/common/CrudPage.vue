<template>
  <div class="panel">
    <div class="toolbar">
      <div class="left">
        <h2 class="title">{{ title }}</h2>
      </div>
      <div class="right">
        <el-input v-model="keyword" placeholder="關鍵字搜尋" clearable style="max-width: 240px" />
        <el-button type="primary" @click="openCreate">新增</el-button>
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
          <el-button size="small" @click="openEdit(row, indexOf($index))">編輯</el-button>
          <el-button size="small" type="danger" @click="remove(indexOf($index))">刪除</el-button>
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
          <el-form-item :label="col.label">
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
          </el-form-item>
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
import { ref, computed } from 'vue'

const props = defineProps({
  title: { type: String, required: true },
  columns: { type: Array, required: true }, // [{prop,label,type?,options?}]
  sampleData: { type: Array, default: () => [] }
})

const data = ref([...props.sampleData])
const keyword = ref('')
const dialogVisible = ref(false)
const dialogMode = ref('create')
const editIndex = ref(-1)
const form = ref({})
const pageSize = 10
let currentPage = ref(1)

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
  return 'el-input'
}

const openCreate = () => {
  dialogMode.value = 'create'
  form.value = Object.fromEntries(props.columns.map(c => [c.prop, '']))
  dialogVisible.value = true
}
const openEdit = (row, index) => {
  dialogMode.value = 'edit'
  editIndex.value = index
  form.value = JSON.parse(JSON.stringify(row))
  dialogVisible.value = true
}
const submit = () => {
  if (dialogMode.value === 'create') {
    data.value.unshift({ ...form.value, _id: Date.now() })
  } else {
    data.value.splice(editIndex.value, 1, { ...form.value })
  }
  dialogVisible.value = false
}
const indexOf = ($index) => (currentPage.value - 1) * pageSize + $index
const remove = (absIndex) => data.value.splice(absIndex, 1)
const resetData = () => { data.value = [...props.sampleData]; keyword.value = ''; currentPage.value = 1 }
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
</style>
