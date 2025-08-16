<template>
    <div class="modalOverlay" @click.self="closeModel">
        <section class="goalsetModal">
            <button class="closeBtn" @click="closeModel">x</button>
            <div class="goalset">
                <h2>{{ props.item.kind }} 目標設定</h2>
                <input type="number" class="goal" id="bidGoal" :placeholder="`輸入今年目標`" v-model="currentGoal" @input="updateGoal" min="1" max="30">
                <div class="buttunWrapper">
                    <button @click="submitGoal">提交</button>
                </div>
            </div>
        </section>
    </div>
    <!-- <ModalSlot title="大百岳1 目標設定" @btnlCick="handleOnClick">
        <template #content>
                <input type="text" class="goal" id="bidgoal" :placeholder="`請輸入今年目標`" min="1" max="30">
        </template>
    </ModalSlot> -->
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import ModalSlot  from './ModalSlot.vue'

//接收 <mychallenge_progress /> 傳來的 props
const props = defineProps({
    isVisible: {
        type: Boolean,
        default: false
    },
    item: {
        type: Object,
        default: () => ({})
    }
})

const emit = defineEmits(['close', 'updateGoal'])

const closeModel = () => {
    emit('close')
}


// 當前輸入的目標值
const currentGoal = ref('')

// 監聽 props.item 的變化，設定初始值
watch(() => props.item, (newItem) => {
    if (newItem && newItem.goal) {
        currentGoal.value = newItem.goal
    }
}, { immediate: true })

// 即時更新目標值到父組件
const updateGoal = () => {
    const goalValue = parseInt(currentGoal.value)
    if (goalValue >= 1 && goalValue <= 30) {
        // 發送即時更新事件給父組件
        emit('updateGoal', goalValue)
    }
}

// 提交目標設定
const submitGoal = () => {
    const goalValue = parseInt(currentGoal.value)
    
    if (!goalValue || goalValue < 1 || goalValue > 30) {
        alert('請輸入 1-30 之間的數字')
        return
    }
    
    console.log('提交的目標:', goalValue)
    
    // 將新的目標值傳給父組件
    emit('close', { goal: goalValue })
}

</script>

<style scoped lang="scss">
    @import '@/assets/styles/main.scss';
    .goal{
    width: 100%;
    font-size: $pcFont-H2;
    text-align: center;
    margin: 24px 0 ;
    border-radius: 16px;
}
    .modalOverlay{
        position: fixed;
        inset: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: rgba(20, 20, 20, 0.2);
        
        .goalsetModal{
            position: relative;
            max-width: 800px;
            width: 100%;
            // height: 600px;
            padding: 60px;
            background-color: $ivory-gray-100;
            overflow:visible;
            z-index: 20;
        
            .closeBtn{
                position: absolute;
                top: -12px;
                right: -12px;
                width: 40px;
                height: 40px;
                font-size: $pcFont-H4;
                color: #fff;
                background-color: $tag;
                border: none;      
                border-radius: 50%;
                cursor: pointer;
            }

            .goalset{
                display: flex;
                flex-direction: column;
                align-items: center;
                width: fit-content;
                margin: 0 auto;

                h2{
                    font-size: $pcFont-H2;
                    font-weight: $semiBold;
                }

                .goal{
                    font-size: $pcFont-H2;
                    text-align: center;
                    margin: 24px 0 ;
                    border-radius: 16px;
                }

                .buttunWrapper{
                    display: flex;
                    justify-content: center;
                    margin-top: 24px;
                
                    button{
                        height: 46px;
                        width: 120px;
                        align-items: center;
                        padding: 8px 12px;
                        font-size: $pcFont-p-m;
                        font-weight: $semiBold;
                        color: #fff;
                        background-color: $tag;
                        border: 1px solid $tag;
                        border-radius: 999px;
                        cursor: pointer;
                    }
                }
            }
        }
    }
</style>