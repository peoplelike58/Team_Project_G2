<template>
    <div class="section-main">
        <h1 class="title"># 還沒決定目的地？</h1>
        <h3 class="subtitle">別擔心，先看看近期的熱門活動，說不定就有你喜歡的！</h3>
        <p class="text">不必一開始就煩惱要挑哪條路線，先來看看近期熱門活動吧。從輕鬆健走到挑戰高山，這裡有多種選擇，還能結識志同道合的山友。說不定，一個有趣的活動就能替你安排好整趟行程，讓你輕鬆踏出第一步！</p>
    </div>
</template>
<script setup>
</script>
<style scoped lang="scss">
    @import '@/assets/styles/main.scss';
    
    .section-main {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-end;
        gap: 40px;
        margin-left: auto;
        margin-right: 160px;
        width: 100%;
        max-width: 640px;
        height: 90vh;
    }

    .title {
        font-size: $pcFont-bigTitle-m;
        font-weight: $bold;
    }
    .subtitle {
        font-size: $pcFont-H3;
        font-weight: $semiBold;
    }
    .text {
        font-weight: $semiBold;
        line-height: $lineHeight-p-200;
    }

    @media (max-width: 430px) {
        .section-main {
            height: 80vh;
            padding: 0 24px;
            box-sizing: border-box;

            align-items: flex-start;
        }
        .title {
            font-size: $pcFont-H1-l;
            font-weight: $bold;
        }
        .subtitle {
            line-height: $lineHeight-title-120;
        }
    }
</style>