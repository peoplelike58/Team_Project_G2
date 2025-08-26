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
    <brandFooter/>
</template>

<script setup>
    import navMenu from '@/components/An/navMenu.vue';
    import carousel from '../components/An/carousel.vue';
    import news from '@/components/An/news.vue';
    import horizontalScroll from '@/components/An/horizontalScroll.vue';
    import popularRoutes from '@/components/An/popularRoutes.vue';
    import bridgeSection from '@/components/An/bridgeSection.vue';
    import activitiesSection from '@/components/An/activitiesSection.vue';
    import activitiesJson from '@/components/An/mock/activities.json';
    import brandFooter from '@/components/An/footer.vue';
</script>

<style scoped lang="scss">
    @import '../assets/styles/main.scss';
</style>