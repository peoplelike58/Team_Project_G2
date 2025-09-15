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
                <li v-for="(item, index) in news" :key="item.id ?? `news-${index}`" class="news-row">
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
                <RouterLink to="/allnewspage" class="view-all" @click.prevent>
                    查看全部消息
                </RouterLink>
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
import { ref, onMounted, onBeforeUnmount, onActivated, onDeactivated, nextTick } from 'vue'
import axios from 'axios'
import gsap from 'gsap'
import {ScrollTrigger} from 'gsap/ScrollTrigger'

gsap.registerPlugin(ScrollTrigger)
let ctx // gsap context
let inited = false // 是否已初始化 GSAP

const boardWrapRef = ref(null)
const listColumnRef = ref(null)
const badgeColumnRef = ref(null)
  
// 遠端資料狀態
const news = ref([])          // 渲染來源
const loading = ref(false)
const error = ref('')
 
// 假資料模式：/public/news.json；之後接後端把 USE_FAKE 改 false，API_ENDPOINT 換掉即可
const USE_FAKE = true
const API_ENDPOINT = USE_FAKE ? import.meta.env.BASE_URL + 'json/homepage/news.json' : '/api/news'

async function fetchNews() {
    if (news.value.length) return
    loading.value = true
    error.value = ''
    try {
        const { data } = await axios.get(API_ENDPOINT)
        // 假資料模式：data 為「純陣列」
        // 若後端回傳 { items: [...], total: 123 }，可改成：news.value = data.items ?? []
        news.value = Array.isArray(data) ? data : (data.items ?? [])
    } catch (e) {
        error.value = e?.message ?? '載入失敗'
    } finally {
        loading.value = false
    }
}

function initGsap() {
    if (inited) return
    ctx = gsap.context(() => {
        const timeline = gsap.timeline({
            defaults: { ease: 'none' },
            scrollTrigger: {
                trigger: boardWrapRef.value,
                start: 'top top',
                end: 'bottom top',
                scrub: true,
                invalidateOnRefresh: true, // 高度變動時自動重算
            },
        })
        if (listColumnRef.value) timeline.to(listColumnRef.value, { yPercent: -10 }, 0)
        if (badgeColumnRef.value) timeline.to(badgeColumnRef.value, { yPercent: -100 }, 0)
    })
    inited = true
    requestAnimationFrame(() => ScrollTrigger.refresh())
}

function destroyGsap() {
    ctx?.revert()
    ctx = null
    inited = false
}
  
onMounted(async () => { await fetchNews()     // 先拿資料
    await nextTick()      // 等 DOM 渲染完成
    initGsap()            // 再初始化 GSAP
})

onBeforeUnmount(() => {
    destroyGsap()
})

onActivated(async () => {  // 回到頁面：資料已在（因為上面做了快取），只要刷新觸發點
    await nextTick()
    initGsap()
    ScrollTrigger.refresh()
})

onDeactivated(() => {
    // 被切走（但未銷毀）也要還原，避免重複初始化
    destroyGsap()
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

      @media (max-width: 980px) {
      flex-direction: column;
      align-items: stretch;
}
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
    margin-left: 40px;
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
  