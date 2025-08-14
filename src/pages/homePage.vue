<template>
    <digitReel
    :to="3952"
    :stagger="0"
    :loopsArr="[1, 1, 1, 1]"
    :durationArr="[1.7, 1.7, 1.7, 1.7]"
    :easeArr="['power4.out', 'back.out(1.2)', 'power2.out', 'power1.out']"
    :heightArr="[56, 56, 56, 56]"
    :delayArr="[0, 0, 0, 0.05]"
    />
    <!-- <parallaxTest/> -->
    <horizontalScroll/>
</template>

<style scoped lang="scss">
    @import '../assets/styles/main.scss';

</style>

<script setup>
    import digitReel from '@/components/homePage/digitReel.vue';
    import parallaxTest from '@/components/homePage/parallaxTest.vue';
    import horizontalScroll from '@/components/homePage/horizontalScroll.vue';
</script>