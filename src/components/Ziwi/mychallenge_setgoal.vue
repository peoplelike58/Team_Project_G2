<template>
    <div class="modal-overlay" >
        <section class="goalset-modal">
            <button class="close-btn" @click="closeModal">x</button>
            <div class="goalset">
                <h2>大百岳 目標設定</h2>
                <input type="text" class="goal" id="bidgoal" :placeholder="`請輸入今年目標`" min="1" max="30">
                <div class="buttun-wrapper">
                    <button>提交</button>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { ref } from 'vue'


const emit = defineEmits(['modalClose'])

    const handalSave = () => {

        //todo 儲存

        emit('modalClose')
    }



</script>

<style scoped lang="scss">
    @import '@/assets/styles/main.scss';
    .modal-overlay{
        position: fixed;
        inset: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: rgba(20, 20, 20, 0.2);
        
        .goalset-modal{
            position: relative;
            max-width: 800px;
            width: 100%;
            // height: 600px;
            padding: 60px;
            background-color: $ivory-gray-100;
            overflow:visible;
            z-index: 20;
        
            .close-btn{
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

                .buttun-wrapper{
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