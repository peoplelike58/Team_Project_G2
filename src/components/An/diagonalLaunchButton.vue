<!-- BoomerangArrowButton.vue -->
<template>
    <button class="diag-btn" aria-label="open">
        <svg class="arrow" viewBox="0 0 24 24" aria-hidden="true">
            <line x1="5" y1="19" x2="18" y2="6" class="shaft"/>
            <polyline points="8,5 19,5 19,16" class="head"/>
        </svg>
    </button>
</template>
  
<script setup>
  
</script>
  
<style scoped>
@import '@/assets/styles/main.scss';

.diag-btn{
    --size: 40px;        /* 按鈕尺寸 */
    --icon: 24px;        /* 箭頭大小 */
    --fly: 24px;         /* 飛出距離（右上 / 左下） */
    --dur: 720ms;        /* 動畫時間 */
  
    position: relative;
    width: var(--size);
    height: var(--size);
    background-color: #262626;
    border: 0;
    border-radius: 4px;
    overflow: hidden;
}
  
.arrow{
    width: var(--icon);
    height: var(--icon);
    position: absolute;
    left: 4px;
    bottom: 4px;
    will-change: transform, opacity;
}
  
.shaft, .head{
    stroke: #fff;
    stroke-width: 2px;
    stroke-linecap: square;
    stroke-linejoin: square;
    fill: none;
}

.diag-btn:hover .arrow,
.diag-btn:focus-visible .arrow{
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
</style>
  