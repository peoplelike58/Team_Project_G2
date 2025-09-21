// stores/cart.js-購物相關：增刪改查商品
import { ref, computed, reactive,nextTick  } from 'vue'
import { defineStore } from 'pinia'
import { useUserStore } from './user'
import { ElMessage } from 'element-plus'
import { debounce } from 'lodash'   //lodash.debounce套件，延遲點擊同步

export const useCartStore = defineStore('cart', () => {
  const userStore = useUserStore()
  // ========== 響應式資料 ==========
  // 購物車商品列表，存放陣列
  const cartItems = ref([]) 
  const checkedMap =  reactive({})  //勾選狀態
  const coupons=ref([
    // { id:'A', title:'不使用優惠券', amount:0 ,},
    // { id:'B', title:'新朋友 $200 折扣', amount:200 ,}
  ])
  const chosenCoupon=ref(null)  // 目前選擇的優惠券
  const isLoading = ref(false)  // 載入狀態，避免重複點擊

  // 新增：配送方式相關資料
  const shipOptions = [
    // { id: 'none', label: '無', fee: 0, needStore: false },
    { id: '711', label: '7-11 取貨', fee: 60, needStore: true },
    { id: 'family', label: '全家 取貨', fee: 60, needStore: true },
    { id: 'home', label: '宅配到府', fee: 80, needStore: false },
  ]
  // 目前選擇的配送方式ID，預設為無
  const selectedShipMethod = ref('none')  


  /*========== 計算屬性 ==========*/

  // 找出所有被勾選的商品 ID
  const checkedIds =computed(() => 
    Object.entries(checkedMap)
    .filter(([, isChecked]) => isChecked)  // 只取勾選的
    .map(([id]) => Number(id))  // 轉換成數字 ID
  )

  const checkedItems = computed(()=>cartItems.value.filter(item => checkedMap[item.id]))

  // 計算勾選商品的總數量
  const checkedTotalQty = computed(() => 
    checkedItems.value.reduce((total, item) => total + item.qty, 0)
  )

  // 計算勾選商品的總金額
  const checkedTotalPrice = computed(() => 
    checkedItems.value.reduce((total, item) => total + (item.price * item.qty), 0)
  )
    //購物車優惠券 
  const discount = computed(()=> chosenCoupon.value?.discount ?? 0)

  //  購物車-計算最終金額（商品金額 - 優惠券 ）
  const checkedFinalTotal = computed(() => 
    Math.max(checkedTotalPrice.value - discount.value, 0)
  )

  //  結賬-計算最終金額（商品金額 - 優惠券 + 運費）
  const FinalTotalWithShip = computed(() => 
    Math.max(checkedTotalPrice.value - discount.value + shippingFee.value, 0)
  )

  
   // 判斷是否全選
  const isAllChecked = computed(() => {
    if (cartItems.value.length === 0) return false
    return cartItems.value.every(item => checkedMap[item.id])
  })

  // 購物車是否為空
  const isEmpty = computed(() => cartItems.value.length === 0)

  // 計算目前選擇配送方式的運費
  const shippingFee = computed(() => {
    // 如果沒有商品被勾選，運費為0
    if (checkedTotalQty.value === 0) return 0

    // 根據選擇的配送方式ID找到對應的運費
    const selectedOption = shipOptions.find(option => option.id === selectedShipMethod.value)
    return selectedOption ? selectedOption.fee : 0
  })

  // 取得目前選擇的配送方式資訊
  const selectedShipOption = computed(() => {
    return shipOptions.find(option => option.id === selectedShipMethod.value)
  })
  // 整台購物車的總計方式 
  // 勾選的的購物車總數量 - 把所有商品的 qty 加起來,.reduce(計算函數，初始值為0)-陣列語法
  // const totalQty = computed(() => {
  //   return cartItems.value.reduce((total, item) => total + item.qty, 0)
  // })

  // 勾選的購物車總金額 - 把所有商品的 (價格 × 數量) 加起來
  // const totalPrice = computed(() => {
  //   return cartItems.value.reduce((total, item) => total + (item.price * item.qty), 0)
  // })

  //勾選的實際金額=總金額-優惠金額
  // const finaltotal = computed(()=> Math.max(totalPrice.value - discount.value, 0))

  
  /*========== 勾選狀態管理方法 ==========*/
  
  // 初始化勾選狀態
  const initializeCheckedMap = () => {
    // 清空現有的勾選狀態
    Object.keys(checkedMap).forEach(key => {
      delete checkedMap[key]
    })
    
    // 重新設置所有商品為勾選狀態
    cartItems.value.forEach(item => {
      checkedMap[item.id] = true
    })
  }

    // 切換全選狀態
  const toggleAllChecked = (checked) => {
    cartItems.value.forEach(item => {
      checkedMap[item.id] = checked
    })
  }

    // 更新單個商品的勾選狀態
  const updateItemChecked = (itemId, checked) => {
    checkedMap[itemId] = checked
  }

    // 移除商品的勾選狀態
  const removeItemFromChecked = (itemId) => {
    delete checkedMap[itemId]
  }

  /*========== 配送方式管理方法 ==========*/
  
  // 設定配送方式
  const setShippingMethod = (methodId) => {
    // 檢查傳入的methodId是否存在於shipOptions中
    const isValidMethod = shipOptions.some(option => option.id === methodId)
    if (isValidMethod) {
      selectedShipMethod.value = methodId
      console.log(`配送方式已更改為: ${methodId}`)
    } else {
      console.error('無效的配送方式ID:', methodId)
    }
  }

  // 重置配送方式為預設值
  const resetShippingMethod = () => {
    selectedShipMethod.value = '711'  // 重置為預設的7-11取貨
  }


  /* ========== 資料格式轉換函數 ==========*/
  /**
   * 將後端資料格式轉換為前端需要的格式
   * @param {Array} backendItems - 後端回傳的購物車資料
   * @returns {Array} 轉換後的前端格式資料
   */
  const transformBackendDataToFrontend = (backendItems) => {
    return backendItems.map(item => ({
      // 將後端的大寫欄位名稱轉換為前端的小寫格式
      id: item.CART_ID,           // 後端: CART_ID → 前端: id
      productId: item.PRODUCT_ID, // 後端: PRODUCT_ID → 前端: productId  
      name: item.PRODUCT_NAME,    // 後端: PRODUCT_NAME → 前端: name
      price: parseInt(item.PRICE), // 後端: PRICE → 前端: price (轉為數字)
      image: item.IMAGE,          // 後端: IMAGE → 前端: image
      size: item.SIZE,            // 後端: SIZE → 前端: size
      color: item.COLOR,          // 後端: COLOR → 前端: color  
      qty: parseInt(item.QUANTITY) // 後端: QUANTITY → 前端: qty (轉為數字)
    }))
  }

  /* ========== 購物車操作方法/行為 ========== */

   // 選擇優惠券
  const applyCoupon = (coupon) => { 
    chosenCoupon.value = coupon 
  }


  const errorMessage = ref("")


  // 從API載入優惠券資料的函數
  const getCoupons = async () => {
  try{
    isLoading.value = true // 開始載入
    errorMessage.value = '' // 清空錯誤訊息
    const response = await fetch(import.meta.env.VITE_AJAX_URL + '/LoginPage_getCoupon.php', {
          method: 'POST',
          headers: { 
          'Content-Type': 'application/json',
          'Accept': 'application/json'
          },
          credentials: 'include',
          body: JSON.stringify({})
      })

      const result = await response.json()

      // 檢查API回傳狀態
      if (result.success) {
        coupons.value =result.coupons 
        console.log('設定後的coupons:', coupons.value)
      } else {
        errorMessage.value = response.data.message || '載入優惠券資料失敗'
      }
      
    } catch (error) {
      // 處理網路錯誤或其他異常
      console.error('載入優惠券資料時發生錯誤:', error)
      errorMessage.value = '網路連線錯誤，請稍後再試'
    } finally {
      isLoading.value = false // 結束載入
    }
  }

  /**
   * 加入商品到購物車
   * @param {Object} product - 商品物件，包含 id, name, price, image 等
   * @param {string} size - 尺寸 (如: S, M, L)
   * @param {string} color - 顏色 (如: 紅, 藍)
   * @param {number} qty - 數量，預設為 1
   */
  const addToCart = async (product, size = null, color = null, qty = 1) => {

    // 檢查使用者是否登入
    if (!userStore.isLoggedIn) {
      ElMessage.warning('請先登入才能加入購物車！')
      return
    }

    try {
      isLoading.value = true  // 開始載入

      // 建立購物車商品物件
      const cartItem = {
        id: Date.now(),    //暫時替代，實際使用後端回傳的cart——id確認項目
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
        // 新增商品時自動設為勾選
        checkedMap[cartItem.id] = true
        ElMessage.success('商品已加入購物車！')
      }

      // 呼叫後端 API 同步購物車資料
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
   * 更新商品數量更新商品數量（使用防抖）
   * @param {number} itemId - 購物車商品 ID
   * @param {number} newQty - 新數量
   */

  // 包一層 debounced 函式
  const debouncedUpdateQty = debounce(async (itemId, newQty) => {
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
}, 60)   // ← 60ms

// 封裝成 store 暴露的方法
const updateQty = (itemId, newQty) => {
  debouncedUpdateQty(itemId, newQty)
}


/* 刪除單一商品 */
  const removeFromCart = async (itemId) => {
    try{
    //加入載入狀態，防止重複操作
    isLoading.value = true

    //先從前端陣列中找到要刪除的商品
    const itemIndex = cartItems.value.findIndex(item => item.id === itemId)
    if (itemIndex === -1) {
      // 如果找不到商品，顯示錯誤訊息並結束
      ElMessage.error('找不到該商品')
      return
    }
    await nextTick()
    //先呼叫後端刪除 API
    const deleteSuccess = await  deleteCartItem(itemId)
    if (deleteSuccess) {
      // 從前端陣列中移除商品（使用 splice 方法）
      cartItems.value.splice(itemIndex, 1)
      //  同時移除勾選狀態
      removeItemFromChecked(itemId)
      ElMessage.success('商品已從購物車移除')
    } else {
      // 如果後端刪除失敗，顯示錯誤訊息
      ElMessage.error('移除商品失敗，請稍後再試')
    }
    }catch (error) {
      // 加入錯誤處理
      console.error('移除商品時發生錯誤:', error)
      ElMessage.error('操作失敗，請稍後再試')
    } finally {
      // 無論成功或失敗都要關閉載入狀態
      isLoading.value = false
    }
  }

  // 刪除已勾選的商品
  // const removeCheckedItems = async () => {
  //   if (checkedIds.value.length === 0) {
  //     ElMessage.info('請先勾選要刪除的商品')
  //     return
  //   }

  //   try {
  //     isLoading.value = true
      
  //     // 逐一刪除勾選的商品
  //     for (const id of checkedIds.value) {
  //       await removeFromCart(id)
  //     }
      
  //     ElMessage.success('已刪除選中的商品')
  //   } catch (error) {
  //     console.error('批量刪除失敗:', error)
  //     ElMessage.error('刪除失敗，請稍後再試')
  //   } finally {
  //     isLoading.value = false
  //   }
  // }



//  修改批量刪除函數 - 先刪後端，成功後再更新前端
const removeCheckedItems = async () => {
  if (checkedIds.value.length === 0) {
    ElMessage.info('請先勾選要刪除的商品')
    return
  }

  try {
    isLoading.value = true
    
    // 複製要刪除的 ID（避免響應式數據變動）
    const idsToDelete = [...checkedIds.value]
    
    // 備份商品資料（以防需要恢復）
    const itemsBackup = [...cartItems.value]
    const checkedBackup = { ...checkedMap }
    
    // 先呼叫後端刪除，不要先更新前端
    const deleteResults = []
    
    for (let i = 0; i < idsToDelete.length; i++) {
      const id = idsToDelete[i]
      
      // 加入延遲避免伺服器壓力
      if (i > 0) {
        await new Promise(resolve => setTimeout(resolve, 100))
      }
      
      try {
        const success = await deleteCartItem(id)
        deleteResults.push({ id, success })
      } catch (error) {
        console.error(`刪除 ${id} 失敗:`, error)
        deleteResults.push({ id, success: false })
      }
    }
    
    //  根據後端結果更新前端
    const successIds = deleteResults
      .filter(result => result.success)
      .map(result => result.id)
    
    const failedIds = deleteResults
      .filter(result => !result.success)
      .map(result => result.id)
    
    //  只移除成功刪除的商品
    if (successIds.length > 0) {
      // 一次性更新前端
      cartItems.value = cartItems.value.filter(
        item => !successIds.includes(item.id)
      )
      
      // 清除勾選狀態
      successIds.forEach(id => {
        delete checkedMap[id]
      })
      loadCartFromBackend()
      // 使用 nextTick 確保 DOM 更新
      await nextTick()
    }
    
    // 顯示結果
    if (failedIds.length === 0) {
      ElMessage.success(`已成功刪除 ${successIds.length} 件商品`)
    } else if (successIds.length === 0) {
      //   如果全部失敗，恢復原始狀態
      cartItems.value = itemsBackup
      Object.assign(checkedMap, checkedBackup)
      ElMessage.error('刪除失敗，請稍後再試')
    } else {
      ElMessage.warning(`已刪除 ${successIds.length} 件商品，${failedIds.length} 件刪除失敗`)
    }
    
  } catch (error) {
    console.error('批量刪除發生錯誤:', error)
    ElMessage.error('刪除失敗，請稍後再試')
  } finally {
    isLoading.value = false
  }
}

  /* 清空購物車*/
  const clearCart = async () => {
    cartItems.value = []
    Object.keys(checkedMap).forEach(key => delete checkedMap[key])
    await syncCartToBackend()
    ElMessage.success('購物車已清空')
  }

  /* ========== 後端相關方法 ========== */
  
   /* 同步購物車資料到後端*/
  const syncCartToBackend = async () => {
    try {
      const response = await fetch(import.meta.env.VITE_AJAX_URL + '/ShopPage_addToCart.php', {
        method: 'POST',
        credentials: 'include',  // 包含 session cookie
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ cartItems: cartItems.value })
      })
      
      console.log('模擬同步購物車到後端:', cartItems.value)
    } catch (error) {
      console.error('同步購物車失敗:', error)
    }
  }

  /**
   * 從後端資料庫刪除單一購物車項目
   * @param {number} itemId - 要刪除的購物車商品 ID
   * @returns {boolean} 是否刪除成功
   */
 const deleteCartItem = async (itemId) => {
  console.log('准备删除的 itemId:', itemId);  // 檢查参数
  try {
    const response = await fetch(import.meta.env.VITE_AJAX_URL + '/ShopPage_removeFromCart.php', {
      method: 'POST',
      credentials: 'include',  // 包含 session cookie
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({cartId: itemId})
    })
    if (!response.ok) {
      console.error(`HTTP 錯誤: ${response.status} ${response.statusText}`)
      return false
    }    
  const result = await response.json();  
  if (result.success) {
        console.log('後端刪除成功:', result.message)
        // await loadCartFromBackend()                //避免重複載入和畫面閃爍
        return true  // 回傳 true 表示刪除成功
      } else {
        console.error('後端刪除失敗:', result.message)
        return false  // 回傳 false 表示刪除失敗
      }

    } catch (error) {
      // 捕捉網路錯誤或其他異常
      console.error('刪除商品時發生網路錯誤:', error)
      return false  // 回傳 false 表示刪除失敗
    }
  }

/*🔍 也在 deleteCartItem 加入日誌*/
// const deleteCartItem = async (itemId) => {
//   console.log(`[deleteCartItem] 開始刪除 ID: ${itemId}`)
  
//   try {
//     const url = import.meta.env.VITE_AJAX_URL + '/ShopPage_removeFromCart.php'
//     console.log(`[deleteCartItem] 請求 URL: ${url}`)
    
//     const requestBody = {cartId: itemId}
//     console.log(`[deleteCartItem] 請求內容:`, requestBody)
    
//     const response = await fetch(url, {
//       method: 'POST',
//       credentials: 'include',
//       headers: {'Content-Type': 'application/json'},
//       body: JSON.stringify(requestBody)
//     })

//     console.log(`[deleteCartItem] HTTP 狀態: ${response.status} ${response.statusText}`)
    
//     if (!response.ok) {
//       console.error(`[deleteCartItem] HTTP 錯誤: ${response.status}`)
//       return false
//     }

//     const result = await response.json()
//     console.log(`[deleteCartItem] 後端回應:`, result)
    
//     if (result.success) {
//       console.log(`[deleteCartItem] 成功刪除 ID: ${itemId}`)
//       return true
//     } else {
//       console.error(`[deleteCartItem] 後端刪除失敗:`, result.message)
//       return false
//     }

//   } catch (error) {
//     console.error(`[deleteCartItem] 網路錯誤:`, error)
//     return false
//   }
// }


  /*從後端載入購物車資料*/
  const loadCartFromBackend = async () => {
    
    if (!userStore.isLoggedIn) return

    try {
      isLoading.value = true
      const response = await fetch(import.meta.env.VITE_AJAX_URL + '/ShopPage_loadCart.php', {
        method: 'POST',
        credentials: 'include',  // 包含 session cookie
        headers: {'Content-Type': 'application/json'},
      })
      const data = await response.json()
      const cartItemsFromBack = transformBackendDataToFrontend(data.cartItems)
      cartItems.value = cartItemsFromBack || []

       // 載入完成後重新初始化勾選狀態
      initializeCheckedMap()
      
      console.log('從後端載入購物車')
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
    checkedMap,
    coupons,
    chosenCoupon,
    isLoading,
    shipOptions,        
    selectedShipMethod, 
    
    // 計算屬性
    checkedIds,
    checkedItems,
    checkedTotalQty,
    checkedTotalPrice,
    checkedFinalTotal,
    FinalTotalWithShip,
    shippingFee,        
    selectedShipOption, 
    // totalQty,
    // totalPrice,
    discount,
    // finaltotal,
    isEmpty,
    isAllChecked,
    
    // 勾選狀態管理
    initializeCheckedMap,
    toggleAllChecked,
    updateItemChecked,
    removeItemFromChecked,

    // 配送方式管理
    setShippingMethod,
    resetShippingMethod,


    // 方法
    getCoupons,
    addToCart,
    updateQty,
    removeFromCart,
    removeCheckedItems,
    clearCart,
    loadCartFromBackend,
    applyCoupon,
    deleteCartItem,
  }
})

