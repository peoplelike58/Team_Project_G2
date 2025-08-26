<template>
    <section class="mychallengeHistroy">
        <div class="back" @click="closeHistory">
            <img src="../../assets/images/mychallenge/left.png" alt="">
            <h4>返回</h4>
        </div>
        <h2>[ 歷史足跡 ]</h2>
        <div class="myhistory">
            <div class="myhistoryTitle">
                <h3 class="mountainName">山名</h3>
                <h3>上傳日期</h3>
            </div>
            <article class="myhistoryMountain" v-for="history in histories">
                <div class="mountainTitle">
                    <div class="mountainTitleLeft" @click="toggle(history.name)">
                        <h4 class="mountain">{{ history.name }}</h4>
                        <h4>{{ history.date }}</h4>
                    </div>
                    <img src="../../assets/images/mychallenge/down.png" alt="">
                </div>
                <transition name="dropdown">
                    <div class="totalScore"  v-show="openItem == history.name">
                        <div class="total">
                            <p>[累積高度]</p>
                            <p><span>{{ history.height }}</span> m</p>
                        </div>
                        <div class="total">
                            <p>[累積里程]</p>
                            <p><span>{{ history.kilo }}</span> km</p>
                        </div>
                        <div class="total">
                            <p>[累積時間]</p>
                            <p><span>{{ history.time }}</span> hr</p>
                        </div>
                    </div>
                </transition>
            </article>
        </div>
    </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'

    // --- 1.控制手風琴開關 ---
    const openItem = ref(null)     // 全關

    const toggle = (index) => {
        if(openItem.value == index){
            // 如果點擊的是已經展開的項目 → 關閉它
            openItem.value = null
        }else{
            // 如果點擊的是其他項目 → 展開它（同時會關閉之前展開的）
            openItem.value = index
        }
    }

    // --- 2.emit 傳遞事件 ---
    const emit = defineEmits(['closeHistoryComp'])

    const closeHistory = () => {
        emit('closeHistoryComp')  // 告訴父組件要關閉 history
    }

    // --- 3.載入 Json資料 ---
    const histories = ref({})

    onMounted(async() => {
        try{
            const res = await fetch("/json/mychallenge/histories.json")
            // console.log(res)
            const data = await res.json()
            // console.log(data)
            // 將資料存入變數
            histories.value = data
        }catch(err){
            console.error("讀取失敗:", err)
        }
    
    })

</script>

<style scoped lang="scss">
    @import '../../assets/styles/main.scss';
    .mychallengeHistroy{
        height: 100%;
        overflow: auto;

        .back{
            display: flex;
            align-items: center; 
            cursor: pointer;   
            
            img{
                margin-right: 24px;
                height: 20px;
            }
        }

        h2{
            font-size: $pcFont-H2;
            font-weight: $semiBold;
            margin: 40px 0;
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
                    width: 16px;
                    height: 8px;
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
                        }
                    }
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