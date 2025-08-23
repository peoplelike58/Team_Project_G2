<template>
    <section class="challengeProgress" v-for="(item, index) in goals">
        <div class="title">
            <h3>
                [{{ item.kind }}]
            </h3>
            <img src="../../assets/images/mychallenge/goalSet.png"
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
        <p>今年目標已完成  <span>{{ item.done }}</span>  /  {{ item.goal }}  座</p>
        <div class="flagArea">
            <div class="line">
            </div>
            <img src="../../assets/images/mychallenge/flag.png" alt="旗子" 
                :style="{ transform: imgPosition(item) }">
        </div>
    </section>
</template>

<script setup>
import { ref } from 'vue'
import { storeToRefs } from 'pinia'
import { useGoalStore } from '@/stores/goalStore'

import mychallenge_setgoal from './mychallenge_setgoal.vue'

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
    }

    // 關閉 <mychallenge_setgoal />
    const closeSetgoal = (item, data) => {
        if (data && data.goal) {
            goalStore.updateGoal(item.kind, data.goal)
        }
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
            
            .line{
                width: 100%;
                height: 12px;
                border-radius: 999px;
                background-color: $ash-olive-400;
            }

            img{
                position: absolute;
                top: -24px;
                left: 0;
            }
        }
    }

</style>