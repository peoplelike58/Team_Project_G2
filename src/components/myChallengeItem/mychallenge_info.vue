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
    import { ref, onMounted } from 'vue'
    import axios from 'axios'
    // import { useRecordStore } from "@/stores/recordStore"
    // import { storeToRefs } from "pinia"

    const heightTotal = ref('0.00')
    const kiloTotal = ref('0.00')
    const timeTotal = ref('0.00')

    const API_URL = `${import.meta.env.VITE_AJAX_URL}/mychallenge_info.php`

    // --- 1.emit 傳遞事件 ---
    const emit = defineEmits(['openHistoryComp'])

    const openHistory = () => {
        emit('openHistoryComp')  // 告訴父組件要打開 history
    }


    const loadTotalStats = async () => {
    
        const response = await axios.post(API_URL,{}, {
            withCredentials: true,
            headers: {
                'Content-Type': 'application/json'
            }
        })
        
        if (response.data.success) {
            heightTotal.value = response.data.heightTotal
            kiloTotal.value = response.data.kiloTotal
            timeTotal.value = response.data.timeTotal
            console.log('累積數據載入成功:', response.data)
        } else {
            throw new Error(response.data.error || '載入失敗')
        }

    }

    // 🔧 新增：重新載入數據的方法（給父組件調用）
    const refreshStats = async () => {
        await loadTotalStats()
    }

    // 🔧 對外暴露方法
    defineExpose({
        refreshStats
    })

    // 🔧 組件載入時取得數據
    onMounted(() => {
        loadTotalStats()
    })
    // --- 2.利用 Pinia+解構賦值，把store裡的state轉乘ref
    // const recordStore = useRecordStore()
    // const { heightTotal, kiloTotal, timeTotal } = storeToRefs(recordStore)

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
                // margin-left: 204px;
                padding: 8px;
                border: 1px solid $black-14;
                border-radius: 8px;
                cursor: pointer;
            }
        }
    }

    @media screen and (max-width: 800px) {

        .mychallengeInfomation{
    
            .totalInfo{
                button{
                    font-size: 14px;
                }
            }
        }
        
    }

        @media screen and (max-width: 650px) {

        .mychallengeInfomation{
    
            .totalInfo{
                button{
                    font-size: $pcFont-H4;
                }
            }
        }
        
    }

</style>