<template>
  <div class="modal-content">
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
              <path v-if="showPassword" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2"/>
              <circle v-if="showPassword" cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
              <path v-if="!showPassword" d="m1 1 22 22M9.88 9.88a3 3 0 1 0 4.24 4.24M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 11 8 11 8a13.16 13.16 0 0 1-1.67 2.68" stroke="currentColor" stroke-width="2"/>
              <path v-if="!showPassword" d="M6.61 6.61A13.526 13.526 0 0 0 1 12s4 8 11 8a9.74 9.74 0 0 0 5.39-1.61" stroke="currentColor" stroke-width="2"/>
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
            <path v-if="showConfirmPassword" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2"/>
            <circle v-if="showConfirmPassword" cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
            <path v-if="!showConfirmPassword" d="m1 1 22 22M9.88 9.88a3 3 0 1 0 4.24 4.24M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 11 8 11 8a13.16 13.16 0 0 1-1.67 2.68" stroke="currentColor" stroke-width="2"/>
            <path v-if="!showConfirmPassword" d="M6.61 6.61A13.526 13.526 0 0 0 1 12s4 8 11 8a9.74 9.74 0 0 0 5.39-1.61" stroke="currentColor" stroke-width="2"/>
          </svg>
          </button>
        </div>
      </div>
    
      <!-- reCAPTCHA -->
      <!-- <div class="captcha-group">
        <label class="captcha-checkbox">
          <input type="checkbox" v-model="formData.isNotRobot" required>
          <span class="checkmark"></span>
          我不是機器人
        </label>
      </div> -->


      <!-- 我不是機器人驗證 -->
      <div class="captcha-wrapper">
         <div class="g-recaptcha" :data-sitekey="siteKey"></div>
      </div>

      
      <!-- 立即註冊按鈕 -->
      <button type="button" class="submit-btn" :disabled="!isFormValid" @click="GoRegister">
        <!-- 暫時沒有認證，直接註冊成功 -->
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
        <div class="divider-line"></div>
        <span class="divider-text">or</span>
        <div class="divider-line"></div>
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
        
        <button type="button" class="social-btn line">
          <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="100" height="100" viewBox="0 0 48 48">
          <path fill="#00c300" d="M12.5,42h23c3.59,0,6.5-2.91,6.5-6.5v-23C42,8.91,39.09,6,35.5,6h-23C8.91,6,6,8.91,6,12.5v23C6,39.09,8.91,42,12.5,42z"></path><path fill="#fff" d="M37.113,22.417c0-5.865-5.88-10.637-13.107-10.637s-13.108,4.772-13.108,10.637c0,5.258,4.663,9.662,10.962,10.495c0.427,0.092,1.008,0.282,1.155,0.646c0.132,0.331,0.086,0.85,0.042,1.185c0,0-0.153,0.925-0.187,1.122c-0.057,0.331-0.263,1.296,1.135,0.707c1.399-0.589,7.548-4.445,10.298-7.611h-0.001C36.203,26.879,37.113,24.764,37.113,22.417z M18.875,25.907h-2.604c-0.379,0-0.687-0.308-0.687-0.688V20.01c0-0.379,0.308-0.687,0.687-0.687c0.379,0,0.687,0.308,0.687,0.687v4.521h1.917c0.379,0,0.687,0.308,0.687,0.687C19.562,25.598,19.254,25.907,18.875,25.907z M21.568,25.219c0,0.379-0.308,0.688-0.687,0.688s-0.687-0.308-0.687-0.688V20.01c0-0.379,0.308-0.687,0.687-0.687s0.687,0.308,0.687,0.687V25.219z M27.838,25.219c0,0.297-0.188,0.559-0.47,0.652c-0.071,0.024-0.145,0.036-0.218,0.036c-0.215,0-0.42-0.103-0.549-0.275l-2.669-3.635v3.222c0,0.379-0.308,0.688-0.688,0.688c-0.379,0-0.688-0.308-0.688-0.688V20.01c0-0.296,0.189-0.558,0.47-0.652c0.071-0.024,0.144-0.035,0.218-0.035c0.214,0,0.42,0.103,0.549,0.275l2.67,3.635V20.01c0-0.379,0.309-0.687,0.688-0.687c0.379,0,0.687,0.308,0.687,0.687V25.219z M32.052,21.927c0.379,0,0.688,0.308,0.688,0.688c0,0.379-0.308,0.687-0.688,0.687h-1.917v1.23h1.917c0.379,0,0.688,0.308,0.688,0.687c0,0.379-0.309,0.688-0.688,0.688h-2.604c-0.378,0-0.687-0.308-0.687-0.688v-2.603c0-0.001,0-0.001,0-0.001c0,0,0-0.001,0-0.001v-2.601c0-0.001,0-0.001,0-0.002c0-0.379,0.308-0.687,0.687-0.687h2.604c0.379,0,0.688,0.308,0.688,0.687s-0.308,0.687-0.688,0.687h-1.917v1.23H32.052z"></path>
          </svg>
        </button>
      </div>
    </form>
  </div>

</template>

<script setup>
// import member from '@/router/member'
import { ref, computed, onMounted} from 'vue'
import { useRouter } from 'vue-router'


// Google reCAPTCHA 金鑰(Yuki)
const siteKey ="6LepsL4rAAAAACyRJsYbyJaL3v4XH-3RBGwhBJd-"



const router = useRouter()

// 表單資料
const formData = ref({
  email: '',
  name: '',
  phone: '',
  password: '',
  confirmPassword: '',
  // isNotRobot: false
})

//機器人驗證(Yuki)
const recaptchaToken = ref('')
const isNotRobot = computed(() => recaptchaToken.value !=='')



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
         isNotRobot.value
})

//掛載機器人(Yuki)
onMounted(() => {
  const interval = setInterval(()=>{
    if(window.grecaptcha && document.querySelector('.g-recaptcha')){
      window.grecaptcha.render(document.querySelector('.g-recaptcha'),{
        sitekey: siteKey,
        callback: (token) => {
          recaptchaToken.value = token
        }
      })
      clearInterval(interval)
    }
  })
})



// 方法
const togglePassword = () => {
  showPassword.value = !showPassword.value
}

const toggleConfirmPassword = () => {
  showConfirmPassword.value = !showConfirmPassword.value
}


const switchToLogin = () => {
  // 切換到登入模式
  router.push({name:'loginregister-fontrelogin'})
}

const GoRegister = () => {
  //機器人驗證檢查(Yuki)
  if(!recaptchaToken.value){
    alert("請先完成驗證！")
    return;
  }



  fetch(import.meta.env.VITE_AJAX_URL + '/LoginPage_register.php', {   //http://localhost/teamproject/LoginPage_register.php（local端測試網址）
  method: 'POST',
  headers:{'Content-Type':'application/json'},
  credentials: 'include',
  body:JSON.stringify({
    email:formData.value.email,
    name:formData.value.name,
    password:formData.value.password,
    phone:formData.value.phone,
    recaptcha: recaptchaToken.value   // 把 token 傳給後端
  })  //前端把使用者輸入的資料打包成 JSON，送去後端
  })
  .then(resp=>resp.json())
  .then(register => {
    const {success,message} = register;
    alert(message);
    if(success){
      //暫時不認證，直接成功,就直接使用這裡
      router.push({name:'loginregister-registercoupon'})
    }else{
      window.grecaptcha.reset() // 失敗的話要重置機器人驗證(Yuki)
      recaptchaToken.value = ''
    }
  })
  
}

</script>

<style lang="scss" scoped>
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';



.modal-content {
  padding: 48px 32px;
  width: 90%;
  max-width: 440px;
  box-sizing: border-box;
  margin: auto;
  
  @media (max-width: 768px) {
    padding: 32px 24px;
    width: 95%;
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
  @include flexcenter(0px, column);
  align-items: stretch;
  
  label {
    font-size: $pcFont-label;
    font-weight: $medium;
    color: $black-14;
    text-align: left;
    // margin-bottom: 12px;
  }
  
  input {
    padding: 12px 16px;
    background: transparent;
    border: none;
    border-bottom: 2px solid #ccc;
    outline: none;
    font-size: $pcFont-p-s;
    transition: border-color 0.2s ease;
    
    &:focus {
    border-bottom-color: #6b7280;
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
  @include flexcenter(40px, row);
  font-size: $pcFont-p-s;
  color: #666;
  margin-top: 16px;
  
  .login-link {
    border: none;
    @include btn(0);
    color: $black-14;
    font-weight: $medium;
    line-height: $lineHeight-p-200;
    border-bottom: 1px solid $black-14;
    transition: color 0.3s;
    
    &:hover {
      color: #374151;
    }
  }
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
  
  &.line:hover {
    border-color: #00C300;
  }
}
</style>

<style lang="scss">

// 機器人驗證(Yuki)
.captcha-wrapper{
  display: flex;
  justify-content: center;
  min-height: 80px;
  z-index: 100000;
}
</style>