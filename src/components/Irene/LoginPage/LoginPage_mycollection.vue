<template>
    <div class="member-favorites">
    <!-- 頁面標題 -->
    <div class="page-header">
      <h1 class="page-title">商品收藏</h1>
    </div>

    <!-- 收藏商品列表 -->
    <div class="favorites-grid">
      <div 
        v-for="product in FavoriteStore.favorites.products" 
        :key="product.PRODUCT_ID"
        class="product-card"
      >
        <div class="product-image">
          <img :src="`/images/Products/products/${product.IMAGE}`" :alt="product.name" />
          <button class="favorite-btn" @click="removeFavorite(product.PRODUCT_ID)">
            <i class="heart-icon">❤️</i>
          </button>
        </div>
        <div class="product-info">
          <h3 class="product-name">{{ product.PRODUCT_NAME }}</h3>
          <div class="box">
            <p class="product-price">${{ product.PRICE }}</p>
            <button class="add_cart">加入購物車</button>
          </div>
        </div>
      </div>
    </div>
  </div>
    
</template>

<script setup>
import { onMounted,watch } from 'vue'
import { useFavoriteStore } from '@/stores/favorites'
import { useUserStore } from '@/stores/user'

const FavoriteStore = useFavoriteStore()
const user = useUserStore()

// 收藏商品資料
// const favoriteProducts = ref([
//   {
//     id: 1,
//     name: '三人帳篷快速收合登山帳',
//     price: 1000,
//     image: 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=600&h=600&fit=crop&auto=format'
//   },
//   {
//     id: 2,
//     name: '帳篷快速收合登山帳',
//     price: 2000,
//     image: ''
//   },
//   {
//     id: 3,
//     name: '快速收合登山帳',
//     price: 3000,
//     image: 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=600&h=600&fit=crop&auto=format'
//   }
// ])
// const favoriteProducts = FavoriteStore.favorites.products


/* 移除收藏商品*/
const removeFavorite = async(productId) => {
  // favoriteProducts.value = favoriteProducts.value.filter(product => product.id !== productId)
  await FavoriteStore.removeFavorite(user.id,productId)
  FavoriteStore.loadFavorites(user.id)
}

// 監聽收藏商品數量變化
watch(() => FavoriteStore.favorites.products.length, (newCount, oldCount) => {
  console.log(`收藏商品數量變化: ${oldCount} -> ${newCount}`)
}, { immediate: true })

onMounted(async() => {
  // 載入收藏資料的API呼叫
  await FavoriteStore.loadFavorites(user.id)
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
      
    }
    
    .product-image {
      position: relative;
      @include product_card_img(100%, 300px, 0);
      > img{
        height: 100%
      }
      .favorite-btn {
        @include btn(0);
        position: absolute;
        top: 12px;
        right: 12px;
        width: 40px;
        height: 40px;
        background-color: transparent;
        @include flexcenter(0, row);
        .heart-icon {
          width: 24px;
          height: 24px;
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
      .box{
        display: flex;
        justify-content: space-between;
        .product-price {
          font-size: $pcFont-H4;
          font-weight: $semiBold;
          color: $black-14;
        }
        .add_cart{
          @include btn(0);
          border-bottom: 1px solid $black-14;
          font-size: $pcFont-p-s;
          font-weight: $semiBold;
          line-height: $lineHeight-p-150;
          &:hover {
            color: #374151;
            border-bottom:1px solid #374151;
          }
        }
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