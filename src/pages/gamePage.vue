<template>
  <div id="app" class="game-app">
    <!-- 載入中狀態 -->
    <div v-if="isLoading" class="loading">
      <div class="spinner"></div>
      <p>載入中...</p>
    </div>

    <!-- 1) 地圖場景：未選關時顯示 -->
    <MapScene 
      v-if="!activeLevel && !isLoading" 
      @startLevel="startLevel" 
    />

    <!-- 2) 拼圖關卡：level 1,2 -->
    <PuzzleGame 
      v-if="activeLevel && activeLevel <= 2" 
      :key="`puzzle-${activeLevel}-${gameKey}`"
      :level="activeLevel" 
      :timeLimit="TIME_LIMIT" 
      :showDebug="isDevelopment"
      @fail="handleFail" 
      @success="handleSuccess" 
      ref="puzzleGameRef"
    />

    <!-- 3) 問答關卡：level 3,4 -->
    <QuizGame 
      v-if="activeLevel && activeLevel > 2" 
      :key="`quiz-${activeLevel}-${gameKey}`"
      :level="activeLevel" 
      :timeLimit="TIME_LIMIT" 
      @fail="handleFail" 
      @success="handleSuccess" 
      ref="quizGameRef"
    />

    <!-- 4) 失敗彈窗 -->
    <ResultModal 
      v-if="showModal" 
      :level="failedLevel" 
      @retry="retryLevel" 
      @showSolution="viewSolution" 
    />

    <!-- 5) 解答面板 -->
    <SolutionPanel 
      v-if="showSolutionPanel" 
      :level="failedLevel" 
      @close="closeSolution" 
    />

    <!-- 返回地圖按鈕 -->
    <button 
      v-if="activeLevel" 
      class="back-to-map-btn"
      @click="confirmBackToMap"
    >
      ← 返回地圖
    </button>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import MapScene from '@/components/GAME/MapScene.vue'
import PuzzleGame from '@/components/GAME/PuzzleGame.vue'
import QuizGame from '@/components/GAME/QuizGame.vue'
import ResultModal from '@/components/GAME/ResultModal.vue'
import SolutionPanel from '@/components/GAME/SolutionPanel.vue'

// 常數設定
const TIME_LIMIT = 3 * 60 // 3分鐘

// 計算屬性
const isDevelopment = computed(() => {
  try {
    return import.meta.env?.DEV || false
  } catch {
    return false
  }
})

// 響應式狀態
const activeLevel = ref(null)
const failedLevel = ref(null)
const showModal = ref(false)
const showSolutionPanel = ref(false)
const isLoading = ref(false)
const gameKey = ref(Date.now())

// 組件引用
const puzzleGameRef = ref(null)
const quizGameRef = ref(null)

// 遊戲統計
const gameStats = ref({
  completedLevels: [],
  attempts: {},
  totalTime: 0
})

// 函數定義

// 1. 開始關卡
async function startLevel(level) {
  try {
    isLoading.value = true
    console.log(`開始關卡 ${level}`)
    
    // 模擬載入時間
    await new Promise(resolve => setTimeout(resolve, 300))
    
    activeLevel.value = level
    gameKey.value = Date.now()
    
    // 記錄嘗試次數
    if (!gameStats.value.attempts[level]) {
      gameStats.value.attempts[level] = 0
    }
    gameStats.value.attempts[level]++
    
  } catch (error) {
    console.error('開始關卡時發生錯誤:', error)
  } finally {
    isLoading.value = false
  }
}

// 2. 遊戲成功
function handleSuccess() {
  console.log(`關卡 ${activeLevel.value} 完成！`)
  
  // 記錄完成的關卡
  if (!gameStats.value.completedLevels.includes(activeLevel.value)) {
    gameStats.value.completedLevels.push(activeLevel.value)
  }
  
  // 延遲顯示成功提示
  setTimeout(() => {
    const currentLevel = activeLevel.value
    const nextLevel = currentLevel + 1
    
    if (nextLevel <= 4) {
      if (confirm(`🎉 關卡 ${currentLevel} 完成！\n\n是否繼續下一關？`)) {
        startLevel(nextLevel)
      } else {
        backToMap()
      }
    } else {
      alert('🎉 恭喜完成所有關卡！')
      backToMap()
    }
  }, 1000)
}

// 3. 遊戲失敗
function handleFail(level) {
  console.log(`關卡 ${level} 失敗`)
  failedLevel.value = level
  showModal.value = true
}

// 4. 重試關卡
async function retryLevel() {
  console.log(`重試關卡 ${failedLevel.value}`)
  
  showModal.value = false
  const level = failedLevel.value
  
  // 重置並重新開始
  activeLevel.value = null
  gameKey.value = Date.now()
  
  // 等待重新渲染
  await new Promise(resolve => setTimeout(resolve, 100))
  activeLevel.value = level
  
  // 更新嘗試次數
  if (gameStats.value.attempts[level]) {
    gameStats.value.attempts[level]++
  }
}

// 5. 查看解答
function viewSolution() {
  console.log(`查看關卡 ${failedLevel.value} 解答`)
  showModal.value = false
  showSolutionPanel.value = true
}

// 6. 關閉解答面板
function closeSolution() {
  showSolutionPanel.value = false
}

// 7. 返回地圖
function backToMap() {
  console.log('返回地圖')
  activeLevel.value = null
  failedLevel.value = null
  showModal.value = false
  showSolutionPanel.value = false
  gameKey.value = Date.now()
}

// 8. 確認返回地圖
function confirmBackToMap() {
  if (confirm('確定要返回地圖嗎？當前進度將會遺失。')) {
    backToMap()
  }
}

// 9. 保存遊戲統計
function saveGameStats() {
  try {
    if (typeof window !== 'undefined' && window.localStorage) {
      localStorage.setItem('gameStats', JSON.stringify(gameStats.value))
    }
  } catch (error) {
    console.error('保存遊戲統計失敗:', error)
  }
}

// 10. 載入遊戲統計
function loadGameStats() {
  try {
    if (typeof window !== 'undefined' && window.localStorage) {
      const savedStats = localStorage.getItem('gameStats')
      if (savedStats) {
        gameStats.value = JSON.parse(savedStats)
      }
    }
  } catch (error) {
    console.error('載入遊戲統計失敗:', error)
  }
}

// 組件掛載
onMounted(() => {
  console.log('GamePage 組件已掛載')
  loadGameStats()
})

// 監聽統計變化並保存
watch(gameStats, saveGameStats, { deep: true })
</script>

<style lang="scss" scoped>
.game-app {
  min-height: 100vh;
  position: relative;
  overflow-x: hidden;
}

.loading {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  height: 100vh;
  color: white;
  
  .spinner {
    width: 50px;
    height: 50px;
    border: 4px solid rgba(255, 255, 255, 0.3);
    border-top: 4px solid white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 20px;
  }
  
  p {
    font-size: 1.2rem;
    margin: 0;
  }
}

.back-to-map-btn {
  position: fixed;
  top: 20px;
  left: 20px;
  z-index: 1000;
  background: rgba(255, 255, 255, 0.9);
  border: none;
  padding: 10px 15px;
  margin-top: 40px;
  border-radius: 8px;
  // cursor: pointer;
  font-weight: bold;
  color: #333;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
  
  &:hover {
    background: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  }
}

// 響應式設計
@media (max-width: 768px) {
  .back-to-map-btn {
    top: 10px;
    left: 10px;
    padding: 8px 12px;
    font-size: 0.9rem;
  }
}

// 動畫
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>