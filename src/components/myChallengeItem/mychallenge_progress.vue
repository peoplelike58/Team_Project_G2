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
            @updateGoal="(newGoal) => updateGoal(item, newGoal)"
            @refreshData="progressData"
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
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'


import mychallenge_setgoal from './mychallenge_setgoal.vue'

const BASE = import.meta.env.BASE_URL
const API_URL = `${import.meta.env.VITE_AJAX_URL}/mychallenge_progress.php`

const props = defineProps({
    isLoggedIn: {
        type: Boolean,
        default: false
    }
})

const goals = ref([])

const progressData = async () => {
    if (!props.isLoggedIn) {
            // 未登入時設置預設值
            goals.value = [
                { kind: '大百岳', done: 0, goal: 10, openSetgoal: false },
                { kind: '小百岳', done: 0, goal: 10, openSetgoal: false }
            ]
            return
        }

    try {

        const response = await axios.post(
                API_URL,{},{
                withCredentials: true,  // ← 讓 session 可以運作
                headers: {
                    'Content-Type': 'application/json'  // 重要！
                }}
            )

        const data = response.data
        if (data.success) {

            goals.value = [
                {
                    kind: '大百岳',
                    done: data.data.mountain_types.big_mountain.count,
                    goal: data.data.mountain_types.big_mountain.target,
                    openSetgoal: false
                },
                {
                    kind: '小百岳', 
                    done: data.data.mountain_types.small_mountain.count,
                    goal: data.data.mountain_types.small_mountain.target,
                    openSetgoal: false
                }
            ]
        } else {
            // console.error('API錯誤:', data.message)
            goals.value = [
                { kind: '大百岳', done: 0, goal: 10, openSetgoal: false },
                { kind: '小百岳', done: 0, goal: 10, openSetgoal: false }
            ]
        }
    } catch (error) {
        // console.error('獲取資料失敗:', error)
        goals.value = [
            { kind: '大百岳', done: 0, goal: 10, openSetgoal: false },
            { kind: '小百岳', done: 0, goal: 10, openSetgoal: false }
        ]
    }
}

watch(() => props.isLoggedIn, async (newValue, oldValue) => {
    
    if (newValue === false) {
        // 登出時重置為預設狀態
        goals.value = [
            { kind: '大百岳', done: 0, goal: 10, openSetgoal: false },
            { kind: '小百岳', done: 0, goal: 10, openSetgoal: false }
        ]

    } else if (newValue === true) {
        // 登入時重新載入
        await progressData()
    }
})

onMounted(() => {
    if (props.isLoggedIn) {
        progressData()
    } else {
        // 未登入時設置預設值
        goals.value = [
            { kind: '大百岳', done: 0, goal: 10, openSetgoal: false },
            { kind: '小百岳', done: 0, goal: 10, openSetgoal: false }
        ]
    }
})

// 開啟 <mychallenge_setgoal />
const openSetgoal = (item) => {
    if (!props.isLoggedIn) {
        alert('請先登入才能設定目標！')
        return
    }
    item.openSetgoal = true
}

const updateGoal = (item, newGoal) => {

    if (newGoal === undefined || newGoal === null || newGoal === '') {
        // console.error('新目標值無效:', newGoal)
        return
    }
    
    const targetItem = goals.value.find(goal => goal.kind === item.kind)
    
    if (targetItem) {
        targetItem.goal = parseInt(newGoal) || 0 // 確保是數字
    } else {
        // console.error('找不到對應的目標項目:', item.kind)
        // 如果找不到，直接更新整個陣列中對應的項目
        const index = goals.value.findIndex(goal => goal.kind === item.kind)
        if (index !== -1) {
            goals.value[index].goal = parseInt(newGoal) || 0
        }
    }
    
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

defineExpose({ 
    progressData,
    refreshData: progressData 
})

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