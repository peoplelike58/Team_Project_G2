<template>   
  <div class="modal-content">
    <h1 class="title">忘記密碼</h1>
    <p class="subtitle">請輸入您註冊時使用的電子郵件地址，我們將發送重設密碼的連結給您。</p>
    
    <form class="form-container">
      <div class="input-group">
        <label for="fp-email" class="input-label">帳號</label>
        <div class="input-wrapper">
          <input
            id="fp-email"
            type="email"
            class="input-field"
            placeholder="請輸入電子郵件"
          />
        </div>
      </div>
      
      <div class="input-group">
        <label class="input-label">驗證</label>
        <div class="checkbox-wrapper">
          <input id="isHuman" type="checkbox" class="checkbox-input" />
          <label for="isHuman" class="checkbox-label">我不是機器人</label>
        </div>
      </div>
      
      <div class="action-row">
        <button type="submit" class="primary-btn" @click="gochek">立即發送</button>
        <button type="button" class="secondary-btn" @click="backToLogin">返回登入</button>
      </div>
    </form>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router'

const router = useRouter()
const backToLogin = ()=>{
  router.push({name:'loginregister-fontrelogin' })
}
const gochek = () =>{
  router.push({name:'loginregister-forgetsend' })
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
</style>