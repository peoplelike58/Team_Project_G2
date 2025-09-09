<!-- PopularRoutesSection.vue -->
<template>
    <section class="popular-routes">
        <header class="section-header">
            <h1 class="section-title"># 熱門路線</h1>
            <h3 class="section-subtitle">POPULAR ROUTES</h3>
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
                        v-for="route in routes"
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
                            <svg class="arrow" viewBox="0 0 24 24" aria-hidden="true">
                                <!-- 斜線 -->
                                <line x1="5" y1="19" x2="18" y2="6" class="shaft"/>
                                <!-- 箭頭 -->
                                <polyline points="8,5 19,5 19,16" class="head"/>
                            </svg>
                        </button>
                    </li>
                </ul>
            </aside>
        </div>
        <div class="cta-box">
            <RouterLink to="/routes" class="cta">規劃你的路線</RouterLink>
            <button class="diag-btn" aria-label="open">
                <svg class="arrow" viewBox="0 0 24 24" aria-hidden="true">
                    <line x1="5" y1="19" x2="18" y2="6" class="shaft"/>
                    <polyline points="8,5 19,5 19,16" class="head"/>
                </svg>
            </button>
        </div>
    </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import Xiangshan from '@/assets/images/routesImg/Xiangshan.png'
import Qixing    from '@/assets/images/routesImg/Qixing.png'
import Caoling   from '@/assets/images/routesImg/Caoling.png'
import Qingtiangang   from '@/assets/images/routesImg/Qingtiangang.png'
import Wangyou   from '@/assets/images/routesImg/Wangyou.png'
import Hehuan   from '@/assets/images/routesImg/Hehuan.png'

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
            thumb: Xiangshan,
        },
        {
            id: '2',
            name: '七星山主峰、東峰步道',
            distance: 4.8,
            time: { hour: 3, minute: 0 },
            difficulty: '中',
            thumb: Qixing,
        },
        {
            id: '3',
            name: '草嶺古道',
            distance: 8.5,
            time: { hour: 4, minute: 30 },
            difficulty: '中',
            thumb: Caoling,
        },
        {
            id: '4',
            name: '擎天崗環形步道',
            distance: 2.3,
            time: { hour: 1, minute: 10 },
            difficulty: '低',
            thumb: Qingtiangang,
        },
        {
            id: '5',
            name: '忘憂谷步道',
            distance: 1.6,
            time: { hour: 0, minute: 50 },
            difficulty: '低',
            thumb: Wangyou,
        },
        {
            id: '6',
            name: '合歡山北峰步道',
            distance: 3.2,
            time: { hour: 3, minute: 20 },
            difficulty: '高',
            thumb: Hehuan,
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
    padding: 0 40px;
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

    cursor: pointer;
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

.cta-box .cta {
    display: block;
    width: fit-content;
    margin: 0 auto;

    color: $black-14;
    font-size: $pcFont-H3;
    font-weight: $extraBold;
    text-decoration: 1px underline;
    text-underline-offset: 10px;
    line-height: 150%;
    cursor: pointer;
}

//-------------------- route-item 箭頭 --------------------
.open{
    --size: 32px;
    --shift: 4px; /* hover 時往右上位移量 */

    width: var(--size);
    height: var(--size);
    background: $tag;
    border: none;
    border-radius: 4px;
    display: inline-grid;
    place-items: center;
    overflow: hidden;
    transition: transform 0.4s ease;

    position: relative;
}

.open .arrow{
    width: 20px;
    height: 20px;
    transition: transform 0.4s ease;

    position: absolute;
    left: 4px;
    bottom: 4px;
}

.open .shaft, .head{
    stroke: #fff;
    stroke-width: 2px;
    stroke-linecap: square;
    stroke-linejoin: square;
    fill: none;
}

.route-item:hover .open .arrow{
    transform: translate(var(--shift), calc(var(--shift) * -1)); /* 右上 */
}

//-------------------- cta 箭頭 --------------------

.cta-box {
    display: flex;
    align-items: end;
    width: fit-content;
    margin: 0 auto;
    gap: 8px;

    transition: gap 0.4s ease;
}

.cta-box:hover {
    gap: 16px;
}

.cta-box .diag-btn{
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
  
.cta-box .arrow{
    width: var(--icon);
    height: var(--icon);
    position: absolute;
    left: 4px;
    bottom: 4px;
    will-change: transform, opacity;
}
  
.cta-box .shaft, .head{
    stroke: #fff;
    stroke-width: 2px;
    stroke-linecap: square;
    stroke-linejoin: square;
    fill: none;
}

.cta-box:hover .arrow,
.cta-box:focus-visible .arrow{
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

/* 響應式：直欄疊放 */
@media (max-width: 430px) {
    .popular-routes .left {
        display: none;
    }
    .popular-routes .right {
        width: 100%;
        padding: 0 16px;
        box-sizing: border-box;
        border-left: none;
    }
    .right .route-list {
        width: 100%;
    }
    .cta {
        font-size: $pcFont-H4;
        text-underline-offset: 8px;
    }
}
</style>