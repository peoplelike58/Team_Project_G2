<template>
  <div class="map-container">
    <img src="/images/map.png" alt="Map" class="map-image" />

    <!-- 四個閃點 -->
    <div 
      v-for="n in 4" 
      :key="n" 
      :class="['point', 'p' + n]" 
      @click="$emit('startLevel', n)"
    >
      <span class="arrow"><img src="/images/arrow.svg" alt="arrow" width="50px" height="50px" /></span>
    </div>
  </div>
</template>

<script setup>

</script>

<style lang="scss" scoped>
.map-container {
  position: relative;
  width: 100%;
  max-width: 90%;
  margin: 0 auto;
}

.map-image {
  margin: 0 auto;
  width: 73%;
  display: block;
}

/* 四個定位點，用 p1~p4 放在地圖上 */
.point {
  position: absolute;
  width: 20px;
  height: 20px;
  background: #F34336;
  border-radius: 50%;
  animation: blink 1.5s infinite;
  cursor: pointer;

  .arrow {
    position: absolute;
    top: -70px;
    left: -15px;
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

.p1 { top: 88%;  left: 35.5%; }
.p2 { top: 56%;  left: 63.2%; }
.p3 { top: 73.5%;  left: 51.5%; }
.p4 { top: 31.1%;  left: 64.1%; }
</style>
