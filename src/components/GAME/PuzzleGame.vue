<template>
  <div class="puzzle-game">
    <h2>拼圖關卡 {{ level }}</h2>

    <!-- 倒數計時 -->
    <div class="timer">剩餘時間：{{ formattedTime }}</div>

    <!-- 拼圖區塊 -->
    <div class="grid">
      <div 
        v-for="(piece, idx) in pieces" 
        :key="piece.id"
        :class="['cell', selected === idx ? 'selected' : '']"
        @click="onPieceClick(idx)"
        :style="{ backgroundImage: `url(${imgUrl})`, backgroundPosition: piece.pos }"
      ></div>
    </div>

    <!-- 碼流：若拼完自動觸發成功 -->
  </div>
</template>

<script setup>
import { ref, watchEffect, onMounted, onUnmounted } from 'vue'

// 從父層傳入關卡號與秒數
const props = defineProps({
  level: Number,
  timeLimit: Number
})

// 原始圖片路徑
const imgUrl = new URL(`@/assets/puzzles/puzzle${props.level}.jpg`, import.meta.url).href

// 拆成 3x3 共 9 片
const size = 3
const pieces = ref([])     // 每片 { id, pos, correctIndex }
let timer = null
const timeLeft = ref(props.timeLimit)
const selected = ref(null) // 紀錄使用者第一次點的格子 index

// 初始化：生成正確順序，再打亂
function initPieces() {
  const arr = []
  for (let i = 0; i < size * size; i++) {
    const row = Math.floor(i / size)
    const col = i % size
    arr.push({
      id: i,
      pos: `-${col * 100 / (size - 1)}% -${row * 100 / (size - 1)}%`,
      correctIndex: i
    })
  }
  // 洗牌
  pieces.value = arr
    .map(v => ({ ...v }))
    .sort(() => Math.random() - 0.5)
}

// 監聽時間到達
watchEffect(() => {
  if (timeLeft.value <= 0) {
    clearInterval(timer)
    // 通知父層失敗
    emit('fail', props.level)
  }
})

// 點擊交換邏輯
function onPieceClick(idx) {
  if (selected.value === null) {
    selected.value = idx
    return
  }
  // 交換位置
  const a = pieces.value[selected.value]
  pieces.value[selected.value] = pieces.value[idx]
  pieces.value[idx] = a
  selected.value = null

  // 檢查是否完成
  if (pieces.value.every((p, i) => p.correctIndex === i)) {
    clearInterval(timer)
    emit('success')
  }
}

// 格式化倒數文字
const formattedTime = computed(() => {
  const m = Math.floor(timeLeft.value / 60).toString().padStart(2, '0')
  const s = (timeLeft.value % 60).toString().padStart(2, '0')
  return `${m}:${s}`
})

// 啟動倒數
onMounted(() => {
  initPieces()
  timer = setInterval(() => timeLeft.value--, 1000)
})
onUnmounted(() => clearInterval(timer))
</script>

<style lang="scss" scoped>
.puzzle-game {
  text-align: center;
  .timer { margin-bottom: 1rem; font-size: 1.2rem; }

  .grid {
    display: grid;
    grid-template-columns: repeat(3, 120px);
    grid-template-rows: repeat(3, 120px);
    gap: 4px;
    justify-content: center;
  }

  .cell {
    width: 120px;
    height: 120px;
    background-size: 300% 300%;
    border: 2px solid #ccc;
    cursor: pointer;
    &.selected { border-color: #007bff; }
  }
}
</style>
