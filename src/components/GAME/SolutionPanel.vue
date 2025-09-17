<template>
  <div class="panel">
    <h3>關卡 {{ level }} 解答</h3>

    <!-- 拼圖完整圖 -->
    <img v-if="level<=2" :src="puzzleFullUrl" alt="solution" />

    <!-- 問答正確選項 -->
    <div v-else>
      <p>{{ question.q }}</p>
      <p>正確答案：{{ question.options[question.answer] }}</p>
    </div>

    <button @click="$emit('close')">關閉</button>
    </div>
  </template>

<script setup>
import { computed } from 'vue'
const props = defineProps({ level:Number })
// 隨機挑圖的結果要一致，直接重用 PuzzleGame 不暴露 randomIndex，故用 level 決定
const puzzleFullUrl = `/images/GAME/Game${props.level}.jpg`
const bank = [
  { q:'山上突遇大霧，應該？', options:['前進','等待','導航','求援'], answer:1 },
  { q:'失蹤時最重要？',     options:['留原地','呼喊','走動','揮旗'], answer:0 }
]
const question = computed(() => bank[props.level-3])
</script>

<style lang="scss" scoped>
.panel {
  position:fixed; top:10%; left:50%;
  transform:translateX(-50%);
  background:#fff; padding:1rem; border-radius:8px;
  box-shadow:0 4px 12px rgba(0,0,0,0.2); width:80%; max-width:400px;
  text-align:center;

  img { width:100%; margin-bottom:1rem; }
  button {
    padding:0.6rem 1.2rem; background:#dc3545; color:#fff;
    border:none; border-radius:4px; cursor:pointer;
  }
}
</style>
