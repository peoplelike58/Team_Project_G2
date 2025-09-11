<template>
    <navMenu />
    <div class="wrapper">
        <main>
            <div class="breadCrumb">
                <mychallenge_breadcrumb />
            </div>
            <div class="mychallengeInfo">
                <div class="mychallengeMap">
                    <mychallenge_map
                        :mountains="mountains"
                        @openUploadModal="openModal"
                        style="z-index: 0;"
                        ref="mapRef"
                    />
                </div>
                <div class="mychallengeAcheve">
                    <div class="totalAcheve" v-show="!showHistory">
                        <h2>[您的成就]</h2>
                        <mychallenge_info 
                            ref="infoRef"
                            v-show="!showHistory" 
                            @openHistoryComp="showHistory = true"/>
                        <mychallenge_progress />
                    </div>
                    <mychallenge_history
                        v-show="showHistory"
                        @closeHistoryComp="closeHistory"/>
                </div>
            </div>
            <div class="mychallengeRank">
                <h2>🏆 百岳勇士排行榜 🏆</h2>
                <mychallenge_ranking />
            </div>
            <mychallenge_modal
                v-for="mountain in mountains"
                :mountain="mountain"
                v-show="openWindows[mountain.name]"
                @closeUploadModal="closeModal"
                @saveGpx="handleGpxSave"
                @refreshStats="handleRefreshStats"
                />
        </main>
    </div>
    <brandFooter />
</template>

<script setup>
    import navMenu from '@/components/An/navMenu.vue';
    import mychallenge_breadcrumb from '@/components/myChallengeItem/mychallenge_breadcrumb.vue';
    import mychallenge_map from '@/components/myChallengeItem/mychallenge_map.vue';
    import mychallenge_info from '@/components/myChallengeItem/mychallenge_info.vue';
    import mychallenge_progress from '@/components/myChallengeItem/mychallenge_progress.vue';
    import mychallenge_ranking from '@/components/myChallengeItem/mychallenge_ranking.vue';
    import mychallenge_modal from '@/components/myChallengeItem/mychallenge_modal.vue';
    import mychallenge_history from '@/components/myChallengeItem/mychallenge_history.vue';
    import brandFooter from '@/components/An/footer.vue';
    
    import { ref, onMounted } from 'vue'
    import * as turf from "@turf/turf"
    
    import { useGoalStore } from "@/stores/goalStore"
    import { useRecordStore } from "@/stores/recordStore"
    import axios from 'axios';

    const showHistory = ref(false)
    const mountains = ref([])

    const openWindows = ref({})

    const infoRef = ref(null)

    const BASE = import.meta.env.BASE_URL
    // const jsonPath = `${BASE}json/mychallenge/mountains.json`
    // const jsonPath = `http://localhost/php/mychallenge_mountains.php`
    const API_URL = `${import.meta.env.VITE_AJAX_URL}/mychallenge_mountains.php`

    function openModal(mountainName) {
        openWindows.value[mountainName] = true
    }

    function closeModal(mountainName) {
        if (mountainName) {
            openWindows.value[mountainName] = false
        }
    }

    function closeHistory() {
        showHistory.value = false
    }

    const handleRefreshStats = async () => {
    console.log('收到刷新請求，正在重新載入累積數據...')
    if (infoRef.value && typeof infoRef.value.refreshStats === 'function') {
        await infoRef.value.refreshStats()
        console.log('累積數據已刷新')
    }
    }

    const goalStore = useGoalStore()
    const mapRef = ref(null)

    function handleGpxSave({ mountain, coords }) {
        console.log("上傳 GPX 給", mountain, coords)

        // 找到對應山
        const target = mountains.value.find(m => m.name === mountain)
        if (!target) return

        // 建立 turf 點 (山頂)
        const mountainPoint = turf.point([target.longitude, target.latitude])

        let climbed = false
        for (const [lon, lat] of coords) {
            const gpxPoint = turf.point([lon, lat]) // 正確：[lon, lat]
            const distance = turf.distance(mountainPoint, gpxPoint, { units: "kilometers" })
            console.log("距離:", mountain, "vs", [lon, lat], "=", distance, "km")

            if (distance < 0.5) {
                climbed = true
                break
            }
        }

        if (climbed) {
            // 換 icon
            // target.icon = "flag.png"
            mapRef.value.setClimbed(mountain)

            // 讀 localStorage
            let climbedList = JSON.parse(localStorage.getItem("climbedMountains") || "[]")
            let progress = JSON.parse(localStorage.getItem("myGoals") || "[]")

            if (progress.length === 0) {
                progress = [
                    { kind: "大百岳", done: 0, goal: 10, openSetgoal: false },
                    { kind: "小百岳", done: 0, goal: 10, openSetgoal: false }
                ]
            }

            // 只在第一次登頂時做以下動作
            if (!climbedList.includes(mountain)) {
                climbedList.push(mountain)
                localStorage.setItem("climbedMountains", JSON.stringify(climbedList))

                // 更新 Pinia 進度
                goalStore.addDone(target.kind)
            }

            } else {
                if (coords && coords.length > 0) {
                    alert(`${mountain}：GPX 沒有登頂紀錄，沒有插旗子！`)
                }
            }
        }


        const recordStore = useRecordStore()


        onMounted(() => {
            const climbed = JSON.parse(localStorage.getItem("climbedMountains") || "[]")
            mountains.value.forEach(m => {
                if (climbed.includes(m.name)) {
                m.icon = "flag.png"   // 重新套旗子
                }
            })
            recordStore.loadAllRecords()
            goalStore.loadFromStorage()
        })

        
        onMounted(async() => {
            try{
                const res = await axios.post(API_URL)
                mountains.value = res.data.map(mountain => ({
                    name: mountain.MOUNTAIN_NAME,        // 轉換欄位名稱
                    kind: mountain.type,                 // 轉換欄位名稱  
                    latitude: parseFloat(mountain.LATITUDE),   // 確保是數字
                    longitude: parseFloat(mountain.LONGITUDE), // 確保是數字
                    icon: 'mountain.png'                 // 預設圖示
                }))

                // console.log('PHP 回傳的原始資料:', res.data)
                // console.log('資料型別:', typeof res.data)
                // console.log('是否為陣列:', Array.isArray(res.data))

                mountains.value.forEach(mountain => {
                openWindows.value[mountain.name] = false
                })   
            }catch(err){
                console.error("讀取失敗:", err)
            }
        })

</script>

<style scoped lang="scss">
    @import '../assets/styles/main.scss';
    .wrapper{
        width: 1200px;
        margin: 0 auto;

        .breadCrumb{
            margin-top: 48px;
            margin-bottom: 20px;
        }
        
        .mychallengeInfo{
            display: flex;
            height: 713px;
            align-items: stretch;
            // justify-content: space-around;

            .mychallengeMap{
                width: 50%;
                // height: 713px;
            }
        
            .mychallengeAcheve, .mychallenge-history{
                width: 50%;
                // height: 514px;
                margin: 20px 0 20px 80px;
            }
            .mychallengeAcheve h2{
                font-size: $pcFont-H2;
                font-weight: $semiBold;
                line-height: $lineHeight-p-150;
                margin-bottom: 60px;
            }
        }
        
        .mychallengeRank{
            // max-width: 1067px;
            width: 100%;
            margin-top: 152px;
            padding: 64px;
            background-color: $ivory-gray-100;
            box-sizing: border-box;
        
            h2{
                font-size: $pcFont-H2;
                font-weight: $semiBold;
                line-height: $lineHeight-p-150;
                text-align: center;
            }
        }

    }

    @media screen and (max-width: 1200px) {
		.wrapper{
            width: calc(100% - 40px);
            padding: 20px;

            .mychallengeRank{
                margin-top: 152px;
                background-color: $ivory-gray-100;
                box-sizing: border-box;
            }
        }
	}

    @media screen and (max-width: 650px) {
		.wrapper{
            
            .mychallengeInfo{
                display: flex;
                flex-direction: column;
                height: auto;
                margin-bottom: 0;
                box-sizing: border-box;
                
                .mychallengeMap{
                    width: 100%;
                    height: 600px;
                }
                .mychallengeAcheve, .mychallenge-history{
                    width: 100%;
                    margin: 40px 0;
                    margin: 20px 0 20px 0px;
                }
                
            }

            .mychallengeRank{
                box-sizing: border-box;
                // max-width: 100%;
                width: 100%;
                padding: 32px 16px;
        
            }
        }
	}

    @media screen and (max-width: 430px) {
		.wrapper{
            max-width: 430px;
            width: 100%;
            padding: 0 16px;

            .breadCrumb{
                margin-top: 49px;
                margin-bottom: 20px;
            }

            
            .mychallengeInfo{
                display: flex;
                flex-direction: column;
                height: auto;
                margin-bottom: 0;
                box-sizing: border-box;
                
                .mychallengeMap{
                    width: 100%;
                    height: 600px;
                }
                .mychallengeAcheve, .mychallenge-history{
                    width: 100%;
                    margin: 40px 0;
                }
                
            }

            .mychallengeRank{
                box-sizing: border-box;
                // max-width: 100%;
                width: 100%;
                padding: 32px 16px;
                margin: 60px 0;
        
            }
        }
	}
</style>

<!-- 
MyChallenge.vue ←（父元件，整個頁面） 
│ ├── MyChallengeBreadcrumb.vue ←（純顯示用，待處裡處裡） 
├── MyChallengeMap.vue ←（套用leaflet） 
│ └── MyChallengeModal.vue ←（點擊leaflet裡不同位置的icon來上傳不同山的gpx資料） 
├── 
| └── mychallenge_info.vue ←（裡面有一個按鈕，點擊即顯示MyChallengeHistory.vue 彈窗）
| └── mychallenge_progress ←（一個區塊裡的小組件，裡面有按鈕，點擊即顯示MyChallengeSetGoal.vue 彈窗） 
├── MyChallengeHistory.vue ←（一個與MyChallengeInfo.vue+MyChallengeProgress.vue在同一個位置的窗格，平常被隱藏，直到點擊了MyChallengeInfo.vue的按鈕）
└── MyChallengeSetGoal.vue ←（彈窗，，平常被隱藏，直到點擊了MyChallengeProgress.vue的按鈕，設定目標 Modal，接收 props、emit 提交） └──MyChallengeRank.vue ←（排行榜） 
-->