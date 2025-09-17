<template>   
  <div class="modal-content">
    <h1 class="title">重置密碼</h1>
    <!-- <p class="subtitle">請設定您的新密碼，密碼長度至少8個字元，建議包含英文字母、數字和特殊符號。</p> -->
    <p class="subtitle">請設定您的新密碼，密碼長度至少8位數字。</p>


    <form class="form-container"  @submit.prevent="resetPassword">
      <div class="input-group">
        <label for="new-password" class="input-label">新密碼</label>
        <div class="input-wrapper">
          <input
            id="new-password"
            :type="showPassword ? 'text' : 'password'"
            class="input-field"
            placeholder="請輸入新密碼"
            v-model="newPassword"
            @input="handlePasswordInput($event, 'new')"
            inputmode="numeric"
          />
          <button 
            type="button" 
            class="password-toggle" 
            @click="showPassword = !showPassword"
          >
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
              <path v-if="showPassword" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2"/>
              <circle v-if="showPassword" cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
              <path v-if="!showPassword" d="m1 1 22 22M9.88 9.88a3 3 0 1 0 4.24 4.24M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 11 8 11 8a13.16 13.16 0 0 1-1.67 2.68" stroke="currentColor" stroke-width="2"/>
              <path v-if="!showPassword" d="M6.61 6.61A13.526 13.526 0 0 0 1 12s4 8 11 8a9.74 9.74 0 0 0 5.39-1.61" stroke="currentColor" stroke-width="2"/>
            </svg>
          </button>
        </div>
        <span v-if="passwordError" class="field-error">{{ passwordError }}</span>
      </div>
      
      <div class="input-group">
        <label for="confirm-password" class="input-label">確認新密碼</label>
        <div class="input-wrapper">
          <input
            id="confirm-password"
            :type="showConfirmPassword ? 'text' : 'password'"
            class="input-field"
            placeholder="請再次輸入新密碼"
            v-model="confirmPassword"
            @input="handlePasswordInput($event, 'confirm')"
            inputmode="numeric"
          />
          <button 
            type="button" 
            class="password-toggle" 
            @click="showConfirmPassword = !showConfirmPassword"
          >
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
            <path v-if="showConfirmPassword" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2"/>
            <circle v-if="showConfirmPassword" cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
            <path v-if="!showConfirmPassword" d="m1 1 22 22M9.88 9.88a3 3 0 1 0 4.24 4.24M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 11 8 11 8a13.16 13.16 0 0 1-1.67 2.68" stroke="currentColor" stroke-width="2"/>
            <path v-if="!showConfirmPassword" d="M6.61 6.61A13.526 13.526 0 0 0 1 12s4 8 11 8a9.74 9.74 0 0 0 5.39-1.61" stroke="currentColor" stroke-width="2"/>
          </svg>

          </button>
        </div>
        <span v-if="confirmError" class="field-error">{{ confirmError }}</span>
      </div>
      
      <!-- 錯誤訊息 -->
      <div class="error-message" v-if="errorMessage">
        {{ errorMessage }}
      </div>
      
          <div v-if="statusMessage" :class="['status-message', statusType]">
            <svg v-if="statusType === 'success'" class="status-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <svg v-else-if="statusType === 'error'" class="status-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span>{{ statusMessage }}</span>
          </div>
    

      <div class="action-row">
        <button 
          type="submit" 
          class="primary-btn" 
          :disabled="!isFormValid"
        >
          確認重置
        </button>
        <button type="button" class="secondary-btn" @click="backToLogin">返回登入</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router'

import axios from 'axios';

const router = useRouter()
const newPassword = ref('')
const confirmPassword = ref('')
const showPassword = ref(false)
const showConfirmPassword = ref(false)
const passwordError = ref('')
const confirmError = ref('')
const statusMessage = ref('')
const statusType = ref('')
const loading = ref(false)
const resetToken = ref('')
const errorMessage = ref('')

const isFormValid = computed(() => {
  return newPassword.value.length >= 8 &&  newPassword.value === confirmPassword.value &&
         !passwordError.value && !confirmError.value
})

// 初始化檢查
onMounted(() => {
  // 從sessionStorage取得重設token
  resetToken.value = sessionStorage.getItem('resetToken') || ''
  
  console.log('重設密碼頁面初始化')
  console.log('ResetToken:', resetToken.value ? '已取得' : '未取得')
  
  if (!resetToken.value) {
    statusMessage.value = '頁面資料遺失，請重新申請忘記密碼'
    statusType.value = 'error'
    setTimeout(() => {
      router.push({ name: 'loginregister-forgetpassword' })
    }, 3000)
  }
})

// 處理密碼輸入（只允許數字）
const handlePasswordInput = (event, field) => {
  // 只允許數字輸入
  let value = event.target.value.replace(/\D/g, '')
  
  // 限制長度（例如最多20位數字）
  if (value.length > 20) {
    value = value.substring(0, 20)
  }
  
  // 更新對應的欄位
  if (field === 'new') {
    newPassword.value = value
    validatePassword()
  } else if (field === 'confirm') {
    confirmPassword.value = value
    validateConfirmPassword()
  }
  
  // 強制更新input的值（確保顯示的是處理後的值）
  event.target.value = value
}

// 清除狀態訊息
const clearStatus = () => {
  statusMessage.value = ''
  statusType.value = ''
}

const validatePassword = () => {
  clearStatus()
  passwordError.value = ''

  if (newPassword.value && newPassword.value.length < 8) {
    passwordError.value = '密碼長度至少需要8個字元'
  }

  // 如果確認密碼已輸入，重新驗證一致性
  if (confirmPassword.value) {
    validateConfirmPassword()
  }
}

// 驗證確認密碼
const validateConfirmPassword = () => {
  clearStatus()
  confirmError.value = ''
  if (confirmPassword.value && newPassword.value !== confirmPassword.value) {
    confirmError.value = '兩次輸入的密碼不一致'
  }
}

const API_URL = `${import.meta.env.VITE_AJAX_URL}/LoginPage_resetpassword.php`

// 重設密碼處理
const resetPassword = async () => {
  console.log('=== 開始重設密碼 ===')
  console.log('=== 開始重設密碼調試 ===')
  console.log('API_URL:', API_URL)
  console.log('newPassword長度:', newPassword.value.length)
  console.log('resetToken存在:', !!resetToken.value)
  
  // 驗證表單
  if (!isFormValid.value) {
    if (newPassword.value.length < 8) {
      passwordError.value = '密碼長度至少需要8個字元'
    } else if (newPassword.value !== confirmPassword.value) {
      confirmError.value = '兩次輸入的密碼不一致'
    }
    return
  }

  if (!resetToken.value) {
    statusMessage.value = '缺少重設權限，請重新申請'
    statusType.value = 'error'
    return
  }

  loading.value = true
  clearStatus()

  try {
    // 調用PHP重設密碼
    const response = await axios.post(API_URL, {
      reset_token: resetToken.value,
      new_password: newPassword.value
    })

    const result = response.data
    console.log('重設密碼回應:', result)

    if (result.success) {
      statusMessage.value = result.message
      statusType.value = 'success'
      
      // 清除sessionStorage中的重設相關資料
      sessionStorage.removeItem('resetToken')
      sessionStorage.removeItem('resetEmail')
      
      // 重設成功後3秒跳轉到登入頁面
      setTimeout(() => {
        router.push({ name: 'loginregister-fontrelogin' })
      }, 1000)
      
    } else {
      statusMessage.value = result.message || '密碼重設失敗，請稍後再試'
      statusType.value = 'error'
    }

  } catch (error) {
    console.error('重設密碼錯誤:', error)
    
    if (error.response?.data?.message) {
      statusMessage.value = error.response.data.message
    } else {
      statusMessage.value = '網路連接錯誤，請稍後再試'
    }
    statusType.value = 'error'
  } finally {
    loading.value = false
  }
}

const backToLogin = () => {
  // 清除sessionStorage
  sessionStorage.removeItem('resetToken')
  sessionStorage.removeItem('resetEmail')
  router.push({ name: 'loginregister-fontrelogin' })
}

</script>

<style lang="scss" scoped>
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';

.modal-content {
  padding: 40px 32px 32px;
}

.title {
  font-size: $pcFont-H3;
  font-weight: $bold;
  color: $black-14;
  margin: 0 0 8px 0;
  text-align: center;
}

.subtitle {
  font-size: $mbFont-label;
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
  font-size: $mbFont-label;
  font-weight: $medium;
  color: #374151;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-field {
  width: 100%;
  padding: 12px 60px 12px 16px;
  border: 2px solid #dee2e6;
  border-radius: 8px;
  font-size: $pcFont-p-s;
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
}

.password-toggle {
  position: absolute;
  right: 12px;
  background: none;
  border: none;
  color: #6c757d;
  font-size: 14px;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 4px;
  transition: all 0.2s ease;
  
  &:hover {
    background: #f8f9fa;
    color: #374151;
  }
}

.error-message {
  color: #dc3545;
  font-size: 14px;
  text-align: center;
  padding: 8px;
  background: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 8px;
}

.field-error {
  color: #ef4444;
  font-size: 12px;
  margin-top: 4px;
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
  font-weight: $medium;
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

// 載入動畫
.loading-spinner {
  display: inline-block;
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
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