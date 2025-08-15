<template>
    <section class="challengeProgress" v-for="(item, index) in items">
        <div class="title">
            <h3>
                [{{ item.kind }}]
            </h3>
            <img src="../../assets/images/mychallenge/goalSet.png" alt="目標設定" @click="openSetgoal(index)">
            <mychallenge_setgoal
            v-if="item.openSetgoal"
            :isVisible="item.openSetgoal"
            :item="item"
            @close="closeSetgoal(index)"
            @updateGoal="updateGoalRealtime(index, $event)"
            style="z-index: 20;
            "/>
        </div>
        <p>今年目標已完成  <span>{{ item.done }}</span>  /  {{ item.goal }}  座</p>
        <div class="flagArea">
            <div class="line">
            </div>
            <img src="../../assets/images/mychallenge/flag.png" alt="旗子">
        </div>
    </section>
</template>

<script setup>
import { ref,computed } from 'vue'
import mychallenge_setgoal from './mychallenge_setgoal.vue'
    
    const items = ref([
        {kind:'大百岳', done: 1, goal: 10, openSetgoal: false},
        {kind:'小百岳', done: 2, goal: 10, openSetgoal: false }
    ])


    // 開啟 <mychallenge_setgoal />
    const openSetgoal = (index) => {
        items.value[index].openSetgoal = true
    }

    // 即時更新目標值（不關閉彈窗）
    const updateGoalRealtime = (index, newGoal) => {
        items.value[index].goal = newGoal
    }

    // 關閉 <mychallenge_setgoal />
    const closeSetgoal = (index, data) => {
    if (data && data.goal) {
        // 確保最終提交的值也被更新
        items.value[index].goal = data.goal
    }
    items.value[index].openSetgoal = false
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
                line-height: $linHeight-p-150;
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
            line-height: $linHeight-p-150;
            
            span{
                font-size: $pcFont-H1-m;
                font-weight: $semiBold;
                line-height: $linHeight-p-150;
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