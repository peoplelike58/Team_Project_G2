<template>
<newsFilter
    v-model:keyword="keyword"
    v-model:activeTag="activeTag"
/>
    <section class="board-wrap" ref="boardWrapRef">
        <div class="list-column" ref="listColumnRef">
            <div class="left-title">
                <h1 class="left-title-l"># 消息列表</h1>
                <h3 class="left-title-m">INFORMATION</h3>
            </div>
            <ul class="news-list">
                <li v-for="(item, index) in filtered" :key="item.NEWS_ID" class="news-row">
                    <div class="date-tag">
                        <time class="news-date">{{ item.UPLOAD_AT }}</time>
                        <div class="news-tag">{{ item.TYPE }}</div>
                    </div>
                    <a class="news-title" href="#" @click.prevent>
                        {{ item.TITLE }}
                    </a>
                </li>
            </ul>
            <!-- 載入更多 -->
            <div class="load-controls">
                <button
                v-if="!loading && hasMore && !error"
                class="load-more-btn"
                @click="loadMore"
                >
                載入更多
                </button>
                <p v-if="loading" class="load-text">載入中...</p>
                <p v-if="!hasMore && !loading && news.length" class="load-text">沒有更多了</p>
                <p v-if="!news.length && !loading" class="load-text">敬請期待更多消息...</p>
            </div>
        </div>
    </section>
</template>
  
<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

import newsFilter from '@/components/An/newsFilter.vue'
const keyword = ref('')
const activeTag = ref('全部')

const filtered = computed(() => {
    const kw = keyword.value.trim().toLowerCase()
    return news.value.filter(a => {
        const byTag = activeTag.value === '全部' || a.TYPE === activeTag.value
        const byKw = !kw || String(a?.TITLE ?? '').toLowerCase().includes(kw)
        return byTag && byKw
    })
})


const boardWrapRef = ref(null)
const listColumnRef = ref(null)

/* ---- 分頁狀態 ---- */
const news = ref([])        // 已載入的所有新聞
const page = ref(1)         // 目前頁碼（從 1 開始）
const pageSize = 10         // 每頁幾筆（可做成 prop）
const loading = ref(false)
const hasMore = ref(true)
const error = ref('')

const USE_FAKE = false
const API_URL = USE_FAKE
    ? import.meta.env.BASE_URL + 'json/homepage/news.json'
    : `${import.meta.env.VITE_AJAX_URL}/NewsPage.php`

async function fetchNews({ page: p, pageSize: ps }) {
    loading.value = true
    error.value = ''
    try {
        if (USE_FAKE) {
            const { data } = await axios.get(API_URL)
            const start = (p - 1) * ps
            const slice = data.slice(start, start + ps)
            news.value.push(...slice)
            hasMore.value = start + ps < data.length
        } else {
            const { data } = await axios.get(API_URL, { params: { page: p, pageSize: ps } })

            if (!Array.isArray(data) && Array.isArray(data.items)) {
                news.value.push(...data.items)
                const total = Number(data.total ?? data.count ?? 0)
                hasMore.value = total ? news.value.length < total : (data.items.length === ps)
                return
            }

            const list = Array.isArray(data)
                ? data
                : (Array.isArray(data.data) ? data.data : [])

            const start = (p - 1) * ps
            const slice = list.slice(start, start + ps)

            news.value.push(...slice)
            hasMore.value = start + ps < list.length
        }
    } catch (e) {
        error.value = e?.message ?? '發生未知錯誤'
    } finally {
        loading.value = false
    }
}


function loadMore() {
    if (loading.value || !hasMore.value) return
    page.value += 1
    fetchNews({ page: page.value, pageSize })
}

onMounted(() => {
    fetchNews({ page: page.value, pageSize })
})
</script>
  
<style scoped lang="scss">
@import '@/assets/styles/main.scss';

/* ---- Layout ---- */
.board-wrap {
    display: flex;
    justify-content: space-between;
    align-items: end;
    width: 100%;
    max-width: 1000px;
    margin: 0 auto;
    padding: 40px 24px 120px;
}
  
.list-column {
    flex: 1 0 auto;
    width: 100%;
}

.left-title {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.left-title-l {
    font-size: $pcFont-H1-l;
    font-weight: $bold;
}

.left-title-m {
    font-size: $pcFont-H3;
    font-weight: $semiBold;
}

  
/* ---- List ---- */
.news-list {
    display: flex;
    flex-direction: column;
    margin: 16px 0 48px;
    list-style: none;
}

.news-row {
    display: flex;
    align-items: center;
    gap: 24px;

    padding: 24px 20px;
    border-bottom: 1px dashed $tag;

    transition: gap 0.4s ease;

    cursor: pointer;
}
  
.news-row:hover {
    gap: 32px;
}

.date-tag {
    display: flex;
    align-items: center;
    gap: 24px;
    transition: gap 0.4s ease;
}

.news-row:hover .date-tag {
    gap: 32px;
}

.news-date {
    font-size: $pcFont-label;
    font-weight: $semiBold;
    min-width: 100px; /* 齊頭對齊日期寬度 */
}

.news-tag {
    padding: 8px 0;
    width: 100px;
    min-width: 80px;
    background: $tag;
    color: #fff;
    font-size: 14px;
    font-weight: $medium;
    text-align: center;
    white-space: nowrap;
    border-radius: 4px;
}
  
.news-title {
    font-weight: $semiBold;
    font-size: $pcFont-p-s;
    line-height: $lineHeight-p-150;
    color: $black-14;
    text-decoration: none;
    transition: opacity 0.4s ease;

    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis; 
}
.news-row:hover .news-title{
    opacity: 0.7;
}
.load-more-btn {
    display: block;
    width: 160px;
    margin: 0 auto;
    padding: 8px 0;
    background-color: $black-14;
    border: none;
    border-radius: 4px;
    font-weight: $bold;
    font-size: 16px;
    color: #fff;
    transition: background-color 0.3s ease;
    cursor: pointer;
}
.load-more-btn:hover {
    background-color: rgba(20, 20, 20, 0.8);
}
.load-text{
    font-size: $pcFont-p-s;
    color: #5F6368;
    text-align: center;
}

/* ---- RWD ---- */
@media (max-width: 768px) {
    .board-wrap {
        flex-direction: column;
        align-items: stretch;

        padding: 40px 24px;
        box-sizing: border-box;
    }
    .news-row {
        padding: 20px 8px;
    }
    .date-tag {
        flex-direction: column;
        align-items: center;
        gap: 12px;
    }
    .news-row:hover .date-tag {
        gap: 12px;
    }
    .news-date {
        text-align: center;
    }
    .news-title {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        white-space: wrap;
    }
}
</style>