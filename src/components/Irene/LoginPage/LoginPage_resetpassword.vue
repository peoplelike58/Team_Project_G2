<template>   
  <div class="modal-content">
    <h1 class="title">重置密碼</h1>
    <p class="subtitle">請設定您的新密碼，密碼長度至少8個字元，建議包含英文字母、數字和特殊符號。</p>
    
    <form class="form-container">
      <div class="input-group">
        <label for="new-password" class="input-label">新密碼</label>
        <div class="input-wrapper">
          <input
            id="new-password"
            :type="showPassword ? 'text' : 'password'"
            class="input-field"
            placeholder="請輸入新密碼"
            v-model="newPassword"
            @input="validatePassword"
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
            @input="validateConfirmPassword"
          />
          <button 
            type="button" 
            class="password-toggle" 
            @click="showConfirmPassword = !showConfirmPassword"
          >
            <!-- {{ showConfirmPassword ? '隱藏' : '顯示' }} -->
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
            <path v-if="showConfirmPassword" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2"/>
            <circle v-if="showConfirmPassword" cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
            <path v-if="!showConfirmPassword" d="m1 1 22 22M9.88 9.88a3 3 0 1 0 4.24 4.24M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 11 8 11 8a13.16 13.16 0 0 1-1.67 2.68" stroke="currentColor" stroke-width="2"/>
            <path v-if="!showConfirmPassword" d="M6.61 6.61A13.526 13.526 0 0 0 1 12s4 8 11 8a9.74 9.74 0 0 0 5.39-1.61" stroke="currentColor" stroke-width="2"/>
          </svg>

          </button>
        </div>
      </div>
      
      <!-- 錯誤訊息 -->
      <div class="error-message" v-if="errorMessage">
        {{ errorMessage }}
      </div>
      
      <div class="action-row">
        <button 
          type="submit" 
          class="primary-btn" 
          :disabled="!isFormValid"
          @click="resetPassword"
        >
          確認重置
        </button>
        <button type="button" class="secondary-btn" @click="backToLogin">返回登入</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router'

const router = useRouter()
const newPassword = ref('')
const confirmPassword = ref('')
const showPassword = ref(false)
const showConfirmPassword = ref(false)
const errorMessage = ref('')

const isFormValid = computed(() => {
  return newPassword.value.length >= 8 &&  newPassword.value === confirmPassword.value
})

const validatePassword = () => {
  errorMessage.value = ''
  if (newPassword.value && newPassword.value.length < 8) {
    errorMessage.value = '密碼長度至少需要8個字元'
  }
}

const validateConfirmPassword = () => {
  errorMessage.value = ''
  if (confirmPassword.value && newPassword.value !== confirmPassword.value) {
    errorMessage.value = '兩次輸入的密碼不一致'
  }
}

const resetPassword = (event) => {
  event.preventDefault()
  
  if (!isFormValid.value) {
    if (newPassword.value.length < 8) {
      errorMessage.value = '密碼長度至少需要8個字元'
    } else if (newPassword.value !== confirmPassword.value) {
      errorMessage.value = '兩次輸入的密碼不一致'
    } else if (passwordStrength.value < 30) {
      errorMessage.value = '密碼強度太弱，請使用更複雜的密碼'
    }
    return
  }
  
  // 這裡添加重置密碼的邏輯
  console.log('重置密碼:', newPassword.value)
  
  // 重置成功後可以跳轉到登入頁面
  router.push({ name: 'loginregister-fontrelogin' })
}

const backToLogin = () => {
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