<template>   
  <div class="modal-content">
    <h1 class="title">忘記密碼</h1>
    <p class="subtitle">請輸入您註冊時使用的電子郵件地址，我們將發送重設密碼的連結給您。</p>
    
    <form class="form-container" @submit.prevent="handleSubmit" novalidate>
      <div class="input-group">
        <label for="fp-email" class="input-label">帳號</label>
        <div class="input-wrapper">
          <input
            id="fp-email"
            type="email"
            class="input-field"
            placeholder="請輸入電子郵件"
            v-model="email"
            :class="{ 'error': emailError }"
          />
          <!-- 錯誤訊息顯示 -->
        </div>
        <p v-if="emailError" class="error-message">{{ emailError }}</p>
      </div>
      
      <div class="input-group">
        <label class="input-label">驗證</label>
        <div class="checkbox-wrapper">
          <input id="isHuman" type="checkbox" class="checkbox-input"/>
          <label for="isHuman" class="checkbox-label">我不是機器人</label>
        </div>
      </div>

      <div v-if="statusMessage" :class="['status-message', statusType]">
        {{ statusMessage }}
      </div>
      
      <div class="action-row">
        <button type="submit" class="primary-btn" :disabled="loading">立即發送</button>
        <button type="button" class="secondary-btn" @click="backToLogin">返回登入</button>
      </div>
    </form>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router'

import axios from 'axios';

const router = useRouter()

const backToLogin = ()=>{
  router.push({name:'loginregister-fontrelogin' })
}
// const gochek = () =>{
//   router.push({name:'loginregister-forgetsend' })
// }

// 響應式變數
const email = ref('')
const isVerified = ref(false)
const loading = ref(false)
const emailError = ref('')
const verifyError = ref('')
const statusMessage = ref('')
const statusType = ref('') // 'success', 'error'

// 計算屬性：驗證email格式(正則表達式)
// const isValidEmail = computed(() => {
//   const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
//   return emailRegex.test(email.value)
// })

// 計算屬性：驗證email格式（簡單易懂版本）
const isValidEmail = computed(() => {
  // 如果email是空的，直接返回false
  if (!email.value) {
    return false
  }
  
  // 檢查是否包含@符號
  if (!email.value.includes('@')) {
    return false
  }
  
  // 將email用@分割成兩部分
  const parts = email.value.split('@')
  
  // 必須剛好分割成2部分（@符號前後各一部分）
  if (parts.length !== 2) {
    return false
  }
  
  const beforeAt = parts[0]  // @符號前面的部分
  const afterAt = parts[1]   // @符號後面的部分
  
  // @符號前面不能是空的
  if (!beforeAt || beforeAt.length === 0) {
    return false
  }
  
  // @符號後面不能是空的
  if (!afterAt || afterAt.length === 0) {
    return false
  }
  
  // @符號後面必須包含至少一個點（.）
  if (!afterAt.includes('.')) {
    return false
  }
  
  // 將@後面的部分用點分割
  const domainParts = afterAt.split('.')
  
  // 檢查點前後都不能是空的
  for (let i = 0; i < domainParts.length; i++) {
    if (!domainParts[i] || domainParts[i].length === 0) {
      return false
    }
  }
  
  // 所有檢查都通過，返回true
  return true
})

// 清除所有錯誤訊息
const clearErrors = () => {
  emailError.value = ''
  verifyError.value = ''
  statusMessage.value = ''
  statusType.value = ''
}

// 驗證表單
const validateForm = () => {
  clearErrors()
  let isValid = true

  // 驗證email
  if (!email.value.trim()) {
    emailError.value = '請輸入電子郵件地址'
    isValid = false
  } else if (!isValidEmail.value) {
    emailError.value = '請輸入有效的電子郵件格式'
    isValid = false
  }

  return isValid
}

const API_URL = `${import.meta.env.VITE_AJAX_URL}/LoginPage_forget.php`


// 處理表單提交
const handleSubmit = async () => {
  // 先驗證表單
  if (!validateForm()) {
    return
  }

  loading.value = true
  clearErrors()

  try {
    // 調用PHP檢查email並發送驗證碼
   const response = await axios.post(API_URL, {
      email: email.value.trim()
    })

    const result = response.data

    if (result.success) {
      statusMessage.value = '驗證碼已發送到您的信箱'
      statusType.value = 'success'
      
      // 將email存到sessionStorage，供下一頁使用
      sessionStorage.setItem('resetEmail', email.value.trim())
      sessionStorage.setItem('verifyToken', result.verify_token)
      
      // 延遲1秒後跳轉，讓用戶看到成功訊息
      setTimeout(() => {
        router.push({ name: 'loginregister-forgetsend' })
      }, 1000)

    } else {
      // 顯示來自後端的錯誤訊息
      statusMessage.value = result.message || '發送失敗，請稍後再試'
      statusType.value = 'error'
    }

  } catch (error) {
    
    // 處理axios錯誤
    if (error.response) {
      // 伺服器回應了錯誤狀態碼
      const errorMessage = error.response.data?.message || '伺服器錯誤'
      statusMessage.value = errorMessage
      statusType.value = 'error'
    } else if (error.request) {
      // 請求發送了但沒收到回應
      statusMessage.value = '網路連接錯誤，請檢查網路連接'
      statusType.value = 'error'
    } else {
      // 其他錯誤
      statusMessage.value = '發送請求時發生錯誤，請稍後再試'
      statusType.value = 'error'
    }
  } finally {
    loading.value = false
  }
}

</script>

<style lang="scss" scoped>
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';

.modal-content {
  padding: 40px 32px 32px;
}

.title {
  font-size: 24px;
  font-weight: 700;
  color: $black-14;
  margin: 0 0 8px 0;
  text-align: center;
}

.subtitle {
  font-size: 14px;
  color: #6c757d;
  margin: 0 0 32px 0;
  text-align: center;
  line-height: 1.5;
}

.form-container {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.input-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.input-label {
  font-size: 14px;
  font-weight: 500;
  color: #374151;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-field {
  width: 100%;
  padding: 12px 16px 12px 16px;
  border: 2px solid #dee2e6;
  border-radius: 8px;
  font-size: 16px;
  transition: all 0.2s ease;
  outline: none;
  box-sizing: border-box;
  
  &::placeholder {
    color: #9ca3af;
  }
  
  &:focus {
    border-color: $black-14;
    box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
  }

  &.error {
    border-color: #dc2626;
    
    &:focus {
      border-color: #dc2626;
      box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }
  }
}

.checkbox-wrapper {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 8px 4px;
}

.checkbox-input {
  width: 18px;
  height: 18px;
  cursor: pointer;
}

.checkbox-label {
  font-size: 14px;
  color: #374151;
  cursor: pointer;
  user-select: none;
}

// 錯誤訊息樣式
.error-message {
  color: #dc2626;
  font-size: 12px;
  margin: 0;
  padding: 0;
}

// 狀態訊息樣式
.status-message {
  padding: 12px 16px;
  border-radius: 8px;
  font-size: 14px;
  text-align: center;
  margin: 0;
  
  &.success {
    background-color: #d1fae5;
    color: #065f46;
    border: 1px solid #a7f3d0;
  }
  
  &.error {
    background-color: #fee2e2;
    color: #dc2626;
    border: 1px solid #fecaca;
  }
}

.action-row {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.primary-btn,
.secondary-btn {
  padding: 12px 24px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
}

.primary-btn {
  background: $black-14;
  color: white;
  
  &:hover {
    background: #333;
  }
}

.secondary-btn {
  background: transparent;
  color: #6c757d;
  border: 2px solid #dee2e6;
  
  &:hover {
    background: #f8f9fa;
    color: #374151;
  }
}




.social-login {
  display: flex;
  justify-content: center;
  gap: 12px;
}

.social-btn {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  
  .social-icon {
    font-size: 16px;
    font-weight: 600;
  }
}

.google-btn {
  background: #db4437;
  color: white;
  
  &:hover {
    background: #c23321;
  }
}

.facebook-btn {
  background: #3b5998;
  color: white;
  
  &:hover {
    background: #2d4373;
  }
}

.line-btn {
  background: #00c300;
  color: white;
  
  &:hover {
    background: #009a00;
  }
  
  .social-icon {
    font-size: 12px;
  }
}

// 響應式設計
@media (max-width: 480px) {
  .modal-container {
    width: 95%;
    margin: 16px;
  }
  
  .modal-content {
    padding: 32px 24px 24px;
  }
  
  .title {
    font-size: 20px;
  }
  
  .input-field {
    font-size: 16px;
  }
}

// 響應式設計
// @media (max-width: 480px) {
//   .modal-content {
//     padding: 32px 24px 24px;
//   }
  
//   .title {
//     font-size: 20px;
//   }
  
//   .input-field {
//     font-size: 16px;
//   }
// }
</style>