<template>
    <section class="challengeProgress" v-for="item in goals">
        <div class="title">
            <h3>
                [{{ item.kind }}]
            </h3>
            <img :src="`${BASE}images/myChallenge/goalSet.png`"
            alt="目標設定" 
            @click="openSetgoal(item)"
            >
            <mychallenge_setgoal
            v-if="item.openSetgoal"
            :isVisible="item.openSetgoal"
            :item="item"
            @close="closeSetgoal(item, $event)"
            @updateGoal="updateGoal(item, $event)"
            style="z-index: 20;
            "/>
        </div>
        <p>目標已完成  <span>{{ item.done }}</span>  /  {{ item.goal }}  座</p>
        <div class="flagArea">
            <div class="Progressbar">
            </div>
            <img :src="`${BASE}images/myChallenge/flag.png`" alt="旗子" 
                :style="{ transform: imgPosition(item) }">
        </div>
    </section>
</template>

<script setup>
import { ref } from 'vue'
import { storeToRefs } from 'pinia'
import { useGoalStore } from '@/stores/goalStore'

import mychallenge_setgoal from './mychallenge_setgoal.vue'

const BASE = import.meta.env.BASE_URL

const goalStore = useGoalStore()
goalStore.initDefault()

// 把 goals 轉成 reactive 引用
const { goals } = storeToRefs(goalStore)

    // 開啟 <mychallenge_setgoal />
    const openSetgoal = (item) => {
        item.openSetgoal = true
    }

    // 即時更新目標值（不關閉彈窗）
    const updateGoal = (item, newGoal) => {
        goalStore.updateGoal(item.kind, newGoal)

        item.openSetgoal = false
    }

    // 關閉 <mychallenge_setgoal />
    const closeSetgoal = (item, data) => {

        item.openSetgoal = false
    }

    // 計算圖片應該前進的長度（依照每個 item 自己的 done/goal）
    const imgPosition = (item) => {
        if (!item.goal || item.goal <= 0) return "translateX(0px)"
        const ratio = Math.min(item.done / item.goal, 1)
        const lineLength = 553
        const flagWidth = 41
        const maxDistance = lineLength - flagWidth
        return `translateX(${ratio * maxDistance}px)`
    }

</script>

<style scoped lang="scss">
    @import '@/assets/styles/main.scss';

    .challengeProgress{
        margin-top: 60px;
        
        .title{
            display: flex;
            justify-content: space-between;
            align-items: end;
            
            h3{
                font-size: $pcFont-H3;
                font-weight: $semiBold;
                line-height: $lineHeight-p-150;
            }
            
            img{
                width: 24px;
                height: 24px;
                cursor: pointer;
            }
        }

        p{
            font-size: $pcFont-p-s;
            font-weight: $bold;
            line-height: $lineHeight-p-150;
            
            span{
                font-size: $pcFont-H1-m;
                font-weight: $semiBold;
                line-height: $lineHeight-p-150;
            }
        }

        .flagArea{
            position: relative;
            margin-top: 20px;
            
            .Progressbar{
                width: 100%;
                height: 12px;
                border-radius: 999px;
                background-color: $ash-olive-400;
            }

            img{
                width: 41px;
                height: 41px;
                position: absolute;
                top: -24px;
                left: 0;
            }
        }
    }

</style>