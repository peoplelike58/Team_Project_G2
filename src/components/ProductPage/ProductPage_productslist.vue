<template>
    <!--  商品展示  -->
    <div class="products_view">
        <!-- 標題-排序 -->
        <div class="products_title">
            <h2>全部商品</h2>
            <div class="order_box">
                <label for="order_box"id="order_box">排序 : </label>
                <select name="order_box" id="order_box">
                    <option value="">最新上架</option>
                    <option value="">價格:低至高</option>
                    <option value="">價格:高至低</option>
                </select>
            </div>
        </div>
        <!-- 商品卡片 -->
        <!-- <div class="products_content">
            <div class="product_card" v-for="item in products"> -->
                <!-- <img :src="new URL(`@/assets/images/Products/${item.pic}`, import.meta.url).href" :alt="item.name"> -->
                <!-- <h4>{{item.name }}</h4>
                <p>{{item.price}}</p>
            </div>
        </div> -->

        <div class="products-grid">
            <div class="product-card" v-for="product in filteredProducts" :key="product.id" @click="goToProduct(product.id)">
                <div class="product-image">
                    <img :src="product.image" :alt="product.name" />
                    <button class="favorite-btn" @click.stop="toggleFavorite(product.id)">
                        {{ favorites.includes(product.id) ? '❤️' : '🤍' }}
                    </button>
                </div>
                <h3 class="product-name">{{ product.name }}</h3>
                <p class="product-price">${{ product.price }}</p>
            </div>
        </div>
    </div>
</template>

<script setup>

import { ref, computed, onMounted } from 'vue'
// 這裡通常會導入 Pinia store 和 Vue Router
// import { useProductStore } from '@/stores/product'
// import { useRouter } from 'vue-router'

// 響應式數據
const currentSlide = ref(0)
const currentPage = ref(1)
const searchTerm = ref('')
const sortBy = ref('default')
const genderFilters = ref([])
const favorites = ref([])

// 模擬商品數據 (實際專案中會從 Pinia store 獲取)
const featuredProducts = ref([
  { id: 1, name: '露營帳篷', image: 'https://images.unsplash.com/photo-1537565266759-d30edc3c178f?w=300&h=200&fit=crop' },
  { id: 2, name: '登山背包', image: 'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=300&h=200&fit=crop' },
  { id: 3, name: '戶外帳篷', image: 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=300&h=200&fit=crop' },
  { id: 4, name: '望遠鏡', image: 'https://images.unsplash.com/photo-1574263867128-a3d5c1b1decc?w=300&h=200&fit=crop' }
])

const products = ref([
  { id: 1, name: '三人帳篷透氣版登山山峰', price: 2000, image: 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=300&h=300&fit=crop', gender: 'unisex' },
  { id: 2, name: '三人帳篷透氣版登山山峰', price: 2000, image: 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=300&h=300&fit=crop', gender: 'unisex' },
  { id: 3, name: '三人帳篷透氣版登山山峰', price: 2000, image: 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=300&h=300&fit=crop', gender: 'unisex' },
  { id: 4, name: '三人帳篷透氣版登山山峰', price: 2000, image: 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=300&h=300&fit=crop', gender: 'unisex' },
  { id: 5, name: '三人帳篷透氣版登山山峰', price: 2000, image: 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=300&h=300&fit=crop', gender: 'unisex' },
  { id: 6, name: '三人帳篷透氣版登山山峰', price: 2000, image: 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=300&h=300&fit=crop', gender: 'unisex' },
  { id: 7, name: '三人帳篷透氣版登山山峰', price: 2000, image: 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=300&h=300&fit=crop', gender: 'unisex' },
  { id: 8, name: '三人帳篷透氣版登山山峰', price: 2000, image: 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=300&h=300&fit=crop', gender: 'unisex' },
  { id: 9, name: '三人帳篷透氣版登山山峰', price: 2000, image: 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=300&h=300&fit=crop', gender: 'unisex' }
])

// 計算屬性 (類似 getter)
const filteredProducts = computed(() => {
  let filtered = products.value

  // 搜尋過濾
  if (searchTerm.value) {
    filtered = filtered.filter(product => 
      product.name.includes(searchTerm.value)
    )
  }

  // 性別過濾
  if (genderFilters.value.length > 0) {
    filtered = filtered.filter(product => 
      genderFilters.value.includes(product.gender) || product.gender === 'unisex'
    )
  }

  // 排序
  if (sortBy.value === 'price-low') {
    filtered = [...filtered].sort((a, b) => a.price - b.price)
  } else if (sortBy.value === 'price-high') {
    filtered = [...filtered].sort((a, b) => b.price - a.price)
  }

  return filtered
})

const totalPages = computed(() => Math.ceil(filteredProducts.value.length / 9))

// 方法
const toggleMenu = () => {
  console.log('Toggle menu')
}

const prevSlide = () => {
  if (currentSlide.value > 0) {
    currentSlide.value--
  }
}

const nextSlide = () => {
  if (currentSlide.value < featuredProducts.value.length - 4) {
    currentSlide.value++
  }
}

const toggleFavorite = (productId) => {
  const index = favorites.value.indexOf(productId)
  if (index > -1) {
    favorites.value.splice(index, 1)
  } else {
    favorites.value.push(productId)
  }
}

const goToProduct = (productId) => {
  // 使用 Vue Router 導航到商品詳情頁
  // router.push(`/product/${productId}`)
  console.log(`Navigate to product ${productId}`)
}

const setPage = (page) => {
  currentPage.value = page
}

// 生命週期鉤子
onMounted(() => {
  console.log('Component mounted')
  // 這裡可以發 API 請求獲取數據
})

</script>

<style scoped lang="scss">
@import '../../assets/styles/main.scss';

/* 標題 + 排序 */
.products_view{
    @include flexcenter(40px,column);
    .products_title{
        @include flexcenter(auto,row);
        h2{
            font-size: $pcFont-H2;
            font-weight: $semiBold;
            line-height: $linHeight-p-200;
        }
        label{
            font-size: $pcFont-H4;
        }
        select{
            width: 200px;
            padding: 5px 10px;
            margin-left: 5px;
            border-radius: 4px;
            @include border;
        }
    }
}

/* 商品卡片 */
 .products-grid {
    flex: 1;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;

    .product-card {
      background: white;
      border-radius: 12px;
      overflow: hidden;
      cursor: pointer;
      transition: transform 0.2s, box-shadow 0.2s;

      &:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      }

      .product-image {
        position: relative;
        width: 100%;
        height: 250px;

        img {
          width: 100%;
          height: 100%;
          object-fit: cover;
        }

        .favorite-btn {
          position: absolute;
          top: 12px;
          right: 12px;
          background: rgba(255, 255, 255, 0.9);
          border: none;
          width: 36px;
          height: 36px;
          border-radius: 50%;
          cursor: pointer;
          font-size: 16px;

          &:hover {
            background: white;
          }
        }
      }

      .product-name {
        padding: 16px 16px 8px;
        font-size: 16px;
        font-weight: 500;
        margin: 0;
      }

      .product-price {
        padding: 0 16px 16px;
        font-size: 18px;
        font-weight: bold;
        margin: 0;
      }
    }
  }

</style>