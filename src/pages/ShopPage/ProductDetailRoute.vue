<script setup>
import { ref,computed,onMounted } from 'vue'
import { useRouter,useRoute } from 'vue-router'
import { useCartStore } from '@/stores/cart'
// import Products from '@/assets/json/products.json'

const CartStore = useCartStore()
const router = useRouter()
const route = useRoute()
const BASE = import.meta.env.BASE_URL;

// 響應式變數，存放商品資料
// const products = ref([])


// // 載入資料的函式
// const loadProducts = async () => {
//   try {
//     // [修改] fetch 從 public/products.json 抓資料
//     const res = await fetch('/json/products/products.json')
//     if (!res.ok) throw new Error('載入失敗')

//     // [修改] 將 JSON 字串轉成 JS 物件
//     const data = await res.json()
//     products.value = data
//   } catch (err) {
//     console.error('讀取商品資料錯誤:', err)
//   }
// }

const products = ref([])

// 🟢 載入資料的函式
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
  } catch (err) {
    console.error('讀取商品資料錯誤:', err)
  }
}

// 元件掛載完成後自動執行
onMounted(() => {
  loadProducts()
})




//關閉回到商品頁
const close = () => {
  // router.push('/Shop')
  router.go(-1) // 直接返回上一頁，不管是從哪裡來的
}

//複製鏈接(使用現代Clipboard 剪貼簿API，回傳promise)
function copyURL(){
  const url = window.location.href                           // 取當前商品明細的網址
  if(navigator.clipboard && navigator.clipboard.writeText){  //偵測這個API是否存在
    navigator.clipboard.writeText(url)                       //用Clipboard API：把字串寫進剪貼簿，多半要求安全環境（HTTPS 或 localhost）
    .then(()=>{alert('已複製網址')})
    .catch(()=>{alert('無法自動複製，請手動複製網址')})
  }else{
    alert('此瀏覽器不支援一鍵複製，請手動複製網址')
  }
}

//數量
const quantity=ref(1)
const Increase = ()=>{
  quantity.value++
}
const Reduce = ()=>{
  if(quantity.value > 1){
    quantity.value--}
  }

//收藏
const favorites = ref([])
const toggleFavorite = (productId) => {
  const index = favorites.value.indexOf(productId)
  if (index > -1) {
    favorites.value.splice(index, 1)
  } else {
    favorites.value.push(productId)
  }
}
//選擇顏色和大小
const selectcolor = ref()  //宣告「選到的顏色」
const selectsize = ref()   

function selectColor(color){  //點誰就把值寫進去
  selectcolor.value = color
}

function selectSize(size){
  selectsize.value = size
}

  //讀取路由參數並找到對應商品
const props = defineProps({ // 用 props.id 來拿商品 id,在router裡有props：true傳遞
  id: { type: String, required: true }
})
const product = computed(() => {                     
  const id = Number(route.params.id)                 // 參數是字串 → 轉數字
  return products.value.find(p => Number(p.id) === id)     // 找到對應商品,find是JavaScript 陣列的方法,會「逐一檢查陣列裡的元素」，找到第一個符合條件的元素就回傳,array.find( callback(element, index, array) )
})

//加入購物車
const handleAddToCart = async () => {
  if (product.value.color?.length && !selectcolor.value) {
    alert('請選擇顏色')
    return false
  }
  if (product.value.size?.length && !selectsize.value) {
    alert('請選擇尺寸')
    return false
  }
  // 呼叫 CartStore 的 addToCart 方法，傳入完整商品資訊
  const success = await CartStore.addToCart(
    product.value,           // 商品物件
    selectsize.value || '',  // 選擇的尺寸（如果沒有尺寸選項則為空字串）
    selectcolor.value || '', // 選擇的顏色（如果沒有顏色選項則為空字串）
    quantity.value           // 數量
  )
  
  // 如果成功加入購物車，可以選擇是否關閉彈窗或其他動作
  if (success) {
    close()
    console.log('商品已成功加入購物車')
  }
}
  


// 立即購買功能（加入購物車後跳轉到購物車頁面）
const buyRightnow = async () => {
  // 先執行加入購物車
    if (product.value.color?.length && !selectcolor.value) {
    alert('請選擇顏色')
    return false
  }
  if (product.value.size?.length && !selectsize.value) {
    alert('請選擇尺寸')
    return false
  }
  // 呼叫 CartStore 的 addToCart 方法，傳入完整商品資訊
  const success = await CartStore.addToCart(
    product.value,           // 商品物件
    selectsize.value || '',  // 選擇的尺寸（如果沒有尺寸選項則為空字串）
    selectcolor.value || '', // 選擇的顏色（如果沒有顏色選項則為空字串）
    quantity.value           // 數量
  )
  
  // 如果成功加入，才跳轉到購物車頁面
  if (success) {
    router.push({name:'Shop-cart'})
  }
}


</script>

<template>
    <!-- 遮罩 -->
  <div class="mask" @click="close" ></div>

  <!-- 彈窗本體（Teleport 避免受父層影響） -->
  <teleport to="body">
    <div v-if= "product" class="product_modal"  @click.stop> <!-- stop是vue的修飾符，讓遮罩的click事件在這個區域停止,v-if讓有找到商品才顯示詳情 -->
      <!-- 右上角關閉 -->
      <button  class="close" @click="close" >×</button>
      <!-- 卡片上半區塊 -->
      <div class="modal_up">
        <!-- 左：主圖 -->
        <div class="product_show">
            <div class="product_image">
                <!-- <img src="@/assets/images/Products/products/望遠鏡_3.png" alt="折疊雙筒望遠鏡"/> -->
                 <img :src="`${BASE}images/Products/products/${product.image}`" :alt="product.name">
            </div> 
          <!-- 標籤 -->
          <div class="product_tags">
            <span class="tag">熱銷</span>
            <span class="tag">新手必備</span>
            <span class="tag">建議難度 低</span>
            <span class="tag">女性特製</span>
          </div>
        </div>
        <!-- 右：規格 -->
        <div class="product_info">
          <h2 class="product_title">{{ product.name }}</h2>
          <!-- 價格&icon -->
          <div class="product_price_icon">
            <div class="product_price">NT$ {{product.price}}</div>
            <div class="share-like">
                <button class="icon_btn" @click.stop="copyURL">🔗</button>
                <button class="icon_btn" @click.stop="toggleFavorite(product.id)">
                  {{ favorites.includes(product.id) ? '❤️' : '🤍' }}
                </button>
            </div>
          </div>
          <!-- 顏色（若有才可選） -->
          <div class="product_row" v-if ="product.color?.length" >
            <div class="product_label">顏色</div>
            <div class="product_options" v-for =" color in product.color" :key="color">
              <!-- <button class="opt">黑</button>
              <button class="opt">白</button>
              <button class="opt">紅</button> -->
              <button class="opt" :class="{active:selectcolor == color}" @click="selectColor(color)">{{ color }}</button>
              <!-- 
              :class="{ active: 條件 }" 是 Vue 的類名綁定語法：當「條件」為 true 時，幫這個元素加上 active 類名；否則不加。 
              selectcolor == color「把我目前選到的顏色 selectcolor，和這一顆按鈕代表的顏色 color 做比較」如果一樣（例如都等於 "黑"）
              ，就回傳 true → 加上 active 類名；不一樣就 false → 不加。-->
            </div>
          </div>
          <!-- 尺寸(若有才可選) -->
          <div class="product_row" v-if ="product.size?.length">
            <div class="product_label">尺寸</div>
            <div class="product_options" v-for =" size in product.size" :key="size">
              <!-- <button class="opt">S</button>
              <button class="opt">M</button>
              <button class="opt">L</button> -->
              <button class="opt" :class="{active : selectsize == size }" @click="selectSize(size)">{{ size }}</button>
            </div>
          </div>
          <!-- 數量 -->
          <div class="product_row">
            <div class="product_label">數量</div>
            <div class="product_quality">
              <button @click="Reduce">-</button>
              <input type="number" v-model="quantity" style="width: 100px; text-align: center;" readonly />
              <button @click="Increase">+</button>
            </div>
          </div>

          <!-- 行動按鈕 -->
          <div class="product_actions">
            <button class="btn-addcart" @click="handleAddToCart">加入購物車</button>
            <button class="btn-paynow" @click="buyRightnow">立即購買</button><!-- 加入購物車,並跳轉到購物車頁面 -->
          </div>
        </div>
      </div>
        <!-- 卡片下半區塊 -->
        <div class="modal_down">
          <!-- 詳細資訊（不同商品內容不同） -->
        <div class="product_detail">
            <h3>商品詳情</h3>
            <p>{{product.description}}</p>
        </div>
            <!-- 配送資訊（永遠不變） -->
        <div class="product_accordions">
            <div class="product_accordion">
                <div class="product_acc-title">付款方式</div>
                <ul>
                  <li>信用卡:VISA / Master / JCB</li>
                  <li>LINE Pay</li>
                  <li>超商取貨付款（限額 $2,000 以下）</li>
                </ul>
            </div>
            <div class="product_accordion">
                <div class="product_acc-title">運送方式</div>
                <ul>
                    <li>7-11 取貨：長邊 ≤ 45cm，重量 ≤ 5kg</li>
                    <li>離島配送：運費另計</li>
                </ul>
            </div>
        <!-- /product_accordions -->
         </div>
        </div>
    </div>
  </teleport>
</template>

<style lang="scss" scoped>
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';

/* 遮罩 */
.mask {
  position: fixed;
  inset: 0;//top: 0;right: 0;bottom: 0;left: 0;的縮寫
  background: rgba(0,0,0,.35);
  z-index: 90;
}

/* 彈窗本體 */
.product_modal {
  position: fixed;
  inset: 40px 24px;//top:40px; right:24px; bottom:40px; left:24px;
  max-width: 1080px;
  margin: auto;
  background: #fff;
  border-radius: 16px;
  padding: 24px;
  z-index: 100;
  box-shadow: 0 20px 60px rgba(0,0,0,.2);
  max-height:80vh; 
  overflow:auto;
  box-sizing: border-box;

  display: flex;
  flex-direction: column;
}

/* 關閉按鈕 */
.close {
  @include btn(0);
  position: absolute;
  right: 12px;
  top: 10px;
  font-size: 24px;
  padding: 6px 10px;
  // align-self: flex-end;
}

/* 上半部版面 */
.modal_up {
  @include flexcenter(100px,row);
  padding: 12px;
}
.product_show{
    @include flexcenter(10px,column);
    align-items: flex-start;
    flex: 0 0 400px;
    .product_image{
        @include product_card_img(400px,400px,16px);
        & > img{
          height: 100%;
        };
    }
    .product_tags{
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        .tag {
            background: $tag;
            color: #fff;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
        }
    }
}
/*右邊內容區*/
.product_info{
  padding-top: 6px;
  flex: 0 0 400px;
  .product_title { 
      font-size: $pcFont-H3; 
      font-weight: $bold; 
      margin: 6px 0 10px; 
  }
  .product_price_icon {
      display: flex; 
      justify-content: space-between; 
      align-items: center;
      margin-bottom: 16px;
      .product_price { font-size: 36px; font-weight: 800; }
      .share-like {
          display: flex; gap: 10px;
          .icon_btn{
          width: 36px; 
          height: 36px; 
          @include flexcenter(0,row);
          @include btn(0);
          }
      }
  }
  /* 規格列 */
  .product_row{ 
    @include flexcenter(16px,row);
    justify-content:flex-start;
    margin: 18px 0;}
    .product_label { 
        width: 44px; 
        font-weight: $bold; 
        color:$black-14; 
    }
    .product_options {
        display: flex; 
        gap: 12px; 
        flex-wrap: wrap;
        .opt {
            min-width: 56px; 
            height: 36px; 
            padding: 0 14px;
            @include btn(8px);
            @include border(#bbb);
            &.active{
              color: white;
              background-color: $black-14;
            }
            }
  }
  /* 數量區（底線風格） */
  .product_quality {
    @include flexcenter(12px,row);
    padding: 6px 12px 12px;
    width: 192px;
    box-sizing: border-box;
    button { 
      width: 32px; 
      height: 32px; 
      font-size: 24px;
      @include border(#bbb);
      @include btn(6px);
    .number { 
      width: 174px; 
      height: 64px; 
      text-align: center; 
      border: none; 
      outline: none; }
      }
  }

  /* 行動按鈕 */
  .product_actions {
    display: flex; 
    gap: 12px; 
    margin-top: 22px;
    .btn-addcart {
      flex: 1; 
      @include btn(10px);
      @include border(#111);
      height: 48px; 
      font-weight: $bold;
    }
    .btn-paynow {
      flex: 1; 
      @include btn(10px);
      @include border(#111);
      height: 48px; 
      background: #111; 
      color: #fff;
      font-weight: 800;
    }
  }
}


/* 下半區塊：左文案、右折疊 */
.modal_down {
 @include flexcenter(100px,row);
 align-items: center;
 margin: 28px;
}
.product_detail{
  flex: 0 0 400px;
  height: 100%;
  h3 { font-size: 16px; font-weight: 800; margin-bottom: 8px; }
  p  { color: #333; margin: 8px 0 0; line-height: 1.6; }
}

.product_accordions{
  flex: 0 0 400px;
  .product_accordion { 
    border-top: 1px solid #e5e5e5;
    padding: 16px 0; }
  .product_acc-title {
    font-weight: 800;
    position: relative;
    padding-right: 24px;
    margin-bottom: 10px;
    .product_acc-title::after {
      content: "";
      position: absolute;
      right: 0; 
      top: 50%;
      width: 10px; 
      height: 10px; 
      border-right: 2px solid #222; 
      border-bottom: 2px solid #222;
      transform: translateY(-50%) rotate(-45deg);
      }
    }
  .product_accordion ul { margin: 0 0 0 16px; line-height: 1.8; color: #333; }
}

@include mq(980px) {
  .modal_up,.modal_down{
    @include flexcenter(20px,row);
  }
}


@include mq(900px) {
  .modal_up,.modal_down{
    @include flexcenter(20px,column);
  }
  .close{
    background-color: transparent;
    
  }
  .modal_up{
    padding: 24px;
    box-sizing: border-box;
  }
}

@include mq(430px) {
.modal_up{
  box-sizing: border-box;
  padding: 24px 0;
}
  .product_show{
    flex: 0 0 300px;
    .product_image{
      @include product_card_img(300px,300px,16px);
      align-self: center;
    }
  }
  .product_info{
    flex: 0 0 300px;
    width: 300px;
  }
.modal_down{
  margin: 0;
  padding: 24px 0;;
  box-sizing: border-box;
  .product_detail{
    flex: 0 0 ;
    width: 300px;
    h3,p{
      margin-bottom: 18px;
    }
  }
  .product_accordions{
    flex: 0 0 ;
    width: 300px;
  }
}

}



</style>

