<template>
  <div class="puzzle-game">
    <!-- 標題 -->
    <h2>拼圖挑戰</h2>

    <!-- 倒數計時 -->
    <div class="timer" :class="{ warning: timeLeft <= 30 }">
      剩餘時間：{{ formattedTime }}
    </div>

    <!-- 成功提示 -->
    <div v-if="isCompleted" class="success-message">
      恭喜完成拼圖！好棒棒
    </div>

    <!-- 拼圖區塊 -->
    <div class="puzzle-container">
      <!-- 預覽圖片 -->
      <div class="preview">
        <img :src="imgUrl" alt="完整圖片" class="preview-image" />
        <p>完整圖片預覽</p>
      </div>

      <!-- 拼圖遊戲區 -->
      <div class="game-area">
        <div class="grid" :class="{ completed: isCompleted }">
          <div 
            v-for="(piece, idx) in puzzlePieces" 
            :key="`piece-${piece.originalId}-${gameId}`"
            :class="['cell', { selected: selected === idx }]"
            @click="onPieceClick(idx)"
            :style="{ 
              backgroundImage: `url(${imgUrl})`, 
              backgroundPosition: piece.correctPosition,
              backgroundSize: '300% 300%'
            }"
          >
          </div>
        </div>

        <!-- 操作按鈕 -->
        <div class="controls">
          <button class="check-btn" @click="checkPuzzle" :disabled="isCompleted">
            完成
          </button>
          <button class="shuffle-btn" @click="shufflePieces" :disabled="isCompleted">
            重新
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue'

// Props 定義
const props = defineProps({
  level: {
    type: Number,
    required: true
  },
  timeLimit: {
    type: Number,
    default: 180
  },
  showDebug: {
    type: Boolean,
    default: false
  }
})

// Emit 定義
const emit = defineEmits(['success', 'fail'])

// 響應式變數
const size = 3 // 3x3 拼圖
const puzzlePieces = ref([]) // 當前拼圖片段排列
const selected = ref(null)
const isCompleted = ref(false)
const timeLeft = ref(props.timeLimit)
const gameId = ref(Date.now())

// 計時器
let timer = null

const imgUrl = computed(() => `/images/GAME/Game${props.level}.jpg`)

// 正確拼圖順序
const correctOrder = [0, 2, 1, 6, 8, 7, 3, 5, 4]

// 格式化時間
const formattedTime = computed(() => {
  const minutes = Math.floor(timeLeft.value / 60)
  const seconds = timeLeft.value % 60
  return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
})

// 創建拼圖片段
function createPuzzlePiece(id) {
  const row = Math.floor(id / size)
  const col = id % size
  
  return {
    originalId: id, // 原始編號 (0-8)
    correctPosition: `-${col * 50}% -${row * 50}%`, // 正確的背景位置
    isInCorrectPosition: false // 是否在正確位置
  }
}

// 初始化拼圖
function initPieces() {  
  // 創建所有拼圖
  const allPieces = []
  for (let i = 0; i < size * size; i++) {
    allPieces.push(createPuzzlePiece(i))
  }
  
  // 打亂順序，確保不是正確順序
  let shuffled
  let attempts = 0
  do {
    shuffled = [...allPieces].sort(() => Math.random() - 0.5)
    attempts++
    if (attempts > 100) break
  } while (isCorrectOrder(shuffled))
  
  puzzlePieces.value = shuffled
  selected.value = null
  isCompleted.value = false
  
}

// 檢查是否為正確順序
function isCorrectOrder(pieces) {
  return pieces.every((piece, index) => piece.originalId === correctOrder[index])
}

// 打亂拼圖
function shufflePieces() {
  if (isCompleted.value) return
  
  let shuffled
  do {
    shuffled = [...puzzlePieces.value].sort(() => Math.random() - 0.5)
  } while (isCorrectOrder(shuffled))
  
  puzzlePieces.value = shuffled
  selected.value = null
  
}

// 點擊拼圖片段
function onPieceClick(index) {
  if (isCompleted.value) return
  
  if (selected.value === null) {
    // 選擇拼圖片段
    selected.value = index
  } else if (selected.value === index) {
    // 取消選擇
    selected.value = null
  } else {
    const newPieces = [...puzzlePieces.value]
    const temp = newPieces[selected.value]
    newPieces[selected.value] = newPieces[index]
    newPieces[index] = temp
    
    puzzlePieces.value = newPieces
    selected.value = null
    
    // 延遲檢查完成狀態
    nextTick(() => {
      setTimeout(() => {
        autoCheckCompletion()
      }, 100)
    })
  }
}

// 自動檢查完成狀態
function autoCheckCompletion() {
  const isComplete = isCorrectOrder(puzzlePieces.value)
  
  if (isComplete && !isCompleted.value) {
    handleGameSuccess()
  }
}

// 手動檢查完成
function checkPuzzle() {
  const isComplete = isCorrectOrder(puzzlePieces.value)
  
  if (isComplete) {
    handleGameSuccess()
  } else {
    alert('拼圖未完成，加油！\n\n提示：需讓拼圖片與完整完整圖片一樣呦。')
  }
}

function showSolution() {
  if (isCompleted.value) return
  
  const correctPieces = []
  for (let i = 0; i < size * size; i++) {
    correctPieces.push(createPuzzlePiece(i))
  }
  
  puzzlePieces.value = correctPieces
  selected.value = null
  
  // 自動檢查完成
  setTimeout(() => {
    autoCheckCompletion()
  }, 500)
}

// 處理遊戲成功
function handleGameSuccess() {
  if (isCompleted.value) return // 防止重複觸發
  
  isCompleted.value = true
  stopTimer()
  
  // 延遲觸發成功事件，確保UI更新
  setTimeout(() => {
    emit('success')
  }, 500)
}

// 處理遊戲失敗
function handleGameFail() {
  if (isCompleted.value) return
  
  stopTimer()
  emit('fail', props.level)
}

// 啟動計時器
function startTimer() {
  stopTimer() // 確保沒有重複計時器
  
  timer = setInterval(() => {
    if (timeLeft.value > 0 && !isCompleted.value) {
      timeLeft.value--
    } else if (timeLeft.value <= 0 && !isCompleted.value) {
      handleGameFail()
    }
  }, 1000)
}

// 停止計時器
function stopTimer() {
  if (timer) {
    clearInterval(timer)
    timer = null
  }
}

// 重置遊戲
function resetGame() {
  console.log('重置遊戲')
  stopTimer()
  timeLeft.value = props.timeLimit
  gameId.value = Date.now()
  initPieces()
  startTimer()
}

// 監聽 level 變化，重置遊戲
watch(() => props.level, () => {
  resetGame()
}, { immediate: false })

// 監聽 timeLimit 變化
watch(() => props.timeLimit, (newLimit) => {
  timeLeft.value = newLimit
})

// 組件掛載
onMounted(() => {
  console.log('PuzzleGame 組件掛載, level:', props.level)
  resetGame()
})

// 組件卸載
onUnmounted(() => {
  stopTimer()
})

// 暴露給父組件的方法
defineExpose({
  resetGame,
  shufflePieces,
  showSolution
})
</script>

<style lang="scss" scoped>
.puzzle-game {
  max-width: 1000px;
  margin: 0 auto;
  padding: 20px;
  text-align: center;

  h2 {
    margin-bottom: 1rem;
    font-size: 1.8rem;
    color: #333;
    font-weight: bold;
  }

  .timer {
    margin-bottom: 1.5rem;
    font-size: 1.3rem;
    color: #555;
    font-weight: bold;
    padding: 8px 16px;
    background-color: #f8f9fa;
    border-radius: 8px;
    display: inline-block;
    
    &.warning {
      color: #dc3545;
      background-color: #f8d7da;
      animation: pulse 1s infinite;
    }
  }

  .success-message {
    font-size: 1.5rem;
    color: #28a745;
    margin-bottom: 1.5rem;
    animation: celebration 2s ease-in-out;
    font-weight: bold;
  }

  .puzzle-container {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    gap: 30px;
    flex-wrap: wrap;
  }

  .preview {
    flex-shrink: 0;
    
    .preview-image {
      width: 200px;
      height: 200px;
      object-fit: cover;
      border: 3px solid #ddd;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    p {
      margin-top: 8px;
      font-size: 0.9rem;
      color: #666;
      font-weight: bold;
    }
  }

  .game-area {
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .grid {
    display: grid;
    grid-template-columns: repeat(3, 120px);
    grid-template-rows: repeat(3, 120px);
    gap: 4px;
    margin-bottom: 20px;
    padding: 15px;
    border: 3px solid #ddd;
    border-radius: 12px;
    background-color: #fafafa;

    &.completed {
      animation: success-flash 1s ease-in-out 3;
      border-color: #28a745;
      background-color: #d4edda;
    }
  }

  .cell {
    width: 120px;
    height: 120px;
    border: 2px solid #ccc;
    cursor: pointer;
    transition: all 0.3s ease;
    border-radius: 6px;
    position: relative;
    overflow: hidden;

    &:hover:not(.selected) {
      transform: scale(1.05);
      border-color: #999;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }

    &.selected {
      border-color: #007bff;
      box-shadow: 0 0 20px rgba(0, 123, 255, 0.8);
      transform: scale(1.1);
      z-index: 10;
    }

    .piece-number {
      position: absolute;
      top: 2px;
      left: 2px;
      background: rgba(0, 0, 0, 0.7);
      color: white;
      font-size: 12px;
      padding: 2px 6px;
      border-radius: 3px;
      font-weight: bold;
    }
  }

  .controls {
    display: flex;
    gap: 15px;
    
    button {
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 1rem;
      font-weight: bold;
      transition: all 0.3s ease;

      &:disabled {
        opacity: 0.6;
        cursor: not-allowed;
      }

      &:active:not(:disabled) {
        transform: translateY(1px);
      }
    }

    .check-btn {
      background: #28a745;
      color: white;

      &:hover:not(:disabled) {
        background: #218838;
      }
    }

    .shuffle-btn {
      background: #ffc107;
      color: #212529;

      &:hover:not(:disabled) {
        background: #e0a800;
      }
    }
  }

  .debug {
    margin-top: 20px;
    padding: 15px;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    font-size: 0.9rem;
    text-align: left;
    
    p {
      margin: 5px 0;
      font-family: monospace;
    }
    
    .debug-btn {
      margin-top: 10px;
      padding: 5px 10px;
      background: #17a2b8;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 0.8rem;
      
      &:hover {
        background: #138496;
      }
    }
  }
}

// 響應式設計
@media (max-width: 768px) {
  .puzzle-container {
    flex-direction: column;
    align-items: center;
  }
  
  .preview .preview-image {
    width: 150px;
    height: 150px;
  }
  
  .grid {
    grid-template-columns: repeat(3, 100px);
    grid-template-rows: repeat(3, 100px);
  }
  
  .cell {
    width: 100px;
    height: 100px;
  }
}

// 動畫效果
@keyframes success-flash {
  0%   { background-color: #fafafa; }
  50%  { background-color: #d4edda; }
  100% { background-color: #d4edda; }
}

@keyframes celebration {
  0%   { transform: scale(1) rotate(0deg); }
  25%  { transform: scale(1.1) rotate(-2deg); }
  50%  { transform: scale(1.2) rotate(2deg); }
  75%  { transform: scale(1.1) rotate(-1deg); }
  100% { transform: scale(1) rotate(0deg); }
}

@keyframes pulse {
  0%   { transform: scale(1); }
  50%  { transform: scale(1.05); }
  100% { transform: scale(1); }
}
</style>