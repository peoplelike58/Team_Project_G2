<script setup>
import rawActivities from '@/assets/activities.json'
import EventCard from '@/components/EventCard.vue'

// 將 JSON 內的相對圖片路徑轉換成 Vite 可用 URL
const activities = rawActivities.map(item => ({
  ...item,
  imageUrl: new URL(`../assets/${item.imageUrl}`, import.meta.url).href
}))
</script>

<template>
  <section class="activities">
    <EventCard
      v-for="(act, idx) in activities"
      :key="act.id"
      :item="act"
      :index="idx"
      @cta-click="(data) => console.log('CTA 點擊', data)"
    />
  </section>
</template>


<style scoped lang="scss">
.activities {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
  gap: 24px;
}
</style>
