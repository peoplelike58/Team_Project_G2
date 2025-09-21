import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        loading: false
    }),

    actions: {
// async loginWithGoogleIdToken(idToken) {
//     this.loading = true
//     try {
//         const postToken = async (idToken) => {
//             const res = await fetch(import.meta.env.VITE_AJAX_URL + '/auth/google_idtoken.php', {
//               method: 'POST',
//               headers: { 'Content-Type': 'application/json' },
//               credentials: 'include',
//               body: JSON.stringify({ idToken })
//             })
//             if (!res.ok) throw new Error('google-idtoken ' + res.status)
          
//             const ct = res.headers.get('content-type') || ''
//             if (!ct.includes('application/json')) {
//               const text = await res.text()
//               console.error('[google-idtoken 非 JSON 回應片段]', text.slice(0, 300))
//               throw new Error('NON_JSON_RESPONSE')
//             }
//             return res.json()
//         }          
  
//       // 第一次打失敗，等 500ms 再打一次（常見 session/網路抖動）
//       let data
//       try {
//         data = await postToken(idToken)
//       } catch (e) {
//         console.warn('第一次 Google 登入請求失敗，500ms 後重試...', e)
//         await new Promise(r => setTimeout(r, 500))
//         data = await postToken(idToken)
//       }
  
//       const raw = (data && (data.user || data.member)) || null
//       if (!raw) throw new Error('NO_MEMBER_IN_RESPONSE')
  
//       // 正規化
//       const normalized = {
//         id: raw.MEMBER_ID ?? raw.id ?? null,
//         email: raw.EMAIL ?? raw.email ?? null,
//         name: raw.NAME ?? raw.name ?? null,
//         avatarUrl: raw.IMAGE ?? raw.avatar ?? null,
//         status: raw.STATUS ?? raw.status ?? null
//       }
//       this.user = normalized
  
//       // 給 PHP 寫 session 一點時間，再向後端確認
//       await new Promise(r => setTimeout(r, 300))
//       await this.fetchMe()   // 若 me.php 回 200 會把 this.user 再校正一次
  
//       return this.user
//     } catch (err) {
//       console.warn('loginWithGoogleIdToken 失敗:', err)
//       this.user = null
//       return null
//     } finally {
//       this.loading = false
//     }
//   }
//   , 

        // async fetchMe() {
        //     try {
        //         const res = await fetch(import.meta.env.VITE_AJAX_URL + '/auth/me.php', {
        //             credentials: 'include'
        //         })
          
        //         // 401/非 200：不要去 parse JSON，直接視為未登入
        //         if (!res.ok) {
        //             this.user = null
        //             return null
        //         }
          
        //         // 只有 200 再 parse
        //         const data = await res.json()
        //         this.user = data || null
        //         return this.user
        //         } catch {
        //             this.user = null
        //             return null
        //         }
        // },

        async logout() {
            try {
                await fetch(import.meta.env.VITE_AJAX_URL + '/auth/logout.php', {
                    method: 'POST',
                    credentials: 'include'
                })
            } finally {
                this.user = null
            }
        },

        // 把後端回傳的欄位轉成前端統一的格式
        normalizeUser(raw) {
            return {
                id: raw.MEMBER_ID ?? raw.id ?? null,
                email: raw.EMAIL ?? raw.email ?? null,
                name: raw.NAME ?? raw.name ?? null,
                avatarUrl: raw.IMAGE ?? raw.avatar ?? null,
                status: raw.STATUS ?? raw.status ?? null
            }
        }
    }
})
