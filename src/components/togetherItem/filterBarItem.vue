<template>
    <div class="filter-bar">
        <div class="filter-content">
            <div class="filter-row">
                <div class="filter-item">
                    <label>地點</label>
                    <select v-model="searchCriteria.location">
                        <option value="">全部地點</option>
                        <option v-for="loc in locationOptions" :key="loc" :value="loc">{{ loc }}</option>
                    </select>
                </div>

                <div class="filter-item">
                    <label>日期</label>
                    <input type="date" v-model="searchCriteria.date"/>
                </div>
            </div>

            <div class="filter-row">
                <div class="filter-item">
                    <label>關鍵字</label>
                    <input type="text" placeholder="輸入關鍵字" v-model="searchCriteria.keyword"/>
                </div>

                <button class="search-btn" @click="emitSearch">搜出揪團</button>
            </div>
        </div>
    </div>
</template>

<script setup="setup">
    import {reactive} from 'vue'

    // 由父層傳入可以避免東西寫死
    const props = defineProps({
        locationOptions: {
            type: Array,
            default: () => []
        }
    })

    // 目前設定的查詢條件
    const searchCriteria = reactive({location: '', date: '', keyword: ''})

    // 點擊按鈕時才觸發搜尋
    const emit = defineEmits(['search'])
    function emitSearch() {
        // 避免父層誤改到子層狀態
        emit('search', JSON.parse(JSON.stringify(searchCriteria)))
    }
</script>

<style lang="scss" scoped="scoped">

    @import '@/assets/styles/main.scss';

    // 篩選清單
    .filter-bar {
        width: 1200px;
        max-width: 100%;
        background-color: #fffaf2;
        margin: 0 auto 80px;
        padding: 30px 0px;

        .filter-content {
            display: flex;
            flex-direction: column;
            gap: 20px;
            max-width: 800px;
            margin: 0 auto;
            .filter-row {
                display: flex;
                gap: 20px;
                align-items: flex-end;
                justify-content: center;
            }

            .filter-item {
                display: flex;
                flex-direction: column;
                flex: 1;

                label {
                    padding-bottom: 8px;
                    font-size: 20px;
                    font-weight: 500;
                }
                input,
                select {
                    padding: 10px 12px;
                    border: none;
                    border-bottom: 2px solid black;
                    background-color: #fffaf2;
                    font-size: 16px;
                    transition: border-color 0.3s ease;
                }
            }
        }

        .search-btn {
            padding: 12px 24px;
            font-size: 20px; 
            color: white;
            border: none;
            border-radius: 6px;
            background-color: $black-14;
            cursor: pointer;
            transition: background-color 0.3s ease;
            white-space: nowrap;
            flex-shrink: 0;
        }
    }

    @media screen and (max-width:430px) {
        .filter-bar {
            max-width: 100%;
            padding: 20px 15px;
            margin: 0 auto 40px;

            .filter-content {
                gap: 15px;
                .filter-row {
                    flex-direction: column;
                    gap: 15px;
                    align-items: stretch; //元素填滿寬度
                    .filter-item {
                        width: 100%;
                        label {
                            font-size: 20px;
                            padding-bottom: 6px;
                        }
                    }

                    input,
                    select {
                        width: 100%;
                        font-size: 16px;
                        padding: 12px;
                        border: none;
                        border-bottom: 2px solid black;
                    }
                }
            }

            .search-btn {
                width: 100%; 
                padding: 14px;
                font-size: 20px; 
                margin-top: 10px;
                border-radius: 6px;
            }
        }
    }
</style>