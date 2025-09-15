<template>
  <div class="quiz-game">
    <!-- 背景裝飾 -->
    <div class="background-decoration">
      <div class="floating-shape shape-1"></div>
      <div class="floating-shape shape-2"></div>
      <div class="floating-shape shape-3"></div>
    </div>

    <!-- 遊戲卡片 -->
    <div class="game-card">
      <!-- 標題區域 -->
      <div class="header">
        <h2 class="title">
          野外求生問答
        </h2>
      </div>

      <!-- 計時器 -->
      <div class="timer-container">
        <div class="timer" :class="{ warning: timeLeft <= 30, critical: timeLeft <= 10 }">
          {{ formattedTime }}
        </div>
        <div class="timer-label">剩餘時間</div>
      </div>

      <!-- 問題內容 -->
      <div v-if="question" class="content">
        <!-- 問題區域 -->
        <div class="question-container">
          <p class="question">{{ question.q }}</p>
        </div>

        <!-- 選項區域 -->
        <div class="choices-container">
          <div class="choices-label">請作答：</div>
          <ul class="choices">
            <li 
              v-for="(opt, i) in question.options" 
              :key="i"
              :class="['choice-item', { chosen: selected === i }]"
              @click="selectOption(i)"
            >
              <div class="choice-marker">{{ String.fromCharCode(65 + i) }}</div>
              <div class="choice-text">{{ opt }}</div>
              <div class="choice-indicator">
                <div v-if="selected === i" class="checkmark">✓</div>
              </div>
            </li>
          </ul>
        </div>

        <!-- 提交按鈕 -->
        <div class="submit-container">
          <button 
            class="submit-btn" 
            :disabled="selected === null" 
            @click="submitAnswer"
          >
            <span class="btn-text">確認答案</span>
          </button>
        </div>
      </div>

      <!-- 載入狀態 -->
      <div v-else class="loading">
        <div class="spinner"></div>
        <p>載入問題中...</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({ 
  level: Number, 
  timeLimit: Number 
})
const emit = defineEmits(['success', 'fail'])

// 題庫
const bank = [
  { 
    q: '在山上突遇大霧時，最安全的做法是？', 
    options: ['繼續前進尋找出路', '原地等待霧散', '使用GPS導航下山', '立即求救'], 
    answer: 1 
  },
  { 
    q: '在野外迷路時，最重要的原則是？', 
    options: ['留在原地等待救援', '大聲呼喊求助', '四處走動尋找出路', '爬到高處揮舞物品'], 
    answer: 0 
  },
  {
    q: '野外過夜時，最重要的是保持？',
    options: ['體溫', '水分', '食物', '方向感'],
    answer: 0
  },
  {
    q: '發現有人中暑時，應該？',
    options: ['立即給冰水', '移到陰涼處', '大量運動', '繼續曝曬'],
    answer: 1
  }
]

// 根據關卡選擇問題
const question = computed(() => {
  const index = (props.level - 3) % bank.length
  return bank[index]
})

const selected = ref(null)
const timeLeft = ref(props.timeLimit)
let timer = null

const formattedTime = computed(() => {
  const m = String(Math.floor(timeLeft.value / 60)).padStart(2, '0')
  const s = String(timeLeft.value % 60).padStart(2, '0')
  return `${m}:${s}`
})

// 選擇選項時的動畫效果
function selectOption(index) {
  selected.value = index
  
  const choiceElement = document.querySelectorAll('.choice-item')[index]
  choiceElement.style.transform = 'scale(0.98)'
  setTimeout(() => {
    choiceElement.style.transform = 'scale(1)'
  }, 150)
}

function submitAnswer() {
  if (selected.value === null) return
  
  clearInterval(timer)
  
  // 添加提交動畫
  const submitBtn = document.querySelector('.submit-btn')
  submitBtn.style.transform = 'scale(0.95)'
  
  setTimeout(() => {
    if (selected.value === question.value.answer) {
      emit('success')
    } else {
      emit('fail', props.level)
    }
  }, 200)
}

onMounted(() => {
  timer = setInterval(() => {
    if (timeLeft.value > 0) {
      timeLeft.value--
    } else {
      clearInterval(timer)
      emit('fail', props.level)
    }
  }, 1000)
})

onUnmounted(() => {
  if (timer) {
    clearInterval(timer)
  }
})
</script>

<style lang="scss" scoped>
.quiz-game {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  position: relative;
  
  .background-decoration {
    position: absolute;
    inset: 0;
    overflow: hidden;
    z-index: 0;
    
    .floating-shape {
      position: absolute;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.1);
      animation: float 6s ease-in-out infinite;
      
      &.shape-1 {
        width: 100px;
        height: 100px;
        top: 10%;
        left: 10%;
        animation-delay: 0s;
      }
      
      &.shape-2 {
        width: 150px;
        height: 150px;
        top: 60%;
        right: 15%;
        animation-delay: 2s;
      }
      
      &.shape-3 {
        width: 80px;
        height: 80px;
        bottom: 20%;
        left: 20%;
        animation-delay: 4s;
      }
    }
  }
}

.game-card {
  position: relative;
  z-index: 1;
  max-width: 600px;
  width: 100%;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-radius: 24px;
  padding: 40px;
  box-shadow: 
    0 20px 60px rgba(0, 0, 0, 0.2),
    0 0 0 1px rgba(255, 255, 255, 0.1);
  animation: slideUp 0.6s ease-out;
}

.header {
  text-align: center;
  margin-bottom: 30px;
  
  .level-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #ff6b6b, #ffa726);
    color: white;
    padding: 8px 20px;
    border-radius: 20px;
    font-weight: bold;
    margin-bottom: 15px;
    box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
    
    .level-text {
      font-size: 0.9rem;
    }
    
    .level-number {
      font-size: 1.2rem;
      background: rgba(255, 255, 255, 0.2);
      width: 30px;
      height: 30px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }
  }
  
  .title {
    margin: 0;
    font-size: 2rem;
    color: #2c3e50;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    
    .icon {
      font-size: 2.2rem;
    }
  }
}

.timer-container {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  margin-bottom: 30px;
  padding: 15px;
  background: linear-gradient(135deg, #f8f9fa, #e9ecef);
  border-radius: 16px;
  border: 2px solid #dee2e6;
  
  .timer-icon {
    font-size: 1.5rem;
  }
  
  .timer {
    font-size: 1.8rem;
    font-weight: bold;
    color: #495057;
    font-family: 'Courier New', monospace;
    padding: 8px 16px;
    background: white;
    border-radius: 8px;
    border: 2px solid #dee2e6;
    transition: all 0.3s ease;
    
    &.warning {
      color: #ff6b35;
      border-color: #ff6b35;
      animation: pulse 1s infinite;
    }
    
    &.critical {
      color: #e74c3c;
      border-color: #e74c3c;
      background: #fdf2f2;
      animation: shake 0.5s infinite;
    }
  }
  
  .timer-label {
    font-size: 0.9rem;
    color: #6c757d;
    font-weight: 500;
  }
}

.content {
  .question-container {
    background: linear-gradient(135deg, #74b9ff, #0984e3);
    color: white;
    padding: 25px;
    border-radius: 16px;
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;
    
    &::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
    }
    
    .question-icon {
      font-size: 2rem;
      margin-bottom: 10px;
    }
    
    .question {
      font-size: 1.4rem;
      font-weight: 600;
      line-height: 1.5;
      margin: 0;
      position: relative;
      z-index: 1;
    }
  }
  
  .choices-container {
    margin-bottom: 30px;
    
    .choices-label {
      font-size: 1.1rem;
      color: #495057;
      margin-bottom: 15px;
      font-weight: 600;
    }
    
    .choices {
      display: grid;
      grid-template-columns: 1fr 1fr;
      grid-template-rows: 1fr 1fr;
      grid-template-areas:
        "choice-0 choice-1"  
        "choice-2 choice-3"; 
      gap: 10px;
      
      .choice-item {
        display: flex;
        align-items: center;
        padding: 17px;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: white;
        position: relative;
        
        &:hover {
          border-color: #74b9ff;
          transform: translateY(-2px);
          box-shadow: 0 8px 25px rgba(116, 185, 255, 0.2);
        }
        
        &.chosen {
          border-color: #0984e3;
          background: linear-gradient(135deg, #e3f2fd, #f0f8ff);
          transform: translateY(-2px);
          box-shadow: 0 8px 25px rgba(9, 132, 227, 0.3);
          
          .choice-marker {
            background: #0984e3;
            color: white;
          }
        }
        
        .choice-marker {
          width: 35px;
          height: 35px;
          border-radius: 50%;
          background: #f8f9fa;
          color: #6c757d;
          display: flex;
          align-items: center;
          justify-content: center;
          font-weight: bold;
          font-size: 1rem;
          margin-right: 15px;
          transition: all 0.3s ease;
        }
        
        .choice-text {
          flex: 1;
          font-size: 1.1rem;
          font-weight: 500;
          color: #495057;
        }
        
        .choice-indicator {
          width: 30px;
          display: flex;
          justify-content: center;
          
          .checkmark {
            color: #28a745;
            font-size: 1.2rem;
            font-weight: bold;
            animation: checkmarkAppear 0.3s ease-out;
          }
        }
      }
    }
  }
  
  .submit-container {
    text-align: center;
    
    .submit-btn {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white;
      border: none;
      padding: 15px 40px;
      border-radius: 12px;
      font-size: 1.1rem;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
      
      &:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
      }
      
      &:disabled {
        background: #6c757d;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
        opacity: 0.6;
      }
      
      .btn-icon {
        font-size: 1.2rem;
      }
    }
  }
}

.loading {
  text-align: center;
  padding: 60px 20px;
  
  .spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #e9ecef;
    border-top: 4px solid #74b9ff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 20px;
  }
  
  p {
    color: #6c757d;
    font-size: 1.1rem;
    margin: 0;
  }
}

// 響應式設計
@media (max-width: 768px) {
  .game-card {
    padding: 25px;
    margin: 10px;
  }
  
  .header .title {
    font-size: 1.6rem;
  }
  
  .content .question-container .question {
    font-size: 1.2rem;
  }
  
  .choices .choice-item {
    padding: 15px;
    
    .choice-text {
      font-size: 1rem;
    }
  }
}

// 動畫效果
@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-20px);
  }
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes pulse {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
}

@keyframes shake {
  0%, 100% {
    transform: translateX(0);
  }
  25% {
    transform: translateX(-3px);
  }
  75% {
    transform: translateX(3px);
  }
}

@keyframes checkmarkAppear {
  from {
    opacity: 0;
    transform: scale(0);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>