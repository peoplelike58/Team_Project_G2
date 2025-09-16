<template>
  <div class="event-list">
    <!-- 載入中 -->
    <div v-if="loading">載入中...</div>
    
    <!-- 錯誤訊息 -->
    <div v-if="error">{{ error }}</div>
    
    <div v-if="!loading && !error" class="cards-container">
      <EventCard 
        v-for="(event, index) in events" 
        :key="event.id"
        :item="event"
        :index="index"
        @cta-click="handleCtaClick"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import EventCard from './eventCard.vue'  // 您現有的組件

const events = ref([])
const loading = ref(false)
const error = ref('')

// API 設定
const API_URL = 'http://localhost/team-projcetG2/eventCard.php'

// 獲取活動資料
async function fetchEvents() {
  loading.value = true
  error.value = ''
  
  try {
    // 使用 axios 發送請求到您的 PHP API
    const response = await axios.get(API_URL)
    
    // 檢查 PHP 回應
    if (response.data.success) {
      // 直接使用 PHP 回傳的資料，無需額外轉換
      // PHP 已經將資料格式化為您的 EventCard 期望的格式
      events.value = response.data.data
      console.log('成功載入活動資料:', events.value)
    } else {
      error.value = response.data.message || '載入失敗'
    }
    
  } catch (err) {
    console.error('請求失敗:', err)
    if (err.response) {
      error.value = `伺服器錯誤: ${err.response.status}`
    } else {
      error.value = '網路連線失敗'
    }
  } finally {
    loading.value = false
  }
}

// 處理 CTA 點擊
function handleCtaClick(eventData) {
  console.log('點擊活動:', eventData)
}

// 組件載入後獲取資料
onMounted(() => {
  fetchEvents()
})
</script>

<style scoped>
.event-list {
  padding: 20px;
}

.cards-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(330px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

@media (max-width: 768px) {
  .cards-container {
    grid-template-columns: 1fr;
  }
}
</style>