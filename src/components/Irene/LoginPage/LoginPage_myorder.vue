<script setup>
import { ref, onMounted } from 'vue'


const orders = ref([])              // 訂單資料
const isLoading = ref(false)        // 載入狀態
const errorMessage = ref('')        // 錯誤訊息
const BASE = import.meta.env.BASE_URL;


// 從API載入訂單資料的函數
const fetchOrderData = async () => {
try{
  isLoading.value = true // 開始載入
  errorMessage.value = '' // 清空錯誤訊息

  const response = await fetch(import.meta.env.VITE_AJAX_URL + '/ShopPage_getOrder.php', {
        method: 'POST',
        headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
        },
        credentials: 'include',
        body: JSON.stringify({})
    })

    if (!response.ok) {
        throw new Error(`HTTP錯誤: ${response.status}`)
    }
    const result = await response.json()

    // 檢查API回傳狀態
    if (result.success) {
      orders.value =result.orders // 將API回傳的資料指派給orders
    } else {
      errorMessage.value = response.data.message || '載入訂單資料失敗'
    }
    
  } catch (error) {
    // 處理網路錯誤或其他異常
    console.error('載入訂單資料時發生錯誤:', error)
    errorMessage.value = '網路連線錯誤，請稍後再試'
  } finally {
    isLoading.value = false // 結束載入
  }
}
// 切換展開/收合商品明細的函數
const toggleProductDetails = (orderId) => {
  // 找到對應的訂單並切換展開狀態
  const order = orders.value.find(order => order.id === orderId)
  if (order) {
    order.isExpanded = !order.isExpanded
  }
}

// 處理圖片載入錯誤的函數
// const handleImageError = (event) => {
//   // 當圖片載入失敗時，使用預設圖片
//   event.target.src = '/images/products/default-product.jpg'
// }
// 元件掛載時載入訂單資料

onMounted(async() => {
  // 載入訂單資料的API呼叫
  fetchOrderData()
  })
</script>
<template>
    <div class="member-orders">
    <!-- 頁面標題 -->
    <div class="page-header">
      <h1 class="page-title">我的訂單</h1>
    </div>

    <!-- 【新增】載入中狀態 -->
    <div v-if="isLoading" class="loading-state">
      <p>載入中...</p>
    </div>

    <!-- 【新增】錯誤訊息顯示 -->
    <div v-else-if="errorMessage" class="error-state">
      <p class="error-message">{{ errorMessage }}</p>
      <button @click="fetchOrderData" class="retry-btn">重新載入</button>
    </div>

    <!-- 訂單列表表格 -->
    <div v-else class="orders-table">
      <div class="table-header">
        <div class="header-cell">訂單編號</div>
        <div class="header-cell">商品明細</div>
        <div class="header-cell">訂單金額</div>
        <div class="header-cell">訂單狀態</div>
      </div>
      
      <div class="table-body">
        <div v-for="order in orders" :key="order.id"class="table-row">

          <div class="body-cell order-number">{{ order.orderNumber }}</div>

          <!-- 商品明細區塊 -->
          <div class="body-cell">
            <div class="product-details">
              <!-- 點擊按鈕展開/收合商品明細 -->
              <button 
                @click="toggleProductDetails(order.id)"
                class="product-toggle-btn"
                :class="{ 'expanded': order.isExpanded }"
              >
                <span>商品明細 ({{ order.products.length }})</span>
                <!-- 箭頭圖示，根據展開狀態旋轉 -->
                <span class="arrow" :class="{ 'expanded': order.isExpanded }">▼</span>
              </button>
              
              <!-- 展開的商品明細列表 -->
              <div 
                v-if="order.isExpanded" 
                class="product-list"
              >
                <div 
                  v-for="product in order.products" 
                  :key="product.id"
                  class="product-item"
                >
                  <!-- 商品圖片和資訊的容器 -->
                  <div class="product-info">
                    <img 
                      :src="`${BASE}images/Products/products/${product.image}`" 
                      :alt="product.name"
                      class="product-image"
                      @error="handleImageError"
                    />
                    <div class="product-details">
                      <div class="product-name">{{ product.name }}</div>
                      <div class="product-specs">
                        數量: {{ product.qty }}  | 尺寸: {{ product.size }}   | 顏色: {{ product.color }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
            
          <div class="body-cell">{{ order.amount }}</div>
          <div class="body-cell">
            <span :class="status-badge">{{ order.status }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- 空狀態- 沒有訂單時顯示 -->
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
  
  /* 【新增】載入中狀態樣式 */
  .loading-state {
    @include flexcenter(0, column);
    padding: 60px 20px;
    text-align: center;
    
    p {
      font-size: $pcFont-p-m;
      color: $ash-olive-400;
    }
  }
  
  /* 【新增】錯誤狀態樣式 */
  .error-state {
    @include flexcenter(16px, column);
    padding: 60px 20px;
    text-align: center;
    
    .error-message {
      font-size: $pcFont-p-m;
      color: #dc3545;
      margin-bottom: 16px;
    }
    
    .retry-btn {
      padding: 8px 16px;
      background-color: $tag;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: $pcFont-label;
      
      &:hover {
        background-color: darken($tag, 10%);
      }
    }
  }
  
  .orders-table {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    
    .table-header {
      display: grid;
      grid-template-columns: 1fr 3fr 0.5fr 0.5fr;
      background: $ivory-gray-100;
      
      @media (max-width: 768px) {
        grid-template-columns: 1fr;
      }
      
      .header-cell {
        padding: 20px 18px;
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
        grid-template-columns: 0.8fr 3fr 0.5fr 0.5fr;
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
          padding: 20px 18px;
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
            
            .product-toggle-btn {
              width: 100%;
              padding: 8px 12px;
              border: 1px solid $ash-olive-400;
              border-radius: 4px;
              background: white;
              font-size: $pcFont-label;
              color: $black-14;
              cursor: pointer;
              display: flex;
              justify-content: space-between;
              align-items: center;
              transition: all 0.3s ease;
              
              &:hover {
                background-color: #f8f9fa;
                border-color: $tag;
              }
              
              &:focus {
                outline: none;
                border-color: $tag;
                box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
              }
              
              .arrow {
                transition: transform 0.3s ease;
                font-size: 12px;
                
                &.expanded {
                  transform: rotate(180deg);
                }
              }
            }
            
            .product-list {
              margin-top: 8px;
              padding: 12px;
              background-color: #f8f9fa;
              border-radius: 4px;
              border: 1px solid #e9ecef;
              
              .product-item {
                padding: 8px 0;
                border-bottom: 1px solid #dee2e6;
                
                &:last-child {
                  border-bottom: none;
                }
                
                /* 商品資訊容器樣式 */
                .product-info {
                  display: flex;
                  align-items: center;
                  gap: 36px;          // 圖片和文字之間的間距
                  
                  .product-image {
                    width: 60px;
                    height: 60px;
                    object-fit: cover; // 保持比例裁切
                    border-radius: 4px;
                    border: 1px solid #dee2e6;
                    flex-shrink: 0;   // 防止圖片被壓縮
                  }
                  
                  .product-details {
                    flex: 1;          // 佔滿剩餘空間
                    display: flex;
                    flex-direction: column;
                    align-items: start;
                    gap:8px;
                    .product-name {
                      font-weight: $medium;
                      color: $black-14;
                      margin-bottom: 4px;
                      font-size: $pcFont-p-s;
                    }
                    
                    .product-specs {
                      font-size: $pcFont-label;
                      color: #6c757d;
                      
                    }
                  }
                }
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




