<template>
    <section class="mychallengeInfomation">
        <article class="totalInfo">
            <div class="total">
                <p>[總累積高度]</p>
                <p><span>{{ heightTotal }}</span> m</p>
                <p>[總累積里程]</p>
                <p><span>{{ kiloTotal }}</span> km</p>
                <p>[總累積時間]</p>
                <p><span>{{ timeTotal }}</span> hr</p>
            </div>
            <div>
                <button  @click="openHistory">您的歷史紀錄</button>
            </div>
        </article>
    </section>
</template>

<script setup>
    import { useRecordStore } from "@/stores/recordStore"
    import { storeToRefs } from "pinia"

    // 定義 emit 事件
    const emit = defineEmits(['openHistoryComp'])

    const openHistory = () => {
        emit('openHistoryComp')  // 告訴父組件要打開 history
    }

    const recordStore = useRecordStore()
    const { heightTotal, kiloTotal, timeTotal } = storeToRefs(recordStore)

</script>

<style scoped lang="scss">
    @import '@/assets/styles/main.scss';

    .mychallengeInfomation{
        margin: 12.5px 0;
    
        .totalInfo{
            display: flex;
            justify-content: space-between;
        
            p{
                font-size: $pcFont-p-s;
                font-weight: $bold;
                line-height: $lineHeight-p-150;
                display: block;
            
                span{
                    font-size: $pcFont-H1-m;
                    font-weight: $medium;
                    line-height: $lineHeight-title-120;
                }
            }
            button{
                font-size: $pcFont-H4;
                font-weight: $semiBold;
                line-height: $lineHeight-p-150;
                background-color: #fff;
                margin-left: 204px;
                padding: 8px;
                border: 1px solid $black-14;
                border-radius: 8px;
                cursor: pointer;
            }
        }
    }

</style>