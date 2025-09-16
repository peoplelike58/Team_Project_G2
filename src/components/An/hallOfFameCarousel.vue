<template>
    <section class="carousel-section" aria-label="百岳勇士名人堂">
        <header class="section-header">
            <h1 class="section-title"># 百岳勇士名人堂</h1>
            <h3 class="section-subtitle">HALL OF FAME</h3>
        </header>
  
        <div class="carousel-viewport" @mouseenter="isPaused = true" @mouseleave="isPaused = false">
            <div
            class="carousel-track"
            :style="{
                '--scroll-duration': scrollDurationSeconds + 's',
                'animation-play-state': isPaused ? 'paused' : 'running'
            }"
            role="list"
            >
                <template v-for="(dataItem, dataIndex) in duplicatedDataList" :key="dataItem.id + '-' + dataIndex">
                    <article class="carousel-card" role="listitem">
                        <img class="card-image" :src="dataItem.avatarUrl" :alt="dataItem.name + ' 的照片'" />
                        <div class="card-meta">
                            <div class="card-name">{{ dataItem.name }}</div>
                            <div class="card-stats">
                                <span>百岳 {{ dataItem.heroCount }} 座</span>
                                <span class="divider">|</span>
                                <span>小百岳 {{ dataItem.minorCount }} 座</span>
                            </div>
                        </div>
                    </article>
                </template>
            </div>
            <div
            class="carousel-track"
            :style="{
                '--scroll-duration': scrollDurationSeconds + 's',
                'animation-play-state': isPaused ? 'paused' : 'running'
            }"
            role="list"
            >
                <template v-for="(dataItem, dataIndex) in duplicatedDataList" :key="dataItem.id + '-' + dataIndex">
                    <article class="carousel-card" role="listitem">
                        <img class="card-image" :src="dataItem.avatarUrl" :alt="dataItem.name + ' 的照片'" />
                        <div class="card-meta">
                            <div class="card-name">{{ dataItem.name }}</div>
                            <div class="card-stats">
                                <span>百岳 {{ dataItem.heroCount }} 座</span>
                                <span class="divider">|</span>
                                <span>小百岳 {{ dataItem.minorCount }} 座</span>
                            </div>
                        </div>
                    </article>
                </template>
            </div>
        </div>
    </section>
</template>
  
<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
  
const originalDataList = ref([])
const isPaused = ref(false)
  
onMounted(async () => {
    try {
        const { data } = await axios.get(import.meta.env.BASE_URL + 'json/homepage/hallOfFame.json')
        // data 就是已經解析好的 JSON，不需要再 .json()
        originalDataList.value = data
    } catch (error) {
        console.error('載入 JSON 發生錯誤:', error)
    }
})

const duplicatedDataList = computed(() => [...originalDataList.value])
  
// 動畫秒數
const scrollDurationSeconds = 20
</script>
  
<style scoped lang="scss">
@import '@/assets/styles/main.scss';
.carousel-section {
    padding: 80px 0;
}

.section-header { 
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;

    width: fit-content;
    margin: 0 auto;
}
  
.section-title {
    font-size: $pcFont-H1-l;
    font-weight: $bold;
}

.section-subtitle{
    font-size: $pcFont-H3;
    font-weight: $semiBold;
}
  
.carousel-viewport{
    display: flex;
    width: 100%;
    overflow: hidden;
    padding: 48px 0;
}

.carousel-track{
    display: flex;
    gap: 24px;
    width: max-content;
    padding-left: 24px;
    box-sizing: border-box;
    will-change: transform;
    animation-name: hof-scroll-left;
    animation-timing-function: linear;
    animation-iteration-count: infinite;
    animation-duration: var(--scroll-duration, 30s);
}
  
@keyframes hof-scroll-left{
    from {
        transform: translateX(0);
    }
    to {
        transform: translateX(-100%);
    }
}
  
.carousel-card {
    flex: 0 0 280px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 6px 24px rgba(0,0,0,0.08);
    padding: 40px 24px;
}
  
.card-image {
    width: 100%;
    max-width: 160px;
    aspect-ratio: 1/1;
    object-fit: cover;
    border-radius: 50%;
}
  
.card-meta {
    width: 100%;
    text-align: center;
}
  
.card-name {
    font-weight: $semiBold;
    font-size: 20px;
    margin-bottom: 12px;
}
  
.card-stats {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #333;
    font-size: 16px;
}
  
.divider {
    opacity: 0.3;
}
  
@media (max-width: 768px) {
    .carousel-card { 
        flex: 0 0 280px;
    }
}
 </style>
  