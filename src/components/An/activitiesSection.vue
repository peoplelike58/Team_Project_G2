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
                        />
                    </template>
                </template>
            </div>

            <!-- 底部 CTA -->
            <footer class="section-footer">
                <RouterLink to="/together" class="view-all">查看活動一覽</RouterLink>
                <button class="diag-btn" aria-label="open">
                    <svg class="arrow" viewBox="0 0 24 24" aria-hidden="true">
                        <line x1="5" y1="19" x2="18" y2="6" class="shaft"/>
                        <polyline points="8,5 19,5 19,16" class="head"/>
                    </svg>
                </button>
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

const displayItems = computed(() => {
   const list = Array.isArray(props.items) ? props.items : []
   return props.limit > 0 ? list.slice(0, props.limit) : list
})

</script>
  
<style scoped lang="scss">
@import '@/assets/styles/main.scss';

.activity-section {
    background-color: $ivory-gray-100;
    padding: 0 40px;
    box-sizing: border-box;
}
.content{
    display: flex;
    flex-direction: column;
    gap: 48px;

    // width: fit-content;
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
    gap: 24px;
}
.section-footer { 
    display: flex;
    align-items: end;
    width: fit-content;
    margin: 0 auto;
    gap: 8px;

    transition: gap 0.4s ease;
}
.section-footer:hover {
    gap: 16px;
}
.section-footer .view-all {  
    color: $black-14;
    font-size: $pcFont-H3;
    font-weight: $bold;
    text-decoration: 1px underline;
    text-underline-offset: 10px;
    line-height: 150%;
    cursor: pointer;
}

.section-footer .diag-btn{
    --size: 40px;        /* 按鈕尺寸 */
    --icon: 24px;        /* 箭頭大小 */
    --fly: 24px;         /* 飛出距離（右上 / 左下） */
    --dur: 720ms;        /* 動畫時間 */
  
    position: relative;
    width: var(--size);
    height: var(--size);
    background-color: $tag;
    border: 0;
    border-radius: 4px;
    overflow: hidden;
}
  
.section-footer .arrow{
    width: var(--icon);
    height: var(--icon);
    position: absolute;
    left: 4px;
    bottom: 4px;
    will-change: transform, opacity;
}
  
.section-footer .shaft, .head{
    stroke: #fff;
    stroke-width: 2px;
    stroke-linecap: square;
    stroke-linejoin: square;
    fill: none;
}

.section-footer:hover .arrow,
.section-footer:focus-visible .arrow{
    animation: boomerang45 var(--dur) cubic-bezier(.2,.7,.2,1) 1;
}
  
@keyframes boomerang45 {
    0% {
        transform: translate(0, 0);
        opacity: 1;
    }
    55% {
        transform: translate(var(--fly), calc(var(--fly) * -1)); /* 右上 */
        opacity: 0;
    }
    56% {
        transform: translate(calc(var(--fly) * -1), var(--fly));  /* 左下 */
        opacity: 0;
    }
    100% {
        transform: translate(0, 0);
        opacity: 1;
    }
}

@media (max-width: 430px) {
    .activity-section{
        padding: 0;
    }
    .activity-section .section-header{
        margin-left: 24px;
    }
    .activity-section .view-all{
        font-size: $pcFont-H4;
        text-underline-offset: 8px;
    }
    .activity-section .card-container{
        width: 100%;
        padding: 0 24px;
        box-sizing: border-box;

        overflow-x: auto;
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
    }
}
</style>
  