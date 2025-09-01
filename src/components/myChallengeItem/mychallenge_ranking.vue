<template>
    <section v-for="rank in ranks" :key="rank.rank" class="nomb">
        <div class="personInfo" @click="toggle(rank.rank)">
            <div class="personInfoTitle">
                <img :src="`${BASE}images/myChallenge/${rank.image}`" alt="" class="head">
                <h4 class="place">{{ rank.rank }}<br />{{ rank.icon }}</h4>
                <h4>{{ rank.name }}</h4>
            </div>
            <div class="allow">
                <img :src="`${BASE}images/myChallenge/down.png`" 
                alt="allow" 
                :class="{ 'rotated': openItem === rank.rank }"
                >
            </div>
        </div>
        <transition name="dropdown">
        <div class="totalScore" v-show="openItem == rank.rank">
            <div class="nombScore">
                <article>
                    <p>總累積高度</p>
                    <p><span>{{ rank.height }}</span> m</p>
                </article>
                <article>
                    <p>總累積里程</p>
                    <p><span>{{ rank.kilo }}</span> km</p>
                </article>
                <article>
                    <p>總累積時間</p>
                    <p><span>{{ rank.time }}</span> hr</p>
                </article>
            </div>
            <div class="mountScore">
                <article>
                    <p>百岳</p>
                    <p><span>{{ rank.big }}</span> 座</p>
                </article>
                <article>
                    <p>百岳</p>
                    <p><span>{{ rank.small }}</span> 座</p>
                </article>
            </div>
        </div>
        </transition>
    </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

    // const ranks = ref([
    //     {image:'head1.png', rank:'第一名', icon:`🥇`, name: 'Yuki', height:10006, kilo:9687, time:456, big:41, small:58 },
    //     {image:'head2.png', rank:'第二名', icon:`🥈`, name: '貓山王', height:8187, kilo:7432, time:400, big:40, small:40 },
    //     {image:'head3.png', rank:'第三名', icon:`🥉`, name: 'JIN', height:6742, kilo:7213, time:420, big:40, small:32 },
    //     {image:'head4.png', rank:'第四名', icon:'', name: '嘉明', height:6810, kilo:7110, time:395, big:34, small:29 },
    //     {image:'head5.png', rank:'第五名', icon:'', name: 'Pei', height:5013, kilo:4128, time:413, big:23, small:18 },
    // ])

    const BASE = import.meta.env.BASE_URL
    const jsonPath = `${BASE}json/mychallenge/ranks.json`


    // 控制手風琴開關
    const openItem = ref(null)

    const toggle = (index) => {
        if(openItem.value == index){
            openItem.value = null
        }else{
            openItem.value = index
        }
    }

    const ranks = ref([])

    onMounted(async() => {
        try{
            const res = await axios.get(jsonPath)
            ranks.value = res.data

        }catch(err){
            console.error("讀取失敗:", err)
        }
    })
    
</script>

<style scoped lang="scss">
    @import '../../assets/styles/main.scss';
    
    .nomb{
        border-bottom: 1px dotted $black-14;
        
        .personInfo{
            display: flex;
            justify-content: space-between;
            margin-top: 24px;
            margin-bottom: 12px;
            align-items: center;
            cursor: pointer;
            
            .personInfoTitle{
                display: flex;
                align-items: center;
            
                .head{
                    width: 40px;
                    height: 40px;
                    object-fit: cover;
                    border: 1px solid $black-14;
                    border-radius: 50%;
                }

                .place{
                    display: flex;
                    align-items: center;
                    position: relative;
                    padding: 0 32px;
                    text-align: center;
            
                    &::before{
                        content: '';
                        position: absolute;
                        left: 20px;
                        width: 1px;
                        height: 80%;
                        background-color: $black-14;
                    }

                    &::after{
                        content: '';
                        position: absolute;
                        right: 20px;
                        width: 1px;
                        height: 80%;
                        background-color: $black-14;
                    }
                }

                h4{
                    font-size: $pcFont-H4;
                    font-weight: $semiBold;
                    line-height: $lineHeight-p-150;
                }
            }

            .allow{
                img{
                    transition: all 0.3s ease-in-out;
                    transform-origin: center;

                    &.rotated{
                        transform: rotate(-180deg);
                    }
                }
            }
        }
    
        .totalScore{
            padding: 40px 0 48px 32px;

            .nombScore{
                display: flex;
            
                article:nth-child(1n+2){
                    margin-left: 48px;
                }
            }
        }

        .mountScore{
            display: flex;
            margin-top: 32px;
        
            article:nth-child(1n+2){
                margin-left: 128px;
            }
        }

        p{
            font-size: $pcFont-p-s;
            font-weight: $bold;
            line-height: $lineHeight-p-150;

            span{
                font-size: $pcFont-H1-m;
                font-weight: $medium;
                line-height: $lineHeight-title-120;
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

    @media screen and (max-width: 1200px) {
        .nomb{
            width: calc(100% - 20px);
        }

    }

    @media screen and (max-width: 650px) {
        .nomb{

            .totalScore{
                padding: 16px 16px;

                .nombScore{
                    display: flex;
                    justify-content: space-between;
            
                    article:nth-child(1n+2){
                        margin-left: 20px;
                    }
                }
            }
        }

    }

    @media screen and (max-width: 500px) {
        .nomb{

            p{

                span{
                    font-size: $pcFont-H3;

                }
            }
        }

    }

</style>