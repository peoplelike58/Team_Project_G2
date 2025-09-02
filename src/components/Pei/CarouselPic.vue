<script setup>
import { ref, onMounted, onBeforeUnmount,defineProps,computed } from 'vue';

const props = defineProps({
  trail: {
    type: Object,
    // required: true
  }
})

// trail.detailUrl轉址
const baseUrl = import.meta.env.BASE_URL                                     // 取得部署子目錄（例如 '/tjd102/g2/'） // 繁中註解

const toUrl = (p) => {                                                        // 將字串路徑轉為可用網址的工具函式 // 繁中註解
  if (!p) return ''                                                           // 空值直接回空字串 // 繁中註解
  if (p.startsWith('http') || p.startsWith('data:') || p.startsWith('/assets/')) return p // 已是完整/打包資產就原樣回傳 // 繁中註解
  return `${baseUrl}${String(p).replace(/^\/+/, '')}`                         // 其他情況加上 base 並移除開頭斜線 // 繁中註解
}

const images = computed(() => {                                               // 計算屬性：把 detailUrl 全部轉成完整網址 // 繁中註解
  const list = Array.isArray(props.trail?.detailUrl) ? props.trail.detailUrl : [] // 取得字串陣列或空陣列 // 繁中註解
  return list.map(toUrl)                                                      // 逐一轉換成可用網址 // 繁中註解
})


// 做輪播,當前圖片索引
const current = ref(0)
const intervalTime = 5000
let timer = null

function next() {
  if (!props.trail.detailUrl?.length) return
  current.value = (current.value + 1) % props.trail.detailUrl.length
}

function goTo(index) {
  current.value = index
}

function startAutoPlay() {
  stopAutoPlay()
  if (props.trail.detailUrl?.length > 1) {
    timer = setInterval(next, intervalTime)
  }
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

function handleEnd(e) {
  if (!isDragging) return
  const endX = e.type === 'touchend' ? e.changedTouches[0].clientX : e.clientX
  const deltaX = endX - startX

  if (deltaX > 50) {
    current.value = (current.value - 1 + props.trail.detailUrl.length) % props.trail.detailUrl.length
  } else if (deltaX < -50) {
    current.value = (current.value + 1) % props.trail.detailUrl.length
  }

  isDragging = false
  startAutoPlay()
}

onMounted(startAutoPlay)
onBeforeUnmount(stopAutoPlay)
</script>

<template>
  <div class="routeContent">
    <h3>{{ props.trail.name }}</h3>
    <span>{{ props.trail.region }}</span>

    <div class="mainContent">
      <div
        class="mainPic"
        @mousedown="handleStart"
        @mouseup="handleEnd"
        @mouseleave="() => { if (isDragging) handleEnd(); startAutoPlay() }"
        @touchstart="handleStart"
        @touchend="handleEnd"
        @mouseenter="stopAutoPlay"
      >
        <!-- 輪播圖 -->
        <div class="carousel-window">
          <div class="track" :style="{ transform: `translateX(-${current * 100}%)` }">
            <div class="slide" v-for="(img, index) in images" :key="index">
              <img :src="img" />
            </div>
          </div>
        </div>

        <!-- 下方圓點 -->
        <div class="dots">
          <span
            v-for="(img, index) in props.trail.detailUrl"
            :key="'dot-' + index"
            class="dot"
            :class="{ active: current === index }"
            @click="goTo(index)"
          ></span>
        </div>
      </div>

      <p v-html="props.trail.introduce"></p>
    </div>
  </div>
</template>

<style scoped lang="scss">
@import '@/assets/styles/main.scss';
@import '@/assets/styles/mixins';

.routeContent {
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  padding: 40px;

  @include m(){
    max-width: 430px;
    padding: 20px;
    font-size: 14px;
    box-sizing: border-box;
  }

  h3 {
    font-size: $pcFont-H2;
    font-weight: $semiBold;
    line-height: $lineHeight-title-120;

  }

  span {
    color: #666;
    line-height: $lineHeight-p-200;
  }

  .mainContent {
    margin-top: 24px;
    display: flex;
    gap: 50px;
    padding: 30px 0;
    
    @include m(){
      flex-direction: column;
      gap:20px;
    }


    .mainPic {
      width: 100%;
      max-width: 500px;
      @include m(){
        max-width: 390px;
      }



      .carousel-window { // 輪播視窗框

        width: 100%;
        height: 350px;
        border-radius: 8px;   // 四個角都圓角
        overflow: hidden;     // 超出裁切掉，圖片才會被套圓角
        position: relative;
        object-fit: cover;   // 保持比例裁切填滿


        @include m(){
          height: 250px;
        }
      }

      .track {
        display: flex;
        transition: transform 0.5s ease; // 滑動動畫
      }

      .slide {
        flex: 0 0 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #f7f7f7;
      }

      .slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;   // 保持比例裁切填滿
        display: block;
        border-radius: 8px;  
      }


      .dots {
        display: flex;
        justify-content: center;
        gap: 16px;
        margin-top: 16px;
        @include m(){
          margin-top: 8px;
        }
      }

      .dot {
        width: 10px;
        height: 10px;
        background-color: #ccc;
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.3s;

        &.active {
          background-color: #333;
        }
      }
    }

    p {
      width: 52%;
      max-width: 632px;
      
      padding: 40px 0;
      box-sizing: border-box;
      font-weight: $regular;
      line-height: $lineHeight-p-200;
      @include m(){
        width: 100%;
        max-width: 390px;
        padding: 20px 0;
       
      }

    }
  }
}
</style>
