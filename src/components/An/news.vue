<!-- src/components/NewsTeaserBoard.vue -->
<template>
    <section class="board-wrap" ref="boardWrapRef">
        <!-- Left: list -->
        <div class="list-column" ref="listColumnRef">
            <div class="left-title">
                <h1 class="left-title-l"># 最新消息</h1>
                <h3 class="left-title-m">INFORMATION</h3>
            </div>
            <ul class="news-list">
                <li v-for="(item, index) in newsItems" :key="`news-${index}`" class="news-row">
                    <div class="date-tag">
                        <time class="news-date">{{ item.date }}</time>
                        <div class="news-tag">{{ item.tag }}</div>
                    </div>
  
                    <a class="news-title" href="#" @click.prevent>
                        {{ item.title }}
                    </a>
                </li>
            </ul>
            <div class="view-box">
                <a href="#" class="view-all" @click.prevent>
                    查看全部消息
                </a>
                <button class="diag-btn" aria-label="open">
                    <svg class="arrow" viewBox="0 0 24 24" aria-hidden="true">
                        <line x1="5" y1="19" x2="18" y2="6" class="shaft"/>
                        <polyline points="8,5 19,5 19,16" class="head"/>
                    </svg>
                </button>
            </div>
        </div>
  
        <!-- Right: circular badge -->
        <div class="badge-wrap" ref="badgeColumnRef">
            <svg class="badge-svg" viewBox="0 0 300 300">
                <defs>
                    <path id="badgeCirclePath" d="M150,150 m-120,0 a120,120 0 1,1 240,0 a120,120 0 1,1 -240,0" />
                </defs>

                <!-- 外圈文字 -->
                <g class="spin-slow">
                    <text class="badge-text">
                        <textPath xlink:href="#badgeCirclePath" startOffset="0%">
                            MOUNTAIN PICK · SEE YOU UP THERE ·  
                        </textPath>
                    </text>
                </g>

                <!-- 中心箭頭 -->
                <g class="badge-center">
                    <circle cx="150" cy="103" r="3" />
                    <circle cx="150" cy="118" r="4" />
                    <line x1="150" y1="135" x2="150" y2="195" />
                    <polyline points="135,180 150,195 165,180" />
                </g>
            </svg>
        </div>

    </section>
</template>
  
<script setup>
import {ref, onMounted} from 'vue'
import gsap from 'gsap'
import {ScrollTrigger} from 'gsap/ScrollTrigger'

gsap.registerPlugin(ScrollTrigger)

const boardWrapRef = ref(null)
const listColumnRef = ref(null)
const badgeColumnRef = ref(null)
  
// demo data
const newsItems = [
    { date: '2025.08.23', tag: '路線旅遊',   title: '手作家山步道，微笑山線由你守護' },
    { date: '2025.08.17', tag: '新聞時事', title: '受豪雨影響步道0.55k崩塌，北大武山步道暫停開放' },
    { date: '2025.08.14', tag: '登山知識',   title: '規劃得好，山就走得順 - 行程規劃指南' },
    { date: '2025.08.03', tag: '路線旅遊',   title: '淡蘭北路：從車站出發，山海美景與小吃全收錄' },
    { date: '2025.07.28', tag: '登山知識',   title: '新手不越級，行程這樣安排才安全' },
]
  
// 視差滾動控制
onMounted(() => {
    const timeline = gsap.timeline({
        defaults: { ease: 'none' },
        scrollTrigger: {
            trigger: boardWrapRef.value,
            start: 'top top',
            end: 'bottom top',
            scrub: true,
        },
    })
  
    timeline
        // left
        .to(listColumnRef.value, { yPercent: -10 }, 0)
        // right
        .to(badgeColumnRef.value, { yPercent: -100 }, 0)
    })
</script>
  
<style scoped lang="scss">
@import '../../assets/styles/main.scss';

/* ---- Layout ---- */
.board-wrap {
    display: flex;
    justify-content: space-between;
    align-items: end;
    width: 100%;
    max-width: 1000px;
    margin: 0 auto;
    padding: 100px 24px 120px;
}

  
.list-column {
    flex: 1 0 auto;
    max-width: 640px;
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

.view-box {
    display: flex;
    align-items: end;
    width: fit-content;
    margin: 0 auto;
    gap: 8px;

    transition: gap 0.4s ease;
}

.view-box:hover {
    gap: 16px;
}
  
.view-box .view-all {
    display: flex;
    align-items: end;
    gap: 16px;
    width: fit-content;
    margin: 0 auto;

    font-size: $pcFont-H4;
    font-weight: $bold;
    color: $black-14;
    text-decoration: 1px underline;
    text-underline-offset: 8px;
    line-height: 150%
}
  
/* ---- Badge ---- */
.badge-wrap {
    width: 240px;
    aspect-ratio: 1/1;
    position: relative;
}


.badge-svg {
    width: 100%;
    height: 100%;
    display: block;
}
  
.badge-text {
    font: $black 32px 'Inter';
    letter-spacing: 0.24rem;
    text-transform: uppercase;
    fill: #24936E;
}

.badge-center line,
.badge-center polyline {
    stroke: #24936E;
    stroke-width: 8;
    fill: none;
    stroke-linecap: round;
    stroke-linejoin: round;
}
.badge-center circle {
    fill: #24936E;
}
  
/* 旋轉動畫 */
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}
.spin-slow {
    animation: spin 18s linear infinite;
    transform-origin: 50% 50%;
}

.view-box .diag-btn{
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
  
.view-box .arrow{
    width: var(--icon);
    height: var(--icon);
    position: absolute;
    left: 4px;
    bottom: 4px;
    will-change: transform, opacity;
}
  
.view-box .shaft, .head{
    stroke: #fff;
    stroke-width: 2px;
    stroke-linecap: square;
    stroke-linejoin: square;
    fill: none;
}

.view-box:hover .arrow,
.view-box:focus-visible .arrow{
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

/* ---- RWD ---- */
@media (max-width: 430px) {
    .board-wrap {
        flex-direction: column;
        align-items: stretch;

        padding: 64px 24px;
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
    .badge-wrap {
        display: none;
    }
}
</style>
  