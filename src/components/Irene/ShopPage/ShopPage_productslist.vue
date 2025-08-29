<script setup>
import { ref,computed,watch } from 'vue'
import { useRouter } from 'vue-router'
import Products from '@/assets/json/products.json'// 從json引入

const products = Products;

/*點擊收藏*/
const favorites = ref([])
const toggleFavorite = (productId) => {
  const index = favorites.value.indexOf(productId)
  if (index > -1) {
    favorites.value.splice(index, 1)
  } else {
    favorites.value.push(productId)
  }
}

/* 點擊出現商品明細卡片 */
const router = useRouter()
function Showdetail(product){
  router.push(`/Shop/product/${product.id}`);
}


// 接收篩選條件&接收分頁參數 props
const props = defineProps({
  filters: {
        type: Object,                 // 指定 props 的類型是 Object，因為filters篩選的情在這個專題裡有3種（關鍵字、分類、性別）
        default: () => ({             // 定義預設值的函數
        search: '',                   // 搜尋關鍵字預設為空字串
        category: '',                 // 商品分類預設為空字串  
        genders: []                   // 性別篩選預設為空陣列
    })
  },
  currentPage: { type: Number, default: 1 },
  pageSize: { type: Number, default: 20 }
})

//接收到篩選方式後篩選商品
const sortOrder = ref('')//排序
const filteredProducts = computed(()=>{
  let result = products ;//原本的顯示結果是所有的商品

  //排序功能 (要在所有篩選之前，不然篩選後才能排序，- 無論有沒有分類都要執行排序)
  if (sortOrder.value === 'price-low') {
    result = result.sort((a, b) => a.price - b.price); // 價格低到高
  } else if (sortOrder.value === 'price-high') {
    result = result.sort((a, b) => b.price - a.price); // 價格高到低
  } else if (sortOrder.value === 'newest') {
    result = result.sort((a, b) => b.id - a.id); // 最新上架 (假設id越大越新)
  }

  //關鍵字搜尋條件
  if(props.filters.search){
    const keyword = props.filters.search;
    result = result.filter(product=>product.name.includes(keyword));//filter是JavaScript的陣列使用方法，會把陣列每個元素丟進「測試函式」。回傳 true 的元素保留，false 的丟掉，最後回傳新陣列
  }
  //性別篩選條件
  if (props.filters.genders.length > 0) {
    result = result.filter(product => props.filters.genders.includes(product.gender) );
  }
  //分類篩選 - 只有當 category 不是空字串時才篩選
  if (props.filters.category && props.filters.category !== '') {
    result = result.filter(product => product.category === props.filters.category);
  }

  return result;
})

// 對父層回報篩選後總數，給分頁用
const emit = defineEmits(['total-change'])  
watch(filteredProducts, (arr) => {          
  emit('total-change', arr.length)          
}, { immediate: true })


// 計算「當頁資料」
const pagedProducts = computed(() => {                
  const page = Number(props.currentPage) || 1
  const size = Number(props.pageSize) || 20
  const start = (page - 1) * size
  const end   = start + size
  return filteredProducts.value.slice(start, end)
})

</script> 

<template>
  <!--  商品展示  -->
  <div class="products_view">
    <!-- 標題-排序 -->
    <div class="products_title">
          <h2>全部商品</h2>
          <div class="order_box">
              <label for="order_box"id="order_box">排序 : </label>
              <select name="order_box" id="order_box" v-model="sortOrder">
                <option value="">預設排序</option>
                <option value="newest">最新上架</option>
                <option value="price-low">價格:低至高</option>
                <option value="price-high">價格:高至低</option>
              </select>
          </div>
    </div>
      <!-- 商品卡片 -->
    <div class="products_content">
      <div class="product_card" v-for="product in pagedProducts" :key="product.id" @click="Showdetail(product)">
          <div class="product_image">
            <img :src="product.image" :alt="product.name">
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


<style scoped lang="scss">
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';

/* 標題 + 排序 */
.products_view{
  flex: 1;
  @include flexcenter(40px,column);
}
.products_title{
  @include flexcenter(auto,row);
  width: 100%;
  justify-content: space-between;
  h2{
      font-size: $pcFont-H2;
      font-weight: $semiBold;
      line-height: $lineHeight-p-200;
  }
  label{
      font-size: $pcFont-H4;
  }
  select{
      width: 200px;
      padding: 5px 10px;
      margin-left: 5px;
      border-radius: 4px;
      @include border(#ccc);
  }
}


/* 商品卡片區 */
.products_content {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  // grid-template-columns: repeat(3, 1fr);
  gap: 20px;
  width: 100%;

  .product_card {
    border-radius: 16px;
    overflow: hidden;
    cursor: pointer;
    
    &:hover {
      // transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .product_image {
      @include product_card_img(100%,250px,16px);
      position: relative;// 這個很重要！讓 absolute 定位有參考點

      img {
        @include img;
      }

      .favorite-btn {
        @include btn(0);
        position: absolute;
        top: 12px;
        right: 12px;
        background: transparent;
        font-size: 16px;
        z-index: 20;
      }
    }

    .product-name {
      padding: 16px 16px 8px;
      font-weight: $bold;
      line-height: $lineHeight-p-200;
    }

    .product-price {
      padding: 0 16px 16px;
      font-size: $pcFont-H3;
      font-weight: $bold;
      line-height: $lineHeight-p-200;
    }
  }
}

@include mq(750px) {
  .products_title{
    @include flexcenter(20px,column);
    align-items: flex-start;
  }
 }

 

</style>