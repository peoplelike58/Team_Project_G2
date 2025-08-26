<template>
    <!-- 上方輪播圖 -->
    <NavMenu/>
    <CardLoopItem/>

    <!-- 篩選選單 -->
    <FilterBarItem/>

    <!-- 活動卡片區塊 -->
    <div class="cardWrapper">
        <div class="cardList">
            <EventCard
                v-for="(event, index) in paginatedItems"
                :key="event.id"
                :item="event"
                :index="(currentPage - 1) * itemsPerPage + i"
                @cta-click="onCtaClick"/>
        </div>
    </div>

    <!-- 頁碼 -->
    <div class="pageItem">
        <button @click="toPage(currentPage - 1)" :disabled="currentPage === 1">
            <
                </button>
                <button v-for="page in totalPages" :key="page" @click="toPage(page)" :class="{ active: page === currentPage }">
                {{ page }}
                </button>
                <button @click="toPage(currentPage + 1)" :disabled="currentPage === totalPages">></button>
        </div>

        <!-- 問與答區塊 -->
        <div class="qaList">
            <TogetherQnaItem/>
        </div>

    </template>

    <script setup="setup">
        import NavMenu from '@/components/An/navMenu.vue';
        import FilterBarItem from '@/components/togetherItem/filterBarItem.vue';
        import CardLoopItem from '@/components/togetherItem/cardLoopItem.vue'
        import EventCard from '@/components/togetherItem/eventCard.vue';
        import TogetherQnaItem from '@/components/togetherItem/togetherQnaItem.vue';
        import activitiesJson from '@/components/togetherItem/activities.json';

        import {ref, computed} from 'vue'
        // 每頁顯示筆數
        const itemsPerPage = 6
        // 當前頁
        const currentPage = ref(1)
        // 總頁數
        const totalPages = computed(
            () => Math.ceil(activitiesJson.length / itemsPerPage)
        )
        // 當前頁要顯示的資料切片
        const paginatedItems = computed(() => {
            const start = (currentPage.value - 1) * itemsPerPage
            return activitiesJson.slice(start, start + itemsPerPage)
        })

        // 切換頁面函式
        function toPage(page) {
            if (page < 1 || page > totalPages.value) 
                return
            currentPage.value = page
        }
    </script>

    <style lang="scss" scoped="scoped">

        @import '../assets/styles/main.scss';
        // 活動卡片區塊
        .cardList {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 30px;
            max-width: 1080px;
        }

        .cardWrapper{
            // border: 1px solid red;
            width: 1080px;
            height: auto;
            margin: 0 auto 50px;
        }
        

        // 問與答區塊
        .qaList {
            // border: 2px solid red;
            width: 1080px;
            height: 650px;
            margin: 50px auto 0;
        }

        // 分頁按鈕
        .pageItem {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-bottom: 80px;

            button {
                padding: 6px 12px;
                border: 1px solid $black-14;
                background: #fff;
                cursor: pointer;
                transition: background 0.2s;

                &:disabled {
                    opacity: 0.4;
                    cursor: not-allowed;
                }

                &.active {
                    background: $black-14;
                    color: #fff;
                }

                &:hover:not(:disabled):not(.active) {
                    background: #f0f0f0;
                }
            }
        }
    </style>