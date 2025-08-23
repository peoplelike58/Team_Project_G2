<template>
  <div>
    <!-- 登入視窗 -->
    <div v-if="isOpen" class="modal-overlay" @click="closeModal">
      <div class="modal-container" @click.stop>
        <!-- 關閉按鈕 -->
        <button @click="closeModal" class="close-btn">
          ×
        </button>

        <div class="modal-content">
          <!-- 標題 -->
          <h1 class="title">會員登入</h1>

          <!-- 登入表單 -->
          <div class="form-container">
            <!-- 帳號輸入 -->
            <div class="input-group">
              <label class="input-label">帳號</label>
              <input
                v-model="email"
                type="email"
                placeholder="請輸入電子郵件"
                class="input-field"
              />
            </div>

            <!-- 密碼輸入 -->
            <div class="input-group">
              <label class="input-label">密碼</label>
              <input
                v-model="password"
                type="password"
                placeholder="請輸入密碼"
                class="input-field"
              />
            </div>

            <!-- 忘記密碼 -->
            <div class="forgot-password">
              <button @click="handleForgotPassword" class="forgot-btn">
                忘記密碼？
              </button>
            </div>

            <!-- 登入按鈕 -->
            <button @click="handleLogin" class="login-btn">
              立即登入
            </button>
          </div>

          <!-- 註冊連結 -->
          <div class="register-section">
            <span class="register-text">還沒有帳號？</span>
            <button @click="handleRegister" class="register-btn">
              立即註冊
            </button>
          </div>

          <!-- 分隔線 -->
          <div class="divider">
            <div class="divider-line"></div>
            <span class="divider-text">or</span>
            <div class="divider-line"></div>
          </div>

          <!-- 社交登入按鈕 -->
          <div class="social-login">
            <button @click="handleSocialLogin('Google')" class="social-btn">
              <span class="google-icon">G+</span>
            </button>
            <button @click="handleSocialLogin('Facebook')" class="social-btn">
              <span class="facebook-icon">f</span>
            </button>
            <button @click="handleSocialLogin('Line')" class="social-btn">
              <div class="line-icon">
                <span class="line-text">LINE</span>
              </div>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

// 響應式數據
const isOpen = ref(true)
const email = ref('')
const password = ref('')

// 方法定義
const openModal = () => {
  isOpen.value = true
}

const closeModal = () => {
  isOpen.value = false
}

const handleLogin = () => {
  console.log('登入資料:', {
    email: email.value,
    password: password.value
  })
  
  if (email.value && password.value) {
    alert(`登入成功！歡迎 ${email.value}`)
    closeModal()
  } else {
    alert('請填寫完整的登入資訊')
  }
}

const handleForgotPassword = () => {
  console.log('忘記密碼')
  alert('忘記密碼功能')
}

const handleRegister = () => {
  console.log('前往註冊')
  alert('前往註冊頁面')
}

const handleSocialLogin = (provider) => {
  console.log(`使用 ${provider} 登入`)
  alert(`使用 ${provider} 登入`)
}
</script>

<style scoped lang="scss">
@import '@/assets/styles/othermixins.scss';
/* Demo 容器 */
.demo-container {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  background-color: #f3f4f6;
}

.open-btn {
  padding: 12px 24px;
  background-color: #3b82f6;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.open-btn:hover {
  background-color: #2563eb;
}

/* 模態視窗遮罩 */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
  z-index: 50;
}

/* 模態視窗容器 */
.modal-container {
  background-color: white;
  border-radius: 16px;
  width: 100%;
  max-width: 448px;
  position: relative;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

/* 關閉按鈕 */
.close-btn {
  position: absolute;
  top: 16px;
  right: 16px;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6b7280;
  font-size: 24px;
  font-weight: 300;
  border: none;
  background: none;
  cursor: pointer;
  transition: color 0.3s;
}

.close-btn:hover {
  color: #374151;
}

/* 模態視窗內容 */
.modal-content {
  padding: 48px 32px;
}

/* 標題 */
.title {
  font-size: 24px;
  font-weight: 600;
  text-align: center;
  margin-bottom: 48px;
  color: #1f2937;
  margin-top: 0;
}

/* 表單容器 */
.form-container {
  margin-bottom: 32px;
}

/* 輸入群組 */
.input-group {
  margin-bottom: 32px;
}

/* 輸入標籤 */
.input-label {
  display: block;
  color: #374151;
  font-size: 16px;
  margin-bottom: 12px;
  font-weight: 500;
}

/* 輸入欄位 */
.input-field {
  width: 100%;
  padding: 12px 0;
  font-size: 16px;
  background: transparent;
  border: none;
  border-bottom: 2px solid #e5e7eb;
  outline: none;
  transition: border-color 0.3s;
  box-sizing: border-box;
}

.input-field::placeholder {
  color: #9ca3af;
}

.input-field:focus {
  border-bottom-color: #6b7280;
}

/* 忘記密碼 */
.forgot-password {
  text-align: left;
  margin-bottom: 32px;
}

.forgot-btn {
  color: #6b7280;
  font-size: 14px;
  border: none;
  background: none;
  cursor: pointer;
  transition: color 0.3s;
}

.forgot-btn:hover {
  color: #374151;
}

/* 登入按鈕 */
.login-btn {
  width: 100%;
  background-color: #000;
  color: white;
  padding: 16px;
  border-radius: 9999px;
  font-size: 16px;
  font-weight: 500;
  border: none;
  cursor: pointer;
  transition: background-color 0.3s;
  margin-top: 32px;
}

.login-btn:hover {
  background-color: #1f2937;
}

/* 註冊區域 */
.register-section {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 32px;
  font-size: 16px;
}

.register-text {
  color: #4b5563;
}

.register-btn {
  color: #000;
  font-weight: 500;
  border: none;
  background: none;
  cursor: pointer;
  border-bottom: 1px solid #000;
  transition: color 0.3s;
}

.register-btn:hover {
  color: #374151;
}

/* 分隔線 */
.divider {
  display: flex;
  align-items: center;
  margin: 32px 0;
}

.divider-line {
  flex: 1;
  height: 1px;
  background-color: #e5e7eb;
}

.divider-text {
  padding: 0 16px;
  color: #6b7280;
  font-size: 16px;
}

/* 社交登入 */
.social-login {
  display: flex;
  justify-content: center;
  gap: 24px;
}

.social-btn {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  border: 2px solid #d1d5db;
  display: flex;
  align-items: center;
  justify-content: center;
  background: white;
  cursor: pointer;
  transition: border-color 0.3s;
}

.social-btn:hover {
  border-color: #6b7280;
}

/* 社交圖標 */
.google-icon {
  font-size: 18px;
  font-weight: bold;
  color: #ef4444;
}

.facebook-icon {
  font-size: 18px;
  font-weight: bold;
  color: #2563eb;
}

.line-icon {
  width: 24px;
  height: 24px;
  background-color: #22c55e;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.line-text {
  color: white;
  font-size: 10px;
  font-weight: bold;
}

/* 響應式設計 */
@media (max-width: 640px) {
  .modal-content {
    padding: 32px 24px;
  }
  
  .title {
    font-size: 20px;
    margin-bottom: 32px;
  }
  
  .input-group {
    margin-bottom: 24px;
  }
}
</style>