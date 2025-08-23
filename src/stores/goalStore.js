import { defineStore } from "pinia"

export const useGoalStore = defineStore("goalStore", {
  state: () => ({
    goals: JSON.parse(localStorage.getItem("myGoals") || "[]")
  }),

  actions: {
    initDefault() {
      if (this.goals.length === 0) {
        // ✅ 第一次初始化
        this.goals = [
          { kind: "大百岳", done: 0, goal: 10, openSetgoal: false },
          { kind: "小百岳", done: 0, goal: 10, openSetgoal: false }
        ]
      } else {
        // ✅ 從 localStorage 還原時，強制 reset openSetgoal = false
        this.goals = this.goals.map(g => ({
          ...g,
          openSetgoal: false
        }))
      }
    },

    save() {
      // ✅ 存的時候不包含 openSetgoal，避免 F5 自動彈窗
      const pureGoals = this.goals.map(g => ({
        kind: g.kind,
        done: g.done,
        goal: g.goal
      }))
      localStorage.setItem("myGoals", JSON.stringify(pureGoals))
    },

    addDone(kind) {
      const target = this.goals.find(g => g.kind === kind)
      if (target) {
        target.done++
        this.save()
      }
    },

    updateGoal(kind, newGoal) {
      const target = this.goals.find(g => g.kind === kind)
      if (target) {
        target.goal = newGoal
        this.save()
      }
    },
    loadFromStorage() {
      const stored = JSON.parse(localStorage.getItem("myGoals") || "[]")
      if (stored.length > 0) {
        this.goals = stored.map(g => ({
          ...g,
          openSetgoal: false   // 保證不會因為 F5 自動開視窗
        }))
      }
    }
  }
})