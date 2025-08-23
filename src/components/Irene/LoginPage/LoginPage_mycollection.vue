<template>
    <div class="member-favorites">
    <!-- 頁面標題 -->
    <div class="page-header">
      <h1 class="page-title">商品收藏</h1>
    </div>

    <!-- 收藏商品列表 -->
    <div class="favorites-grid">
      <div 
        v-for="product in favoriteProducts" 
        :key="product.id"
        class="product-card"
      >
        <div class="product-image">
          <img :src="product.image" :alt="product.name" />
          <button class="favorite-btn" @click="removeFavorite(product.id)">
            <i class="heart-icon filled"></i>
          </button>
        </div>
        <div class="product-info">
          <h3 class="product-name">{{ product.name }}</h3>
          <p class="product-price">${{ product.price }}</p>
        </div>
      </div>
    </div>
  </div>
    
</template>

<script setup>
import { ref, onMounted } from 'vue'

// 收藏商品資料
const favoriteProducts = ref([
  {
    id: 1,
    name: '三人帳篷快速收合登山帳',
    price: 2000,
    image: '/path/to/tent.jpg'
  },
  {
    id: 2,
    name: '三人帳篷快速收合登山帳',
    price: 2000,
    image: '/path/to/binoculars.jpg'
  },
  {
    id: 3,
    name: '三人帳篷快速收合登山帳',
    price: 2000,
    image: '/path/to/poles.jpg'
  }
])

// 移除收藏商品
const removeFavorite = (productId) => {
  favoriteProducts.value = favoriteProducts.value.filter(product => product.id !== productId)
}


onMounted(() => {
  // 載入收藏資料的API呼叫
})

</script>

<style lang="scss" scoped>
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';

.member-favorites {
  .page-header {
    margin-bottom: 40px;
    
    .page-title {
      font-size: $pcFont-H2;
      font-weight: $semiBold;
      color: $black-14;
    }
  }
  
  .favorites-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 24px;
    margin-bottom: 60px;
    
    @media (max-width: 768px) {
      grid-template-columns: 1fr;
      gap: 16px;
    }
  }
  
  .product-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
    
    &:hover {
      transform: translateY(-4px);
    }
    
    .product-image {
      position: relative;
      @include product_card_img(100%, 200px, 0);
      
      .favorite-btn {
        @include btn(50%);
        position: absolute;
        top: 12px;
        right: 12px;
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.9);
        @include flexcenter(0, row);
        
        .heart-icon {
          width: 20px;
          height: 20px;
          
          &.filled {
            background: url('/path/to/heart-filled.svg') no-repeat center;
            background-size: contain;
          }
        }
      }
    }
    
    .product-info {
      padding: 16px;
      
      .product-name {
        font-size: $pcFont-p-s;
        font-weight: $medium;
        margin-bottom: 8px;
        line-height: $lineHeight-title-120;
      }
      
      .product-price {
        font-size: $pcFont-H4;
        font-weight: $semiBold;
        color: $black-14;
      }
    }
  }
  
  .favorite-mountains {
    .section-title {
      font-size: $pcFont-H2;
      font-weight: $semiBold;
      margin-bottom: 24px;
      color: $black-14;
    }
    
    .mountains-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 24px;
      
      @media (max-width: 768px) {
        grid-template-columns: 1fr;
        gap: 16px;
      }
    }
    
    .mountain-card {
      background: white;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      
      .mountain-image {
        @include product_card_img(100%, 180px, 0);
      }
      
      .mountain-info {
        padding: 16px;
        @include flexcenter(12px, row);
        justify-content: space-between;
        
        .mountain-name {
          font-size: $pcFont-H4;
          font-weight: $medium;
          color: $black-14;
        }
        
        .remove-btn {
          @include btn(4px);
          padding: 8px 16px;
          background-color: $bg-gray;
          color: $black-14;
          font-size: $pcFont-label;
          transition: background-color 0.3s ease;
          
          &:hover {
            background-color: $ash-olive-400;
            color: white;
          }
        }
      }
    }
  }
}
    
</style>