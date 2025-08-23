<template>
    <section class="challenge-progress" v-for="item in items">
        <div class="title">
            <h3>
                [{{ item.kind }}]
            </h3>
            <img src="../../assets/images/mychallenge/goalSet.png" alt="目標設定">
        </div>
        <p>今年目標已完成  <span>{{ item.done }}</span>  /  {{ item.goal }}  座</p>
        <div class="flag-area">
            <div class="line">
            </div>
            <img src="../../assets/images/mychallenge/flag.png" alt="" @click="openEmit('openModal')">  <!--點擊按鈕向父組件傳遞事件-->
        </div>
    </section>
</template>

<script setup>
import { ref,computed } from 'vue'

    const items = ref([
        {kind:'大百岳', done: 1, goal: 10 },
        {kind:'小百岳', done: 2, goal: 20 }
    ])

    const openEmit = defineEmits(['openModal'])

    const caculatePosition = () => {





    }
</script>

<style scoped lang="scss">
    @import '@/assets/styles/main.scss';

    .challenge-progress{
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

        .flag-area{
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