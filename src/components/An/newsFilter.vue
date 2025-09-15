<template>
    <div class="filter-bar">
        <!-- 分類選單 -->
        <div class="filter-tags">
            <button
                v-for="tag in tags"
                :key="tag"
                :class="{ active: activeTag === tag }"
                @click="$emit('update:activeTag', tag)"
            >
                {{ tag }}
            </button>
        </div>
  
        <!-- 搜尋框 -->
        <div class="filter-search">
            <input
                type="text"
                :value="keyword"
                placeholder="搜尋文章"
                @input="$emit('update:keyword', $event.target.value)"
            />
            <button class="search-btn">🔍</button>
        </div>
    </div>
</template>
  
<script setup>
const props = defineProps({
    keyword: String,
    activeTag: String,
    tags: { type: Array, default: () => ['全部', '新聞時事', '登山知識', '路線旅遊'] }
})
</script>
  
<style scoped lang="scss">
  .filter-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 0;
    border-bottom: 1px solid #ddd;
    gap: 16px;
  }
  
  .filter-tags {
    display: flex;
    gap: 12px;
  
    button {
      background: none;
      border: none;
      cursor: pointer;
      font-size: 14px;
      padding: 6px 12px;
      border-radius: 4px;
      transition: all 0.3s;
  
      &.active {
        background: #4caf50;
        color: #fff;
        font-weight: bold;
      }
  
      &:hover {
        opacity: 0.7;
      }
    }
  }
  
  .filter-search {
    display: flex;
    align-items: center;
    gap: 8px;
  
    input {
      padding: 6px 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      min-width: 200px;
    }
  
    .search-btn {
      background: none;
      border: none;
      cursor: pointer;
      font-size: 16px;
    }
  }
</style>
  