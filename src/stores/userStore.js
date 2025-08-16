import { defineStore } from 'pinia'

export const useUserStore = defineStore('user', {
  state: () => ({
    name: '山友管理員',
    role: 'admin'
  }),
  actions: {
    setName(n) { this.name = n }
  }
})
