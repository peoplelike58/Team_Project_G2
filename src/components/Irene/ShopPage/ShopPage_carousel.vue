<!-- 精選商品輪播 -->
<script setup>
import { useRouter } from 'vue-router'
import { ref,computed,onMounted,onUnmounted,watch } from 'vue'

const BASE = import.meta.env.BASE_URL;
const carouselproducts = ref([])        //
const products = ref([])                //全部商品資料

// 載入資料的函式
const loadProducts = async () => {
  try {
    // [修改] fetch 從 public/products.json 抓資料改成實際的API端點
    const res = await fetch(import.meta.env.VITE_AJAX_URL + '/ShopPage_getProducts.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({})
    })
    // const res = await fetch('/json/products/products.json')
    if (!res.ok) throw new Error('載入失敗')
    // throw new Error(`HTTP error! status: ${res.status}`)

    // [修改] 將 JSON 字串轉成 JS 物件
    const data = await res.json()
    console.log(data)
    products.value = data.products

    // 從所有商品中選擇9個作為輪播商品,如果想要固定的隨機組合，可以使用固定的種子值
    carouselproducts.value = products.value.slice(0, 9)

  } catch (err) {
    console.error('讀取商品資料錯誤:', err)
  }
}



// 目前顯示的商品索引（從 0 開始）
const currentIndex = ref(0)

const screenWidth = ref(window.innerWidth)

// 響應式的顯示參數
const responsiveSettings = computed(() => {
  if (screenWidth.value <= 430) {
    return { showCount: 1, cardWidth: 240, gap: 20 }
  } else if (screenWidth.value <= 768) {
    return { showCount: 3, cardWidth: 200, gap: 20 }    
  } else if (screenWidth.value < 900) {
    return { showCount: 4, cardWidth: 220, gap: 20 }    
  } else if (screenWidth.value < 1200) {
    return { showCount: 4, cardWidth: 220, gap: 30 }    
  } else {
    return { showCount: 4, cardWidth: 240, gap: 40 }    
  }
})

// 監聽螢幕寺度變化
const handleResize = () => {
  screenWidth.value = window.innerWidth
  
  // 重新計算最大索引並調整當前索引
  const maxIdx = maxIndex.value
  if (currentIndex.value > maxIdx) {
    currentIndex.value = maxIdx
  }
}


//計算最大索引（防止超出範圍
// const maxIndex = computed(() =>
//   Math.max(0, carouselproducts.value.length - showCount)   //意思是最多可以從第5個商品開始顯示（索引0-5）,例如：9個商品，一次顯示3個，最大索引 = 9 - 4 = 5
// )
const maxIndex = computed(() => {
  const totalProducts = carouselproducts.value.length
  const showCount = responsiveSettings.value.showCount
  
  if (showCount === 1) {
    return Math.max(0, totalProducts - 1)
  } else {
    return Math.max(0, totalProducts - showCount)
  }
})
//計算輪播容器的位移距離,
const translateX = computed(() => {
  const { cardWidth, gap } = responsiveSettings.value
  
  if (responsiveSettings.value.showCount === 1) {
    // 430px：簡單的置中計算
    const viewportWidth = 305
    const centerOffset = (viewportWidth - cardWidth) / 2 // 只考慮卡片寬度
    
    // 從置中位置開始移動
    return centerOffset - (currentIndex.value * (cardWidth + gap))
  } else {
    return -(currentIndex.value * (cardWidth + gap))
  }
})



const goPre=()=>{
 // 如果不是第一個商品，就讓 currentIndex 減 1
  if (currentIndex.value > 0) {
    currentIndex.value = currentIndex.value - 1
  }
}

const goNext=()=>{
    console.log('點擊下一個，當前:', currentIndex.value, '最大:', maxIndex.value)
  // 如果還沒到最後一個商品，就讓 currentIndex 加 1
  if (currentIndex.value < maxIndex.value) {
    currentIndex.value = currentIndex.value + 1
     console.log('移動後:', currentIndex.value)
  }
}
// 檢查按鈕是否應該禁用
const isPrevDisabled = computed(() => currentIndex.value === 0)
const isNextDisabled = computed(() => currentIndex.value >= maxIndex.value)


/* 點擊出現商品明細卡片 */
const router = useRouter()
function Showdetail(item){
  // router.push(`/Shop/product/${product.id}`);
  router.push({
    name:'ProductDetailRoute',
    params:{id:item.id}
  })
}


// 元件掛載完成後自動執行
onMounted(() => {
  loadProducts()            // 先載入商品數據
  window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
})

</script>


<template>
    <section class="featured_products">
        <h2>精選商品</h2>
        <div class="carousel_box">
            <button class="products_pre" @click="goPre" :disabled="isPrevDisabled"><</button>
            <!-- 只有容器移動，項目不再個別設置 transform -->
            <div class="carousel_viewport">
                <div class="carousel_content" 
                    :style="{ 
                        transform: `translateX(${translateX}px)`,
                        gap: `${responsiveSettings.gap}px`}"      
                >
                    <div 
                        class="carousel_item" 
                        v-for="(item,index) in carouselproducts" 
                        :key="item.id" 
                        :style="{ 
                            width: `${responsiveSettings.cardWidth}px`}"
                        @click="Showdetail(item)">
                        <div class="item_pic"><img :src="`${BASE}images/Products/products/${item.image}`" alt="" ></div>
                        <p>{{ item.name }}</p>
                    </div>
                </div>
            </div>
            <button class="products_next" @click="goNext" :disabled="isNextDisabled">></button>
        </div>
    </section>
</template>


<style scoped lang="scss">
 
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';

h2{
    font-size : $pcFont-H2;
    color : $black-14;
    font-weight : $semiBold;
    line-height : $lineHeight-title-120;
    padding: 40px 100px;

}
.carousel_box {
    @include flexcenter(20px,row);
    button{
    @include btn(0);
    padding: 10px;
    font-size: $pcFont-H3;
    background-color: transparent;
    font-weight: $bold;
    line-height: $lineHeight-p-200;
    transform: scale(1.2);
    padding: 8px;

    &:hover{
        color: #ccc;
    }
    &:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }
        
        @media (max-width: 768px) {
            transform: scale(1.2);
            padding: 8px;
        }
        @media (max-width: 600px) {
            gap: 10px;
        }
        @media (max-width: 430px) {
            transform: scale(1);
            padding: 6px;
            gap: 15px;
            padding: 0 10px;
        }
    }
    // 新增輪播視窗容器
    .carousel_viewport {
        max-width: 1100px;
        overflow: hidden;
        width: 100%;
        
        // 430px 以下置中顯示
        @media (max-width: 430px) {
            // display: flex;
            // justify-content: center;
        }
    }
    .carousel_content{
        display: flex; 
        align-items: flex-start;
        justify-content: flex-start;
        justify-content: flex-start; 
        transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1); // 過渡動畫
        will-change: transform; // 優化動畫效能
        // 性能優化：減少不必要的重繪
        transform-style: preserve-3d;
        backface-visibility: hidden;
        .carousel_item{
            cursor: pointer;
            flex-shrink: 0; // 防止項目被壓縮
            transition: transform 0.3s ease; 
            &:hover {
                transform: translateY(-5px) scale(1.02);
            }
            .item_pic{
                // @include product_card_img(240px,240px,10px);
                border-radius: 10px;
                overflow: hidden;
                aspect-ratio: 1/1;  // 強制保持正方形比例
                img{
                    @include img;
                    height: 100%;
                }
            }
            p{
            font-size : $pcFont-H4;
            color : $black-14;
            font-weight : $semiBold;
            line-height : $lineHeight-p-150;
            padding: 10px 0;
            text-align: center;
            }
        }

      @media (max-width: 430px) {
        // 430px 下，讓容器內容在父容器中置中
        margin: 0;
      }
    }
}




</style> 
