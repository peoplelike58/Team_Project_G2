<template>
    <!-- 上方輪播圖 -->
    <header>
        <CardLoopItem/>
    </header>

    <!-- 篩選選單 -->
    <main>
        <FilterBarItem/>
    </main>

    <!-- 活動卡片區塊 -->
    <div class="cardList"></div>

    <!-- 頁碼 -->
    <div>
        
    </div>

    <!-- 問與答區塊 -->
    <div class="qaList">
        <TogetherQnaItem/>
    </div>

</template>

<script setup="setup">
    import CardLoopItem from '@/components/togetherItem/cardLoopItem.vue'
    import FilterBarItem from '@/components/togetherItem/filterBarItem.vue';
    import PageItem from '@/components/togetherItem/pageItem.vue';
    import TogetherQnaItem from '@/components/togetherItem/togetherQnaItem.vue';
</script>

<style lang="scss" scoped="scoped">

    @import '../assets/styles/main.scss';
    // 活動卡片區塊
    .cardList {
        border: 2px solid red;
        width: 1080px;
        height: 873px;
        margin: 0 auto 179px;
    }

    // 問與答區塊
    .qaList {
        border: 2px solid red;
        width: 1080px;
        height: 650px;
        margin: 0 auto;
    }
</style>