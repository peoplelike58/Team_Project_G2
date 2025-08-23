<template>
  <div class="container">
    <!-- 導航欄 -->
    <nav class="navbar">
      <div class="nav-content">
        <div class="breadcrumb">
          首頁 > 山峰露營店
        </div>
        <button class="menu-btn" @click="toggleMenu">
          MENU
          <span class="hamburger"></span>
        </button>
      </div>
    </nav>

    <!-- 精選商品區 -->
    <section class="featured-section">
      <h2 class="section-title">精選商品</h2>
      <div class="featured-carousel">
        <button class="carousel-btn prev" @click="prevSlide">‹</button>
        <div class="carousel-container">
          <div class="carousel-track" :style="{ transform: `translateX(-${currentSlide * 25}%)` }">
            <div 
              v-for="(product, index) in featuredProducts" 
              :key="index"
              class="carousel-item"
            >
              <img :src="product.image" :alt="product.name" />
            </div>
          </div>
        </div>
        <button class="carousel-btn next" @click="nextSlide">›</button>
      </div>
    </section>

    <!-- 歡迎禮品區 -->
    <section class="welcome-gift">
      <div class="gift-content">
        <h3>Welcome gift</h3>
        <p class="gift-subtitle">#新朋友限定</p>
        <div class="gift-amount">
          <span class="amount">$200</span>
          <span class="currency">購物金</span>
        </div>
        <p class="gift-note">送給你！</p>
      </div>
      <div class="barcode">
        <div class="barcode-lines"></div>
      </div>
    </section>

    <!-- 商品列表區 -->
    <section class="products-section">
      <div class="products-header">
        <h2>全部商品</h2>
        <select v-model="sortBy" class="sort-select">
          <option value="default">排序</option>
          <option value="price-low">價格由低到高</option>
          <option value="price-high">價格由高到低</option>
        </select>
      </div>

      <div class="products-container">
        <!-- 篩選側邊欄 -->
        <aside class="filters">
          <div class="search-box">
            <input 
              type="text" 
              v-model="searchTerm"
              placeholder="請輸入你想搜尋的商品"
              class="search-input"
            />
            <button class="search-btn">🔍</button>
          </div>

          <div class="filter-group">
            <h4>登山旅行裝備</h4>
            <ul>
              <li>登山適合裝備</li>
              <li>登山配件</li>
              <li>登山團購</li>
              <li>登山鞋鞋</li>
              <li>登山包</li>
            </ul>
          </div>

          <div class="filter-group">
            <h4>顧客 性別</h4>
            <label class="checkbox-label">
              <input type="checkbox" v-model="genderFilters" value="female" />
              女性
            </label>
            <label class="checkbox-label">
              <input type="checkbox" v-model="genderFilters" value="male" />
              男性
            </label>
          </div>
        </aside>

        <!-- 商品網格 -->
        <div class="products-grid">
          <div 
            v-for="product in filteredProducts" 
            :key="product.id"
            class="product-card"
            @click="goToProduct(product.id)"
          >
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

      <!-- 分頁 -->
      <div class="pagination">
        <button 
          v-for="page in totalPages" 
          :key="page"
          :class="['page-btn', { active: page === currentPage }]"
          @click="setPage(page)"
        >
          {{ page }}
        </button>
      </div>
    </section>

    <!-- 底部標語 -->
    <footer class="footer">
      <div class="footer-content">
        <div class="logo">
          <div class="logo-icon">山上見</div>
        </div>
        <p>山で待ってるよ！いっしょに行こう！</p>
      </div>
    </footer>
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
  { id: 4, name: '望遠鏡', image: 'https://images.unsplash.com/photo-1574263867128-a3d5c1b1decc?w=300&h=200&fit=crop' },
  { id: 5, name: '露營帳篷', image: 'https://images.unsplash.com/photo-1537565266759-d30edc3c178f?w=300&h=200&fit=crop' },
  { id: 6, name: '登山背包', image: 'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=300&h=200&fit=crop' },
  { id: 7, name: '戶外帳篷', image: 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=300&h=200&fit=crop' },
  { id: 8, name: '望遠鏡', image: 'https://images.unsplash.com/photo-1574263867128-a3d5c1b1decc?w=300&h=200&fit=crop' },
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
  { id: 9, name: '三人帳篷透氣版登山山峰', price: 2000, image: 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=300&h=300&fit=crop', gender: 'unisex' },
  { id: 10, name: '三人帳篷透氣版登山山峰', price: 2600, image: 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=300&h=300&fit=crop', gender: 'unisex' },
  { id: 11, name: '三人帳篷透氣版登山山峰', price: 5000, image: 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=300&h=300&fit=crop', gender: 'unisex' },
  { id: 12, name: '三人帳篷透氣版登山山峰', price: 1000, image: 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=300&h=300&fit=crop', gender: 'unisex' },
  { id: 13, name: '三人帳篷透氣版登山山峰', price: 3000, image: 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=300&h=300&fit=crop', gender: 'unisex' },
  { id: 14, name: '三人帳篷透氣版登山山峰', price: 3000, image: 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=300&h=300&fit=crop', gender: 'unisex' },
  { id: 15, name: '三人帳篷透氣版登山山峰', price: 2000, image: 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=300&h=300&fit=crop', gender: 'unisex' },
  { id: 16, name: '三人帳篷透氣版登山山峰', price: 4000, image: 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=300&h=300&fit=crop', gender: 'unisex' },
  { id: 17, name: '三人帳篷透氣版登山山峰', price: 2800, image: 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=300&h=300&fit=crop', gender: 'unisex' },
  { id: 18, name: '三人帳篷透氣版登山山峰', price: 5000, image: 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=300&h=300&fit=crop', gender: 'unisex' },

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

<style lang="scss" scoped>

// SCSS 變數

@import '@/assets/styles/main.scss';
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.navbar {
  margin-bottom: 30px;
  
  .nav-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .breadcrumb {
    
    font-size: 14px;
  }

  .menu-btn {
    background: none;
    border: none;
    font-size: 16px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .hamburger {
    width: 20px;
    height: 2px;
    
    position: relative;

    &::before,
    &::after {
      content: '';
      position: absolute;
      width: 100%;
      height: 2px;
    
    }

    &::before { top: -6px; }
    &::after { top: 6px; }
  }
}

.featured-section {
  margin-bottom: 40px;

  .section-title {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 20px;
    
  }

  .featured-carousel {
    position: relative;
    overflow: hidden;

    .carousel-container {
      overflow: hidden;
    }

    .carousel-track {
      display: flex;
      transition: transform 0.3s ease;
    }

    .carousel-item {
      flex: 0 0 25%;
      padding: 0 10px;

      img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 8px;
      }
    }

    .carousel-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background: rgba(255, 255, 255, 0.8);
      border: none;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      cursor: pointer;
      font-size: 18px;
      
      &.prev { left: 10px; }
      &.next { right: 10px; }

      &:hover {
        background: rgba(255, 255, 255, 0.9);
      }
    }
  }
}

.welcome-gift {
  background: linear-gradient(135deg, #b8c5a0 0%, #a8b891 100%);
  border-radius: 12px;
  padding: 30px;
  margin-bottom: 40px;
  display: flex;
  justify-content: space-between;
  align-items: center;

  .gift-content {
    text-align: center;
    color: white;

    h3 {
      font-size: 28px;
      margin-bottom: 8px;
    }

    .gift-subtitle {
      font-size: 14px;
      margin-bottom: 20px;
      opacity: 0.9;
    }

    .gift-amount {
      background: rgba(255, 255, 255, 0.9);
      border-radius: 25px;
      padding: 12px 30px;
      display: inline-block;
      margin-bottom: 10px;

      .amount {
        font-size: 24px;
        font-weight: bold;
    
        margin-right: 8px;
      }

      .currency {
        font-size: 14px;
       
      }
    }

    .gift-note {
      font-size: 16px;
      margin: 0;
    }
  }

  .barcode {
    .barcode-lines {
      width: 80px;
      height: 60px;
      background: repeating-linear-gradient(
        to right,
        #333 0px,
        #333 2px,
        transparent 2px,
        transparent 4px
      );
      border-radius: 4px;
    }
  }
}

.products-section {
  .products-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;

    h2 {
      font-size: 24px;
      font-weight: bold;
      
    }

    .sort-select {
      padding: 8px 16px;
      border: 1px solid #ddd;
      border-radius: 6px;
      background: white;
      cursor: pointer;
    }
  }

  .products-container {
    display: flex;
    gap: 30px;
  }

  .filters {
    flex: 0 0 250px;

    .search-box {
      position: relative;
      margin-bottom: 30px;

      .search-input {
        width: 100%;
        padding: 12px 40px 12px 16px;
        border: 1px solid #ddd;
        border-radius: 25px;
        outline: none;

        &:focus {
          border-color: $ash-olive-400;
        }
      }

      .search-btn {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
      }
    }

    .filter-group {
      margin-bottom: 30px;

      h4 {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 15px;
        
      }

      ul {
        list-style: none;
        padding: 0;

        li {
          padding: 8px 0;
          
          cursor: pointer;
          transition: color 0.2s;

          &:hover {
            color: $ash-olive-400;
          }
        }
      }

      .checkbox-label {
        display: block;
        padding: 8px 0;
        cursor: pointer;

        input {
          margin-right: 8px;
        }
      }
    }
  }

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
        color: $ash-olive-400;
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

  .pagination {
    margin-top: 40px;
    display: flex;
    justify-content: center;
    gap: 8px;

    .page-btn {
      width: 40px;
      height: 40px;
      border: 1px solid #ddd;
      background: white;
      border-radius: 6px;
      cursor: pointer;
      transition: all 0.2s;

      &:hover {
       
        color: white;
        
      }

      &.active {
        
        color: white;
        
      }
    }
  }
}

.footer {
  margin-top: 60px;
  text-align: center;

  .footer-content {
    .logo {
      margin-bottom: 16px;

      .logo-icon {
        display: inline-block;
        background: $ash-olive-400;
        color: white;
        padding: 12px 20px;
        border-radius: 50px;
        font-weight: bold;
      }
    }

    p {
      
      font-size: 16px;
    }
  }
}

// 響應式設計
@media (max-width: 768px) {
  .products-container {
    flex-direction: column;
  }

  .filters {
    flex: none;
  }

  .products-grid {
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  }

  .featured-carousel .carousel-item {
    flex: 0 0 50%;
  }
}
</style>