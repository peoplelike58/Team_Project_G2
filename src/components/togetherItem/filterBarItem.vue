<template>
  <div class="filter-bar">
    <div class="filter-content">
      <div class="filter-item">
        <label>地點</label>
        <select v-model="searchCriteria.location">
          <option value="">全部地點</option>
          <option v-for="loc in locationOptions" :key="loc" :value="loc">{{ loc }}</option>
        </select>
      </div>

      <div class="filter-item">
        <label>日期</label>
        <input type="date" v-model="searchCriteria.date" />
      </div>

      <div class="filter-item">
        <label>關鍵字</label>
        <input type="text" placeholder="輸入關鍵字" v-model="searchCriteria.keyword" />
      </div>

      <button class="search-btn" @click="emitSearch">搜出揪團</button>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'

// 由父層傳入可以避免東西寫死
const props = defineProps({
  locationOptions: { type: Array, default: () => [] }
})

// 目前設定的查詢條件
const searchCriteria = reactive({
  location: '',
  date: '',
  keyword: ''
})

// 點擊按鈕時才觸發搜尋
const emit = defineEmits(['search'])
function emitSearch() {
  // 避免父層誤改到子層狀態
  emit('search', JSON.parse(JSON.stringify(searchCriteria)))
}
</script>

<style lang="scss" scoped="scoped">

    @import '@/assets/styles/main.scss';

    // 篩選清單
    .filter-bar {
        max-width: 1200px;
        max-height: 110px;
        background-color: #fffaf2;
        margin: 0 auto 80px;
        padding-top: 30px;
        padding-bottom: 20px;

        .filter-content {
            display: flex;
            gap: 12px;
            justify-content: center;
            align-items: flex-end;

            .filter-item {
                display: flex;
                margin-left: 50px;
                flex-direction: column;
                label {
                    padding-bottom: 5px;
                    font-size: 24px;
                }
                input,
                select {
                    width: 234px;
                    height: auto;
                    padding: 6px 8px;
                    border: none;
                    border-bottom: 2px solid black;
                    background-color: #fffaf2;
                }
            }

            .search-btn {
                width: 197px;
                height: 48px;
                font-size: 24px;
                padding: 8px 16px;
                margin-left: 50px;
                margin-bottom: 3px;
                color: white;
                border: none;
                border-radius: 5px;
                background-color: $black-14;
                cursor: pointer;
            }
        }
    }
</style>