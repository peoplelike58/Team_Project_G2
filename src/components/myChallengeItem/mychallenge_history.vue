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
import { ref, reactive } from 'vue'

    const histories = reactive([
        {name: '玉山', date:'2025.08.09', height: 3852, kilo: 320, time:52},
        {name: '阿里山', date:'2025.06.23', height: 2413, kilo: 365, time:44},
        {name: '合歡山', date:'2025.05.20', height: 774, kilo: 58, time:56},
        {name: '雪山', date:'2025.04.31', height: 1314, kilo: 520, time:74},
        {name: '大霸尖山', date:'2025.03.23', height: 317, kilo: 45, time: 8},
    ])

    // 控制手風琴開關
    const openItem = ref(null)



    const toggle = (index) => {
        if(openItem.value == index){
            openItem.value = null
        }else{
            openItem.value = index
        }
    }

    const emit = defineEmits(['closeHistoryComp'])

    const closeHistory = () => {
        emit('closeHistoryComp')  // 告訴父組件要關閉 history
    }

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