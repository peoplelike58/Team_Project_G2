<template>
  <NavMenu />
  <CardLoopItem />

  <!-- 傳地點清單資料 與 按鈕觸發時接收搜尋條件 -->
  <FilterBarItem
    :location-options="locationOptions"
    @search="onSearchClicked"
  />

  <!-- 載入狀態 -->
  <div v-if="loading" class="loading-wrapper">
    <div class="loading-content">
      <div class="loading-spinner"></div>
      <p>載入活動資料中...</p>
    </div>
  </div>

  <!-- 錯誤狀態 -->
  <div v-if="error" class="error-wrapper">
    <div class="error-content">
      <h3>載入失敗</h3>
      <p>{{ error }}</p>
      <button @click="fetchActivitiesFromDB" class="retry-btn">重新載入</button>
    </div>
  </div>

  <!-- 活動卡片區塊 -->
  <div v-if="!loading && !error" class="cardWrapper">
    <div class="cardList" v-if="paginatedActivities.length">
      <EventCard
        v-for="(activity, idx) in paginatedActivities"
        :key="activity.id"
        :item="activity"
        :index="(currentPage - 1) * itemsPerPage + idx"
      />
    </div>

    <!-- 沒有結果時 -->
    <div v-else class="empty-state">
      沒有符合條件的活動，請重新查詢
    </div>
  </div>

  <!-- 頁碼 -->
  <div v-if="!loading && !error" class="pageItem">
    <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1">&lt;</button>
    <button
      v-for="page in totalPages"
      :key="page"
      @click="goToPage(page)"
      :class="{ active: page === currentPage }"
    >
      {{ page }}
    </button>
    <button @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages">&gt;</button>
  </div>

  <div class="qaList">
    <TogetherQnaItem />
  </div>
  <Footer />
</template>

<script setup>
import NavMenu from '@/components/An/navMenu.vue'
import FilterBarItem from '@/components/togetherItem/filterBarItem.vue'
import CardLoopItem from '@/components/togetherItem/cardLoopItem.vue'
import EventCard from '@/components/togetherItem/eventCard.vue'
import TogetherQnaItem from '@/components/togetherItem/togetherQnaItem.vue'
import Footer from '@/components/An/footer.vue'

import axios from 'axios'
import { ref, computed, onMounted } from 'vue'

// ===== API 設定 =====
const API_URL = 'http://localhost/team-projcetG2/eventCard.php'

const loading = ref(false)
const error = ref('')

const itemsPerPage = 6
const currentPage = ref(1)

const originalActivities = ref([])        // 原始活動列表
const filteredActivities = ref([])        // 篩選後活動列表

async function fetchActivitiesFromDB() {
  loading.value = true
  error.value = ''
  
  try {    
    const response = await axios.get(API_URL)
    
    if (response.data.success) {
      // 檢查回傳資料是否為陣列
      if (!Array.isArray(response.data.data)) {
        throw new Error('API 回傳的資料格式不正確')
      }
      
      // 將資料庫資料轉換為原本 JSON 的格式
      const dbActivities = response.data.data.map(event => ({
        //JSON 的欄位結構
        id: event.id,
        title: event.title,
        date: event.date,
        imageUrl: event.imageUrl,
        tags: event.tags || ['活動'],
        ctaUrl: event.ctaUrl,
        location: event.meetingPlace || '台北', // 使用集合地點作為 location
        joinQty: event.joinQty,
        eventTime: event.eventTime,
        content: event.content,
        distance: event.distance
      }))
      
      // 確保轉換後的資料是陣列
      if (!Array.isArray(dbActivities)) {
        throw new Error('不是陣列格式')
      }
      
      originalActivities.value = dbActivities
      filteredActivities.value = [...dbActivities]
      
      // console.log(`${dbActivities.length} 筆活動資料`)
      
    } else {
      throw new Error(response.data.message || '載入活動資料失敗')
    }
    
  } catch (err) {
    console.error('載入活動資料失敗:', err)
    
    // 發生錯誤時確保資料是空陣列
    originalActivities.value = []
    filteredActivities.value = []
    
    if (err.response) {
      error.value = `伺服器錯誤 (${err.response.status}): ${err.response.data?.message || err.message}`
    } else if (err.request) {
      error.value = '網路連線失敗，請檢查網路連線'
    } else {
      error.value = err.message || '載入活動資料時發生未知錯誤'
    }
  } finally {
    loading.value = false
  }
}

// 由資料動態產生地點選單
const locationOptions = computed(() => {
  const set = new Set(originalActivities.value.map(a => a.location).filter(Boolean))
  return Array.from(set).sort()
})

// 分頁計算都基於「篩選後清單」
const totalPages = computed(() =>
  Math.max(1, Math.ceil(filteredActivities.value.length / itemsPerPage))
)

const paginatedActivities = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filteredActivities.value.slice(start, start + itemsPerPage)
})

function goToPage(page) {
  if (page < 1 || page > totalPages.value) return
  currentPage.value = page
}

// 把 YYYY-MM-DD 或 YYYY.MM.DD 都轉成 YYYY.MM.DD 來比對
function normalizeDateToDots(input) {
  if (!input) return ''
  return input.includes('-') ? input.replace(/-/g, '.') : input
}

// 單一條件的判斷
function matchesLocation(activity, criteria) {
  return criteria.location ? activity.location === criteria.location : true
}

function matchesDate(activity, criteria) {
  if (!criteria.date) return true
  return normalizeDateToDots(activity.date) === normalizeDateToDots(criteria.date)
}

function matchesKeyword(activity, criteria) {
  const kw = (criteria.keyword || '').trim()
  if (!kw) return true
  // 標題 / 標籤 / 地點 任一
  const inTitle = activity.title.includes(kw)
  const inTags = (activity.tags || []).some(tag => tag.includes(kw))
  const inLocation = (activity.location || '').includes(kw)
  return inTitle || inTags || inLocation
}

// 按下按鈕後才套用篩選
function onSearchClicked(criteria) {
  filteredActivities.value = originalActivities.value.filter(activity =>
    matchesLocation(activity, criteria) &&
    matchesDate(activity, criteria) &&
    matchesKeyword(activity, criteria)
  )
  currentPage.value = 1
}

onMounted(async () => {
  console.log('TogetherPage 啟用')
  await fetchActivitiesFromDB()
})
</script>

<style lang="scss" scoped>
@import '../assets/styles/main.scss';

/* ===== 新增：載入和錯誤狀態樣式 ===== */
.loading-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 300px;
  margin: 50px 0;
}

.loading-content {
  text-align: center;
  
  .loading-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 20px;
  }
  
  p {
    color: #666;
    font-size: 16px;
  }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 300px;
  margin: 50px 0;
}

.error-content {
  background: #fee;
  border: 1px solid #fcc;
  border-radius: 8px;
  padding: 30px;
  text-align: center;
  max-width: 500px;
  
  h3 {
    color: #c33;
    margin-bottom: 10px;
  }
  
  p {
    color: #a33;
    margin-bottom: 20px;
    line-height: 1.5;
  }
  
  .retry-btn {
    background: #e74c3c;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    
    &:hover {
      background: #c0392b;
    }
  }
}


/* 活動卡片區塊 */
.cardList {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 30px;
  max-width: 1080px;
}

.cardWrapper {
  width: 1080px;
  margin: 0 auto 50px;
}

.empty-state {
  max-width: 1080px;
  margin: 0 auto 50px;
  padding: 24px;
  border: 1px dashed #bbb;
  text-align: center;
  color: #666;
}

/* 問與答區塊 */
.qaList {
  width: 1080px;
  height: 650px;
  margin: 50px auto 0;
  margin-bottom: 150px;
}

/* 分頁按鈕 */
.pageItem {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
  margin-bottom: 80px;

  button {
    padding: 6px 12px;
    border: 1px solid $black-14;
    background: #fff;
    cursor: pointer;
    transition: background 0.2s;

    &:disabled {
      opacity: 0.4;
      cursor: not-allowed;
    }

    &.active {
      background: $black-14;
      color: #fff;
    }

    &:hover:not(:disabled):not(.active) {
      background: #f0f0f0;
    }
  }
}

.footerList{
  margin: 150px;
}

@media screen and (max-width: 768px) {
  // 載入和錯誤狀態
  .loading-wrapper,
  .error-wrapper {
    margin: 30px 15px;
    min-height: 200px;
  }
  
  .error-content {
    padding: 20px;
    margin: 0 15px;
  }

  // 卡片區域
  .cardList{
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
    max-width: 100%;
    padding: 10px;
  }

  .cardWrapper{
    width: 100%;
    max-width: 430px;
    margin: 0 auto 40px;
    padding: 0 5px;
  }
  
  .empty-state{
    max-width: 100%;
    margin: 0 15px 40px;
    padding: 24px 16px;
    font-size: 16px;
    border-radius: 1px;
  }
  
  .qaList{
    width: 100%;
    max-width: 430px;
    height: auto;
    max-height: 400px;
    margin: 40px auto 500px;
    padding: 0 5px;

    @media screen and (max-width:430px) {
      margin: 40px auto 250px;
    }
  }
  
  .pageItem{
    margin-bottom: 50px;
    gap: 6px;
    flex-wrap: wrap;
    padding: 0 15px;

    button{
      min-width: 32px;
      height: 32px;
      padding: 4px 8px;
      font-size: 14px;
      border-radius: 4px;

      &:active {
        transform: scale(0.95);
      }
    }
  }
}
</style>