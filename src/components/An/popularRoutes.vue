<!-- PopularRoutesSection.vue -->
<template>
    <section class="popular-routes">
        <header class="section-header">
            <h1 class="section-title"># 熱門路線</h1>
            <div class="section-subtitle">POPULAR ROUTES</div>
        </header>

        <div class="content">
             <!-- 左欄：主資訊 -->
            <div class="left">
                <Transition name="fade" mode="out-in">
                    <div class="route-detail" :key="activeRoute.id">
                        <h2 class="route-title">[ {{ activeRoute.name }} ]</h2>

                        <div class="meta">
                            <div class="meta-block">
                                <span class="label">里程</span>
                                <div class="value">
                                    <strong class="num">{{ activeRoute.distance }}</strong>
                                    <span class="unit">公里</span>
                                </div>
                            </div>

                            <div class="meta-block">
                                <span class="label">花費時間</span>
                                <div class="value">
                                    <strong class="num">{{ activeRoute.time.hour }}</strong>
                                    <span class="unit">小時</span>
                                    <strong class="num">{{ activeRoute.time.minute }}</strong>
                                    <span class="unit">分鐘</span>
                                </div>
                            </div>

                            <div class="meta-block">
                                <span class="label">難度</span>
                                <div class="value">
                                    <strong class="num">{{ activeRoute.difficulty }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>

    
            <!-- 右欄：列表 -->
            <aside class="right">
                <ul class="route-list">
                    <li
                        v-for="route in limitedRoutes"
                        :key="route.id"
                        class="route-item"
                        tabindex="0"
                        @mouseenter="onHoverEnter(route)"
                        @focus="onHoverEnter(route)"
                        :class="{ 'is-hovered': hoveredRoute && hoveredRoute.id === route.id }"
                    >
                        <div class="left-part">
                            <img :src="route.thumb" alt="" class="avatar" />
                            <div class="divider"></div>
                            <a class="name" href="#">{{ route.name }}</a>
                        </div>
                        <button class="open">
                            ↗
                        </button>
                    </li>
                </ul>
            </aside>
        </div>
        <button class="cta">規劃你的路線</button>
    </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const MAX_ITEMS = 6 // 最大顯示筆數

const routes = ref([])
const featured = ref({
    id: '',
    name: ' 載入中... ',
    distance: 0,
    time: { hour: 0, minute: 0 },
    difficulty: '',
})

const hoveredRoute = ref(null) // 目前滑入的項目
const activeRoute = computed(() => hoveredRoute.value || featured.value) // 資料渲染到左側

function onHoverEnter(route) { hoveredRoute.value = route }

const limitedRoutes = computed(() => routes.value.slice(0, MAX_ITEMS))

onMounted(() => {

    // 假資料（之後改成 API 取回）
    const mock = [
        {
            id: '1',
            name: '象山親山步道',
            distance: 2.5,
            time: { hour: 1, minute: 40 },
            difficulty: '低',
            thumb: 'https://picsum.photos/seed/route1/120/120',
        },
        {
            id: '2',
            name: '七星山主峰、東峰步道',
            distance: 4.8,
            time: { hour: 3, minute: 0 },
            difficulty: '中',
            thumb: 'https://picsum.photos/seed/route2/120/120',
        },
        {
            id: '3',
            name: '草嶺古道',
            distance: 8.5,
            time: { hour: 4, minute: 30 },
            difficulty: '中',
            thumb: 'https://picsum.photos/seed/route3/120/120',
        },
        {
            id: '4',
            name: '擎天崗環形步道',
            distance: 2.3,
            time: { hour: 1, minute: 10 },
            difficulty: '低',
            thumb: 'https://picsum.photos/seed/route4/120/120',
        },
        {
            id: '5',
            name: '忘憂谷步道',
            distance: 1.6,
            time: { hour: 0, minute: 50 },
            difficulty: '低',
            thumb: 'https://picsum.photos/seed/route5/120/120',
        },
        {
            id: '6',
            name: '合歡山北峰步道',
            distance: 3.2,
            time: { hour: 3, minute: 20 },
            difficulty: '高',
            thumb: 'https://picsum.photos/seed/route6/120/120',
        },
    ]

    routes.value = mock
    featured.value = mock[0]
})
</script>

<style scoped lang="scss">
@import '../../assets/styles/main.scss';

.popular-routes {
    padding: 80px 0;
    background-color: $bg-pink-100;
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

.content {
    display: flex;
    align-items: center;
    max-width: 1000px;
    margin: 48px auto;
    gap: var(--gap);
}

.left {
    width: 100%;
    padding: 40px 0;
    align-self: stretch;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.4s ease, transform 0.4s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(12px);
}

.route-detail{
    display: flex;
    flex-direction: column;
    height: 100%;
}

.route-title {
    font-size: $pcFont-H2;
    font-weight: $bold;
}

/* 左欄內部資訊區 */
.meta {
    display: flex;
    flex-direction: column;
    justify-content: space-around;

    margin-top: 40px;
    height: 100%;

}

.meta-block .label {
    display: inline-block;
    font-size: $pcFont-H4;
    font-weight: $semiBold;
    margin-bottom: 16px;
}

.meta-block .value {
    display: flex;
    align-items: baseline;
    gap: 8px;
}

.num {
    font-size: 2.5rem;
    font-weight: $bold;
}

.unit {
    font-size: $pcFont-label;
    font-weight: $medium;
}

/* 右欄 */
.right {
    border-left: 1px solid $black-14;
    padding: 4px 0 16px 56px;
}

.route-list {
    display: flex;
    flex-direction: column;
    width: 480px
}

/* 單列項目 */
.route-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 12px;
    border-bottom: 1px dashed $black-14;
}

.left-part {
    display: flex;
    align-items: center;
    gap: 24px;

    transition: gap 0.4s ease;
}

.route-item:hover .left-part{
    gap: 32px;
}

.avatar {
    width: 56px;
    aspect-ratio: 1/1;
    border-radius: 50%;
    object-fit: cover;
    flex: 0 0 auto;

    border: 2px solid $bg-pink-100;
    outline: 1px solid $tag;
    outline-offset: 2px;
}

.divider{
    display: block;
    min-width: 1px;
    height: 36px;
    background-color: $black-14;
}

.name {
    color: $black-14;
    font-size: $pcFont-p-m;
    font-weight: $semiBold;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-decoration: none;
}

.open {
    display: block;
    width: 32px;
    aspect-ratio: 1/1;
    border: none;
    background-color: $tag;
    border-radius: 4px;
    color: #fff;
    cursor: pointer;
}

.cta {
    display: block;
    margin: 0 auto;
    border: none;
    border-bottom: 1px solid $black-14;
    padding: 0 0 2px;
    background: transparent;

    font-size: $pcFont-H3;
    font-weight: $extraBold;
    cursor: pointer;
}

/* 響應式：直欄疊放 */
@media (max-width: 960px) {
  .content {
    flex-direction: column;
  }
  .right {
    width: 100%;
    border-left: none;
    border-top: var(--divider);
    padding-left: 0;
    padding-top: var(--gap);
  }
  .meta {
    grid-template-columns: 1fr;
  }
}
</style>
