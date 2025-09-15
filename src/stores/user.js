// stores/use.js-用戶相關：登入狀態、個人資料
/* options API */
import { defineStore } from 'pinia'           // 匯入定義 store 的 API
export const useUserStore = defineStore(      // 定義一個「使用者」store，並輸出成 hook 函式
  'user',                                     // 這個 store 的唯一 id（字串）；之後 DevTools/插件會用到
  {
    state: () => ({                           // state：集中放「可響應的資料狀態」
      email: null,           //   例如登入 email（未登入就是 null）
      name:null,
      id:null,
      isLoggedIn:false
    }),
    actions: {                                 // actions：放「改變 state 的方法」（可含非同步)    
      // hydrateFromLocalStorage(){               //localStorage 方法：
      // localStorage 方法：每次導航前呼叫：把 localStorage 值「灌回」到 Pinia
      //this.email = localStorage.getItem('email') || null

      login(email, name,id) {                     //   自訂登入行為
        this.email =  email            
        this.name  = name
        this.id = id
        console.log(this.id)
        this.isLoggedIn = true
        /* 把狀態寫回 localStorage，刷新不會掉 */            
        // localStorage.setItem('email', email)                           
        // localStorage.setItem('userRole', role)                         
      },
      logout() {                               //   自訂登出行為
        this.email = null
        this.name = null
        this.id = null
        this.isLoggedIn = false
        /* 同步清掉 localStorage */                           
        // localStorage.removeItem('email')                                
        // localStorage.removeItem('userRole')                             
      }
    }
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

