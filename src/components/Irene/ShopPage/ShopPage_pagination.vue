<template>
<!-- elementplus -->
<div class="elpagination">
  <el-pagination
    class="pager"
    background
    layout="prev, pager, next"
    prev-text=" 上一頁"
    next-text= "下一頁"
    :total="total"                 
    :page-size="12"  
    :current-page="currentPage"
    @current-change="handlePageChange"
  />
  <!--
    :total="total"：總筆數
    :page-size="pageSize"：每頁幾筆（例如 20）
    :current-page="currentPage"：目前頁碼（1 起算）
    @current-change="handlePageChange"：當使用者點別頁時發出事件並攜帶新頁碼（Element Plus 的標準事件之一，可參考其他官方寫法）
   -->
</div> 
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

// 定義接收的 props
const props = defineProps({
  total: {
    type: Number,
    default: 0
  },
  pageSize: {
    type: Number,
    default: 12
  },
  currentPage: {
    type: Number,
    default: 1
  }
})

//定義發送的事件，對外發出的事件
const emit = defineEmits(['page-change'])

//處理頁面改變
const handlePageChange = (page) => {
  emit('page-change', page)
}


</script>

<style scoped lang="scss">
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';


.elpagination{
  @include flexcenter(0,row)
}

//使用 :deep 影響 Element Plus 內部） 
:deep(.pager) {
  &.is-background .el-pager li{
    border-radius: 8px;
    font-size: $pcFont-p-s;
    font-weight:$semiBold;
    line-height: $lineHeight-p-200;
    background-color: transparent;
    &.is-active{
      background-color:$black-14;
    }
    
  }
  .btn-prev,
  .btn-next {
    @include btn(8px);
    padding: 5px 15px !important;
    background-color: $black-14 !important;
    color: white !important;
  }

  
}

</style> 

