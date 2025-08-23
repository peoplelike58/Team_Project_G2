<template>
    <!-- 手刻 -->
    <!-- <div class="pagination">
        <button 
          v-for="page in totalPages" 
          :key="page"
          :class="['page-btn', { active: page === currentPage }]"
          @click="setPage(page)"
        >
          {{ page }}
        </button>
    </div> -->
    <!-- elementplus -->
<div class="elpagination">
  <el-pagination
  class="pager"
  background
  layout="prev, pager, next"
  prev-text=" 上一頁"
  next-text= "下一頁"
  :total="total" 
  :page-size="pageSize"  
  :current-page="currentPage"
  @current-change="handlePageChange"
  />
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
    default: 20
  },
  currentPage: {
    type: Number,
    default: 1
  }
})

//定義發送的事件
const emit = defineEmits(['page-change'])

//處理頁面改變
const handlePageChange = (page) => {
  emit('page-change', page)
}


</script>

<style scoped lang="scss">
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';

// .pagination {
//     margin-top: 40px;
//     display: flex;
//     justify-content: center;
//     gap: 8px;

//     .page-btn {
//       width: 40px;
//       height: 40px;
//       border: 1px solid #ddd;
//       background: white;
//       border-radius: 6px;
//       cursor: pointer;
//       transition: all 0.2s;

//       &:hover {
//         // background: $accent-color;
//         color: white;
//         // border-color: $accent-color;
//       }

//       &.active {
//         // background: $primary-color;
//         color: white;
//         // border-color: $primary-color;
//       }
//     }
//   }
.elpagination{
  @include flexcenter(0,row)
}

/*使用 :deep 影響 Element Plus 內部） */
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

