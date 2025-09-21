<template>
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
        <!-- <input
          v-model="password"
          type="password"
          placeholder="請輸入密碼"
          class="input-field"
        /> -->
        <el-input
          v-model="password"
          type="password"
          placeholder="請輸入密碼"
          show-password
          
        /> 
      </div>

      <!-- 忘記密碼 -->
      <div class="forgot-password">
        <button @click="handleForgotPassword" class="forgot-btn">
          忘記密碼？
        </button>
      </div>

      <!-- 我不是機器人驗證(Yuki) -->
       <div class="captcha-wrapper">
         <div class="g-recaptcha" :data-sitekey="siteKey"></div>
       </div>

      <!-- 登入按鈕 -->
      <button class="login-btn" @click="handleLogin"  :disabled="!isFormValid">
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
      <div id="google-btn" class=" google"></div>
      
      <!-- <button type="button" class="social-btn facebook">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="#1877F2">
          <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
        </svg>
      </button>
      
      <button type="button" class="social-btn line">
        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="100" height="100" viewBox="0 0 48 48">
        <path fill="#00c300" d="M12.5,42h23c3.59,0,6.5-2.91,6.5-6.5v-23C42,8.91,39.09,6,35.5,6h-23C8.91,6,6,8.91,6,12.5v23C6,39.09,8.91,42,12.5,42z"></path><path fill="#fff" d="M37.113,22.417c0-5.865-5.88-10.637-13.107-10.637s-13.108,4.772-13.108,10.637c0,5.258,4.663,9.662,10.962,10.495c0.427,0.092,1.008,0.282,1.155,0.646c0.132,0.331,0.086,0.85,0.042,1.185c0,0-0.153,0.925-0.187,1.122c-0.057,0.331-0.263,1.296,1.135,0.707c1.399-0.589,7.548-4.445,10.298-7.611h-0.001C36.203,26.879,37.113,24.764,37.113,22.417z M18.875,25.907h-2.604c-0.379,0-0.687-0.308-0.687-0.688V20.01c0-0.379,0.308-0.687,0.687-0.687c0.379,0,0.687,0.308,0.687,0.687v4.521h1.917c0.379,0,0.687,0.308,0.687,0.687C19.562,25.598,19.254,25.907,18.875,25.907z M21.568,25.219c0,0.379-0.308,0.688-0.687,0.688s-0.687-0.308-0.687-0.688V20.01c0-0.379,0.308-0.687,0.687-0.687s0.687,0.308,0.687,0.687V25.219z M27.838,25.219c0,0.297-0.188,0.559-0.47,0.652c-0.071,0.024-0.145,0.036-0.218,0.036c-0.215,0-0.42-0.103-0.549-0.275l-2.669-3.635v3.222c0,0.379-0.308,0.688-0.688,0.688c-0.379,0-0.688-0.308-0.688-0.688V20.01c0-0.296,0.189-0.558,0.47-0.652c0.071-0.024,0.144-0.035,0.218-0.035c0.214,0,0.42,0.103,0.549,0.275l2.67,3.635V20.01c0-0.379,0.309-0.687,0.688-0.687c0.379,0,0.687,0.308,0.687,0.687V25.219z M32.052,21.927c0.379,0,0.688,0.308,0.688,0.688c0,0.379-0.308,0.687-0.688,0.687h-1.917v1.23h1.917c0.379,0,0.688,0.308,0.688,0.687c0,0.379-0.309,0.688-0.688,0.688h-2.604c-0.378,0-0.687-0.308-0.687-0.688v-2.603c0-0.001,0-0.001,0-0.001c0,0,0-0.001,0-0.001v-2.601c0-0.001,0-0.001,0-0.002c0-0.379,0.308-0.687,0.687-0.687h2.604c0.379,0,0.688,0.308,0.688,0.687s-0.308,0.687-0.688,0.687h-1.917v1.23H32.052z"></path>
        </svg>
      </button> -->
    </div>
  </div>
</template>

<script setup>
import { ref,computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/user'
import { useAuthStore } from '@/stores/auth'
// import CryptoJS from 'crypto-js' // 需要安裝: npm install crypto-js  （加密方式待考慮）

// 響應式數據
const email = ref('')
const password = ref('')
const router = useRouter()
const user = useUserStore()
const auth = useAuthStore()


// 表單驗證
const isFormValid = computed(() => {
   return email.value.includes('@') && password.value.length >= 8
})

// 密碼加密函數(待考慮)
// const encryptPassword = (password) => {
//   // 使用 SHA-256 雜湊（推薦用於密碼）
//   return CryptoJS.SHA256(password).toString(CryptoJS.enc.Hex)}

// Google reCAPTCHA 金鑰(Yuki)
const siteKey ="6LepsL4rAAAAACyRJsYbyJaL3v4XH-3RBGwhBJd-"

onMounted(() => {
  // 延遲渲染 reCAPTCHA，確保腳本已載入
  setTimeout(() => {
    if (window.grecaptcha && document.querySelector('.g-recaptcha')) {
      window.grecaptcha.render(document.querySelector('.g-recaptcha'), {
        'sitekey': siteKey
      })
    }
  })
});

// Google 登入
function initGoogleSignIn() {
  const gid = window.google?.accounts?.id
  if (!gid) return

  // 防呆：沒設 Client ID 直接警告
  if (!import.meta.env.VITE_GOOGLE_CLIENT_ID) {
    console.warn('VITE_GOOGLE_CLIENT_ID 未設定')
  }

  gid.initialize({
    client_id: import.meta.env.VITE_GOOGLE_CLIENT_ID,
    callback: async (resp) => {
      // 🟢 改用 auth store（內含重試與 cookie 設定）
      const u = await auth.loginWithGoogleIdToken(resp.credential)
      if (u) {
        // 登入成功才導頁（想去哪裡在這裡改）
        router.push({ name: 'member-profile' })
      } else {
        // 不急著 alert，避免「先 401 後成功」的誤報
        console.warn('Google 登入流程尚未完成，稍後再試')
      }
    },
    ux_mode: 'popup'
  })

  const el = document.getElementById('google-btn')
  if (el) {
    gid.renderButton(el, {
      type: 'standard',
      size: 'large',
      theme: 'outline',
      shape: 'pill'
    })
  }
}

// 載入 GIS 腳本（只載一次），載入完成後初始化
function loadGsiScriptThenInit() {
  if (window.google?.accounts?.id) {
    initGoogleSignIn()
    return
  }
  const existing = document.getElementById('gsi-client')
  if (existing) {
    existing.onload = initGoogleSignIn
    return
  }
  const script = document.createElement('script')
  script.id = 'gsi-client'
  script.src = 'https://accounts.google.com/gsi/client'
  script.async = true
  script.defer = true
  script.onload = initGoogleSignIn
  document.head.appendChild(script)
}

onMounted(() => {
  loadGsiScriptThenInit()
})

//修改加入機器人驗證版本 (Yuki)
const  handleLogin = async () => {
  if(email.value  && email.value.includes('@') && password.value && password.value.length >= 8  ){
    //先檢查reCAPTCHA
    const token = grecaptcha.getResponse();
    if(!token){
      alert("請先完成驗證！")
      return;
    }

    // 加密密碼（待考慮）
    // const encryptedPassword = encryptPassword(password.value)

    await fetch(import.meta.env.VITE_AJAX_URL + '/LoginPage_fontlogin.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({
        email: email.value,
        password: password.value,      // encryptedPassword（加密待考慮）
        recaptcha: token
      })
    })
      .then(res => res.json())
      .then(async(member) =>{
        const { success } = member;
        if(success){
           // 如果登入成功，再去檢查 Session
           return fetch(import.meta.env.VITE_AJAX_URL + '/CheckLogin.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' ,'Accept': 'application/json'},
            credentials: 'include'
          })
          .then(res => res.json())
          .then(sessionData => {
            if(sessionData && sessionData.isLogin){
              user.login(
                sessionData.member.email,
                sessionData.member.name,
                sessionData.member.id,
                sessionData.member.nickname,
                sessionData.member.phone,
                sessionData.member.address,
                sessionData.member.avatar
              )
              alert(`登入成功！歡迎 ${email.value}`)
              router.push({ name: 'member-profile' })
            }
          });
        }else {
        alert('帳號或密碼錯誤,請重新輸入');
        grecaptcha.reset(); // 重設 reCAPTCHA
      }
    });
  }else{
    alert('請填寫正確的登入資訊')
  }
}








//立即登入-按鈕(localstorage版)
// 「一般用戶登入」可以把登入資訊放在 localStorage 內，登出時要刪除
// const handleLogin = async () => {
//   if (email.value && password.value && email.value.includes('@') && password.value.length >= 8 ) {
//     const res = await fetch('/tjd102/g2/PHP/LoginPage_fontlogin.php',{//這是server上測試可用的URL：'/tjd102/g2/PHP/LoginPage_fontlogin.php'；http://localhost/teamproject/LoginPage_fontLogin.php 
//       method:'POST',
//       headers:{'Content-Type':'application/json'},
//       credentials: 'include' ,              // 查 Session 要帶 cookie
//       body:JSON.stringify({
//           email:email.value,
//           password:password.value
//       })  //前端把使用者輸入的資料打包成 JSON，送去後端
//     })
//     .then(resp=>resp.json())
//     .then(async(member) => {
//       const {success} = member;
//       alert(success)
//       if(success){
//           const sessionResp = await fetch('/tjd102/g2/PHP/CheckLogin.php', {  //http://localhost/teamproject/CheckLogin.php(lOCAL端測試)
//           method: 'POST',
//           headers:{'Content-Type':'application/json'},
//           credentials: 'include',              // Session 一樣要帶 cookie
//         });
//         const sessionData = await sessionResp.json();
//         if (sessionData.isLogin){
//           user.login(sessionData.member.emal,sessionData.member.name)
//           alert(`登入成功！歡迎 ${email.value}`)/*這個alert前面要加上判斷資料庫匹配成功的條件 */
//           router.push({ name: 'member-profile' })
//         }
//         // console.log(member.data);
//         // user.setMemberData(member.data.email,member.data.name)
//         // localStorage.setItem('email', email.value) //把email資訊存到localstorage
//         // localStorage.setItem('password',password.value)//把password的資料存到localstorage，實際操作時不會使用密碼
//         // console.log('立即登入')
//       }else {
//         alert('帳號或密碼錯誤,請重新輸入')
//       }}
//     );
//     } else{
//       alert('請填寫正確的登入資訊')
//   }
// }

//忘記密碼-按鈕
const handleForgotPassword = () => {
  console.log('忘記密碼')
  router.push({name:'loginregister-forgetpassword' })
}
//立即註冊-按鈕
const handleRegister = () => {
  console.log('立即註冊')
  router.push({name: 'loginregister-fontregister' })
}

//社群登入-按鈕
const handleSocialLogin = (provider) => {
  console.log(`使用 ${provider} 登入`)
  alert(`使用 ${provider} 登入`)
}




</script>

<style scoped lang="scss">
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';



/* 模態視窗內容 */
.modal-content {
  padding: 48px 32px;
   width: 90%;
  max-width: 440px;
  box-sizing: border-box;
  margin: auto;
}

/* 標題 */
.title {
  font-size: $pcFont-H2;
  font-weight: $semiBold;
  text-align: center;
  margin-bottom: 24px;
  color: #1f2937;
  margin-top: 0;
}

/* 表單容器 */
.form-container {
  margin-bottom: 24px;
}

/* 輸入群組 */
.input-group {
  margin-bottom: 20px;}
  /* 輸入標籤 */
.input-label {
  // display: block;
  // color: #374151;
  // margin-bottom: 12px;
  font-size: $pcFont-label;
  font-weight: $medium;
  color: $black-14;
  text-align: left;
  }
  /* 輸入欄位 */
//  .input-field , :deep(.el-input__wrapper){
//     width: 100%;
//     padding: 12px 16px;
//     background: transparent;
//     border: none;
//     border-bottom: 2px solid #ccc;
//     outline: none;
//     transition: border-color 0.2s;
//     box-sizing: border-box;
//     font-size: $pcFont-p-s;
//   }

//   .input-field::placeholder {
//     color: #999;
//   }

//   .input-field:focus {
//     border-bottom-color: #6b7280;
//   }
// }
// .el-input__wrapper:hover{
//   box-shadow:none;
// }
/* 只改 <el-input class="input-field" /> 這一個欄位 */
.input-field{
    width: 100%;
    padding: 12px 16px;
    background: transparent;
    border: none;
    border-bottom: 2px solid #ccc;
    outline: none;
    transition: border-color 0.2s;
    box-sizing: border-box;
    font-size: $pcFont-p-s;
    
  }
  /* 🟢 外框：透明 + 底線 */
  :deep(.el-input__wrapper){
    background-color: transparent;
    border-radius: 0;
    box-shadow: none;              /* 移除預設外框陰影 */
    border-bottom: 2px solid #ccc; /* 只留底線 */
    padding: 0 8px 0 0;                /* 給右側眼睛圖示留空間 */
    outline: none; 
    box-shadow: none; 
  }

  /* 🟢 內部輸入框 */
  :deep(.el-input__inner){
    background: transparent;
    box-shadow: none;
    height: 44px;
    padding: 12px 0;               /* 上下 12、左右 0 → 視覺同你圖片 */
    font-size: $pcFont-p-s;
    color: #111;
    &::placeholder{ color:#9ca3af; }
  

  /*  focus：底線變深，不出現陰影 */
  &:focus-within{
    :deep(.el-input__wrapper){
      border-bottom-color:#6b7280;
    
    }
  }}
  :deep(.el-input__wrapper.is-focus){ box-shadow:none !important; outline:none; }
  :deep(.el-input__wrapper:hover){ box-shadow: none; }
  :deep(.el-input__inner),  :deep(.el-input__inner:focus){
    outline:none; box-shadow:none; background:transparent;padding-left: 16px;
  }
  /* 密碼眼睛圖示間距/顏色 */
  :deep(.el-input__suffix){ padding-left: 6px; }
  :deep(.el-input__password){
    display:inline-flex; align-items:center; justify-content:center;
    width:24px; height:24px; color:#9ca3af;
  }
  :deep(.el-input__password:hover){ color:#6b7280; }

  /*  停用狀態 */
  :deep(.is-disabled .el-input__wrapper){
    border-bottom-color:#e5e7eb;
    background: transparent;
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
  width: 90%;
  background-color: $black-14;
  color: white;
  padding: 16px;
  border-radius: 8px;
  font-weight: $semiBold;
  font-size: $pcFont-p-s;
  border: none;
  cursor: pointer;
  transition: background-color 0.3s;
  display: block;
  margin: 24px auto 0;

  &:hover:not(:disabled) {
  background-color: lighten($black-14, 10%);
  }
  
  &:disabled {
    background-color: #ccc;
    cursor: not-allowed;
  }
}


/* 去註冊區域 */
.register-section {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 40px;
  margin-top: 32px;
}

.register-text {
  color: #4b5563;
}

.register-btn {
  color: $black-14;
  font-weight: $medium;
  line-height: $lineHeight-p-200;
  border: none;
  background: none;
  cursor: pointer;
  border-bottom: 1px solid $black-14;
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
  border-color: $ash-olive-400;
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
<style lang="scss">

// 機器人驗證
.captcha-wrapper{
  display: flex;
  justify-content: center;
  min-height: 80px;
  z-index: 100000;
}
</style>