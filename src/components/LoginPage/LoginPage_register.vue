<template>
  <div class="modal-overlay">
    <div class="modal-container">
      <!-- 關閉按鈕 -->
      <button class="close-btn">
        ×
      </button>

      <!-- 標題 -->
      <h2 class="modal-title">會員註冊</h2>

      <!-- 註冊表單 -->
      <form class="register-form">
        <!-- 帳號 -->
        <div class="form-group">
          <label>帳號</label>
          <input
            type="text"
            placeholder="請輸入電子郵件"
          />
        </div>

        <!-- 姓名 -->
        <div class="form-group">
          <label>姓名</label>
          <input
            type="text"
            placeholder="輸入您的姓名"
          />
        </div>

        <!-- 電話 -->
        <div class="form-group">
          <label>電話</label>
          <input
            type="tel"
            placeholder="請輸入聯絡電話"
          />
        </div>

        <!-- 密碼 -->
        <div class="form-group">
          <label>密碼</label>
          <div class="password-input">
            <input
              type="password"
              placeholder="請輸入密碼"
            />
            <button type="button" class="password-toggle">
              👁️
            </button>
          </div>
        </div>

        <!-- 確認密碼 -->
        <div class="form-group">
          <label>確認密碼</label>
          <div class="password-input">
            <input
              type="password"
              placeholder="請輸入密碼"
            />
            <button type="button" class="password-toggle">
              👁️
            </button>
          </div>
        </div>

        <!-- reCAPTCHA -->
        <div class="form-group">
          <label class="checkbox-container">
            <input type="checkbox" />
            我不是機器人
          </label>
        </div>

        <!-- 註冊按鈕 -->
        <button type="submit" class="register-btn">
          立即註冊
        </button>

        <!-- 登入連結 -->
        <div class="login-section">
          <span>已有帳號？</span>
          <a href="#" class="login-link">立即登入</a>
        </div>

        <!-- 分隔線 -->
        <div class="divider">
          <span>or</span>
        </div>

        <!-- 社群登入 -->
        <div class="social-login">
          <button type="button" class="social-btn google-btn">
            G+
          </button>
          <button type="button" class="social-btn facebook-btn">
            f
          </button>
          <button type="button" class="social-btn github-btn">
            🐙
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
// 目前只是靜態切版，無需任何邏輯
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-container {
  background: white;
  border-radius: 16px;
  width: 90%;
  max-width: 400px;
  max-height: 90vh;
  overflow-y: auto;
  position: relative;
  padding: 32px 24px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.close-btn {
  position: absolute;
  top: 16px;
  right: 16px;
  background: none;
  border: none;
  cursor: pointer;
  color: #666;
  font-size: 24px;
  padding: 8px;
  border-radius: 8px;
  line-height: 1;
}

.close-btn:hover {
  background-color: #f3f4f6;
}

.modal-title {
  font-size: 24px;
  font-weight: bold;
  text-align: center;
  margin-bottom: 32px;
  color: #111827;
}

.register-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  font-weight: 500;
  margin-bottom: 8px;
  color: #374151;
  font-size: 14px;
}

.form-group input {
  padding: 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 16px;
}

.form-group input:focus {
  outline: none;
  border-color: #3b82f6;
}

.form-group input::placeholder {
  color: #9ca3af;
}

.password-input {
  position: relative;
}

.password-toggle {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  color: #6b7280;
  font-size: 16px;
}

.checkbox-container {
  display: flex;
  align-items: center;
  cursor: pointer;
  font-size: 14px;
  color: #374151;
}

.checkbox-container input[type="checkbox"] {
  margin-right: 8px;
  width: 16px;
  height: 16px;
}

.register-btn {
  background-color: #111827;
  color: white;
  border: none;
  padding: 14px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 500;
  cursor: pointer;
}

.register-btn:hover {
  background-color: #1f2937;
}

.login-section {
  text-align: center;
  font-size: 14px;
  color: #6b7280;
}

.login-link {
  color: #3b82f6;
  text-decoration: underline;
  margin-left: 4px;
}

.login-link:hover {
  color: #2563eb;
}

.divider {
  position: relative;
  text-align: center;
  margin: 20px 0;
}

.divider::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 1px;
  background-color: #e5e7eb;
}

.divider span {
  background: white;
  padding: 0 16px;
  color: #9ca3af;
  font-size: 14px;
}

.social-login {
  display: flex;
  justify-content: center;
  gap: 16px;
}

.social-btn {
  width: 48px;
  height: 48px;
  border: 1px solid #e5e7eb;
  border-radius: 50%;
  background: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  color: #666;
}

.social-btn:hover {
  border-color: #d1d5db;
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.google-btn {
  color: #db4437;
}

.facebook-btn {
  color: #1877f2;
  font-size: 20px;
}

.github-btn {
  font-size: 20px;
}

@media (max-width: 480px) {
  .modal-container {
    margin: 16px;
    padding: 24px 20px;
  }
  
  .modal-title {
    font-size: 20px;
    margin-bottom: 24px;
  }
}
</style>