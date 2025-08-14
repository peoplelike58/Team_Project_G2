<template>
    <div class="modal-overlay" v-show="isVisible">
        <section class="mychallenge-modal">
            <button class="close-btn">x</button>
            <article class="gpx">
                <div class="mountain-info">
                    <h2>{{ mountain.name }}</h2>
                    <h4>[{{ mountain.kind }}]</h4>
                </div>
                <div class="upload-area">
                    <div class="add">+</div>
                    <input type="file" id="theFile">
                </div>
                <div class="score">
                    <div>
                        <p>累積高度</p>
                        <p><span>{{ heightTotal }}</span> m</p>
                    </div>
                    <div>
                        <p>累積里程</p>
                        <p><span>{{ kiloTotal }}</span> m</p>
                    </div>
                    <div>
                        <p>累積時間</p>
                        <p><span>{{ timeTotal }}</span> m</p>
                    </div>
                </div>
            </article>
            <article class="think">
                <p>想法記錄</p>
                <textarea id=""></textarea>
                <p class="text-count">{{}} / 200</p>
            </article>
            <div class="buttun-wrapper">
                <button>提交</button>
            </div>
        </section>
    </div>
</template>

<script setup>
import { ref } from 'vue'

    let heightTotal = ref(1)
    let kiloTotal = ref(2)
    let timeTotal = ref(3)

    let mountain = ref({name: '玉山', kind: '大百岳'})

    const isVisible = ref(true)


</script>

<style scoped lang="scss">
    @import '@/assets/styles/main.scss';
    .modal-overlay{
        position: fixed;
        inset: 0;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        background-color: rgba(20, 20, 20, 0.2);
        overflow-y: auto;
        padding: 60px 0;
    
        .mychallenge-modal{
            position: relative;
            max-width: 800px;
            width: 100%;
            // max-height: 600px;
            padding: 60px;
            background-color: $ivory-gray-100;
            overflow: visible;
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
            
            .mountain-info{
                display: flex;
                align-items: end;
                margin-bottom: 24px;
                
                h2{
                    font-size: $pcFont-H2;
                    font-weight: $semiBold;
                }

                h4{
                    font-size: $pcFont-H4;
                    font-weight: $semiBold;
                    margin-left: 20px;
                }
            }
        
            .upload-area{
                position: relative;
                width: 100%;
                height: 300px;
                background-color: $bg-gray;
                border: 1px dashed $black-14;
                border-radius: 16px;
            
                .add{
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
            
                    font-size: $pcFont-H1-l;
                    font-weight: $medium;
                    line-height: $lineHeight-title-120;
                    color: $black-14;
                }
                
                #theFile{
                    width: 100%;
                    height: 100%;
                    
                    position: absolute;
                    top: 0;
                    left: 0;
                    
                    opacity: 0;
                    cursor: pointer;
                }
            }
            .score{
                display: flex;
                margin-top: 24px;
            
                div{
                    width: calc(100% / 3);
                
                }
                p{
                    font-size: $pcFont-p-s;
                    font-weight: $bold;
                    line-height: $linHeight-p-150;
                    display: block;
                    
                    span{
                        font-size: $pcFont-H1-m;
                        font-weight: $medium;
                        line-height: $lineHeight-title-120;
                    }
                }
            }
            
            .think{
                margin: 56px 0;
                position: relative;
            
                p:nth-of-type(1){
                    margin-bottom: 24px;
                }
                
                textarea{
                    width: 100%;
                    height: 300px;
                    padding: 20px;
                    font-size: 20px;
                    background-color: $bg-gray;
                    border: 1px solid $black-14;
                    border-radius: 16px;
                    box-sizing: border-box;
                    resize: none;
                }

                .text-count{
                    position: absolute;
                    bottom: 0;
                    right: 0;
                    padding-right: 12px;
                    padding-bottom: 12px;
                }
            }
            
            .buttun-wrapper{
                display: flex;
                justify-content: center;
            
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
  
</style>