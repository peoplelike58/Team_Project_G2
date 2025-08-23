<script setup>
import { ref, onMounted } from 'vue'

// 訂單資料
const orders = ref([
  {
    id: 1,
    orderNumber: '20250801143',
    products: [
      { id: 1, name: '三人帳篷快速收合登山帳' },
      { id: 2, name: '登山杖組合' }
    ],
    amount: 'NT2500',
    status: 'processing',
    statusText: '備貨中'
  },
  {
    id: 2,
    orderNumber: '20250801143',
    products: [
      { id: 3, name: '登山背包' }
    ],
    amount: 'NT2500',
    status: 'completed',
    statusText: '已完成'
  },
  {
    id: 3,
    orderNumber: '20250801143',
    products: [
      { id: 4, name: '戶外睡袋' }
    ],
    amount: 'NT2500',
    status: 'processing',
    statusText: '備貨中'
  },
  {
    id: 4,
    orderNumber: '20250801143',
    products: [
      { id: 5, name: '登山鞋' }
    ],
    amount: 'NT2500',
    status: 'processing',
    statusText: '備貨中'
  },
  {
    id: 5,
    orderNumber: '20250801143',
    products: [
      { id: 6, name: '防水外套' }
    ],
    amount: 'NT2500',
    status: 'processing',
    statusText: '備貨中'
  }
])

onMounted(() => {
  // 載入訂單資料的API呼叫
})
</script>
<template>
    <div class="member-orders">
    <!-- 頁面標題 -->
    <div class="page-header">
      <h1 class="page-title">我的訂單</h1>
    </div>

    <!-- 訂單列表表格 -->
    <div class="orders-table">
      <div class="table-header">
        <div class="header-cell">訂單編號</div>
        <div class="header-cell">商品明細</div>
        <div class="header-cell">訂單金額</div>
        <div class="header-cell">訂單狀態</div>
      </div>
      
      <div class="table-body">
        <div 
          v-for="order in orders" 
          :key="order.id"
          class="table-row"
        >
          <div class="body-cell order-number">{{ order.orderNumber }}</div>
          <div class="body-cell">
            <div class="product-details">
              <select class="product-select">
                <option value="">商品明細</option>
                <option v-for="product in order.products" :key="product.id" :value="product.id">
                  {{ product.name }}
                </option>
              </select>
            </div>
          </div>
          <div class="body-cell">{{ order.amount }}</div>
          <div class="body-cell">
            <span :class="['status-badge', order.status]">{{ order.statusText }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- 空狀態 -->
    <div v-if="orders.length === 0" class="empty-state">
      <p class="empty-message">目前沒有任何訂單</p>
    </div>
  </div>

</template>

<style lang="scss" scoped>
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';

.member-orders {
  .page-header {
    margin-bottom: 40px;
    
    .page-title {
      font-size: $pcFont-H2;
      font-weight: $semiBold;
      color: $black-14;
    }
  }
  
  .orders-table {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    
    .table-header {
      display: grid;
      grid-template-columns: 1.5fr 2fr 1fr 1fr;
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
        grid-template-columns: 1.5fr 2fr 1fr 1fr;
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
          
          &.order-number {
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
          
          .product-details {
            width: 100%;
            
            .product-select {
              width: 100%;
              padding: 8px 12px;
              border: 1px solid $ash-olive-400;
              border-radius: 4px;
              background: white;
              font-size: $pcFont-label;
              color: $black-14;
              cursor: pointer;
              
              &:focus {
                outline: none;
                border-color: $tag;
              }
            }
          }
          
          .status-badge {
            padding: 6px 12px;
            border-radius: 16px;
            font-size: $pcFont-label;
            font-weight: $medium;
            
            &.processing {
              background-color: #fff3cd;
              color: #856404;
            }
            
            &.completed {
              background-color: #d4edda;
              color: #155724;
            }
            
            &.cancelled {
              background-color: #f8d7da;
              color: #721c24;
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
  .orders-table {
    .table-body {
      .table-row {
        .body-cell {
          &:nth-child(1):before { content: '訂單編號：'; }
          &:nth-child(2):before { content: '商品明細：'; }
          &:nth-child(3):before { content: '訂單金額：'; }
          &:nth-child(4):before { content: '訂單狀態：'; }
        }
      }
    }
  }
}

</style>