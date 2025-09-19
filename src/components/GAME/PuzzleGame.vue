<template>
  <div class="puzzle-game">
    <!-- 標題 -->
    <h2>{{ size }}x{{ size }} 拼圖挑戰</h2>

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
        <div 
          class="grid" 
          :class="{ completed: isCompleted }"
          :style="gridStyle"
        >
          <div 
            v-for="(piece, idx) in puzzlePieces" 
            :key="`piece-${piece.originalId}-${gameId}`"
            :class="['cell', { selected: selected === idx }]"
            @click="onPieceClick(idx)"
            :style="getCellStyle(piece)"
          >
            <!-- 可選：顯示片段編號用於調試 -->
            <span v-if="showDebug" class="piece-number">{{ piece.originalId }}</span>
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
          <button v-if="showHintButton" class="hint-btn" @click="showHint" :disabled="isCompleted">
            提示
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
  size: {
    type: Number,
    default: 5,
    validator: (value) => value >= 2 && value <= 10
  },
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
  },
  showHintButton: {
    type: Boolean,
    default: false
  },
  // 自定義正確順序
  customCorrectOrder: {
    type: Array,
    default: null,
    validator: (value) => {
      if (!value) return true
      // 檢查陣列是否包含所有必要的數字
      const sorted = [...value].sort((a, b) => a - b)
      return sorted.every((num, idx) => num === idx)
    }
  }
})

// Emit 定義
const emit = defineEmits(['success', 'fail'])

// 響應式變數
const puzzlePieces = ref([]) // 拼圖片段排列
const selected = ref(null)
const isCompleted = ref(false)
const timeLeft = ref(props.timeLimit)
const gameId = ref(Date.now())

// 計時器
let timer = null

//API網址
const baseUrl = computed(() => {
    // 從環境變數取得 AJAX URL
    const ajaxUrl = import.meta.env.VITE_AJAX_URL || ''
    return ajaxUrl.replace('/PHP', '/')
})

// 圖片URL
const imgUrl = computed(() => `${baseUrl.value}images/GAME/Game${props.level}.jpg`)

// 預定義的答案配置（根據 size 和 level）
const predefinedOrders = {
  // 2x2 拼圖的答案
  '2': {
    1: [0, 1, 2, 3],     // 2x2 答案
    default: [0, 1, 2, 3] // 預設 2x2 答案
  },
  // 3x3 拼圖的答案  
  '3': {
    1: [0, 2, 1, 6, 8, 7, 3, 5, 4],     //  3x3 答案
    default: [0, 2, 1, 6, 8, 7, 3, 5, 4] // 預設 3x3 答案
  },
  // 4x4 拼圖的答案
  '4': {
    1: [0, 3, 2, 1, 12, 15, 14, 13, 8, 11, 10, 9, 4, 7, 6, 5],
    default: [0, 3, 2, 1, 12, 15, 14, 13, 8, 11, 10, 9, 4, 7, 6, 5]
  },
  // 5x5 拼圖的答案
  '5': {
    1: [0, 4, 3, 2, 1, 20, 24, 23, 22, 21, 15, 19, 18, 17, 16, 10, 14, 13, 12, 11, 5, 9, 8, 7, 6],
    default: [0, 4, 3, 2, 1, 20, 24, 23, 22, 21, 15, 19, 18, 17, 16, 10, 14, 13, 12, 11, 5, 9, 8, 7, 6]
  },
}

// 取得正確順序
const correctOrder = computed(() => {
  // 優先使用自定義順序
  if (props.customCorrectOrder && props.customCorrectOrder.length === props.size * props.size) {
    return props.customCorrectOrder
  }
  
  // 查找預定義順序
  const sizeOrders = predefinedOrders[props.size] || predefinedOrders['default']
  
  if (sizeOrders) {
    const levelOrder = sizeOrders[props.level] || sizeOrders['default']
    if (levelOrder && levelOrder.length === props.size * props.size) {
      return levelOrder
    }
  }
  
  // 如果沒有預定義，返回簡單順序 0-N
  // console.warn(`沒有找到 size ${props.size}, level ${props.level} 的預定義順序，使用預設順序`)
  const order = []
  for (let i = 0; i < props.size * props.size; i++) {
    order.push(i)
  }
  return order
})

// 格式化時間
const formattedTime = computed(() => {
  const minutes = Math.floor(timeLeft.value / 60)
  const seconds = timeLeft.value % 60
  return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
})

// Grid 樣式
const gridStyle = computed(() => {
  const cellSize = getCellSize()
  return {
    gridTemplateColumns: `repeat(${props.size}, ${cellSize}px)`,
    gridTemplateRows: `repeat(${props.size}, ${cellSize}px)`
  }
})

// 根據拼圖大小獲取單元格尺寸
function getCellSize() {
  // 基礎尺寸對應表
  const baseSizes = {
    2: 150,
    3: 120,
    4: 100,
    5: 80,
    6: 70,
    7: 60,
    8: 55,
    9: 50,
    10: 45
  }
  return baseSizes[props.size] || Math.floor(360 / props.size)
}

// 獲取單元格樣式
function getCellStyle(piece) {
  const cellSize = getCellSize()
  const backgroundSize = `${props.size * 100}% ${props.size * 100}%`
  
  return {
    width: `${cellSize}px`,
    height: `${cellSize}px`,
    backgroundImage: `url(${imgUrl.value})`,
    backgroundPosition: piece.correctPosition,
    backgroundSize: backgroundSize
  }
}

// 創建拼圖片段
function createPuzzlePiece(id) {
  const row = Math.floor(id / props.size)
  const col = id % props.size
  const positionPercentage = props.size === 1 ? 0 : 100 / (props.size - 1)
  
  return {
    originalId: id, // 原始編號
    correctPosition: `-${col * positionPercentage}% -${row * positionPercentage}%`, // 正確的背景位置
    isInCorrectPosition: false // 是否在正確位置
  }
}

// 初始化拼圖
function initPieces() {  
  // 創建所有拼圖片段
  const allPieces = []
  const totalPieces = props.size * props.size
  
  // 根據正確順序創建拼圖片段
  for (let i = 0; i < totalPieces; i++) {
    const correctId = correctOrder.value[i]
    allPieces.push(createPuzzlePiece(correctId))
  }
  
  // 打亂順序，確保不是正確順序
  let shuffled
  let attempts = 0
  do {
    shuffled = [...allPieces].sort(() => Math.random() - 0.5)
    attempts++
    if (attempts > 100) {
      const temp = shuffled[0]
      shuffled[0] = shuffled[shuffled.length - 1]
      shuffled[shuffled.length - 1] = temp
      break
    }
  } while (isCorrectOrder(shuffled))
  
  puzzlePieces.value = shuffled
  selected.value = null
  isCompleted.value = false
}

// 檢查是否為正確順序
function isCorrectOrder(pieces) {
  return pieces.every((piece, index) => piece.originalId === correctOrder.value[index])
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
    // 交換拼圖片段
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
    alert(`拼圖未完成，加油！`)
  }
}

// 顯示提示
function showHint() {
  if (isCompleted.value) return
  
  // 找出第一個錯誤位置
  for (let i = 0; i < puzzlePieces.value.length; i++) {
    if (puzzlePieces.value[i].originalId !== i) {
      const row = Math.floor(i / props.size) + 1
      const col = (i % props.size) + 1
      alert(`提示：第 ${row} 行第 ${col} 列的拼圖片不正確！`)
      return
    }
  }
  
  alert('所有拼圖片都在正確位置！點擊"完成"按鈕即可。')
}

// 顯示答案
function showSolution() {
  if (isCompleted.value) return
  
  const correctPieces = []
  for (let i = 0; i < props.size * props.size; i++) {
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
    emit('success', {
      size: props.size,
      timeUsed: props.timeLimit - timeLeft.value,
      level: props.level
    })
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
  // console.log(`重置遊戲 - Size: ${props.size}x${props.size}`)
  stopTimer()
  timeLeft.value = props.timeLimit
  gameId.value = Date.now()
  initPieces()
  startTimer()
}

// 監聽 props 變化
watch(() => props.level, () => {
  resetGame()
})

watch(() => props.size, () => {
  resetGame()
})

watch(() => props.timeLimit, (newLimit) => {
  timeLeft.value = newLimit
})

// 組件掛載
onMounted(() => {
  console.log(`PuzzleGame 組件掛載 - Size: ${props.size}x${props.size}, Level: ${props.level}`)
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

    .hint-btn {
      background: #17a2b8;
      color: white;

      &:hover:not(:disabled) {
        background: #138496;
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
    // Grid 大小會由 computed 自動調整
    gap: 3px;
    padding: 10px;
  }
  
  .cell {
    // Cell 大小會由 computed 自動調整
    &.selected {
      transform: scale(1.05); // 移動端減小縮放
    }
  }
}

// 小螢幕優化
@media (max-width: 480px) {
  .puzzle-game {
    padding: 10px;
    
    h2 {
      font-size: 1.5rem;
    }
    
    .timer {
      font-size: 1.1rem;
    }
  }
  
  .controls {
    button {
      padding: 8px 15px;
      font-size: 0.9rem;
    }
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