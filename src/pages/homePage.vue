<template>
    <navMenu/>
    <carousel/>
    <news/>
    <popularRoutes/>
    <!-- <activityCard/> -->
    <activitiesSection
        :items="activitiesJson"
        :limit="3"
    />
</template>

<script setup>
    import navMenu from '@/components/An/navMenu.vue';
    import carousel from '../components/An/carousel.vue';
    import news from '@/components/An/news.vue';
    import popularRoutes from '@/components/An/popularRoutes.vue';
    // import activityCard from '@/components/An/activityCard.vue';
    import activitiesSection from '@/components/An/activitiesSection.vue';
    import activitiesJson from '@/components/An/mock/activities.json';
</script>

<style scoped lang="scss">
    @import '../assets/styles/main.scss';
</style>