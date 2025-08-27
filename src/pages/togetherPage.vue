<template>
  <NavMenu />
  <CardLoopItem />

  <!-- 傳地點清單資料 與 按鈕觸發時接收搜尋條件 -->
  <FilterBarItem
    :location-options="locationOptions"
    @search="onSearchClicked"
  />

  <!-- 活動卡片區塊 -->
  <div class="cardWrapper">
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
  <div class="pageItem">
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
import activitiesJson from '@/components/togetherItem/activities.json'
import Footer from '@/components/An/footer.vue'

import { ref, computed } from 'vue'

// 分頁
const itemsPerPage = 6
const currentPage = ref(1)

// 資料：原始清單 & 篩選後清單
const originalActivities = activitiesJson
const filteredActivities = ref([...originalActivities])

// 由資料動態產生地點選單
const locationOptions = computed(() => {
  const set = new Set(originalActivities.map(a => a.location).filter(Boolean))
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
  filteredActivities.value = originalActivities.filter(activity =>
    matchesLocation(activity, criteria) &&
    matchesDate(activity, criteria) &&
    matchesKeyword(activity, criteria)
  )
  currentPage.value = 1
}
</script>

<style lang="scss" scoped>
@import '../assets/styles/main.scss';

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
</style>
