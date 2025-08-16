<template>
    <div class="wrapper">
        <main>
            <div class="breadCrumb">
                <mychallenge_breadcrumb />
            </div>
            <div class="mychallengeInfo">
                <mychallenge_map />   
                <div class="mychallengeAcheve">
                    <h2>[您的成就]</h2>
                    <mychallenge_info />
                    <mychallenge_progress />
                </div>
            </div>
            <div class="mychallengeRank">
                <h2>🏆 百岳勇士排行榜 🏆</h2>
                <mychallenge_ranking />
            </div>
        </main>
    </div>
</template>

<script setup>
    import mychallenge_breadcrumb from '@/components/Ziwi/mychallenge_breadcrumb.vue';
    import mychallenge_map from '@/components/Ziwi/mychallenge_map.vue';
    import mychallenge_info from '@/components/Ziwi/mychallenge_info.vue';
    import mychallenge_progress from '@/components/Ziwi/mychallenge_progress.vue';
    import mychallenge_ranking from '@/components/Ziwi/mychallenge_ranking.vue';
    import mychallenge_modal from '@/components/Ziwi/mychallenge_modal.vue';
    import mychallenge_history from '@/components/Ziwi/mychallenge_history.vue';
    import mychallenge_setgoal from '@/components/Ziwi/mychallenge_setgoal.vue';

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
                line-height: $linHeight-p-150;
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
                line-height: $linHeight-p-150;
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
├── MyChallengeInfo.vue ←（裡面有一個按鈕，點擊即顯示MyChallengeHistory.vue 彈窗） 
│ └── MyChallengeHistory.vue ←（一個與MyChallengeInfo.vue+MyChallengeProgress.vue在同一個位置的窗格，平常被隱藏，直到點擊了MyChallengeInfo.vue的按鈕） 
├── MyChallengeProgress.vue ←（一個區塊裡的小組件，裡面有按鈕，點擊即顯示MyChallengeSetGoal.vue 彈窗) 
└── MyChallengeSetGoal.vue ←（彈窗，，平常被隱藏，直到點擊了MyChallengeProgress.vue的按鈕，設定目標 Modal，接收 props、emit 提交） └──MyChallengeRank.vue ←（排行榜） 
-->