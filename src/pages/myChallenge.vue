<template>
    <div class="wrapper">
        <main>
            <div class="bread-crumb">
                <mychallenge_breadcrumb />
            </div>
            <div class="mychallenge-info">
                <mychallenge_map />   
                <div class="mychallenge-acheve">
                    <h2>[您的成就]</h2>
                    <mychallenge_info />
                    <mychallenge_progress @openModal="openEmit"/>
                </div>
                <!-- <div class="mychallenge-history">
                    <mychallenge_history/>
                </div> -->
            </div>
            <div class="mychallenge-rank">
                <h2>🏆 百岳勇士排行榜 🏆</h2>
                <mychallenge_ranking />
            </div>
            <div class="modal">
                <mychallenge_modal />
                <!-- <mychallenge_setgoal v-if="modalOpen" @closeModel="modalClose"/> -->
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

    import { ref } from 'vue'

    const isOpen = ref(false)

    const modalOpen = () => {
        isOpen.value = true
    }

    const modalClose = () => {
        isOpen.value = false
    }

</script>

<style scoped lang="scss">
    @import '../assets/styles/main.scss';
    .wrapper{
        width: 1200px;
        margin: 0 auto;

        .bread-crumb{
            margin-top: 49px;
            margin-bottom: 111px;
        }
        
        .mychallenge-info{
            display: flex;
            height: 713px;
        
            .mychallenge-acheve, .mychallenge-history{
                width: 50%;
                // height: 514px;
                margin: 20px 0 20px 80px;
            }
            .mychallenge-acheve h2{
                font-size: $pcFont-H2;
                font-weight: $semiBold;
                line-height: $linHeight-p-150;
                margin-bottom: 60px;
            }
        }
        
        .mychallenge-rank{
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

    //     .wrapper{
    //     width: 1200px;
    //     margin: 0 auto;
    // }
    // .bread-crumb{
    //     margin-top: 49px;
    //     margin-bottom: 111px;
    // }
    // .mychallenge-info{
    //     display: flex;
    //     height: 713px;
    // }
    // .mychallenge-acheve{
    //     width: 50%;
    //     // height: 514px;
    //     margin: 20px 0 20px 80px;
    // }
    // .mychallenge-acheve h2{
    //     font-size: $pcFont-H2;
    //     font-weight: $semiBold;
    //     line-height: $linHeight-p-150;
    //     margin-bottom: 60px;
    // }
    // .mychallenge-rank{
    //     max-width: 1067px;
    //     width: 100%;
    //     margin: 152px 0;
    //     padding: 64px;
    //     background-color: $ivory-gray-100;
    // }
    // .mychallenge-rank h2{
    //     font-size: $pcFont-H2;
    //     font-weight: $semiBold;
    //     line-height: $linHeight-p-150;
    //     text-align: center;
    // }
</style>