<template>
  <div class="overlay" @click="$emit('backToMap')">
    <div class="modal" @click.stop>
      <div class="success-icon">🎉</div>
      <h3>關卡 {{ level }} 完成！</h3>
      <p class="congratulations">恭喜您成功完成挑戰！</p>
      
      <div class="buttons">
        <button 
          v-if="hasNextLevel" 
          class="continue-btn" 
          @click="$emit('continue')"
        >
          繼續下一關
        </button>
        <button 
          class="map-btn" 
          @click="$emit('backToMap')"
        >
          返回地圖
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  level: {
    type: Number,
    required: true
  }
})

const emit = defineEmits(['continue', 'backToMap'])

const hasNextLevel = computed(() => props.level < 4)
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
  max-width: 400px;
  width: 90%;
  animation: slideUp 0.3s ease-out;
  
  .success-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    animation: bounce 0.6s ease-out;
  }
  
  h3 {
    color: #28a745;
    margin-bottom: 0.5rem;
    font-size: 1.8rem;
    font-weight: bold;
  }
  
  .congratulations {
    color: #666;
    margin-bottom: 2rem;
    font-size: 1.1rem;
  }
  
  .buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
    
    button {
      padding: 0.8rem 1.5rem;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 1rem;
      font-weight: bold;
      transition: all 0.3s ease;
      min-width: 120px;
      
      &:hover {
        transform: translateY(-2px);
      }
      
      &:active {
        transform: translateY(0);
      }
    }
    
    .continue-btn {
      background: #28a745;
      color: white;
      
      &:hover {
        background: #218838;
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
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

@keyframes bounce {
  0%, 20%, 50%, 80%, 100% {
    transform: translateY(0);
  }
  40% {
    transform: translateY(-10px);
  }
  60% {
    transform: translateY(-5px);
  }
}
</style>