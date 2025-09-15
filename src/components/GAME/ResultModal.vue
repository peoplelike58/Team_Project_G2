<template>
  <div class="overlay" @click="$emit('backToMap')">
    <div class="modal" @click.stop>
      <div class="fail-icon">😞</div>
      <h3>關卡 {{ level }} 失敗</h3>
      <p class="message">時間到了！再試一次吧！</p>
      
      <div class="buttons">
        <button class="retry-btn" @click="$emit('retry')">
          重試
        </button>
        <button class="solution-btn" @click="$emit('showSolution')">
          查看解答
        </button>
        <button class="map-btn" @click="$emit('backToMap')">
          返回地圖
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  level: {
    type: Number,
    required: true
  }
})

const emit = defineEmits(['retry', 'showSolution', 'backToMap'])
</script>

<style lang="scss" scoped>
.overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
  backdrop-filter: blur(5px);
  animation: fadeIn 0.3s ease-out;
}

.modal {
  background: white;
  padding: 2.5rem;
  border-radius: 16px;
  text-align: center;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  max-width: 450px;
  width: 90%;
  animation: slideUp 0.3s ease-out;
  
  .fail-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    animation: shake 0.5s ease-out;
  }
  
  h3 {
    color: #dc3545;
    margin-bottom: 0.5rem;
    font-size: 1.8rem;
    font-weight: bold;
  }
  
  .message {
    color: #666;
    margin-bottom: 2rem;
    font-size: 1.1rem;
    line-height: 1.5;
  }
  
  .buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
    
    button {
      padding: 0.8rem 1.2rem;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 0.95rem;
      font-weight: bold;
      transition: all 0.3s ease;
      min-width: 110px;
      
      &:hover {
        transform: translateY(-2px);
      }
      
      &:active {
        transform: translateY(0);
      }
    }
    
    .retry-btn {
      background: #007bff;
      color: white;
      
      &:hover {
        background: #0056b3;
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
      }
    }
    
    .solution-btn {
      background: #ffc107;
      color: #212529;
      
      &:hover {
        background: #e0a800;
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
      }
    }
    
    .map-btn {
      background: #6c757d;
      color: white;
      
      &:hover {
        background: #5a6268;
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
      }
    }
  }
}

@media (max-width: 480px) {
  .modal {
    padding: 2rem;
    
    .buttons {
      flex-direction: column;
      
      button {
        width: 100%;
      }
    }
  }
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
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

@keyframes shake {
  0%, 100% {
    transform: translateX(0);
  }
  25% {
    transform: translateX(-5px);
  }
  75% {
    transform: translateX(5px);
  }
}
</style>