import { defineStore } from 'pinia'           // 匯入定義 store 的 API

export const useUserStore = defineStore(      // 定義一個「使用者」store，並輸出成 hook 函式
  'user',                                     // 這個 store 的唯一 id（字串）；之後 DevTools/插件會用到
  {
    state: () => ({                           // state：集中放「可響應的資料狀態」
      email: null,                            //   例如登入 token=email（未登入就是 null）
      role: 'guest'                          //   使用者角色，預設 guest
    }),
    actions: {                                // actions：放「改變 state 的方法」（可含非同步)
        hydrateFromLocalStorage(){            // 每次導航前呼叫：把 localStorage 值「灌回」到 Pinia
        this.token = localStorage.getItem('token')
        this.role = localStorage.getItem('userRole') || 'guest'
    },
      login(email, role) {                    //   自訂登入行為
        this.email = email                    //   使用 this 直接改當前 store 的狀態
        this.role = role
        },
      logout() {                              //   自訂登出行為
        this.email = null
        this.role = 'guest'
      }
    }
    // （可選）getters: {...}                 // getters：像 computed，從 state 派生資料
  }
)
