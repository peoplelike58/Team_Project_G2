<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'



const pics = [ // 需要串接 trail.detailUrl
  '/img/trails/1.jpg',
  '/img/trails/2.jpg',
  '/img/trails/3.jpg'
]

const current = ref(0) // 當前圖片索引
const intervalTime = 5000 // 自動播放時間（5 秒）
let timer = null // 定時器

function next() {
  current.value = (current.value + 1) % pics.length
}

function goTo(index) {
  current.value = index
}

function startAutoPlay() {
  stopAutoPlay()
  timer = setInterval(next, intervalTime)
}

function stopAutoPlay() {
  if (timer) {
    clearInterval(timer)
    timer = null
  }
}

// 滑鼠拖曳邏輯
let startX = 0
let isDragging = false

function handleStart(e) {
  isDragging = true
  startX = e.type === 'touchstart' ? e.touches[0].clientX : e.clientX
  stopAutoPlay()
}

function handleMove(e) {
  if (!isDragging) return
  // 可加滑動預覽效果（進階功能）
}

function handleEnd(e) {
  if (!isDragging) return
  const endX = e.type === 'touchend' ? e.changedTouches[0].clientX : e.clientX
  const deltaX = endX - startX

  if (deltaX > 50) {
    current.value = (current.value - 1 + pics.length) % pics.length
  } else if (deltaX < -50) {
    current.value = (current.value + 1) % pics.length
  }

  isDragging = false
  startAutoPlay()
}

// 掛載後自動播放、卸載時清除
onMounted(startAutoPlay)
onBeforeUnmount(stopAutoPlay)
</script>

<template>
  <div class="routeContent">
    <h3>大霸尖山</h3> <!-- 需串接 trail.name -->
    <span>新竹縣尖石鄉 苗栗縣泰安鄉</span> <!-- 需串接 trail.region -->

    <div class="mainContent">
      <div
        class="mainPic"
        @mousedown="handleStart"
        @mousemove="handleMove"
        @mouseup="handleEnd"
        @mouseleave="() => { if (isDragging) handleEnd(); startAutoPlay() }"
        @touchstart="handleStart"
        @touchmove="handleMove"
        @touchend="handleEnd"
        @mouseenter="stopAutoPlay"
        >
        <div class="carousel-window">
            <div class="track" :style="{ transform: `translateX(-${current * 100}%)` }">
                <div class="slide" v-for="(src, index) in pics" :key="index">
                <img :src="src" :alt="`slide-${index}`" />
                </div>
            </div>
        </div>

        <div class="dots">
          <span
            v-for="(src, index) in pics"
            :key="'dot-' + index"
            class="dot"
            :class="{ active: current === index }"
            @click="goTo(index)"
          ></span>
        </div>
      </div>

      <p> <!-- 需串接 trail.introduce -->
        　　大霸尖山因其宏偉的岩柱外型，被譽為「帝王之山」，為台灣最具代表性的百岳之一。<br><br>
        　　登頂過程需要穿越林道、溪谷與稜線，是體力與毅力的挑戰。沿途風景原始壯麗，常與小霸尖山、伊澤山、加利山連走，是北部經典縱走路線之一。
      </p>
    </div>
  </div>



</template>


<style lang="scss"> /* 全域樣式（非 scoped） */
body { // 全頁背景色
  background-color: #EFF1F2; // 淺灰底
}
</style>

<style scoped lang="scss"> /* 元件私有樣式 */
@import '../../assets/styles/main.scss';

.routeContent{
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 48px 40px;
    h3{ // 山名
        font-size: $pcFont-H2;
        font-weight: $semiBold;
        line-height: $lineHeight-title-120;
    }

    span{ // 地區
        color: #666;
        line-height: $lineHeight-p-200;
    }

    .mainContent{ // 輪播圖&字介紹外框
        margin-top: 24px;
        display: flex;
        gap: 50px;
        height: 336px;

        .mainPic{ // 輪播
            width: 100%;
            max-width: 430px;
            // border:1px solid black;

            .carousel-window { // 圖外框
                width: 100%;
                height: 310px;
                aspect-ratio: 4 / 3;
                overflow: hidden;
                border-radius: 8px;
                
                // border: 1px solid rgb(141, 31, 31);

            }

            .track {
                display: flex;
                transition: transform 0.5s ease; // 滑動動畫 //
            }

            .slide {
                flex: 0 0 100%;
                display: flex;
                justify-content: center;
                align-items: center;
                background-color: #f7f7f7;
            }

            img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                user-select: none; // 禁止拖曳圖片 //
                pointer-events: none;
            }

            .dots {
                display: flex;
                justify-content: center;
                gap: 16px;
                margin-top: 16px;
            }

            .dot {
                width: 10px;
                height: 10px;
                background-color: #ccc;
                border-radius: 50%;
                cursor: pointer;
                transition: all 0.3s;
            }

            .dot.active {
                background-color: #333;
            }
        }

        }

        p{
            // border: 1px solid black;
            width: 52%;
            max-width: 632px;
            height: 310px;
            padding: 40px 0;
            box-sizing: border-box;

            font-weight: $regular;
            line-height: $lineHeight-p-200;

        }



    }

</style>