<template>
    <div class="breadcrumb">
        <router-link to="/homepage" class="toHome">首頁</router-link>
        >
        <p class="nowAt">最新消息</p>
    </div>
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
    tags: { type: Array, default: () => ['全部', '新聞時事', '登山知識', '路線旅遊','話題'] }
})
</script>
  
<style scoped lang="scss">
@import '@/assets/styles/main.scss';
.breadcrumb {
    display: flex;
    gap: 8px;
    padding: 0 40px;
    margin: 12px auto 64px;
}
.toHome {
    text-decoration: none;
    color: #666;
}
.nowAt {
    font-weight: $bold;
}
.filter-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 952px;
    margin: 0 auto;
    padding: 16px 0;
    gap: 16px;
}
  
.filter-tags {
    display: flex;
    gap: 12px;
  
    button {
        background: none;
        border: none;
        cursor: pointer;
        font-size: $pcFont-label;
        padding: 8px 12px;
        border-radius: 4px;
        transition: all 0.3s;
  
        &.active {
            background: #4caf50;
            color: #fff;
            font-weight: $bold;
        }
    }
}
  
.filter-search {
    display: flex;
    align-items: center;
    gap: 8px;
  
    input {
        padding: 8px 12px;
        border: 1px solid #ccc;
        border-radius: 4px;
        min-width: 200px;
        font-size: 14px;
    }
  
    .search-btn {
      background: none;
      border: none;
      cursor: pointer;
      font-size: 16px;
    }
}

@media (max-width: 768px) {
    .breadcrumb {
        padding: 0 24px;
        margin-bottom: 40px;
    }
    .filter-bar {
        padding: 16px 24px;
    }
}

@media (max-width: 430px) {
    .breadcrumb {
        margin-bottom: 24px;
    }
    .filter-bar {
        flex-direction: column;
        align-items: start;
    }
    .filter-tags{
        button {
            padding: 4px 12px;
        }
    }
}
</style>
  