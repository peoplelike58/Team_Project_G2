// auth.js
import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    loading: false
  }),

  actions: {
    // 統一把後端回傳欄位轉成前端慣用鍵名
    normalizeUser(raw) {
      if (!raw) return null
      const src = raw.user || raw.member || raw
      return {
        id: src.MEMBER_ID ?? src.id ?? null,
        email: src.EMAIL ?? src.email ?? null,
        name: src.NAME ?? src.name ?? null,
        avatarUrl: src.IMAGE ?? src.avatar ?? null,
        status: src.STATUS ?? src.status ?? null
      }
    },

    /******************************************************************
     *  Google IdToken 登入（已停用）
     *  - 固定回傳 null，不觸發任何網路請求
     *  - 保留方法簽章，避免呼叫端 import/型別破壞
     ******************************************************************/
    async loginWithGoogleIdToken(_idToken) {
      console.warn('[auth] Google IdToken 登入已暫時停用')
      return null
    },

    /******************************************************************
     *  讀取目前登入狀態
     *  - 401/非 200：直接視為未登入，**不**解析 JSON
     *  - 非 JSON：回傳 null，避免 Unexpected token 錯誤
     ******************************************************************/
    async fetchMe() {
      try {
        const res = await fetch(import.meta.env.VITE_AJAX_URL + '/auth/me.php', {
          credentials: 'include'
        })

        if (!res.ok) {
          // 401/403/500 等：統一視為未登入
          this.user = null
          return null
        }

        const ct = res.headers.get('content-type') || ''
        if (!ct.includes('application/json')) {
          // 後端非 JSON（可能是 PHP Warning/HTML），避免 JSON.parse 直接報錯
          const text = await res.text()
          console.error('[auth] me.php 非 JSON 回應片段：', text.slice(0, 300))
          this.user = null
          return null
        }

        const data = await res.json()
        const normalized = this.normalizeUser(data)
        this.user = normalized
        return normalized
      } catch (e) {
        console.warn('[auth] fetchMe 失敗：', e)
        this.user = null
        return null
      }
    },

    /******************************************************************
     *  登出：即使後端失敗也會清空本地使用者狀態
     ******************************************************************/
    async logout() {
      try {
        await fetch(import.meta.env.VITE_AJAX_URL + '/auth/logout.php', {
          method: 'POST',
          credentials: 'include'
        })
      } finally {
        this.user = null
      }
    }
  }
})
