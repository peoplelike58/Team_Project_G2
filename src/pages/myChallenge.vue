<template>
    <div class="wrapper">
        <main>
            <div class="breadCrumb">
                <mychallenge_breadcrumb />
            </div>
            <div class="mychallengeInfo">
                <mychallenge_map
                :mountains="mountains"
                @openUploadModal="openModal"
                style="z-index: 0;"
                />   
                <div class="mychallengeAcheve">
                    <div class="totalAcheve" v-show="!showHistory">
                        <h2>[您的成就]</h2>
                        <mychallenge_info 
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
                @closeUploadModal="openWindows[mountain.name] = false"
                @saveGpx="handleGpxSave"
                />
        </main>
    </div>
</template>

<script setup>
    import mychallenge_breadcrumb from '@/components/myChallengeItem/mychallenge_breadcrumb.vue';
    import mychallenge_map from '@/components/myChallengeItem/mychallenge_map.vue';
    import mychallenge_info from '@/components/myChallengeItem/mychallenge_info.vue';
    import mychallenge_progress from '@/components/myChallengeItem/mychallenge_progress.vue';
    import mychallenge_ranking from '@/components/myChallengeItem/mychallenge_ranking.vue';
    import mychallenge_modal from '@/components/myChallengeItem/mychallenge_modal.vue';
    import mychallenge_history from '@/components/myChallengeItem/mychallenge_history.vue';
    
    import { ref, onMounted } from 'vue'
    import * as turf from "@turf/turf"
    
    import { useGoalStore } from "@/stores/goalStore"
    import { useRecordStore } from "@/stores/recordStore"

    const showHistory = ref(false)
    
    const mountains = ref([
        { name: "玉山", kind: "大百岳",  latitude: 23.4712, longitude: 120.9575, icon: "mountain.png"},
        { name: "雪山", kind: "大百岳",  latitude: 24.3886, longitude: 121.2336, icon: "mountain.png"},
        { name: "關山", kind: "小百岳", latitude: 23.3239, longitude: 121.0036, icon: "mountain.png"},
        { name: "南湖大山", kind: "大百岳",  latitude: 24.3819, longitude: 121.4194, icon: "mountain.png"},
        { name: "秀姑巒山", kind: "大百岳",  latitude: 23.4625, longitude: 121.0225, icon: "mountain.png"},
        { name: "合歡主峰", kind: "小百岳",  latitude: 24.1445, longitude: 121.2722, icon: "mountain.png"},
        { name: "能高主峰", kind: "小百岳",  latitude: 24.1028, longitude: 121.2403, icon: "mountain.png"},
        { name: "大霸尖山", kind: "小百岳",  latitude: 24.5167, longitude: 121.2500, icon: "mountain.png"},
        { name: "品田山", kind: "小百岳",  latitude: 24.5056, longitude: 121.2942, icon: "mountain.png"},
        { name: "奇萊主峰", kind: "小百岳",  latitude: 24.1183, longitude: 121.3345, icon: "mountain.png"}
    ])

    const openWindows = ref({})
    mountains.value.forEach(mountain => {
        openWindows.value[mountain.name] = false
    })   

    function openModal(mountainName) {
        openWindows.value[mountainName] = true
    }

    function closeHistory() {
        showHistory.value = false
    }

        const goalStore = useGoalStore()

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
            target.icon = "flag.png"

            // 讀 localStorage
            let climbedList = JSON.parse(localStorage.getItem("climbedMountains") || "[]")
            let progress = JSON.parse(localStorage.getItem("myGoals") || "[]")

            if (progress.length === 0) {
                progress = [
                    { kind: "大百岳", done: 0, goal: 10, openSetgoal: false },
                    { kind: "小百岳", done: 0, goal: 10, openSetgoal: false }
                ]
            }

            // ✅ 只在第一次登頂時做以下動作
            if (!climbedList.includes(mountain)) {
                climbedList.push(mountain)
                localStorage.setItem("climbedMountains", JSON.stringify(climbedList))

                // ✅ 更新 Pinia 進度
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

</script>

<style scoped lang="scss">
    @import '../assets/styles/main.scss';
    .wrapper{
        width: 1200px;
        margin: 0 auto;

        .breadCrumb{
            margin-top: 49px;
            margin-bottom: 111px;
        }
        
        .mychallengeInfo{
            display: flex;
            height: 713px;
        
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
            max-width: 1067px;
            width: 100%;
            margin: 152px 0;
            padding: 64px;
            background-color: $ivory-gray-100;
        
            h2{
                font-size: $pcFont-H2;
                font-weight: $semiBold;
                line-height: $lineHeight-p-150;
                text-align: center;
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