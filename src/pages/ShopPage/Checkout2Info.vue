<script setup>
import NavMenu from '@/components/An/navMenu.vue';
import Checkout_stepup from '@/components/Irene/ShopPage/Checkout_stepup.vue';
import brandFooter from '@/components/An/footer.vue'
import { reactive, ref, computed,onMounted  } from 'vue'
import { useRouter } from 'vue-router';
import { useCartStore } from '@/stores/cart';
import { useCheckoutStore } from '@/stores/ordercheckout';
import { ElMessage } from 'element-plus';

//pinia
const CartStore = useCartStore()
const CheckoutStore = useCheckoutStore()


// 表單
const formRef = ref()
const form = reactive({ 
    name: CheckoutStore.recipientInfo.name ||'', 
    phone: CheckoutStore.recipientInfo.phone ||'', 
    addr: CheckoutStore.recipientInfo.address ||'' })

// 付款
const pay = reactive({ 
    method: CheckoutStore.paymentMethod ||'card', 
    cardNo: CheckoutStore.cardInfo.cardNo ||'', 
    exp: CheckoutStore.cardInfo.exp ||'', 
    cvc: CheckoutStore.cardInfo.cvc ||'' })

// 信用卡到期日驗證函數
const validateExp = (rule, value, callback) => {
  if (pay.method === 'card' && value) {
    // 檢查是否過期
    const [month, year] = value.split('/')
    if (month && year) {
      const expDate = new Date(2000 + parseInt(year), parseInt(month) - 1)
      const now = new Date()
      const currentMonth = new Date(now.getFullYear(), now.getMonth())
      
      if (expDate < currentMonth) {
        callback(new Error('信用卡已過期'))
      } else {
        callback()
      }
    } else {
      callback()
    }
  } else {
    callback()
  }
}

// 表單驗證規則
const rules = {
  name: [
    { required: true, message: '必填', trigger: 'blur' },
    { min: 2, message: '姓名至少2個字', trigger: 'blur' }
    ],
  phone: [
    { required: true, message: '必填', trigger: 'blur' },
    { pattern: /^09\d{8}$/, message: '請輸入正確的手機號碼格式(09xxxxxxxx)', trigger: 'blur' }
   ],
  addr: [
    { required: true, message: '必填', trigger: 'blur' },
     { min: 5, message: '地址至少5個字', trigger: 'blur'}
  ],
}

// 付款方式驗證規則
const payRules = computed(() => {
  if (pay.method === 'card') {
    return {
      cardNo: [
        { required: true, message: '必填', trigger: 'blur' },
        { 
          validator: (rule, value, callback) => {
            const cleanCard = value.replace(/\s/g, '')
            if (!/^\d{16}$/.test(cleanCard)) {
              callback(new Error('請輸入16位信用卡號碼'))
            } else {
              callback()
            }
          }, 
          trigger: 'blur' 
        }
      ],
      exp: [
        { required: true, message: '必填', trigger: 'blur' },
        { pattern: /^(0[1-9]|1[0-2])\/\d{2}$/, message: '請輸入正確格式 MM/YY', trigger: 'blur' },
        { validator: validateExp, trigger: 'blur' }
      ],
      cvc: [
        { required: true, message: '必填', trigger: 'blur' },
        { pattern: /^\d{3}$/, message: '請輸入3位安全碼', trigger: 'blur' }
      ]
    }
  }
  return {}
})

// 信用卡號格式化（每4位加空格）
const formatCardNo = (value) => {
  const cleaned = value.replace(/\s/g, '').replace(/\D/g, '')
  const limited = cleaned.slice(0, 16)
  return limited.replace(/(\d{4})(?=\d)/g, '$1 ')
}

// 到期日格式化（自動加斜線）
const formatExp = (value) => {
  const cleaned = value.replace(/\D/g, '').slice(0, 4)
  if (cleaned.length >= 2) {
    return cleaned.slice(0, 2) + '/' + cleaned.slice(2)
  }
  return cleaned
}

// 輸入處理函數
const handleCardNoInput = () => {
  pay.cardNo = formatCardNo(pay.cardNo)
  syncFormToStore()
}

const handleExpInput = () => {
  pay.exp = formatExp(pay.exp)
  syncFormToStore()
}

// 配送
// const shipOpts = [
//   { id: '711', label: '7-11 取貨', fee: 60, needStore: true },
//   { id: 'family', label: '全家 取貨', fee: 60, needStore: true },
//   { id: 'home', label: '宅配到府', fee: 60, needStore: false },
// ]
// const ship = ref('711')
// function chooseStore(id){ alert(id==='711'?'開啟 7-11 門市（示意）':'開啟全家門市（示意）') }

// 配送方式變更時的處理函數
const handleShippingChange = (methodId) => {
  CartStore.setShippingMethod(methodId)
  console.log('配送方式已變更為:', methodId)
}

// 監聽表單變化，同步到 checkout store
const syncFormToStore = () => {
  CheckoutStore.setRecipientInfo({
    name: form.name,
    phone: form.phone,
    address: form.addr
  })
  
  CheckoutStore.setPaymentMethod(pay.method)
  
  if (pay.method === 'card') {
    CheckoutStore.setCardInfo({
      cardNo: pay.cardNo,
      exp: pay.exp,
      cvc: pay.cvc
    })
  }
}

// 提交條件檢查 - 改用 checkout store 的計算屬性
const canSubmit = computed(() => {
    return CheckoutStore.canSubmitOrder
//   const basicOk = form.name && form.phone && form.addr
//   return pay.method === 'card'
//     ? basicOk && pay.cardNo && pay.exp && pay.cvc
//     : basicOk
})

const router = useRouter();
function goBack(){ 
    // 先同步資料到 store
    syncFormToStore()
    router.push('/Shop/cart') 
}
// function submitNext(){ formRef.value?.validate?.((ok)=> { if (ok) router.push('/Shop/success') }) }

// 提交訂單並進入下一步
const submitNext = async () => {
  // 驗證表單
//   const valid = await formRef.value?.validate?.()
//   if (!valid) {
//     ElMessage.error('請檢查表單資料')
//     return
//   }

  try {
    // 先驗證基本資料表單
    const basicFormValid = await formRef.value?.validate?.()
    if (!basicFormValid) {
      ElMessage.error('請檢查收件人資料')
      return
    }

    // 如果是信用卡付款，額外驗證付款資料
    if (pay.method === 'card') {
      const payFormValid = await payFormRef.value?.validate?.()
      if (!payFormValid) {
        ElMessage.error('請檢查信用卡資料')
        return
      }
    }
    // 同步表單資料到 store
    syncFormToStore()
    
    // 提交訂單
    const result = await CheckoutStore.submitOrder()
    
    if (result.success) {
      // 訂單建立成功，跳轉到成功頁面
      router.push({
        path: '/Shop/success',
        query: { orderId: result.orderId }
      })
    } else {
      ElMessage.error(result.message || '訂單建立失敗')
    }
    
  } catch (error) {
    console.error('提交訂單時發生錯誤:', error)
    ElMessage.error('系統錯誤，請稍後再試')
  }
}

const payFormRef = ref()

// 組件掛載時預填會員資訊
onMounted(() => {
  // 檢查是否有勾選商品，沒有則返回購物車
  if (CartStore.checkedItems.length === 0) {
    ElMessage.warning('請先選擇要結帳的商品')
    router.push('/Shop/cart')
    return
  }
})


</script>

<template>
    <NavMenu/>
    <main>
        <section>
            <Checkout_stepup :current="2"/>
        </section>
        <div class="page">
            <!-- 填寫資料 -->
            <el-card shadow="never" class="panel">
                <template #header>填寫資料</template>
                <el-form :model="form" :rules="rules" ref="formRef" label-width="88px" @input="syncFormToStore" >
                    <el-row :gutter="16">
                    <el-col :span="12"><el-form-item label="收件人" prop="name"><el-input v-model="form.name" placeholder="請輸入收件人姓名" maxlength="10"   @blur="syncFormToStore"/></el-form-item></el-col>
                    <el-col :span="12"><el-form-item label="聯絡電話" prop="phone"><el-input v-model="form.phone" placeholder="請輸入聯絡電話"  @blur="syncFormToStore" /></el-form-item></el-col>
                    <el-col :span="24"><el-form-item label="收貨地址" prop="addr"  ><el-input v-model="form.addr" placeholder="超商取貨請輸入門市地址" @blur="syncFormToStore" /></el-form-item></el-col>
                    </el-row>
                </el-form>
            </el-card>
            <!-- 付款方式 -->
            <el-card shadow="never" class="panel">
                <template #header>付款方式</template>
                <el-form :model="pay" :rules="payRules" ref="payFormRef" label-width="180px">
                    <el-form-item label="選擇付款方式" >
                        <el-select v-model="pay.method" style="width:240px" @change="syncFormToStore">
                            <el-option label="信用卡" value="card" />
                            <el-option label="貨到付款" value="cod" />
                        </el-select>
                    </el-form-item>
                    <template v-if="pay.method==='card'">
                    <el-form-item label="Credit Card Number"><el-input v-model="pay.cardNo" placeholder="xxxx xxxx xxxx xxxx" maxlength="19"  @input="handleCardNoInput"/></el-form-item>
                    <el-row :gutter="16">
                        <el-col :span="12"><el-form-item label="MM/YY"><el-input v-model="pay.exp" placeholder="MM/YY" style="min-width: 160px;" maxlength="5"  @input="handleExpInput"/></el-form-item></el-col>
                        <el-col :span="12"><el-form-item label="CVC"><el-input v-model="pay.cvc" placeholder="3 digits" style="min-width: 160px;" maxlength="3" show-word-limit @blur="syncFormToStore"/></el-form-item></el-col>
                    </el-row>
                    </template>
                </el-form>
            </el-card>
            <!-- 配送方式 -->
            <el-card shadow="never" class="panel">
            <template #header>配送方式</template>
                <el-radio-group v-model="CartStore.selectedShipMethod" class="ship"  @change="handleShippingChange">
                    <div class="ship-row" v-for="opt in CartStore.shipOptions" :key="opt.id">
                    <el-radio :label="opt.id">{{ opt.label }}</el-radio>
                    <!-- <el-button v-if="opt.needStore" size="small" @click="chooseStore(opt.id)">選擇門市</el-button> -->
                    <span class="fee">運費：NT {{ opt.fee }}</span>
                    </div>
                </el-radio-group>
            </el-card>
            <el-divider style="border-top: 1px solid #ccc; padding:0;"/>
            <!-- 總計明細 -->
            <el-card shadow="never" class="panel">
                <div class="sum-line"><span>共{{CartStore.checkedTotalQty}}件商品</span><span>商品金額</span><strong>NT {{CartStore.checkedTotalPrice.toLocaleString()}}</strong></div>
                <div class="sum-line"><span></span><span>活動優惠</span><strong class="discount">- NT {{CartStore.discount.toLocaleString()}}</strong></div>
                <div class="sum-line"><span></span><span>運費</span><strong>NT {{ CartStore.shippingFee.toLocaleString()}}</strong></div>
                <div class="sum-line"><span></span><span>小計</span><strong>NT$ {{CartStore.FinalTotalWithShip.toLocaleString()}}</strong></div>
            </el-card>
            <!-- 按鈕 -->
            <div class="actions">
                <el-button @click="goBack">上一步</el-button>
                <el-button color="#141414"  :disabled="!canSubmit|| CheckoutStore.isSubmitting" @click="submitNext" :loading="CheckoutStore.isSubmitting">下一步</el-button>
            </div>
        </div>
    </main>
    <brandFooter/>
</template>

<style lang="scss" scoped>
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';
// main{max-width:1200px;padding: 2vh,;}

.page { max-width: 980px; margin: 0 auto; background-color: transparent; }//文字區塊
.panel { margin-bottom: 16px; }//每個區塊間隔
.ship { display:block; }//配送點選按鈕
.ship-row { display:grid; grid-template-columns: 1fr auto auto; align-items:center; gap:12px; padding:8px 0; }
.fee{ color:#555; }//運費字體
.sum-line{ display:grid; grid-template-columns:1fr auto auto; align-items:center; padding:6px 0; }//總計排版
.discount{ color:#a33; }//優惠金額
.actions{ display:flex; justify-content:flex-end; gap:12px; margin: 20px 0 60px; }//按鈕
.el-row {
    flex-direction: column;
    gap: 20px;
}
:deep(.el-card__body .el-form){
    display: flex;
    flex-direction: column;
    gap: 20px;
}
.el-card__body:last-child{
    border-top: 1px solid #ccc;
}

@include mq(980px) {
    main{
        padding: 2vh;
    }
}

@include mq(980px) {
    main{
        padding: 2vh;
    }
}

// === 新增的RWD修正 ===
@include mq(768px) {
    .el-row {
        flex-direction: column !important;
        gap: 16px;
    }
    
    // 讓所有欄位在小螢幕時都佔滿寬度
    .el-col {
        max-width: 100% !important;
        flex: 0 0 100% !important;
    }
    
    // 確保表單項目有足夠空間
    :deep(.el-form-item) {
        margin-bottom: 16px;
    }
    
    // 輸入框在小螢幕時佔滿可用寬度
    :deep(.el-input) {
        width: 100%;
    }
    
    // 付款方式選擇框也要適應
    :deep(.el-select) {
        width: 100% !important;
    }
}

@include mq(430px) {
    .page {
        margin: 0 16px; // 給頁面一些邊距
    }
    
    // 進一步優化小螢幕顯示
    :deep(.el-form) {
        .el-form-item__label {
          text-align: left !important;
          justify-content:flex-start !important;
        }
    }
    
    // 信用卡號碼和到期日/CVC在超小螢幕時垂直排列
    .el-row {
        .el-col {
            margin-bottom: 12px;
        }
    }
    
    // 按鈕區域在小螢幕時調整
    .actions {
        flex-direction: column;
        gap: 8px;
        
        .el-button {
            width: 100%;
        }
    }
}


</style>