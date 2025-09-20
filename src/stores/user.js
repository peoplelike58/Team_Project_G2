// stores/use.js-用戶相關：登入狀態、個人資料
/* options API */
import { defineStore } from 'pinia'           // 匯入定義 store 的 API
export const useUserStore = defineStore(      // 定義一個「使用者」store，並輸出成 hook 函式
  'user',                                     // 這個 store 的唯一 id（字串）；之後 DevTools/插件會用到
  {
    state: () => ({                           // state：集中放「可響應的資料狀態」
      // 基本登入資訊
      id:null,
      email: null,           //   例如登入 email（未登入就是 null）
      name:null,
      isLoggedIn:false,

      // 個人資料
      profile: {
        nickname: null,
        birthday: null,
        phone: null,
        address: null,
        aboutme: null,
        avatar: null,          // 頭像檔名（從資料庫讀取）
        avatarUrl: null        // 完整的頭像 URL 路徑
      },
    
      // 載入狀態
      loading: {
        profile: false,
        updating: false,
        loginChecking: false,  // 檢查登入狀態的載入狀態
        uploadingAvatar: false //頭像上傳載入狀態
      }
    }),

    actions: {                                 // actions：放「改變 state 的方法」（可含非同步)    
      // //localStorage 方法：
      // localStorage 方法：每次導航前呼叫：把 localStorage 值「灌回」到 Pinia
      //this.email = localStorage.getItem('email') || null
      async hydrateFromSession(){                    // 從伺服器檢查登入狀態（頁面刷新時呼叫）
        // 如果已經在檢查中，就不重複檢查
        if (this.loading.loginChecking) return
        this.loading.loginChecking = true

        try {
          const response = await fetch(import.meta.env.VITE_AJAX_URL + '/CheckLogin.php', {
            method: 'POST',
            headers: { 
              'Content-Type': 'application/json',
              'Accept': 'application/json'
            },
            credentials: 'include'  // 帶上 cookie，讓伺服器檢查 session
          })
          
          const sessionData = await response.json()
          
          // 如果伺服器回傳已登入狀態，就更新 Pinia 狀態
          if (sessionData && sessionData.isLogin && sessionData.member)  {
            this.email = sessionData.member.email
            this.name = sessionData.member.name
            this.id = sessionData.member.id
            this.isLoggedIn = true
            this.profile.nickname = sessionData.member.nickname
            this.profile.phone = sessionData.member.phone
            this.profile.address = sessionData.member.address
            this.profile.avatar = sessionData.member.avatar
            console.log('從 session 恢復登入狀態:', sessionData.member)
          } else {
            // 伺服器沒有登入狀態，清除本地狀態
            this.logout()
          }
        } catch (error) {
        console.error('檢查登入狀態失敗:', error)
        // 發生錯誤時，為了安全起見，清除登入狀態
        this.logout()
        } finally {
          this.loading.loginChecking = false
        }
      } ,
      login(email, name,id,nickname,phone,address,avatar) {                     //   自訂登入行為
      this.email =  email            
      this.name  = name
      this.id = id
      this.isLoggedIn = true
      this.profile.nickname = nickname
      this.profile.phone = phone
      this.profile.address = address
      this.profile.avatar = avatar

      console.log('登入成功，用戶ID:', this.id)
      /* 把狀態寫回 localStorage，刷新不會掉 */            
      // localStorage.setItem('email', email)                           
      // localStorage.setItem('userRole', role)                         
    },
      logout() {                               //   自訂登出行為
        this.email = null
        this.name = null
        this.id = null
        this.isLoggedIn = false
        this.profile.nickname = null
        this.profile.phone = null
        this.profile.address = null
        this.profile.avatar = null
        this.loading.loginChecking = false 
        // this.clearProfile()
        /* 同步清掉 localStorage */                           
        // localStorage.removeItem('email')                                
        // localStorage.removeItem('userRole')                             
      },
      // 更新個人資料（包含頭像）
      updateProfile(profileData) {
        // 更新個人資料狀態
        Object.assign(this.profile, profileData)
        
        
        // 如果有頭像檔名，生成完整 URL
        if (this.profile.avatar) {
          // 根據環境判斷路徑格式：開發環境用 /uploads，生產環境用 uploads
          const basePath = import.meta.env.MODE === 'development' ? '/uploads' : 'uploads'
          this.profile.avatarUrl = `${basePath}/avatars/${this.profile.avatar}`
        }
        return `${import.meta.env.BASE_URL}images/Products/default-avatar.jpg`
      },

      // 更新頭像檔名
      updateAvatar(filename) {
        this.profile.avatar = filename
        // 根據環境判斷路徑格式：開發環境用 /uploads，生產環境用 uploads
        const basePath = import.meta.env.MODE === 'development' ? '/uploads' : 'uploads'
        this.profile.avatarUrl = `${basePath}/avatars/${filename}`
      }
    },              
  }
)
