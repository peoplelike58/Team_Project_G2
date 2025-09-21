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
                        :isLoggedIn="isLoggedIn"
                        @openUploadModal="handleOpenModal"
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
                            :isLoggedIn="isLoggedIn"
                            @openHistoryComp="showHistory = true"/>
                        <mychallenge_progress 
                            ref="progressRef"
                            :isLoggedIn="isLoggedIn"
                        />
                    </div>
                    <mychallenge_history
                        v-show="showHistory"
                        :isLoggedIn="isLoggedIn"
                        ref="historyRef"
                        @closeHistoryComp="closeHistory"/>
                </div>
            </div>
            <div class="mychallengeRank">
                <h2>🏆 百岳勇士排行榜 🏆</h2>
                <mychallenge_ranking ref="rankingRef"/>
            </div>
            <mychallenge_modal
                v-for="mountain in mountains"
                :mountain="mountain"
                v-show="openWindows[mountain.name]"
                @closeUploadModal="() => closeModal(mountain.name)"
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
    
    import { ref, onMounted, computed, watch } from 'vue'
    import { useRouter } from 'vue-router'
    import * as turf from "@turf/turf"
    
    import { useGoalStore } from "@/stores/goalStore"
    import { useRecordStore } from "@/stores/recordStore"
    import { useUserStore } from '@/stores/user'
    import axios from 'axios';

    const showHistory = ref(false)
    const mountains = ref([])
    const openWindows = ref({})
    const infoRef = ref(null)
    const historyRef = ref(null)
    const mapRef = ref(null)
    const rankingRef = ref(null)
    const progressRef = ref(null)

    const router = useRouter()
    const userStore = useUserStore()

    const userInfo = ref({
        email: '',
        name: ''
    })

    const isLoggedIn = computed(() => userStore.isLoggedIn)

    const BASE = import.meta.env.BASE_URL
    const API_URL = `${import.meta.env.VITE_AJAX_URL}/mychallenge_mountains.php`

    watch(isLoggedIn, async (newValue, oldValue) => {
        
        // 如果從登入變為登出
        if (oldValue === true && newValue === false) {
            
            // 重置所有山峰圖示為預設狀態
            mountains.value.forEach(mountain => {
                mountain.icon = 'mountain.png'
            })
            
        }
        
        // 如果從登出變為登入
        if (oldValue === false && newValue === true) {
            await loadMountainsData()
        }
    })

    const loadMountainsData = async () => {
        try {
            const res = await axios.get(API_URL, {
                withCredentials: true,
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            
            if (res.data.success) {
                const { mountains: mountainsData, climbed: climbedIds, isLoggedIn: apiIsLoggedIn, member_id } = res.data

                // 設定所有山峰資料
                mountains.value = mountainsData.map(mountain => ({
                    name: mountain.MOUNTAIN_NAME,
                    kind: mountain.type,
                    latitude: parseFloat(mountain.LATITUDE),
                    longitude: parseFloat(mountain.LONGITUDE),
                    icon: 'mountain.png' // 預設圖示
                }))
                
                // **修改：使用 computed 的 isLoggedIn**
                if (isLoggedIn.value && climbedIds.length > 0) {
                    // 已登入且有攀登記錄：根據資料庫資料標記
                    mountains.value.forEach(mountain => {
                        const mountainData = mountainsData.find(mount => mount.MOUNTAIN_NAME === mountain.name)
                        if (mountainData && climbedIds.includes(mountainData.MOUNTAIN_ID)) {
                            mountain.icon = 'flag.png'
                        }
                    })
                }

                // 初始化視窗狀態
                mountains.value.forEach(mountain => {
                    openWindows.value[mountain.name] = false
                })
                
            } else {
                console.error('API 請求失敗:', res.data)
            }
        } catch(err) {
            console.error("讀取失敗:", err)
        }
    }

    function closeModal(mountainName) {
        if (mountainName) {
            openWindows.value[mountainName] = false
        }
    }

    function closeHistory() {
        showHistory.value = false
    }

    function handleOpenModal(mountainName) {
        // 檢查是否已登入
        if (!isLoggedIn.value) {
            alert('請先登入才能上傳 GPX 檔案！')
            // 導向登入頁面
            router.push('/loginregister/fontrelogin')
            return
        }
        
        // 已登入則開啟 modal
        openWindows.value[mountainName] = true
    }

    const handleRefreshStats = async () => {

            try {
                // 1. 刷新 info 組件的數據
                if (infoRef.value && typeof infoRef.value.refreshStats === 'function') {
                    await infoRef.value.refreshStats()
                }

                // 2. 刷新 progress 組件的數據
                if (progressRef.value && typeof progressRef.value.progressData === 'function') {
                    await progressRef.value.progressData()
                }

                // 3. 刷新 history 組件的數據
                if (historyRef.value && typeof historyRef.value.loadHistories === 'function') {  // ✅ 移除 showHistory 條件
                    await historyRef.value.loadHistories()  // ✅ 保留函數調用
                }

                // 4. 刷新 ranking 組件的數據
                if (rankingRef.value && typeof rankingRef.value.refreshRanking === 'function') {
                    await rankingRef.value.refreshRanking()
                }

                // 5. 重新載入山峰狀態
                await loadMountainsData()

            } catch (error) {
                console.error('刷新數據時發生錯誤:', error)
            }

    }

    const goalStore = useGoalStore()

    function handleGpxSave({ mountain, coords }) {

        // 找到對應山
        const target = mountains.value.find(m => m.name === mountain)
        if (!target) return

        // 建立 turf 點 (山頂)
        const mountainPoint = turf.point([target.longitude, target.latitude])

        let climbed = false
        for (const [lon, lat] of coords) {
            const gpxPoint = turf.point([lon, lat])
            const distance = turf.distance(mountainPoint, gpxPoint, { units: "kilometers" })

            if (distance < 0.01) {
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

                if (rankingRef.value && typeof rankingRef.value.refreshRanking === 'function') {
                    rankingRef.value.refreshRanking()
                }
            }

            }
        }


        const recordStore = useRecordStore()
        
        onMounted(async() => {

        // **修改：使用獨立的載入函數**
        await loadMountainsData()
        
        // 載入 store 資料
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
            margin-top: 48px;
            margin-bottom: 20px;

            @media screen and (max-width: 430px) {
                margin-top: 49px;
                margin-bottom: 20px;
            }
        }
        
        .mychallengeInfo{
            display: flex;
            height: 713px;
            align-items: stretch;
            // justify-content: space-around;

            .mychallengeMap{
                width: 50%;
                // height: 713px;

                @media screen and (max-width: 768px) {
                    width: 100%;
                    height: 600px;
                }

                @media screen and (max-width: 430px) {
                    width: 100%;
                    height: 600px;
                }
            }
        
            .mychallengeAcheve, .mychallenge-history{
                width: 50%;
                margin: 20px 0 20px 80px;

                @media screen and (max-width: 768px) {
                    width: 100%;
                    margin: 40px 0;
                    margin: 20px 0 20px 0px;
                }

                @media screen and (max-width: 430px) {
                    width: 100%;
                    margin: 40px 0;
                }
            }
            .mychallengeAcheve h2{
                font-size: $pcFont-H2;
                font-weight: $semiBold;
                line-height: $lineHeight-p-150;
                margin-bottom: 60px;
            }

            @media screen and (max-width: 768px) {
                display: flex;
                flex-direction: column;
                height: auto;
                margin-bottom: 0;
                box-sizing: border-box;
            }

            @media screen and (max-width: 430px) {
                display: flex;
                flex-direction: column;
                height: auto;
                margin-bottom: 0;
                box-sizing: border-box;
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

                @media screen and (max-width: 440px) {
                    font-size: $pcFont-H3;
                }
            }

            @media screen and (max-width: 1200px) {
                margin-top: 152px;
                background-color: $ivory-gray-100;
                box-sizing: border-box;

            }

            @media screen and (max-width: 768px) {
                box-sizing: border-box;
                width: 100%;
                padding: 32px;
            }

            @media screen and (max-width: 585px) {
                box-sizing: border-box;
                width: 100%;
                padding: 32px 16px;
                margin: 60px 0;
            }
        }

        @media screen and (max-width: 1200px) {
            width: calc(100% - 40px);
            padding: 20px;
        }

        @media screen and (max-width: 430px) {
            max-width: 430px;
            width: 100%;
            padding: 0 16px;
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