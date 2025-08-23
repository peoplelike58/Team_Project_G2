<template>
    <section class="activity-section">
        <div class="content">
            <header class="section-header">
                <h2 class="section-title"># 近期活動</h2>
                <div class="section-subtitle">ACTIVITIES</div>
            </header>

            <!-- 卡片區 -->
            <div class="card-container">
                <template v-if="activitiesJson.length">
                 <template
                        v-for="(activity, activityIndex) in activitiesJson"
                        :key="activity.id ?? activityIndex">
                        <slot name="item" :item="activity" :index="activityIndex">
                            <activityCard
                                :item="activity"
                                :index="activityIndex"
                                @cta-click="handleCtaClick"
                            />
                        </slot>
                    </template>
                </template>
            </div>

            <!-- 底部 CTA -->
            <footer class="section-footer">
                <a
                    class="view-all"
                >
                    查看活動一覽
                </a>
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
  
const emit = defineEmits(['view-all', 'cta-click'])
  
const activitiesJson = computed(() => props.items.slice(0, props.limit))
  
function handleCtaClick(item) {
    emit('cta-click', item)
}
</script>
  
<style scoped lang="scss">
@import '../../assets/styles/main.scss';

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
    font-size: $pcFont-H3;
    font-weight: $bold;
    text-decoration: 1px underline;
    text-underline-offset: 10px;
}

</style>
  