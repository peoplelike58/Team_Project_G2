<template>
    <navMenu/>
    <carousel/>
    <news/>
    <horizontalScroll/>
    <popularRoutes/>
    <bridgeSection/>
    <activitiesSection
        :items="activities"
        :limit="3"
    />
    <hallOfFameCarousel/>
    <brandFooter/>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

import navMenu from '@/components/An/navMenu.vue'
import carousel from '@/components/An/carousel.vue'
import news from '@/components/An/news.vue'
import horizontalScroll from '@/components/An/horizontalScroll.vue'
import popularRoutes from '@/components/An/popularRoutes.vue'
import bridgeSection from '@/components/An/bridgeSection.vue'
import activitiesSection from '@/components/An/activitiesSection.vue'
import hallOfFameCarousel from '@/components/An/hallOfFameCarousel.vue'
import brandFooter from '@/components/An/footer.vue'

// import diagonalLaunchButton from '@/components/An/diagonalLaunchButton.vue'

//----------------------------------------------------------------------

const activities = ref([])
const loading = ref(false)
const error = ref('')

onMounted(async () => {
    loading.value = true
    try {
        const { data } = await axios.get(import.meta.env.BASE_URL + 'json/homepage/activities.json') // 或 /api/activities
        // 假資料為純陣列；若後端回 {items,total} 則用 data.items ?? []
        activities.value = Array.isArray(data) ? data : (data.items ?? [])
    } catch (e) {
        error.value = e?.message ?? '載入失敗'
    } finally {
        loading.value = false
    }
})

</script>

<style scoped lang="scss">
@import '@/assets/styles/main.scss';
</style>