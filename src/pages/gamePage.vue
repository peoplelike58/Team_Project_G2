<template>
  <div class="game-container">
    <!-- 返回地圖按鈕 -->
    <button 
      v-if="gameMode !== 'map'" 
      class="back-to-map-btn"
      @click="backToMap"
    >
      ← 返回地圖
    </button>
    
    <!-- 地圖場景 -->
    <MapScene 
      v-if="gameMode === 'map'" 
      @startLevel="startLevel" 
    />
    
    <!-- 拼圖遊戲 (關卡1-2) -->
    <PuzzleGame 
      v-else-if="gameMode === 'puzzle'"
      :level="currentLevel"
      :size="puzzleSize"
      :timeLimit="180"
      :showDebug="false"
      :showHintButton="true"
      @success="handleSuccess"
      @fail="handleFail"
      ref="puzzleGameRef"
    />
    
    <!-- 問答遊戲 (關卡3-4) -->
    <QuizGame 
      v-else-if="gameMode === 'quiz'"
      :level="currentLevel"
      :timeLimit="180"
      @success="handleSuccess"
      @fail="handleFail"
      ref="quizGameRef"
    />
    
    <!-- 成功彈窗 -->
    <SuccessModal 
      v-if="showSuccessModal"
      :level="currentLevel"
      @continue="continueToNext"
      @backToMap="backToMap"
    />
    
    <!-- 失敗彈窗 -->
    <ResultModal 
      v-if="showFailModal"
      :level="currentLevel"
      @retry="retryLevel"
      @showSolution="showSolution"
      @backToMap="backToMap"
    />
    
    <!-- 解答面板 -->
    <SolutionPanel 
      v-if="showSolutionPanel"
      :level="currentLevel"
      @close="closeSolution"
    />
  </div>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue'
import MapScene from '@/components/GAME/MapScene.vue'
import PuzzleGame from '@/components/GAME/PuzzleGame.vue'
import QuizGame from '@/components/GAME/QuizGame.vue'
import SuccessModal from '@/components/GAME/SuccessModal.vue'
import ResultModal from '@/components/GAME/ResultModal.vue'
import SolutionPanel from '@/components/GAME/SolutionPanel.vue'

// Emit 定義
const emit = defineEmits(['fail'])

// 遊戲狀態
const gameMode = ref('map') // 'map', 'puzzle', 'quiz'
const currentLevel = ref(1)
const showSuccessModal = ref(false)
const showFailModal = ref(false)
const showSolutionPanel = ref(false)

// 組件引用
const puzzleGameRef = ref(null)
const quizGameRef = ref(null)

const puzzleSize = computed(() => {
  const sizeMap = {
    1: 5, // 關卡1: 5x5
    2: 5  // 關卡2: 5x5
  }
  return sizeMap[currentLevel.value] || 5
})

// 開始關卡
function startLevel(level) {
  // console.log(`開始關卡 ${level}`)
  currentLevel.value = level
  
  // 重置所有彈窗狀態
  showSuccessModal.value = false
  showFailModal.value = false
  showSolutionPanel.value = false
  
  // 根據關卡決定遊戲模式
  if (level <= 2) {
    gameMode.value = 'puzzle'
  } else {
    gameMode.value = 'quiz'
  }
}

// 處理成功
function handleSuccess(data) {
  // console.log('關卡成功！', data)
  showSuccessModal.value = true
  showFailModal.value = false
}

// 處理失敗
function handleFail(level) {
  // console.log('關卡失敗！', level)
  showFailModal.value = true
  showSuccessModal.value = false
  
  // 發出失敗事件給父組件
  emit('fail', level)
}

// 重試關卡
async function retryLevel() {
  // console.log('重試關卡', currentLevel.value)
  showFailModal.value = false
  showSolutionPanel.value = false
  
  // 根據當前遊戲模式重置對應的遊戲
  await nextTick()
  
  if (gameMode.value === 'puzzle' && puzzleGameRef.value) {
    // 重置拼圖遊戲
    puzzleGameRef.value.resetGame()
  } else if (gameMode.value === 'quiz') {
    // 對於問答遊戲，重新載入整個組件
    const tempLevel = currentLevel.value
    gameMode.value = 'map'
    await nextTick()
    startLevel(tempLevel)
  }
}

// 顯示解答
function showSolution() {
  // console.log('顯示解答')
  showSolutionPanel.value = true
  showFailModal.value = false
  
  // 如果是拼圖遊戲，調用顯示解答方法
  if (gameMode.value === 'puzzle' && puzzleGameRef.value) {
    puzzleGameRef.value.showSolution()
  }
}

// 關閉解答面板
function closeSolution() {
  showSolutionPanel.value = false
}

// 繼續下一關
function continueToNext() {
  // console.log('繼續下一關')
  showSuccessModal.value = false
  
  if (currentLevel.value < 4) {
    startLevel(currentLevel.value + 1)
  } else {
    // 已經完成所有關卡，返回地圖
    backToMap()
  }
}

// 返回地圖
function backToMap() {
  // console.log('返回地圖')
  gameMode.value = 'map'
  showSuccessModal.value = false
  showFailModal.value = false
  showSolutionPanel.value = false
  currentLevel.value = 1
}

// 暴露給父組件的方法
defineExpose({
  startLevel,
  backToMap
})
</script>

<style lang="scss" scoped>
.game-container {
  width: 100%;
  height: 100%;
  position: relative;
  z-index: 1;
}

.back-to-map-btn {
  position: absolute;
  top: 20px;
  left: 20px;
  z-index: 100;
  padding: 10px 20px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: bold;
  cursor: pointer;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
  transition: all 0.3s ease;
  
  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
  }
  
  &:active {
    transform: translateY(0);
  }
}
</style>