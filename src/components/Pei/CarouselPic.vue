<script setup>
import { ref, onMounted, onBeforeUnmount, defineProps, computed } from 'vue'

const props = defineProps({
  trail: { type: Object }
})

const baseUrl = import.meta.env.BASE_URL

// 只吃 trail.imgDetail（字串 JSON），直接拚固定路徑
const images = computed(() =>
  JSON.parse(props.trail?.imgDetail || '[]').map((fileName) =>
    `${baseUrl}images/Mountain/${props.trail.MOUNTAIN_ID}/${fileName}`
  )
)

// 輪播狀態
const current = ref(0)
const intervalTime = 5000
let timer = null

function next() {
  if (images.value.length <= 1) return
  current.value = (current.value + 1) % images.value.length
}

function goTo(index) {
  if (index >= 0 && index < images.value.length) {
    current.value = index
  }
}

function startAutoPlay() {
  stopAutoPlay()
  if (images.value.length > 1) {
    timer = setInterval(next, intervalTime)
  }
}

function stopAutoPlay() {
  if (timer) {
    clearInterval(timer)
    timer = null
  }
}

// 拖曳
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
    current.value = (current.value - 1 + images.value.length) % images.value.length
  } else if (deltaX < -50) {
    current.value = (current.value + 1) % images.value.length
  }

  isDragging = false
  startAutoPlay()
}

onMounted(startAutoPlay)
onBeforeUnmount(stopAutoPlay)

</script>

<template>
  <div class="routeContent">
    <h3>{{ props.trail.MOUNTAIN_NAME }}</h3>
    <span>{{ props.trail.REGION }}</span>

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
        <div class="dots" v-if="images.length > 1">
          <span 
            v-for="(_, i) in images.length"
            :key="i" class="dot"
            :class="{ active: current === i }" 
            @click="goTo(i)">
          </span>
        </div>
      </div>

      <p v-html="props.trail.INTRO"></p>
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
    max-width: 768px;
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
        max-width: 728px;
      
      }



      .carousel-window { // 輪播視窗框

        width: 100%;
        height: 350px;
        border-radius: 8px;   // 四個角都圓角
        overflow: hidden;     // 超出裁切掉，圖片才會被套圓角
        position: relative;
        object-fit: cover;   // 保持比例裁切填滿


        @include m(){
          height: 330px;
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
      
      padding: 50px 0 40px;
      box-sizing: border-box;
      font-weight: $regular;
      line-height: $lineHeight-p-200;
      @include m(){
        width: 100%;
        max-width: 100%;
        padding: 20px 0;
       
      }

    }
  }
}
</style>
