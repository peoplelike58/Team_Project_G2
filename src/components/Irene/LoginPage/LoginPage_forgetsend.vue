<template>   
  <div class="modal-content">
    <h1 class="title">輸入驗證碼</h1>
    <p class="subtitle">我們已經發送驗證碼到<strong>{{ maskedEmail }}</strong>，請檢查您的信箱並輸入6位數驗證碼。</p>
    
    <form class="form-container" @submit.prevent="handleVerification">
      <div class="input-group">
        <label for="verification-code" class="input-label">驗證碼</label>
        <div class="input-wrapper">
          <input
            id="verification-code"
            type="text"
            class="input-field"
            placeholder="請輸入6位數驗證碼"
            maxlength="6"
            v-model="verificationCode"
            @input="handleCodeInput"
            :class="{ 'error': codeError }"
          />
        </div>
        <!-- 錯誤訊息 -->
        <p v-if="codeError" class="error-message">{{ codeError }}</p>

        <div class="code-info">
          <span class="resend-text">沒有收到驗證碼？</span>
          <button type="button" 
            class="resend-btn" 
            :disabled="countdown > 0" 
            @click="resendCode">
            {{ countdown > 0 ? `重新發送 (${countdown}s)` : '重新發送' }}
          </button>
        </div>
      </div>
      <!-- 狀態訊息 -->
      <div v-if="statusMessage" :class="['status-message', statusType]">
        {{ statusMessage }}
      </div>
      
      <div class="action-row">
        <button type="submit" class="primary-btn" :disabled="verificationCode.length !== 6 || loading" :class="{ 'loading': loading }">{{ loading ? '驗證中...' : '確認驗證' }}</button>
        <button type="button" class="secondary-btn" @click="backToForgotPassword">返回上一步</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { useRouter } from 'vue-router'
import axios from 'axios';

const router = useRouter()

// 響應式變數
const verificationCode = ref('')
const countdown = ref(60)
let timer = null
const loading = ref(false)
const resendLoading = ref(false)
const codeError = ref('')
const statusMessage = ref('')
const statusType = ref('') // 'success', 'error'
const email = ref('')
const verifyToken = ref('')

// 提示
const maskedEmail = computed(() => {
  if (!email.value) return ''
  const [localPart, domain] = email.value.split('@')
  if (localPart.length <= 2) return email.value
  const maskedLocal = localPart.charAt(0) + '●'.repeat(localPart.length - 2) + localPart.charAt(localPart.length - 1)
  return maskedLocal + '@' + domain
})

// 清除狀態訊息函數
const clearStatus = () => {
  codeError.value = ''
  statusMessage.value = ''
  statusType.value = ''
}

const handleCodeInput = (event) => {
  // 只允許數字輸入
  let value = event.target.value.replace(/\D/g, '')
  if (value.length > 6) {
    value = value.substring(0, 6)
  }
  verificationCode.value = value
  
  // 清除錯誤訊息
  if (codeError.value && value.length > 0) {
    codeError.value = ''
  }
}

const startCountdown = () => {
  timer = setInterval(() => {
    countdown.value--
    if (countdown.value <= 0) {
      clearInterval(timer)
      timer = null
    }
  }, 1000)
}

const VERIFY_API_URL = `${import.meta.env.VITE_AJAX_URL}/LoginPage_forgetsend.php`
const RESEND_API_URL = `${import.meta.env.VITE_AJAX_URL}/LoginPage_forget.php`

// 初始化頁面
onMounted(() => {
  // 從sessionStorage取得email和驗證token
  email.value = sessionStorage.getItem('resetEmail') || ''
  verifyToken.value = sessionStorage.getItem('verifyToken') || ''
  
  if (!email.value || !verifyToken.value) {
    statusMessage.value = '頁面資料遺失，請重新申請'
    statusType.value = 'error'
    setTimeout(() => {
      router.push({ name: 'loginregister-forgetpassword' })
    }, 2000)
    return
  }
  
  startCountdown()
})

// 驗證碼驗證處理
const handleVerification = async () => {
  
  clearStatus()
  
  // 驗證輸入
  if (verificationCode.value.length !== 6) {
    codeError.value = '請輸入6位數驗證碼'
    return
  }

  loading.value = true

  try {
    // 調用PHP API驗證驗證碼
    const response = await axios.post(VERIFY_API_URL, {
      verify_token: verifyToken.value,
      verification_code: verificationCode.value
    })

    const result = response.data

    if (result.success) {
      statusMessage.value = '驗證成功！'
      statusType.value = 'success'
      
      // 將重設密碼token存到sessionStorage
      sessionStorage.setItem('resetToken', result.reset_token)
      sessionStorage.setItem('resetEmail', result.email)
      
      // 清除驗證相關資料
      sessionStorage.removeItem('verifyToken')
      
      // 驗證成功後跳轉到重設密碼頁面
      setTimeout(() => {
        router.push({ name: 'loginregister-resetpassword' })
      }, 1000)
    } else {
      codeError.value = result.message || '驗證碼錯誤，請重新輸入'
      // 如果驗證失敗，清空輸入框
      verificationCode.value = ''
    }

  } catch (error) {

    if (error.response?.data?.message) {
      codeError.value = error.response.data.message
    } else {
      statusMessage.value = '網路連接錯誤，請稍後再試'
      statusType.value = 'error'
    }
  } finally {
    loading.value = false
  }
}

// 重新發送驗證碼
const resendCode = async () => {
  if (countdown.value > 0 || resendLoading.value) return
  
  resendLoading.value = true
  clearStatus()

  try {
    // 調用原本的發送驗證碼API
    const response = await axios.post(RESEND_API_URL, {
      email: email.value
    })

    const result = response.data

    if (result.success) {
      // 更新驗證token
      verifyToken.value = result.verify_token
      sessionStorage.setItem('verifyToken', result.verify_token)
      
      statusMessage.value = '新的驗證碼已發送到您的信箱'
      statusType.value = 'success'
      
      // 重新開始倒數計時
      countdown.value = 60
      startCountdown()
    } else {
      statusMessage.value = result.message || '重新發送失敗，請稍後再試'
      statusType.value = 'error'
    }

  } catch (error) {
    statusMessage.value = '網路連接錯誤，請稍後再試'
    statusType.value = 'error'
  } finally {
    resendLoading.value = false
  }
}

const backToForgotPassword = () => {
  // 清理sessionStorage
  sessionStorage.removeItem('resetEmail')
  sessionStorage.removeItem('verifyToken')
  router.push({ name: 'loginregister-forgetpassword' })
}

onUnmounted(() => {
  if (timer) {
    clearInterval(timer)
  }
})


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
  text-align: center;
  letter-spacing: 4px;
  font-weight: 600;
  
  &::placeholder {
    color: #9ca3af;
    letter-spacing: normal;
    font-weight: normal;
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

.code-info {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-top: 4px;
}

.resend-text {
  font-size: 14px;
  color: #6c757d;
}

.resend-btn {
  background: none;
  border: none;
  color: $black-14;
  font-size: 14px;
  cursor: pointer;
  text-decoration: underline;
  padding: 0;
  
  &:hover:not(:disabled) {
    color: #333;
  }
  
  &:disabled {
    color: #6c757d;
    cursor: not-allowed;
    text-decoration: none;
  }
}

.error-message {
  color: #dc2626;
  font-size: 12px;
  margin: 0;
  padding: 0;
}

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
  
  &:hover:not(:disabled) {
    background: #333;
  }
  
  &:disabled {
    background: #6c757d;
    cursor: not-allowed;
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
</style>