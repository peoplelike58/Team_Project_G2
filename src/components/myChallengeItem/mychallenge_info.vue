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
    import { ref, onMounted, watch } from 'vue'
    import axios from 'axios'

    const props = defineProps({
        isLoggedIn: {
            type: Boolean,
            default: false
        }
    })

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
        if (!props.isLoggedIn) {
            // 未登入時重置為預設值
            heightTotal.value = '0.00'
            kiloTotal.value = '0.00'
            timeTotal.value = '0.00'
            console.log('用戶未登入，重置累積數據')
            return
        }

        try{
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

        }catch(err){
            console.error('載入數據失敗:', err)
            // 發生錯誤時保持預設值
            heightTotal.value = '0.00'
            kiloTotal.value = '0.00'
            timeTotal.value = '0.00'
        }
    }

    watch(() => props.isLoggedIn, async (newValue, oldValue) => {
        console.log('Info 組件：登入狀態變化', oldValue, '->', newValue)
        
        if (newValue === false) {
            // 登出時清空資料
            heightTotal.value = '0.00'
            kiloTotal.value = '0.00'
            timeTotal.value = '0.00'
            console.log('已清空累積數據')
        } else if (newValue === true) {
            // 登入時重新載入
            await loadTotalStats()
        }
    })

    // 重新載入數據的方法（給父組件調用）
    const refreshStats = async () => {
        await loadTotalStats()
    }

    // 🔧 對外暴露方法
    defineExpose({
        refreshStats
    })

    // 🔧 組件載入時取得數據
    onMounted(() => {
        if (props.isLoggedIn) {
            loadTotalStats()
        }
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

</style>