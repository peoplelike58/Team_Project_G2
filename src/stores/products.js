export const useProductStore = defineStore('products', () => {
  const products = ref([])
  const loading = ref(false)
  const favorites = ref([])

  const loadProducts = async () => {
    loading.value = true
    try {
      const res = await fetch('/json/products/products.json')
      if (!res.ok) throw new Error('載入失敗')
      products.value = await res.json()
    } catch (err) {
      console.error('讀取商品資料錯誤:', err)
    } finally {
      loading.value = false
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

  const getProductById = (id) => {
    return products.value.find(p => p.id === id)
  }

  return { 
    products, 
    loading, 
    favorites, 
    loadProducts, 
    toggleFavorite, 
    getProductById 
  }
})