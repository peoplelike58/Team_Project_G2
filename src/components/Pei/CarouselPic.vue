<script setup>
import { ref, onMounted, onBeforeUnmount,defineProps } from 'vue'

const props = defineProps({
  trail: {
    type: Object,
    // required: true
  }
})

// 當前圖片索引
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
            <div class="slide" v-for="(img, index) in props.trail.detailUrl" :key="index">
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
@import '../../assets/styles/main.scss';

.routeContent {
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  padding: 40px 40px;

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
    height: 336px;

    .mainPic {
      width: 100%;
      max-width: 430px;

    .carousel-window { // 輪播視窗框
      width: 100%;
      height: 310px;
      border-radius: 8px;   // 四個角都圓角
      overflow: hidden;     // 超出裁切掉，圖片才會被套圓角
      position: relative;
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
      border-radius: 8px;  // 保險，避免被 overflow 漏掉
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

        &.active {
          background-color: #333;
        }
      }
    }

    p {
      width: 52%;
      max-width: 632px;
      height: 310px;
      padding: 40px 0;
      box-sizing: border-box;
      font-weight: $regular;
      line-height: $lineHeight-p-200;
    }
  }
}
</style>
