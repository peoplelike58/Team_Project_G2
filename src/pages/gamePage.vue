<template>
  <div id="app">
    <!-- 先顯示地圖場景，選完關卡才顯示遊戲 -->
    <MapScene 
      v-if="!activeLevel" 
      @startLevel="startLevel" 
    />

    <!-- 根據關卡類型載入不同遊戲 -->
    <PuzzleGame 
      v-if="activeLevel && activeLevel <= 2" 
      :level="activeLevel" 
      :timeLimit="TIME_LIMIT" 
      @fail="handleFail" 
      @success="handleSuccess" 
    />

    <QuizGame 
      v-if="activeLevel && activeLevel > 2" 
      :level="activeLevel" 
      :timeLimit="TIME_LIMIT" 
      @fail="handleFail" 
      @success="handleSuccess" 
    />

    <!-- 失敗後彈窗 -->
    <ResultModal 
      v-if="showModal" 
      :level="failedLevel" 
      @retry="retryLevel" 
      @showSolution="viewSolution" 
    />

    <!-- 解答面板 -->
    <SolutionPanel 
      v-if="showSolutionPanel" 
      :level="failedLevel" 
      @close="showSolutionPanel = false" 
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import MapScene from './components/MapScene.vue'
import PuzzleGame from './components/PuzzleGame.vue'
import QuizGame from './components/QuizGame.vue'
import ResultModal from './components/ResultModal.vue'
import SolutionPanel from './components/SolutionPanel.vue'

// 時限：3分鐘
const TIME_LIMIT = 3 * 60

// 目前關卡 (1–4)，null 表示尚未選擇
const activeLevel = ref(null)

// 失敗後用來傳給彈窗及解答的關卡
const failedLevel = ref(null)

// 彈窗顯示
const showModal = ref(false)

// 解答面板顯示
const showSolutionPanel = ref(false)

// 點地圖後啟動關卡
function startLevel(level) {
  activeLevel.value = level
}

// 遊戲失敗時觸發，顯示彈窗
function handleFail(level) {
  failedLevel.value = level
  showModal.value = true
}

// 遊戲成功時觸發，直接跳回地圖
function handleSuccess() {
  activeLevel.value = null
}

// 在彈窗點「重來」
function retryLevel() {
  showModal.value = false
  // 重新載入同一關卡
  const lvl = failedLevel.value
  activeLevel.value = null
  // 組件重建
  setTimeout(() => activeLevel.value = lvl, 0)
}

// 觀看解答
function viewSolution() {
  showModal.value = false
  showSolutionPanel.value = true
}
</script>
