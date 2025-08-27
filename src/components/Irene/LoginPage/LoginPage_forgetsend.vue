<template>   
  <div class="modal-content">
    <h1 class="title">輸入驗證碼</h1>
    <p class="subtitle">我們已經發送驗證碼到您的電子郵件，請檢查您的信箱並輸入6位數驗證碼。</p>
    
    <form class="form-container">
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
          />
        </div>
        <div class="code-info">
          <span class="resend-text">沒有收到驗證碼？</span>
          <button type="button" class="resend-btn" :disabled="countdown > 0" @click="resendCode">
            {{ countdown > 0 ? `重新發送 (${countdown}s)` : '重新發送' }}
          </button>
        </div>
      </div>
      
      <div class="action-row">
        <button type="submit" class="primary-btn" :disabled="verificationCode.length !== 6"  @click="goReset">確認驗證</button>
        <button type="button" class="secondary-btn" @click="backToForgotPassword">返回上一步</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router'

const router = useRouter()
const verificationCode = ref('')
const countdown = ref(60)
let timer = null

const handleCodeInput = (event) => {
  // 只允許數字輸入
  event.target.value = event.target.value.replace(/\D/g, '')
  verificationCode.value = event.target.value
}

const resendCode = () => {
  if (countdown.value > 0) return
  
  // 這裡添加重新發送驗證碼的邏輯
  console.log('重新發送驗證碼')
  
  // 開始倒數計時
  countdown.value = 60
  startCountdown()
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

const backToForgotPassword = () => {
  router.push({ name: 'loginregister-forgetpassword' })
}

const goReset = () =>{
    router.push({ name: 'loginregister-resetpassword' })
}

onMounted(() => {
  startCountdown()
})

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