<template>
    <div class="member-activities">
    <!-- 頁面標題 -->
    <div class="page-header">
      <h1 class="page-title">我的活動</h1>
    </div>

    <!-- 活動列表表格 -->
    <div class="activities-table">
      <div class="table-header">
        <div class="header-cell">活動名稱</div>
        <div class="header-cell">報名時間</div>
        <div class="header-cell">出發日期</div>
        <div class="header-cell">操作</div>
      </div>
      
      <div class="table-body">
        <div 
          v-for="activity in activities" 
          :key="activity.id"
          class="table-row"
        >
          <div class="body-cell activity-name">{{ activity.name }}</div>
          <div class="body-cell">{{ activity.registerDate }}</div>
          <div class="body-cell">{{ activity.departureDate }}</div>
          <div class="body-cell">
            <button class="cancel-btn" @click="cancelActivity(activity.id)">
              取消報名
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- 空狀態 -->
    <div v-if="activities.length === 0" class="empty-state">
      <p class="empty-message">目前沒有報名任何活動</p>
    </div>
  </div>

</template>

<script setup>
import { ref, onMounted } from 'vue'

// 活動資料
const activities = ref([
  {
    id: 1,
    name: '百岳入門',
    registerDate: '2025/08/04',
    departureDate: '2025/09/01'
  },
  {
    id: 2,
    name: '百岳入門',
    registerDate: '2025/08/04',
    departureDate: '2025/09/01'
  },
  {
    id: 3,
    name: '百岳入門',
    registerDate: '2025/08/04',
    departureDate: '2025/09/01'
  },
  {
    id: 4,
    name: '百岳入門',
    registerDate: '2025/08/04',
    departureDate: '2025/09/01'
  },
  {
    id: 5,
    name: '百岳入門',
    registerDate: '2025/08/04',
    departureDate: '2025/09/01'
  }
])

// 取消活動報名
const cancelActivity = (activityId) => {
  if (confirm('確定要取消報名嗎？')) {
    activities.value = activities.value.filter(activity => activity.id !== activityId)
  }
}

onMounted(() => {
  // 載入活動資料的API呼叫
})

</script>

<style lang="scss" scoped>
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';

.member-activities {
  .page-header {
    margin-bottom: 40px;
    
    .page-title {
      font-size: $pcFont-H2;
      font-weight: $semiBold;
      color: $black-14;
    }
  }
  
  .activities-table {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    
    .table-header {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr 1fr;
      background: $ivory-gray-100;
      
      @media (max-width: 768px) {
        grid-template-columns: 1fr;
      }
      
      .header-cell {
        padding: 20px 24px;
        font-size: $pcFont-p-s;
        font-weight: $semiBold;
        color: $black-14;
        text-align: center;
        
        &:first-child {
          text-align: left;
        }
        
        @media (max-width: 768px) {
          display: none;
          
          &:first-child {
            display: block;
            text-align: center;
          }
        }
      }
    }
    
    .table-body {
      .table-row {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr;
        @include border($ash-olive-400);
        border-left: none;
        border-right: none;
        border-bottom: none;
        
        &:last-child {
          border-bottom: 1px solid $ash-olive-400;
        }
        
        @media (max-width: 768px) {
          grid-template-columns: 1fr;
          gap: 8px;
          padding: 16px;
        }
        
        .body-cell {
          padding: 20px 24px;
          font-size: $pcFont-p-s;
          color: $black-14;
          @include flexcenter(0, row);
          align-items: center;
          text-align: center;
          
          &.activity-name {
            justify-content: flex-start;
            text-align: left;
          }
          
          @media (max-width: 768px) {
            padding: 4px 0;
            justify-content: flex-start;
            text-align: left;
            
            &:before {
              content: attr(data-label);
              font-weight: $semiBold;
              width: 100px;
              margin-right: 10px;
            }
          }
          
          .cancel-btn {
            @include btn(4px);
            padding: 8px 16px;
            background-color: $bg-gray;
            color: $black-14;
            font-size: $pcFont-label;
            transition: all 0.3s ease;
            @include border($ash-olive-400);
            
            &:hover {
              background-color: #ff6b6b;
              color: white;
              border-color: #ff6b6b;
            }
          }
        }
      }
    }
  }
  
  .empty-state {
    @include flexcenter(0, column);
    padding: 60px 20px;
    text-align: center;
    
    .empty-message {
      font-size: $pcFont-p-m;
      color: $ash-olive-400;
    }
  }
}

// Mobile 專用樣式
@media (max-width: 768px) {
  .activities-table {
    .table-body {
      .table-row {
        .body-cell {
          &:nth-child(1):before { content: '活動名稱：'; }
          &:nth-child(2):before { content: '報名時間：'; }
          &:nth-child(3):before { content: '出發日期：'; }
          &:nth-child(4):before { content: '操作：'; }
        }
      }
    }
  }
}

</style>