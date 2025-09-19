<template>
    <section v-for="(rank, index) in ranks" :key="index" class="nomb">
        <div class="personInfo" @click="toggle(index)">
            <div class="personInfoTitle">
                <img :src="getAvatarPath(rank.image)"
                    alt="頭像" 
                    class="head"
                    @error="handleImageError">
                <h4 class="place">{{ rankText(index) }}<br />{{ rankIcon(index) }}</h4>
                <h4>{{ rank.name }}</h4>
            </div>
            <div class="allow">
                <img :src="`${BASE}images/myChallenge/down.png`" 
                alt="allow" 
                :class="{ 'rotated': openItem === index }"
                >
            </div>
        </div>
        <transition name="dropdown">
        <div class="totalScore" v-show="openItem == index">
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
                    <p>大百岳</p>
                    <p><span>{{ rank.big }}</span> 座</p>
                </article>
                <article>
                    <p>小百岳</p>
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
    
    const BASE = import.meta.env.BASE_URL; 
    const API_URL = `${import.meta.env.VITE_AJAX_URL}/mychallenge_rank.php`

    // 控制手風琴開關
    const openItem = ref(null)

    const toggle = (index) => {
        if(openItem.value == index){
            openItem.value = null
        }else{
            openItem.value = index
        }
    }

    const getAvatarPath = (image) => {
        return `${BASE}uploads/avatars/${image || 'default-avatar.png'}`
    }

    const handleImageError = (event) => {
        event.target.src = `${BASE}uploads/avatars/default-avatar.png`
    }

    const ranks = ref([])

    onMounted(async() => {
        await loadRankingData()
    })

    const rankText = (index) => {
        const rankTexts = ['第一名', '第二名', '第三名', '第四名', '第五名']
        return rankTexts[index]
    }
    
    const rankIcon = (index) => {
        const rankIcons = ['🥇', '🥈', '🥉', '', '']
        return rankIcons[index]
    }

    const loadRankingData = async () => {
        try {
            const res = await axios.get(API_URL)
            ranks.value = res.data
        } catch (err) {
            console.error("載入排行榜失敗:", err)
        }
    }

    const refreshRanking = async () => {
        // 重新載入排行榜資料的邏輯
        await loadRankingData()
    }

    defineExpose({ refreshRanking })

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

                    @media screen and (max-width: 650px) {
                        margin-left: 20px;
                    }
                }

                @media screen and (max-width: 650px) {
                    display: flex;
                    justify-content: space-between;
                }
            }

            @media screen and (max-width: 650px) {
                padding: 16px 16px;
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

                @media screen and (max-width: 500px) {
                    font-size: $pcFont-H3;
                }
            }

            @media screen and (max-width: 500px) {
                font-size: 12px;
            }
        }

        @media screen and (max-width: 1200px) {
            width: calc(100% - 20px);
        }
    }

    .dropdown-enter-active{
        transition: all 0.3s ease;
    }

    .dropdown-enter-from {
        opacity: 0;
        transform: translateY(-20px);
    }

</style>