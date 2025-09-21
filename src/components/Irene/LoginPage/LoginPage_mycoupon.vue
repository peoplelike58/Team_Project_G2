<script setup>
import { ref, onMounted } from 'vue'
import { useCartStore } from '@/stores/cart'


const CartStore=useCartStore()
// 優惠券資料
const coupons = ref([
  // {
  //   id: 1,
  //   name: '歡迎新朋友',
  //   discount: 'NT 200',
  //   expiryDate: '入會後一個月',
  //   usageRule: '單筆消費滿1000立減200元'
  //   isExpired: false
  // }
])
const isLoading = ref(false)        // 載入狀態
const errorMessage = ref("")


// 從API載入優惠券資料的函數
const getCoupons = async () => {
try{
  isLoading.value = true // 開始載入
  errorMessage.value = '' // 清空錯誤訊息
  const response = await fetch(import.meta.env.VITE_AJAX_URL + '/LoginPage_getCoupon.php', {
        method: 'POST',
        headers: { 
        'Content-Type': 'application/json',
        'Accept': 'application/json'
        },
        credentials: 'include',
        body: JSON.stringify({})
    })

    // if (!response.ok) {
    //     throw new Error(`HTTP錯誤: ${response.status}`)
    // }
    const result = await response.json()

    // 檢查API回傳狀態
    if (result.success) {
      // 過濾掉「不適用優惠券」或「不使用優惠券」
      coupons.value = result.coupons.filter(coupon => 
        coupon.discount !== 0 
      )
      console.log('設定後的coupons:', coupons.value)
    } else {
      errorMessage.value = response.data.message || '載入優惠券資料失敗'
    }
    
  } catch (error) {
    // 處理網路錯誤或其他異常
    console.error('載入優惠券資料時發生錯誤:', error)
    errorMessage.value = '網路連線錯誤，請稍後再試'
  } finally {
    isLoading.value = false // 結束載入
  }
}

onMounted(() => {
  // 載入優惠券資料的API呼叫
  getCoupons()
  })
</script>

<template>
  <div class="member-coupons">
    <!-- 頁面標題 -->
    <div class="page-header">
      <h1 class="page-title">我的優惠</h1>
    </div>

    <!-- 優惠券列表表格 -->
    <div class="coupons-table">
      <div class="table-header">
        <div class="header-cell">優惠名稱</div>
        <div class="header-cell">折扣金額</div>
        <div class="header-cell">到期時間</div>
        <div class="header-cell">使用規則</div>
      </div>
      
      <div class="table-body">
        <div 
          v-for="coupon in coupons" 
          :key="coupon.id"
          class="table-row"
          :class="{ 'expired': coupon.isExpired }"
        >
          <div class="body-cell coupon-name">{{ coupon.title }}</div>
          <div class="body-cell discount-amount">{{ coupon.discount }}</div>
          <div class="body-cell">{{ coupon.expiryDate }}</div>
          <div class="body-cell usage-rule">{{ coupon.usageRule}}</div>
        </div>
      </div>
    </div>

    <!-- 空狀態 -->
    <div v-if="coupons.length === 0" class="empty-state">
      <p class="empty-message">目前沒有任何優惠券</p>
    </div>
    <div class="useBtn">
      <router-link class="usebtn" to="/shop">立即使用</router-link>
    </div>
  </div>



</template>

<style scoped lang="scss">
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';

.member-coupons {
  .page-header {
    margin-bottom: 40px;
    
    .page-title {
      font-size: $pcFont-H2;
      font-weight: $semiBold;
      color: $black-14;
    }
  }
  
  .coupons-table {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    // margin-bottom: 40px;
    
    .table-header {
      display: grid;
      grid-template-columns: 1.5fr 1fr 1fr 2fr;
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
        grid-template-columns: 1.5fr 1fr 1fr 2fr;
        @include border($ash-olive-400);
        border-left: none;
        border-right: none;
        border-bottom: none;
        transition: opacity 0.3s ease;
        
        &:last-child {
          border-bottom: 1px solid $ash-olive-400;
        }
        
        &.expired {
          opacity: 0.5;
          background-color: #f8f9fa;
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
          line-height: $lineHeight-p-150;
          
          &.coupon-name {
            justify-content: flex-start;
            text-align: left;
            font-weight: $medium;
          }
          
          &.discount-amount {
            font-weight: $semiBold;
            color: #e74c3c;
          }
          
          &.usage-rule {
            font-size: 14px;
            color: $ash-olive-400;
            text-align: left;
            justify-content: flex-start;
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

.useBtn{
  padding: 40px 0 ;
  display: flex;
  justify-content: center;
  .usebtn{
    @include btn(8px);
    @include border($ash-olive-400);
    text-decoration: none;
    background-color: #F2F2E9;
    color: $black-14;
    padding: 12px 24px;
    font-size: $pcFont-p-s;
    font-weight: $regular;
    &:hover {
      background-color: $ash-olive-400;
      color: white;
    }
  }
}

// RWD
@media (max-width: 768px) {
  .coupons-table {
    .table-body {
      .table-row {
        .body-cell {
          &:nth-child(1):before { content: '優惠名稱：'; }
          &:nth-child(2):before { content: '折扣金額：'; }
          &:nth-child(3):before { content: '到期時間：'; }
          &:nth-child(4):before { content: '使用規則：'; }
        }
      }
    }
  }
}
</style>