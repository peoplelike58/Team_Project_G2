<template>
    <!-- 篩選選單 -->
     <div class="filter-bar">
            <div class="filter-content">
                <div class="filter-item">
                    <label>地點</label>
                    <select v-model="filters.location">
                        <option value="">選擇城市</option>
                        <option>台北</option>
                        <option>新北</option>
                        <option>台中</option>
                        <option>高雄</option>
                    </select>
                </div>

                <div class="filter-item">
                    <label>日期</label>
                    <input type="date" v-model="filters.date"/>
                </div>

                <div class="filter-item">
                    <label>關鍵字</label>
                    <input type="text" placeholder="輸入關鍵字" v-model="filters.keyword"/>
                </div>

                <button class="search-btn" @click="search">搜出揪團</button>
            </div>
        </div>
</template>

<script setup>

    // 篩選清單
    import {reactive} from 'vue'

    const filters = reactive({location: '', date: '', keyword: ''})

    const search = () => {
        console.log('搜尋條件：', filters)
    }

</script>

<style lang="scss" scoped>

    @import '@/assets/styles/main.scss';

    // 篩選清單
    .filter-bar {
        max-width: 1200px;
        max-height: 110px;
        background-color: #fffaf2;
        margin: 0 auto;
        margin-bottom: 80px;
        padding-top: 30px;

        .filter-content {
            display: flex;
            gap: 12px;
            justify-content: center;
            align-items: flex-end;

            .filter-item {
                display: flex;
                margin-left: 50px;
                flex-direction: column;
                label {
                    padding-bottom: 5px;
                    font-size: 24px;
                }
                input,
                select {
                    width: 234px;
                    height: auto;
                    padding: 6px 8px;
                    border: none;
                    border-bottom: 2px solid black;
                    background-color: #fffaf2;
                }
            }

            .search-btn {
                width: 197px;
                height: 48px;
                font-size: 24px;
                padding: 8px 16px;
                margin-left: 50px;
                margin-bottom: 3px;
                color: white;
                border: none;
                border-radius: 5px;
                background-color: $black-14;
                cursor: pointer;
            }
        }
    }

</style>