<script setup>
import NavMenu from '@/components/An/navMenu.vue';
import Checkout_stepup from '@/components/Irene/ShopPage/Checkout_stepup.vue';
import brandFooter from '@/components/An/footer.vue'
import { ref, watch,onMounted } from 'vue'
import { ArrowDown } from '@element-plus/icons-vue'//是elementplus圖示元件
import { ElMessage } from 'element-plus'  ///是全域提示訊息（toast）API。
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart';


const CartStore = useCartStore()
const router = useRouter()
const allChecked = ref(true)    // 從 store 取得全選狀態，不需要自己維護


// const checkedMap = reactive(Object.fromEntries(CartStore.cartItems.map(i => [i.id, true])))  
//.map把一個陣列「逐一」轉換成「另一個陣列」,轉換成[[1,true],[2,true]...],Object.fromEntries(...)把陣列變回物件=> { "1": true, "2": true }，checkedMap變成物件，記錄每個商品的勾選狀態

// 監聽 store 的全選狀態，同步到本地 ref
watch(() => CartStore.isAllChecked, (newValue) => {
  allChecked.value = newValue
})

// 頁面載入時從後端載入購物車資料
onMounted(async () => {
  try {
    await CartStore.loadCartFromBackend()
    console.log('購物車載入完成')
  } catch (error) {
    console.error('載入購物車失敗:', error)
    ElMessage.error('載入購物車失敗，請重新整理頁面')
  }
})

// 全選/取消全選功能
function toggleAll() { 
  CartStore.toggleAllChecked(allChecked.value)
}

// 刪除單一商品
function remove(id) { 
  console.log('準備刪除商品 ID:', id)
  CartStore.removeFromCart(id)
}

// 刪除已勾選的商品
function removeChecked() {
  CartStore.removeCheckedItems()
}


// 監聽購物車商品變化，自動更新勾選狀態
// watch(要監聽的東西, 當改變時要執行的函式, 選項)：監聽資料變化，當資料改變時執行函式
// watch(() => CartStore.cartItems, (newItems) => {
//     // 當購物車商品變化時，也要更新勾選狀態
//     newItems.forEach(item => {
//         if (!(item.id in CartStore.checkedMap)) {
//             CartStore.checkedMap[item.id] = true
//         }
//         //「如果這個商品在勾選清單中不存在」，就把這個商品設為true-已勾選
//     })
    
    // 移除已不存在商品的勾選狀態
//     Object.keys(checkedMap).forEach(id => {
//         const exists = newItems.some(item => item.id.toString() === id)
//         if (!exists) {
//             delete checkedMap[id]
//         }
//     })
// }, { deep: true })

//全選勾選
// function toggleAll(){ 
//     Object.keys(CartStore.checkedMap).forEach(k => (CartStore.checkedMap[k] = allChecked.value))
//     //Object.keys(): 取得物件的所有key， 當使用者點擊「全選」按鈕時執行，把所有商品的勾選狀態都設定成跟全選按鈕一樣
// }
//前端監聽全選的狀態
// watch(() =>                     
//     Object.values(CartStore.checkedMap),           //Object.values(): 取得物件所有的值
//     vals => allChecked.value = vals.every(Boolean),      //vals.every(Boolean): 檢查陣列中是否所有值都為 true，最後把結果賦值給 allChecked.value。
//     { deep: true}                        //deep: true: 物件參數 (options object)，深度監聽物件內部的值的變化，我需要監聽它裡面每個 key 的變動（深層追蹤），不只監聽最外層
// )


// 原本的 remove 函數 - 刪除單一商品
// function remove(id) { 
//     console.log('購物車項目',id)
//     // 直接使用 store 的 removeFromCart 方法
//     CartStore.removeFromCart(id)
//     console.log('後端要刪除的removeFromCart購物車項目',id)
//     // 同時也要刪除勾選狀態
//     delete CartStore.checkedMap[id]
// }

// 原本的 removeChecked 函數 - 刪除已勾選的商品
// function removeChecked() {
//     if (!CartStore.checkedIds.value.length) {
//         ElMessage.info('請先勾選要刪除的商品')
//         return
//     }
    
//     // 使用 store 的方法逐一刪除
//     CartStore.checkedIds.value.forEach(id => {
//         CartStore.removeFromCart(id)
//         delete CartStore.checkedMap[id]  // 同時清除勾選狀態
//     })
// }

// function remove(id){ 
//     const idx = CartStore.cartItems.findIndex(i => i.id === id); 
//     if (idx>-1){ CartStore.cartItems.splice(idx,1); 
//         delete checkedMap[id]
//     } 
// }
// //splice(起始位置, 刪除幾個元素) 是 列方法,

// function removeChecked(){
//   const ids = Object.entries(checkedMap).filter(([,v])=>v).map(([k])=>+k)
//   ids.forEach(remove)
//   if(!ids.length) ElMessage.info('請先勾選要刪除的商品')
// }

// 優惠券
// const coupons=[{ id:'A', title:'新朋友 $200 折扣', amount:200 }]
// const chosenCoupon=ref(null)
// function applyCoupon(c){ chosenCoupon.value=c }

// 小計
// const subtotal = computed(()=> items.reduce((s,i)=> s + i.price*i.qty, 0))
// const totalQty = computed(()=> items.reduce((s,i)=> s + i.qty, 0))
// const discount = computed(()=> chosenCoupon.value?.amount ?? 0)
// const total = computed(()=> Math.max(subtotal.value - discount.value, 0))

// 導頁


function goBack(){ router.push('/Shop') }
function goNext(){ 
    //檢查是否有勾選商品才能進入下一步
    if (CartStore.checkedIds.length === 0) {
        ElMessage.warning('請先勾選要結帳的商品')
        return
    }
    router.push('/Shop/info') 
}

</script>

<template>
    <NavMenu/>
    <div class="wrapper">
        <main>
            <!-- 步驟 -->
            <section>
            <Checkout_stepup :current="1"/>
            </section>
            <!-- 全部選擇的按鈕 -->
            <div class="toolbar">
                <el-checkbox v-model="allChecked" @change="toggleAll">全選</el-checkbox>
                <!-- @change: 監聽事件，當值改變時執行函式 -->
                <el-button link type="info"  @click="removeChecked">刪除</el-button>
                <!-- link 是內建屬性，讓按鈕外觀像文字連結，type="info" 使用內建配色。 -->
            </div>
            <!-- 已加入商品列表 -->
            <el-table :data="CartStore.cartItems"  stripe class="cart-table"><!-- stripe 開啟斑馬紋列 -->
                <el-table-column label="" width="54" align="center">
                    <!-- 這裡的<template>是 Vue 提供的「語法糖 (虛擬容器)」，常用來做： 插槽 (slot) 的佔位，<slot> 是放在子元件裡的，在 el-table-column 裡面定義好了，不需要再寫，只要在template裡面放要放的東西就可以了-->
                    <template #default="{ row }">
                        <el-checkbox v-model="CartStore.checkedMap[row.id]" />
                    </template>
                </el-table-column>

                <el-table-column label="商品圖片" width="140">
                    <template #default="{ row }">
                        <el-image :src="`/images/Products/products/${row.image}`" fit="cover" style="width:120px;height:120px;border-radius:6px;" />
                    </template>
                </el-table-column>
                
                <el-table-column prop="name" label="商品內容" min-width="220"><!-- 商品title -->
                    <template #default="{ row }">
                        <div class="name">{{ row.name }}</div><!-- 商品名稱 -->
                        <div class="sku">尺寸：{{ row.size }}   顏色：{{ row.color }}</div><!-- 各商品規格 -->
                    </template>
                </el-table-column>

                <el-table-column label="數量" width="160" align="center"><!-- 數量title -->
                    <template #default="{ row }">
                        <el-input-number v-model="row.qty" :min="1" @change="(value) => CartStore.updateQty(row.id, value)"/>
                    </template><!-- 各商品數量選擇 -->
                </el-table-column>

                <el-table-column label="單價" width="120" align="right"><!-- 單價title -->
                    <template #default="{ row }">NT${{ row.price }}</template><!-- 各商品的單價 -->
                </el-table-column>

                <el-table-column label="" width="64" align="center">
                    <template #default="{ row }"><el-button link type="danger" @click="remove(row.id)">✕</el-button></template>
                </el-table-column>
            </el-table>
            <div class="totaldetail">
                <!-- 選擇優惠券 -->
                <div class="coupon">
                    <el-dropdown @command="CartStore.applyCoupon">
                        <el-button>
                        選擇優惠券
                        <el-icon class="ml-1"><ArrowDown/></el-icon>
                        </el-button>
                        <template #dropdown>
                        <el-dropdown-menu>
                            <el-dropdown-item v-for="c in CartStore.coupons" :key="c.id" :command="c">{{ c.title }}</el-dropdown-item>
                        </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                    <span v-if="CartStore.chosenCoupon" class="coupon-tag">已使用：{{ CartStore.chosenCoupon.title }}</span>
                </div>
                <!-- 購物總計,(後期優化時讓每格排版對齊) -->
                <el-card shadow="never" class="summary">
                    <div class="line">
                        <span>共 {{CartStore.checkedTotalQty }} 件商品</span>
                        <span>商品金額</span>
                        <strong> $ {{ CartStore.checkedTotalPrice.toLocaleString() }}</strong>
                        <!--toLocaleString 是「自動加上地區的格式」。例如：加千分位、貨幣符號、日期格式。 -->
                    </div>
                    <div class="line">
                        <span></span>
                        <span>活動優惠</span>
                        <strong class="discount">- $ {{ CartStore.discount.toLocaleString() }}</strong>
                    </div>
                    <el-divider style="border-top: 1px solid #ccc; padding:0;"/>
                    <div class="line total">
                        <span></span>
                        <span>小計</span>
                        <strong>$ {{ CartStore.checkedFinalTotal.toLocaleString() }}</strong></div>
                </el-card>
            </div>
            <!-- 按鈕 -->
            <div class="actions">
                <el-button @click="goBack">上一步</el-button>
                <el-button color="#141414" :dark="isDark" :disabled="!CartStore.cartItems.length" @click="goNext">下一步</el-button>
            </div>
        </main>
    </div>
    <brandFooter/>
</template>

<style lang="scss" scoped>
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';
.wrapper{
    width: 100%;
    main{
        max-width: 1200px;
        padding: 2vh;
        margin: auto;

        // section{
        //     padding: 40px 0;
        //     :deep(element.style){
        //         text-align: center;
        //     }
        // }
    }
}
.toolbar{//全部選擇按鈕
    display: flex;
    justify-content: space-between;
    padding: 20px;
    :deep(.el-checkbox__label){font-size: $pcFont-p-m;}//全選字體大小
    :deep(.el-button>span){font-size: $pcFont-p-m;}//全部刪除字體大小
}
/* 表格 */
:deep(.el-table .cell){padding: 16px 0;}  //表格每列樣式
:deep(.el-table_1_column_3 .cell){//商品欄文字樣式
    display: flex;
    flex-direction: column;
    gap: 48px;
}
:deep(.el-button--danger span){//叉叉的顏色
    color: $black-14;
}
.totaldetail{
    @include flexcenter(0,row);
    justify-content:space-between;
    align-items: flex-start;
    .coupon{
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 18px;
    }
    :deep(.el-card__body){
        @include flexcenter(18px,column);
        align-items: flex-end;
        >.line span,strong{
            padding-left: 20px;
        }
    }
}

.actions{
    padding: 20px;
    display: flex;
    justify-content: end;
}

</style>