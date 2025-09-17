// stores/ordercheckout.js-結賬相關：表單管理、訂單提交
import { defineStore } from 'pinia'
import { useUserStore } from './user'
import { useCartStore } from './cart'
import { ref,computed,onMounted   } from 'vue'
import { ElMessage } from 'element-plus'

export const useCheckoutStore = defineStore('checkout', () => {
    const userStore = useUserStore()
    const CartStore = useCartStore()
    
    /* ========== 響應式資料 ========== */
    // 收件人資訊
    const recipientInfo = ref({
        name:'',
        phone:'',
        address:''
    })
    
    const paymentMethod = ref('card')   //付款方式：信用卡、貨到付款
    // 信用卡資訊
    const cardInfo = ref({
        cardNo: '',
        exp: '',
        cvc: ''
    })

    const deliveryMethod = ref('HOME')    //配送方式：超商取貨、宅配


    const isSubmitting = ref(false)       // 提交狀態
    const orderResult = ref(null)         //訂單結果

    const counts = ref({
        productprice: 0,
        shipfee: 0,
        discount: 0,
        finalprice: 0
    })

      /* ========== 計算屬性 ========== */
    // 檢查是否可以提交訂單
    const canSubmitOrder = computed(() => {
        const basicInfoOk = recipientInfo.value.name && 
                        recipientInfo.value.phone && 
                        recipientInfo.value.address
        
        // 如果選擇信用卡付款，需要檢查信用卡資訊
        const paymentInfoOk = paymentMethod.value === 'card' 
        ? cardInfo.value.cardNo && cardInfo.value.exp && cardInfo.value.cvc
        : true  // 貨到付款不需要額外資訊
        
        return basicInfoOk && 
            paymentInfoOk && 
            CartStore.checkedItems.length > 0 &&  // 必須有勾選商品
            !isSubmitting.value  // 不在提交中
    })

    // 從購物車取得計算好的金額資訊
    const orderSummary = computed(() => ({
        totalQty: CartStore.checkedTotalQty,           // 商品數量
        subtotal: CartStore.checkedTotalPrice,         // 商品小計
        discount: CartStore.discount,                  // 優惠金額
        shippingFee: CartStore.shippingFee,           // 運費
        finalTotal: CartStore.FinalTotalWithShip      // 最終總金額
    }))

    // 取得勾選的商品清單（用於訂單明細）
    const orderItems = computed(() => 
        CartStore.checkedItems.map(item => ({
        productId: item.productId,
        productName: item.name,
        price: item.price,
        size: item.size,
        color: item.color,
        quantity: item.qty,
        subtotal: item.price * item.qty,
        image: item.image
        }))
    )

    /* ========== 方法 ========== */
    /**
     * 設定收件人資訊
     * @param {Object} info - 收件人資訊 {name, phone, address}
     */
    const setRecipientInfo = (info) => {
        recipientInfo.value = { ...info }
    }

    /**
     * 設定付款方式
     * @param {string} method - 付款方式 'card' 或 'cod'
     */
    const setPaymentMethod = (method) => {
        paymentMethod.value = method
        // 如果切換到貨到付款，清空信用卡資訊
        if (method === 'cod') {
        cardInfo.value = {
            cardNo: '',
            exp: '',
            cvc: ''
        }
        }
    }

    /**
     * 設定信用卡資訊
     * @param {Object} info - 信用卡資訊 {cardNo, exp, cvc}
     */
    const setCardInfo = (info) => {
        cardInfo.value = { ...info }
    }

    /**
     * 生成訂單編號
     * @returns {string} 訂單編號
     */
    const generateOrderId = () => {
        const now = new Date()
        const timestamp = now.getTime().toString().slice(-8)  // 取時間戳後8位
        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0')  // 3位隨機數
        return `${timestamp}${random}`
    }

    /**
     * 提交訂單到後端
     * @returns {Object} 訂單結果
     */
    const submitOrder = async () => {
        if (!canSubmitOrder.value) {
        ElMessage.error('請檢查訂單資訊是否完整')
        return { success: false, message: '訂單資訊不完整' }
        }

        try {
        isSubmitting.value = true
        
        // 準備訂單資料
        const orderData = {
            // 基本資訊
            orderCode: generateOrderId(),
            memberId: userStore.id,  // 會員ID
            
            // 收件人資訊
            recipientName: recipientInfo.value.name,
            recipientPhone: recipientInfo.value.phone,
            recipientAddress: recipientInfo.value.address,
            
            // 付款資訊
            paymentMethod: paymentMethod.value,
            
            // 配送資訊
            shippingMethod: CartStore.selectedShipMethod,
            shippingFee: CartStore.shippingFee,
            
            // 優惠資訊
            couponId: CartStore.chosenCoupon?.id || null,
            discountAmount: CartStore.discount,
            
            // 金額資訊
            subtotal: CartStore.checkedTotalPrice,
            finalTotal: CartStore.FinalTotalWithShip,
            
            // 商品明細
            items: orderItems.value,
            
            // 購物車ID列表（用於刪除購物車商品）
            cartIds: CartStore.checkedIds
        }

        console.log('準備提交訂單資料:', orderData)

        // 呼叫後端API建立訂單
        const response = await fetch(import.meta.env.VITE_AJAX_URL + '/ShopPage_createOrder.php', {
            method: 'POST',
            credentials: 'include',  // 包含 session cookie
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(orderData)
        })

        const result = await response.json()

        if (result.success) {
            // 訂單建立成功
            orderResult.value = {
            success: true,
            orderId: result.orderId,
            finalOrderId:result.finalOrderId,
            message: '訂單建立成功！',
            orderData: orderData
            }

            // 重新載入購物車（後端會自動刪除已結帳的商品）
            await CartStore.loadCartFromBackend()
            
            ElMessage.success('訂單建立成功！')
            return orderResult.value
            
        } else {
            // 訂單建立失敗
            console.error('建立訂單失敗:', result.message)
            ElMessage.error(result.message || '訂單建立失敗')
            return { success: false, message: result.message }
        }

        } catch (error) {
        console.error('提交訂單時發生錯誤:', error)
        ElMessage.error('網路錯誤，請稍後再試')
        return { success: false, message: '網路錯誤' }
        
        } finally {
        isSubmitting.value = false
        }
    }

    /**
     * 重置結賬資料
     */
    const resetCheckout = () => {
        recipientInfo.value = {
        name: '',
        phone: '',
        address: ''
        }
        paymentMethod.value = 'card'
        cardInfo.value = {
        cardNo: '',
        exp: '',
        cvc: ''
        }
        orderResult.value = null
        isSubmitting.value = false
    }

    

    return{
        // 響應式資料
    recipientInfo,
    paymentMethod,
    cardInfo,
    isSubmitting,
    orderResult,
    
    // 計算屬性
    canSubmitOrder,
    orderSummary,
    orderItems,
    
    // 方法
    setRecipientInfo,
    setPaymentMethod,
    setCardInfo,
    submitOrder,
    resetCheckout,
    // prefillMemberInfo,
    generateOrderId
    }
})
