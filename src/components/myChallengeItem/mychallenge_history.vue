<template>
    <section class="mychallengeHistroy">
        <div class="back" @click="closeHistory">
            <img :src="`${BASE}images/myChallenge/left.png`" alt="">
            <h4>返回</h4>
        </div>
        <div class="title">
            <h2>[ 歷史足跡 ]</h2>
                <p>💡 雙擊山名可顯示完整名稱</p>
        </div>
        <div class="myhistory">
            <div class="myhistoryTitle">
                <h3 class="mountainName">山名</h3>
                <h3>上傳日期</h3>
            </div>
            <article class="myhistoryMountain" v-for="(history, index) in histories" >
                <div class="mountainTitle">
                    <div class="mountainTitleLeft" @click="toggle(index)">
                        <h4 class="mountain"
                            :class="{ 'show-full': showFullName === index }"
                            @dblclick="toggleFullName(index)"
                            :title="history.name"                      
                        >{{ history.name }}</h4>
                        <h4>{{ history.date }}</h4>
                    </div>
                    <img 
                    :src="`${BASE}images/myChallenge/down.png`"
                    alt="down"
                    :class="{ 'rotated': openItem === index }"
                    />
                </div>
                <transition name="dropdown">
                    <div class="totalScore"  v-show="openItem == index">
                        <div class="total">
                            <p>[累積高度]</p>
                            <p><span>{{ history.height }}</span> <br/>m</p>
                        </div>
                        <div class="total">
                            <p>[累積里程]</p>
                            <p><span>{{ history.kilo }}</span> <br/>km</p>
                        </div>
                        <div class="total">
                            <p>[累積時間]</p>
                            <p><span>{{ history.time }}</span> <br/>hr</p>
                        </div>
                    </div>
                </transition>
            </article>
        </div>
        
    </section>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import axios from 'axios'

    const props = defineProps({
        isLoggedIn: {
            type: Boolean,
            default: false
        }
    })

    // --- 1.控制手風琴開關 ---
    const openItem = ref(null)     // 全關
    const showFullName = ref(null)

    const toggle = (index) => {
        if(openItem.value == index){
            // 如果點擊的是已經展開的項目 → 關閉它
            openItem.value = null
        }else{
            // 如果點擊的是其他項目 → 展開它（同時會關閉之前展開的）
            openItem.value = index
        }
    }

    const isMobile = computed(() => {
        return window.innerWidth <= 768
    })

    const toggleFullName = (index) => {
        if(showFullName.value === index){
            showFullName.value = null
        }else{
            showFullName.value = index
        }
        
        setTimeout(() => {
            if(showFullName.value === index) {
                showFullName.value = null
            }
        }, 3000)
    }

    // --- 2.emit 傳遞事件 ---
    const emit = defineEmits(['closeHistoryComp'])

    const closeHistory = () => {
        emit('closeHistoryComp')  // 告訴父組件要關閉 history
    }

    // --- 3.載入資料 ---
    const histories = ref([])

    const BASE = import.meta.env.BASE_URL
    // const jsonPath = `http://localhost/php/mychallenge_history.php?member_id=${memberId.value}`
    const API_URL = `${import.meta.env.VITE_AJAX_URL}/mychallenge_history.php`

    // 載入歷史資料
    const loadHistories = async () => {
         if (!props.isLoggedIn) {
            histories.value = []
            console.log('用戶未登入，清空歷史資料')
            return
        }

        try {
            const response = await axios.post(API_URL, {}, {
                withCredentials: true,  // ← 讓 session 可以運作
                headers: {
                    'Content-Type': 'application/json'
                }
            })

            if (response.data.success && response.data.data) {
                if (props.isLoggedIn) {
                    // 已登入且有資料
                    histories.value = response.data.data || []
                    console.log('歷史資料載入成功:', response.data)
                } else {
                    // 未登入
                    histories.value = []
                    console.log('用戶未登入')
                    console.log('Session 內容:', response.data.session_data)
                }
            } else {
                histories.value = []
                console.log('無歷史資料或未登入')
            }
            
        } catch (err) {
            console.error("讀取失敗:", err)
            histories.value = []
        }
    }

    watch(() => props.isLoggedIn, async (newValue, oldValue) => {
        console.log('History 組件：登入狀態變化', oldValue, '->', newValue)
        
        if (newValue === false) {
            // 登出時清空資料
            histories.value = []
            openItem.value = null  // 關閉所有展開項目
            console.log('已清空歷史資料')
        } else if (newValue === true) {
            // 登入時重新載入
            await loadHistories()
        }
    })
    
    onMounted(async() => {
        if (props.isLoggedIn) {
            await loadHistories()
        }
    })

    defineExpose({ 
        loadHistories,
        refreshData: loadHistories 
    })

</script>

<style scoped lang="scss">
    @import '../../assets/styles/main.scss';
    .mychallengeHistroy{
        height: 100%;
        overflow: auto;

        scrollbar-width: none; /* Firefox */
        &::-webkit-scrollbar {
            display: none; /* Chrome/Safari/Opera */
        }

        .back{
            display: flex;
            align-items: center; 
            cursor: pointer;   
            
            img{
                margin-right: 24px;
                height: 20px;
            }

            @media screen and (max-width: 768px) {
                h4{
                    font-size: $pcFont-H2;
                }

                img{
                    height: 40px;
                }
            }
        }

        .title{
            margin: 40px 0;
            display: flex;
            flex-direction: column;
            h2{
                font-size: $pcFont-H2;
                font-weight: $semiBold;
            }
            p{
                font-size: $pcFont-H4;
                align-self: flex-end;
                
                
                @media (min-width: 769px) {
                    display: none;
                }
            }
        }

        .myhistoryTitle{
            position: relative;
            display: flex;
            padding-bottom: 20px;
            border-bottom: 1px solid $black-14;
            
            .mountainName{
                width: 100px;
                margin-right: 40px;
                
            }

            &::after{
                content: '';
                position: absolute;
                left: 120px;
                width: 1px;
                height: 42px;
                background-color: $black-14;
            }

            h3{
                font-size: $pcFont-H3;
                font-weight: $bold;
                line-height: $lineHeight-p-150;
            }
        }

        .myhistoryMountain{
            border-bottom: 1px dashed ;
            
            .mountainTitle{
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin: 20px 0;
                cursor: pointer;
                
                .mountainTitleLeft{
                    display: flex;
                    position: relative;
                    
                    h4{
                        font-size: $pcFont-H4;
                        font-weight: $medium;
                        line-height: 42px;
                        
                        &:first-of-type{
                            width: 100px;
                            margin-right: 40px;
                            overflow: hidden;
                            white-space: nowrap;
                            text-overflow: ellipsis;
                            
                            // hover 顯示完整內容
                            &:hover {
                                overflow: visible;
                                white-space: normal;
                                background-color: rgba(255, 255, 255, 0.9);
                                padding: 2px 4px;
                                border-radius: 4px;
                                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                                position: relative;
                                z-index: 10;
                            }
                            
                            &::after{
                                content: '';
                                position: absolute;
                                left: 120px;
                                width: 1px;
                                height: 42px;
                                background-color: $black-14;
                            }
                        }   
                    }
                }

                img{
                    width: 40px;
                    height: 40px;

                    transition: all 0.3s ease-in-out;
                    transform-origin: center;

                    &.rotated{
                        transform: rotate(-180deg);
                    }
                }
            }

            .totalScore{
                display: flex;
                margin: 16px 0 24px;
                
                .total{
                    width: calc(100% / 3);
                    display: flex;
                    flex-direction: column;
                    
                    div:nth-child(1n+2){
                        margin-left: 24px;
                    }
                    
                    
                    p{
                        font-size: $pcFont-p-s;
                        font-weight: $bold;
                        line-height: $lineHeight-title-120;
                        
                        span{
                            font-size: $pcFont-H1-m;
                            font-weight: $medium;
                            line-height: $lineHeight-title-120;
                        
                            @media screen and (max-width: 490px) {
                                font-size: $pcFont-H3;
                            }
                        }
                    }
                }

                @media screen and (max-width: 1200px) {

                    flex-direction: column;
                    gap: 16px;
                }
            }
        }



        .dropdown-enter-active{
            transition: all 0.3s ease;
        }

        .dropdown-enter-from {
            opacity: 0;
            transform: translateY(-20px);
        }
    
    }

</style>