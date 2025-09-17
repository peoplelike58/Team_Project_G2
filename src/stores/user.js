// stores/use.js-用戶相關：登入狀態、個人資料
/* options API */
import axios from 'axios'
const API_BASE = import.meta.env.VITE_AJAX_URL
import { defineStore } from 'pinia'           // 匯入定義 store 的 API
export const useUserStore = defineStore(      // 定義一個「使用者」store，並輸出成 hook 函式
  'user',                                     // 這個 store 的唯一 id（字串）；之後 DevTools/插件會用到
  {
    state: () => ({                           // state：集中放「可響應的資料狀態」
      // 基本登入資訊
      email: null,           //   例如登入 email（未登入就是 null）
      name:null,
      id:null,
      isLoggedIn:false,

      // 個人資料
      profile: {
        nickname: null,
        birthday: null,
        phone: null,
        address: null,
        aboutme: null
      },
    
      // 載入狀態
      loading: {
        profile: false,
        updating: false
      }
    }),

    actions: {                                 // actions：放「改變 state 的方法」（可含非同步)    
      // hydrateFromLocalStorage(){               //localStorage 方法：
      // localStorage 方法：每次導航前呼叫：把 localStorage 值「灌回」到 Pinia
      //this.email = localStorage.getItem('email') || null

      login(email, name,id) {                     //   自訂登入行為
        this.email =  email            
        this.name  = name
        this.id = id
        this.isLoggedIn = true
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
        this.clearProfile()
        /* 同步清掉 localStorage */                           
        // localStorage.removeItem('email')                                
        // localStorage.removeItem('userRole')                             
      },

      // ==== (Pei) F5重新整理後session灌回pinia,保持登入狀態 ====
      loginWithUser(user){
        if (!user) return
        this.id        = user.id ?? null
        this.email     = user.email ?? null
        this.name      = user.name ?? null
        // this.avatar = user.avatar ?? user.image ?? null
        this.isLoggedIn = !!this.id
      },

      // ==== (Pei) 讓其他pages可以使用user.hydrateFromSession() ====
      async hydrateFromSession(){
        try{
          const { data } = await axios.get(`${API_BASE}/CheckLogin.php`, { withCredentials: true })
          // 依CheckLoggin.php 裡的鍵名 isLogin、member
          const loggedIn = Boolean(data.isLogin)
          const member = data.member || null 

          if (loggedIn && member && member.id) {
            // 在 session[member] 裡就是子目錄 email/name/id，直接映射進 Pinia
            this.loginWithUser({
              id:    member.id,
              email: member.email,
              name:  member.name
              // avatar 尚未開發,待補
            })
          } else {
            this.logout()
          }
        }catch(e){
          console.error('hydrateFromSession failed', e)
          this.logout()
        }
      }
    }

    // actions: {
    //   clearProfile(){
    //     this.profile = { nickname:null, birthday:null, phone:null, address:null, aboutme:null }
    //   },

    //   // 保留舊簽名：login(email, name, id)
    //   login(email, name, id){
    //     this.email = email ?? null
    //     this.name  = name ?? null
    //     this.id    = id ?? null
    //     this.isLoggedIn = !!this.id
    //   },

    //   // 新增：一次塞後端回來的 user 物件
    //   loginWithUser(user){
    //     if (!user) return
    //     this.id        = user.id ?? null
    //     this.email     = user.email ?? null
    //     this.name      = user.name ?? user.nickname ?? null
    //     // this.avatar = user.avatar ?? user.image ?? null
    //     this.isLoggedIn = !!this.id
    //   },

    //   logout(){
    //     this.email = null
    //     this.name  = null
    //     this.id    = null
    //     this.avatarKey = null
    //     this.isLoggedIn = false
    //     this.clearProfile()
    //   },

    //   // 重新整理/進站時，向後端查 Session 並回灌
    //   async hydrateFromSession(){
    //     try{
    //       const { data } = await axios.get(`${API_BASE}/CheckLogin.php`, { withCredentials: true })

    //       const loggedIn = !!data?.isLogin            // ← 後端鍵名：isLogin
    //       const m = data?.member || null              // ← 後端鍵名：member

    //       if (loggedIn && m && m.id) {
    //         // 你同學在 session 裡就是 email/name/id，直接映射進 Pinia
    //         this.loginWithUser({
    //           id:    m.id,
    //           email: m.email,
    //           name:  m.name
    //           // avatar 你暫時不處理可先不傳
    //         })
    //       } else {
    //         this.logout()
    //       }
    //     }catch(e){
    //       console.error('hydrateFromSession failed', e)
    //       this.logout()
    //     }
    //   }

    // }

  }
)


/* composition API */

// import { ref } from 'vue'
// import { defineStore } from 'pinia'

// export const useUserStore = defineStore(
//   'user',
//   () => {
//     const email = ref(null)
//     const name = ref(null)
//     const isLoggedIn = ref(false)

//     function login(newEmail, newName) {
//       email.value = newEmail
//       name.value = newName
//       isLoggedIn.value = true
//       // localStorage.setItem('email', newEmail)
//       // localStorage.setItem('userRole', role)
//     }

//     function logout() {
//       email.value = null
//       name.value = null
//       isLoggedIn.value = false
//       // localStorage.removeItem('email')
//       // localStorage.removeItem('userRole')
//     }

//     return {
//       email,
//       name,
//       isLoggedIn,
//       login,
//       logout
//     }
//   }
// )

