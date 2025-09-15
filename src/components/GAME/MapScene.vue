<template>
  <div class="map-container">
    <img src="/images/eventCard/cardimg1.jpg" alt="Map" class="map-image" />

    <!-- 四個閃點 -->
    <div 
      v-for="n in 4" 
      :key="n" 
      :class="['point', 'p' + n]" 
      @click="$emit('startLevel', n)"
    >
      <span class="arrow"><img src="@/assets/arrow.svg" alt="arrow" /></span>
    </div>
  </div>
</template>

<script setup>

</script>

<style lang="scss" scoped>
.map-container {
  position: relative;
  width: 100%;
  max-width: 800px;
  margin: 0 auto;
}

.map-image {
  width: 100%;
  display: block;
}

/* 四個定位點，用 p1~p4 放在地圖上 */
.point {
  position: absolute;
  width: 20px;
  height: 20px;
  background: rgba(255,255,0,0.8);
  border-radius: 50%;
  animation: blink 1s infinite;
  cursor: pointer;

  .arrow {
    position: absolute;
    top: -24px;
    left: -6px;
    opacity: 0;
    transition: opacity 0.2s;
  }
  &:hover .arrow {
    opacity: 1;
  }
}

@keyframes blink {
  0%, 100% { opacity: 1; }
  50%      { opacity: 0.3; }
}

/* 手動調整四點位置 */
.p1 { top: 20%;  left: 30%; }
.p2 { top: 40%;  left: 70%; }
.p3 { top: 65%;  left: 25%; }
.p4 { top: 80%;  left: 60%; }
</style>
