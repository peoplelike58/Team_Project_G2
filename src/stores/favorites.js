import { defineStore } from "pinia";
import { reactive,computed } from 'vue'

export const useFavoriteStore = defineStore('favorites',()=>{
    // state 用 reactive 包裝整個狀態物件
    const favorites = reactive({
        products: [],      // 收藏的商品完整資料陣列
        productIds: [],      // 收藏的商品ID陣列（用於快速檢查）
        loading: false,      // 載入收藏資料的狀態
        updating: false      // 新增/移除收藏的載入狀態
    })
    //  Getters 使用 computed 定義計算屬性
    const favoriteCount = computed(() => {
    // 計算收藏商品數量
        return favorites.products.length
    })

    const isFavorite = computed(() => {
        // 回傳一個函數，用來檢查某商品是否已收藏
        return (productId) => {
        return favorites.productIds.includes(Number(productId))
        }
    })

    const isEmpty = computed(() => {
        // 檢查收藏清單是否為空
        return favorites.products.length === 0
    })
    
    // 取得指定商品的收藏資訊
    const getFavoriteProduct = computed(() => {
        return (productId) => {
        return favorites.products.find(product => Number(product.id) === Number(productId))
        }
    })

    //  Actions  定義操作方法

    /**
     * 從後端載入用戶的收藏商品列表
     * @param {number} memberId - 用戶ID
     */
    const loadFavorites = async (memberId) => {
        if (!memberId) {
        console.warn('loadFavorites: 缺少用戶ID')
        return false
        }
        favorites.loading = true    

        try {
            const response = await fetch(import.meta.env.VITE_AJAX_URL + '/MemberCenter_getFavorites.php', {
                method: 'POST',
                headers: { 
                'Content-Type': 'application/json',
                'Accept': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify({
                member_id: memberId
                })
            })

            if (!response.ok) {
                throw new Error(`HTTP錯誤: ${response.status}`)
            }

            const result = await response.json()
      
            if (result.success) {
                //  直接修改陣列，reactive 會自動追蹤變化
                favorites.products = result.favorites || []
                favorites.productIds = favorites.products.map(product => Number(product.id))
                
                console.log(`載入收藏成功: ${favorites.products.length} 個商品`)
                return true
            } else {
                console.error('載入收藏失敗:', result.message)
                return false
            }
        } catch (error) {
            console.error(' 載入收藏時發生錯誤:', error)
            return false
        } finally {
            favorites.loading = false
        }
    }
    
      /**
   * 新增商品到收藏
   * @param {number} memberId - 用戶ID
   * @param {number} productId - 商品ID
   */
  const addFavorite = async (memberId, productId) => {
    if (!memberId || !productId) {
      console.error('❌ addFavorite: 參數不完整')
      return false
    }

    // 檢查是否已經收藏
    if (isFavorite.value(productId)) {
      console.log('ℹ️ 商品已在收藏清單中')
      return true
    }

    favorites.updating = true

    try {
      const response = await fetch(import.meta.env.VITE_AJAX_URL + '/MemberCenter_addFavorite.php', {
        method: 'POST',
        headers: { 
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        credentials: 'include',
        body: JSON.stringify({
          member_id: memberId,
          product_id: productId
        })
      })

      if (!response.ok) {
        throw new Error(`HTTP錯誤: ${response.status}`)
      }

      const result = await response.json()
      
      if (result.success) {
        // 🟢 使用陣列方法直接操作，更直觀
        const numericProductId = Number(productId)
        favorites.productIds.push(numericProductId)
        
        // 如果後端有回傳完整商品資訊，加入到商品陣列
        if (result.product) {
          favorites.products.push(result.product)
        }
        
        console.log('✅ 新增收藏成功')
        return true
      } else {
        console.error('❌ 新增收藏失敗:', result.message)
        return false
      }
    } catch (error) {
      console.error('❌ 新增收藏時發生錯誤:', error)
      return false
    } finally {
      favorites.updating = false
    }
  }

    /**
   * 從收藏中移除商品
   * @param {number} memberId - 用戶ID  
   * @param {number} productId - 商品ID
   */
  const removeFavorite = async (memberId, productId) => {
    if (!memberId || !productId) {
      console.error('❌ removeFavorite: 參數不完整')
      return false
    }

    favorites.updating = true

    try {
      const response = await fetch(import.meta.env.VITE_AJAX_URL + '/MemberCenter_removeFavorite.php', {
        method: 'POST',
        headers: { 
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        credentials: 'include',
        body: JSON.stringify({
          member_id: memberId,
          product_id: productId
        })
      })

      if (!response.ok) {
        throw new Error(`HTTP錯誤: ${response.status}`)
      }

      const result = await response.json()
      
      if (result.success) {
        // 🟢 直接使用 filter 方法，更簡潔
        const numericProductId = Number(productId)
        favorites.productIds = favorites.productIds.filter(id => id !== numericProductId)
        favorites.products = favorites.products.filter(product => Number(product.id) !== numericProductId)
        
        console.log('✅ 移除收藏成功')
        return true
      } else {
        console.error('❌ 移除收藏失敗:', result.message)
        return false
      }
    } catch (error) {
      console.error('❌ 移除收藏時發生錯誤:', error)
      return false
    } finally {
      favorites.updating = false
    }
  }

    /**
   * 切換收藏狀態（智慧切換：已收藏則移除，未收藏則新增）
   * @param {number} memberId - 用戶ID
   * @param {number} productId - 商品ID
   */
  const toggleFavorite = async (memberId, productId) => {
    if (isFavorite.value(productId)) {
      return await removeFavorite(memberId, productId)
    } else {
      return await addFavorite(memberId, productId)
    }
  }
          

    return{
        // State
        favorites,

        // Getters (computed)
        favoriteCount, // 收藏商品數量
        isFavorite,    // 檢查是否已收藏的函數
        isEmpty,       // 檢查收藏是否為空
        getFavoriteProduct,  // 取得指定商品的收藏資訊

        // Actions (methods)
        loadFavorites,    // 載入收藏列表
        addFavorite,      // 新增收藏
        removeFavorite,   // 移除收藏
        toggleFavorite    // 切換收藏狀態
    }
})