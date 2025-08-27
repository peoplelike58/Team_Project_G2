<template>
    <section class="activity-section">
        <div class="content">
            <header class="section-header">
                <h2 class="section-title"># 近期活動</h2>
                <div class="section-subtitle">ACTIVITIES</div>
            </header>

            <!-- 卡片區 -->
            <div class="card-container">
                <template v-if="displayItems.length">
                    <template
                        v-for="(activity, activityIndex) in displayItems"
                        :key="activity.id ?? activityIndex">
                            <activityCard
                                :item="activity"
                                :index="activityIndex"
                                @cta-click="handleCtaClick"
                            />
                    </template>
                </template>
            </div>

            <!-- 底部 CTA -->
            <footer class="section-footer">
                <RouterLink to="/together" class="view-all">查看活動一覽</RouterLink>
            </footer>
        </div>
    </section>
</template>
  
<script setup>
import { computed } from 'vue'
import activityCard from './activityCard.vue'
  
const props = defineProps({
    items: { type: Array, default: () => [] },    // 靜態 JSON 轉來的陣列
    limit: { type: Number, default: 3 },          // 首頁顯示 3 張
})
  
// 取前 limit 筆，並把 imageUrl 轉為可用 URL
const displayItems = computed(() =>
    (props.items).slice(0, props.limit).map(item => ({
        ...item, imageUrl: transformImageUrl(item.imageUrl)
    }))
)

// URL 轉址
const transformImageUrl = (url) => {
    // '@/assets/...' 轉成 /src/assets/... 再用 new URL 解析（讓 Vite 參與打包）
    const normalized = url.replace('@/assets/', '/src/assets/')
    return new URL(normalized, import.meta.url).href
}

const emit = defineEmits(['view-all', 'cta-click'])  
  
function handleCtaClick(item) {
    emit('cta-click', item)
}

</script>
  
<style scoped lang="scss">
@import '@/assets/styles/main.scss';

.activity-section {
    background-color: $ivory-gray-100;
}
.content{
    display: flex;
    flex-direction: column;
    gap: 48px;

    max-width: 1200px;
    margin: 0 auto;
    padding: 80px 0;
}
.section-header { 
    display: flex;
    flex-direction: column; 
    gap: 12px; 
}
.section-title{
    font-size: $pcFont-H1-l;
    font-weight: $bold;
}
.section-subtitle{
    font-size: $pcFont-H3;
    font-weight: $semiBold;
}
.card-container {
    display: flex;
    margin: 0 auto;
    gap: 20px;
}
.section-footer { 
    display: flex; 
    justify-content: center;
}
.view-all {  
    color: $black-14;
    font-size: $pcFont-H3;
    font-weight: $bold;
    text-decoration: 1px underline;
    text-underline-offset: 10px;
    cursor: pointer;
}

</style>
  