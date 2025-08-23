<template>
  <div class="modal-overlay" @click.self="closeModal">
    <div class="register-modal">
      <!-- 關閉按鈕 -->
      <button class="close-btn" @click="closeModal">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
          <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
      </button>
      
      <!-- 標題 -->
      <h2 class="modal-title">會員註冊</h2>
      
      <!-- 註冊表單 -->
      <form @submit.prevent="handleSubmit" class="register-form">
        
        <!-- 帳號欄位 -->
        <div class="form-group">
          <label for="email">帳號</label>
          <input 
            type="email" 
            id="email" 
            v-model="formData.email"
            placeholder="請輸入電子郵件"
            required
          />
        </div>
        
        <!-- 姓名欄位 -->
        <div class="form-group">
          <label for="name">姓名</label>
          <input 
            type="text" 
            id="name" 
            v-model="formData.name"
            placeholder="輸入您的姓名"
            required
          />
        </div>
        
        <!-- 電話欄位 -->
        <div class="form-group">
          <label for="phone">電話</label>
          <input 
            type="tel" 
            id="phone" 
            v-model="formData.phone"
            placeholder="請輸入聯絡電話"
            required
          />
        </div>
        
        <!-- 密碼欄位 -->
        <div class="form-group">
          <label for="password">密碼</label>
          <div class="password-input">
            <input 
              :type="showPassword ? 'text' : 'password'" 
              id="password" 
              v-model="formData.password"
              placeholder="請輸入密碼"
              required
            />
            <button type="button" class="eye-btn" @click="togglePassword">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <path v-if="!showPassword" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2"/>
                <circle v-if="!showPassword" cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                <path v-if="showPassword" d="m1 1 22 22M9.88 9.88a3 3 0 1 0 4.24 4.24M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 11 8 11 8a13.16 13.16 0 0 1-1.67 2.68" stroke="currentColor" stroke-width="2"/>
                <path v-if="showPassword" d="M6.61 6.61A13.526 13.526 0 0 0 1 12s4 8 11 8a9.74 9.74 0 0 0 5.39-1.61" stroke="currentColor" stroke-width="2"/>
              </svg>
            </button>
          </div>
        </div>
        
        <!-- 確認密碼欄位 -->
        <div class="form-group">
          <label for="confirmPassword">確認密碼</label>
          <div class="password-input">
            <input 
              :type="showConfirmPassword ? 'text' : 'password'" 
              id="confirmPassword" 
              v-model="formData.confirmPassword"
              placeholder="請輸入密碼"
              required
            />
            <button type="button" class="eye-btn" @click="toggleConfirmPassword">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <path v-if="!showConfirmPassword" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2"/>
                <circle v-if="!showConfirmPassword" cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                <path v-if="showConfirmPassword" d="m1 1 22 22M9.88 9.88a3 3 0 1 0 4.24 4.24M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 11 8 11 8a13.16 13.16 0 0 1-1.67 2.68" stroke="currentColor" stroke-width="2"/>
                <path v-if="showConfirmPassword" d="M6.61 6.61A13.526 13.526 0 0 0 1 12s4 8 11 8a9.74 9.74 0 0 0 5.39-1.61" stroke="currentColor" stroke-width="2"/>
              </svg>
            </button>
          </div>
        </div>
        
        <!-- reCAPTCHA -->
        <div class="captcha-group">
          <label class="captcha-checkbox">
            <input type="checkbox" v-model="formData.isNotRobot" required>
            <span class="checkmark"></span>
            我不是機器人
          </label>
        </div>
        
        <!-- 立即註冊按鈕 -->
        <button type="submit" class="submit-btn" :disabled="!isFormValid">
          立即註冊
        </button>
        
        <!-- 底部登入連結 -->
        <div class="login-section">
          <span>已有帳號？</span>
          <button type="button" class="login-link" @click="switchToLogin">
            立即登入
          </button>
        </div>
        
        <!-- 分隔線 -->
        <div class="divider">
          <span>or</span>
        </div>
        
        <!-- 社群登入 -->
        <div class="social-login">
          <button type="button" class="social-btn google">
            <svg width="20" height="20" viewBox="0 0 24 24">
              <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
              <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
              <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
              <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
          </button>
          
          <button type="button" class="social-btn facebook">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="#1877F2">
              <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
          </button>
          
          <button type="button" class="social-btn github">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
            </svg>
          </button>
        </div>
        
      </form>
    </div>
  </div>
</template>

<script setup>
// 目前只是靜態切版，無需任何邏輯
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

// 表單資料
const formData = ref({
  email: '',
  name: '',
  phone: '',
  password: '',
  confirmPassword: '',
  isNotRobot: false
})

// 密碼顯示狀態
const showPassword = ref(false)
const showConfirmPassword = ref(false)

// 表單驗證
const isFormValid = computed(() => {
  return formData.value.email && 
         formData.value.name && 
         formData.value.phone && 
         formData.value.password && 
         formData.value.confirmPassword && 
         formData.value.password === formData.value.confirmPassword &&
         formData.value.isNotRobot
})

// 方法
const togglePassword = () => {
  showPassword.value = !showPassword.value
}

const toggleConfirmPassword = () => {
  showConfirmPassword.value = !showConfirmPassword.value
}

const closeModal = () => {
  // 發送關閉事件給父組件
  emit('close')
}

const switchToLogin = () => {
  // 切換到登入模式
  emit('switch-to-login')
}

const handleSubmit = async () => {
  if (!isFormValid.value) return
  
  try {
    // 這裡會調用註冊 API
    console.log('註冊資料:', formData.value)
    // await registerUser(formData.value)
  } catch (error) {
    console.error('註冊失敗:', error)
  }
}

// 事件發送
const emit = defineEmits(['close', 'switch-to-login'])
</script>

<style lang="scss" scoped>
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  @include flexcenter(0, column);
  z-index: 1000;
}

.register-modal {
  background: white;
  border-radius: 16px;
  padding: 40px 32px;
  width: 90%;
  max-width: 440px;
  max-height: 90vh;
  overflow-y: auto;
  position: relative;
  
  @media (max-width: 768px) {
    padding: 32px 24px;
    width: 95%;
  }
}

.close-btn {
  @include btn(50%);
  position: absolute;
  top: 20px;
  right: 20px;
  width: 32px;
  height: 32px;
  @include flexcenter(0, row);
  color: #666;
  
  &:hover {
    background-color: #f5f5f5;
  }
}

.modal-title {
  font-size: $pcFont-H2;
  font-weight: $bold;
  text-align: center;
  margin-bottom: 32px;
  color: $black-14;
  
  @media (max-width: 768px) {
    font-size: $pcFont-H3;
    margin-bottom: 24px;
  }
}

.register-form {
  @include flexcenter(20px, column);
  align-items: stretch;
}

.form-group {
  @include flexcenter(8px, column);
  align-items: stretch;
  
  label {
    font-size: $pcFont-label;
    font-weight: $medium;
    color: $black-14;
    text-align: left;
  }
  
  input {
    padding: 12px 16px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: $pcFont-p-s;
    transition: border-color 0.3s ease;
    
    &:focus {
      outline: none;
      border-color: $tag;
    }
    
    &::placeholder {
      color: #999;
    }
  }
}

.password-input {
  position: relative;
  
  input {
    padding-right: 48px;
  }
  
  .eye-btn {
    @include btn(4px);
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
    padding: 4px;
    
    &:hover {
      background-color: #f5f5f5;
    }
  }
}

.captcha-group {
  margin: 8px 0;
}

.captcha-checkbox {
  @include flexcenter(12px, row);
  justify-content: flex-start;
  cursor: pointer;
  font-size: $pcFont-p-s;
  color: $black-14;
  
  input[type="checkbox"] {
    display: none;
  }
  
  .checkmark {
    width: 20px;
    height: 20px;
    border: 2px solid #ddd;
    border-radius: 4px;
    position: relative;
    transition: all 0.3s ease;
    
    &:after {
      content: "";
      position: absolute;
      display: none;
      left: 6px;
      top: 2px;
      width: 6px;
      height: 10px;
      border: solid white;
      border-width: 0 2px 2px 0;
      transform: rotate(45deg);
    }
  }
  
  input:checked ~ .checkmark {
    background-color: $tag;
    border-color: $tag;
    
    &:after {
      display: block;
    }
  }
}

.submit-btn {
  @include btn(8px);
  background-color: $black-14;
  color: white;
  padding: 16px 24px;
  font-size: $pcFont-label;
  font-weight: $semiBold;
  transition: all 0.3s ease;
  margin-top: 8px;
  
  &:hover:not(:disabled) {
    background-color: lighten($black-14, 10%);
  }
  
  &:disabled {
    background-color: #ccc;
    cursor: not-allowed;
  }
}

.login-section {
  @include flexcenter(8px, row);
  font-size: $pcFont-p-s;
  color: #666;
  margin-top: 16px;
  
  .login-link {
    @include btn(4px);
    color: $tag;
    font-weight: $medium;
    padding: 4px 8px;
    text-decoration: underline;
    
    &:hover {
      background-color: #f5f5f5;
    }
  }
}

.divider {
  position: relative;
  text-align: center;
  margin: 24px 0;
  
  &:before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background-color: #eee;
  }
  
  span {
    background-color: white;
    color: #999;
    padding: 0 16px;
    font-size: 14px;
  }
}

.social-login {
  @include flexcenter(16px, row);
}

.social-btn {
  @include btn(50%);
  width: 48px;
  height: 48px;
  @include flexcenter(0, row);
  @include border(#eee);
  transition: all 0.3s ease;
  
  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }
  
  &.google:hover {
    border-color: #4285F4;
  }
  
  &.facebook:hover {
    border-color: #1877F2;
  }
  
  &.github:hover {
    border-color: $black-14;
  }
}
</style>