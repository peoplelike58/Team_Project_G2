// stores/cart.js
import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import { useUserStore } from './user'
import { ElMessage } from 'element-plus'

export const useCartStore = defineStore('cart', () => {
  // ========== 響應式資料 ==========
  // 購物車商品列表，存放陣列
  const cartItems = ref([
//     { id: 1, name: '折疊雙節望遠鏡', size: 'S', color: '紅', price: 3200, qty: 1, image: 'https://images.unsplash.com/photo-1516223725307-6f76b9ec8742?w=600&fit=crop' },
//     { id: 2, name: '折疊雙節望遠鏡', size: 'S', color: '紅', price: 3200, qty: 1, image: 'https://images.unsplash.com/photo-1519681393784-d120267933ba?w=600&fit=crop' },
  ]) 
  const coupons=[{ id:'A', title:'新朋友 $200 折扣', amount:200 ,}]
  const chosenCoupon=ref(null)  // 目前選擇的優惠券
  const isLoading = ref(false)  // 載入狀態，避免重複點擊

  // ========== 計算屬性 ==========
  // 購物車總數量 - 把所有商品的 qty 加起來,.reduce(計算函數，初始值為0)-陣列語法
  const totalQty = computed(() => {
    return cartItems.value.reduce((total, item) => total + item.qty, 0)
  })

  // 購物車總金額 - 把所有商品的 (價格 × 數量) 加起來
  const totalPrice = computed(() => {
    return cartItems.value.reduce((total, item) => total + (item.price * item.qty), 0)
  })

  //購物車優惠券 - 
  const discount = computed(()=> chosenCoupon.value?.amount ?? 0)

  //實際金額=總金額-優惠金額
  const finaltotal = computed(()=> Math.max(totalPrice.value - discount.value, 0))

  // 購物車是否為空
  const isEmpty = computed(() => cartItems.value.length === 0)



  // ========== 方法/行為 ==========

  //選擇優惠券
  const applyCoupon = (c)=>{ chosenCoupon.value=c }


  /**
   * 加入商品到購物車
   * @param {Object} product - 商品物件，包含 id, name, price, image 等
   * @param {string} size - 尺寸 (如: S, M, L)
   * @param {string} color - 顏色 (如: 紅, 藍)
   * @param {number} qty - 數量，預設為 1
   */
  const addToCart = async (product, size = 'S', color = '紅', qty = 1) => {

    // 檢查使用者是否登入
    const userStore = useUserStore()
    if (!userStore.isLoggedIn) {
        ElMessage.warning('請先登入才能加入購物車！')
        return false
    }
    

    try {
      isLoading.value = true  // 開始載入

      // 建立購物車商品物件
      const cartItem = {
        id: Date.now(),  // 簡單的唯一 ID，實際應用建議用後端生成
        productId: product.id,  // 原商品 ID
        name: product.name,
        price: product.price,
        image: product.image,
        size: size,
        color: color,
        qty: qty
      }

      // 檢查購物車是否已有相同商品（相同商品ID、尺寸、顏色）
      const existingItem = cartItems.value.find(item => 
        item.productId === product.id && 
        item.size === size && 
        item.color === color
      )

      if (existingItem) {
        // 如果已存在，就增加數量
        existingItem.qty += qty
        ElMessage.success('商品數量已更新！')
      } else {
        // 如果不存在，就新增到購物車
        cartItems.value.push(cartItem)
        ElMessage.success('商品已加入購物車！')
      }

      // 呼叫後端 API 同步購物車資料（這裡先模擬）
      await syncCartToBackend()
      
      return true
    } catch (error) {
      console.error('加入購物車失敗:', error)
      ElMessage.error('加入購物車失敗，請稍後再試')
      return false
    } finally {
      isLoading.value = false  // 結束載入
    }
  }

  /**
   * 更新商品數量
   * @param {number} itemId - 購物車商品 ID
   * @param {number} newQty - 新數量
   */
  const updateQty = async (itemId, newQty) => {
    if (newQty < 1) {
      ElMessage.warning('數量不能小於 1')
      return
    }

    const item = cartItems.value.find(item => item.id === itemId)
    if (item) {
      item.qty = newQty
      await syncCartToBackend()
      ElMessage.success('數量已更新')
    }
  }

  /**
   * 從購物車移除商品
   * @param {number} itemId - 購物車商品 ID
   */
  const removeFromCart = async (itemId) => {
    const index = cartItems.value.findIndex(item => item.id === itemId)
    if (index > -1) {
      cartItems.value.splice(index, 1)
      await syncCartToBackend()
      ElMessage.success('商品已從購物車移除')
    }
  }

  /**
   * 清空購物車
   */
  const clearCart = async () => {
    cartItems.value = []
    await syncCartToBackend()
    ElMessage.success('購物車已清空')
  }

  /**
   * 同步購物車資料到後端（目前先模擬，實際要串接你的 PHP API）
   */
  const syncCartToBackend = async () => {
    try {
      // 這裡之後要串接你的後端 API
      // const response = await fetch('/api/cart/sync', {
      //   method: 'POST',
      //   credentials: 'include',  // 包含 session cookie
      //   headers: {
      //     'Content-Type': 'application/json'
      //   },
      //   body: JSON.stringify({ cartItems: cartItems.value })
      // })
      
      console.log('模擬同步購物車到後端:', cartItems.value)
    } catch (error) {
      console.error('同步購物車失敗:', error)
    }
  }

  /**
   * 從後端載入購物車資料
   */
  const loadCartFromBackend = async () => {
    const userStore = useUserStore()
    if (!userStore.isLoggedIn) return

    try {
      isLoading.value = true
      
      // 這裡之後要串接你的後端 API
      // const response = await fetch('/api/cart', {
      //   credentials: 'include'
      // })
      // const data = await response.json()
      // cartItems.value = data.cartItems || []
      
      console.log('模擬從後端載入購物車')
    } catch (error) {
      console.error('載入購物車失敗:', error)
    } finally {
      isLoading.value = false
    }
  }

  // 回傳所有需要在元件中使用的資料和方法
  return {
    // 響應式資料
    cartItems,
    coupons,
    chosenCoupon,
    isLoading,
    
    // 計算屬性
    totalQty,
    totalPrice,
    discount,
    finaltotal,
    isEmpty,
    
    // 方法
    addToCart,
    updateQty,
    removeFromCart,
    clearCart,
    loadCartFromBackend,
    applyCoupon
  }
})