<template>
    <navMenu/>
    <carousel/>
    <news/>
    <horizontalScroll/>
    <popularRoutes/>
    <bridgeSection/>
    <activitiesSection
        :items="activitiesJson"
        :limit="3"
    />
    <hallOfFameCarousel/>
    <brandFooter/>
</template>

<script setup>
import { ref, onMounted } from 'vue'

import navMenu from '@/components/An/navMenu.vue'
import carousel from '../components/An/carousel.vue'
import news from '@/components/An/news.vue'
import horizontalScroll from '@/components/An/horizontalScroll.vue'
import popularRoutes from '@/components/An/popularRoutes.vue'
import bridgeSection from '@/components/An/bridgeSection.vue'
import activitiesSection from '@/components/An/activitiesSection.vue'
import hallOfFameCarousel from '@/components/An/hallOfFameCarousel.vue'
import brandFooter from '@/components/An/footer.vue'

import diagonalLaunchButton from '@/components/An/diagonalLaunchButton.vue'

//----------------------------------------------------------------------

const activitiesJson = ref([])

onMounted(async () => {
    const res = await fetch('/json/homepage/activities.json')
    activitiesJson.value = await res.json()
})

</script>

<style scoped lang="scss">
@import '../assets/styles/main.scss';
</style>