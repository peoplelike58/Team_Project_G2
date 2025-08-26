<template>
<NavMenu/>
<div class="wrapper">
    <div class="wrapperTop">
        <div class="top">
            <div class="topInfoImg">
                <img src="@/assets/images/eventCard/cardimg1.jpg" alt="">
            </div>

            <div class="topInfo">
                <h1>揪團中</h1>
                <h2>陽明山緩慢散步之旅</h2>
                <div class="topInfoP">
                    <p>日期</p>
                    <p>8/5</p>
                </div>
                <div class="topInfoP-2">
                    <p>時間</p>
                    <p>09:00 - 17:00</p>
                </div>
            </div>
        </div>


        <div class="mainInfo">
            <h1>活動簡介</h1>
            <p>這趟陽明山緩慢散步之旅，將帶你遠離城市喧囂，輕輕走進自然懷抱。行程從陽明山遊客服務中心出發，沿途漫步在靜謐的林蔭小徑，欣賞隨季節變換的山景，途中停留於小巧的觀景點，聽導覽老師分享陽明山的花卉故事與地質秘密。最後，我們會在溫泉區短暫休息，啜飲一杯熱茶，讓身心徹底放鬆，帶回一段最緩慢、最純粹的美好時光。</p>
        </div>
    </div>

    <div class="cardWrapper">
        <div class="infoCard">
            <div class="infoList">
                <div>
                    <h1>[集合時間與地點]</h1>
                    <div class="infoListH2">
                        <h2>8/15</h2> 
                        <span class="time">9:00</span><span class="ampm">am</span>
                    </div>
                    <p class="infoListP">劍潭捷運站 1 號出口</p>
                </div>
                <div>
                    <div class="leftMain">
                        <p>[路程]</p>
                        <span class="lefiMainCH">約</span><span class="leftMainMM">15</span><span class="mmkm">km</span>
                        <p>冷水坑 → 七星公園 → 夢幻湖</p>
                    </div>
                    <div class="leftFooter">
                        <p>[花費時間]</p>
                        <span class="lefiMainCH2">約</span><span class="leftMainMM2">4</span><span class="hms">小時</span>
                </div>
                </div>
            </div>
            
            <div class="rightInfoCard">
                <img src="@/assets/images/eventCard/cardimg1.jpg" alt="">
                
                <div class="rightInfo">
                    <span class="rightInfoNA">報名人數</span><span class="rightInfoNB">10</span>
                </div>
                    <span class="rightInfoBNA">8/13</span><span class="rightInfoBT">12:00</span><span class="rightInfoBBNA">截止</span>
            </div>
        </div>

    </div>
        <div class="wooniInfo">
            <ul class="wooniUl">注意事項：
                <li>請穿著輕便衣物與防滑鞋</li>
                <li>建議攜帶水壺．帽子．防蚊液</li>
                <li>活動前3日若遇大雨將公告延期</li>
            </ul>
        </div>

            <div>
                <button>報名參加</button>
            </div>
</div>
</template>

        <script setup>
        import { computed } from 'vue';
        import { useRoute } from 'vue-router';
        import activitiesJson from '@/components/togetherItem/activities.json';
        import NavMenu from '../An/navMenu.vue';

        const route = useRoute()
        // const json = activitiesJson()

        // const id = computed(() => parseInt(route.params.id))

        // const cardId = comp
    
        </script>

        <style lang="scss" scoped="scoped">
            @import '../../assets/styles/main.scss';
            .wrapper{
                display: flex;
                flex-direction: column;
                align-items: center;
                background-color: #fff;
            }

            // top資訊區
            .topInfoImg {
                img {
                    width: 500px;
                    height: 500px;
                    border-radius: 10px;
                }
            }

            .top{
                display: flex;
                margin-bottom: 100px;
                // justify-content: center;
            }

            .topInfo{
                margin-left: 80px;
                h1{
                    display: flex;
                    width: 98px;
                    height: 43px;
                    font-size: 16px;
                    font-weight: bold;
                    justify-content: center;
                    align-items: center;
                    margin-bottom: 40px;
                    border-radius: 999px;
                    background-color: #01685E;
                    color: #ffffff;
                }
                h2{
                    width: 420px;
                    height: 36px;
                    font-size: 24px;
                    font-weight: bold;
                    color: $black-14;
                    gap: 10px;
                }
            }

            .topInfoP{
                width: 260px;
                height: auto;
                display: flex;
                margin: 20px 0;
                border-top: solid $black-14;
                p{
                    margin-top: 20px;
                    color: $black-14;
                    font-size: 20px;
                    font-weight: bold;
                    gap: 16px;
                } 
                P:last-child{
                    margin-left: 16px;
                }
            }

            .topInfoP-2{
                width: 260px;
                height: auto;
                display: flex;
                border-bottom: solid $black-14;
                p{
                    margin-bottom: 20px;
                    color: $black-14;
                    font-size: 20px;
                    font-weight: bold;
                    gap: 16px;
                } 
                P:last-child{
                    margin-left: 16px;
                }
            }
            // 中間活動資訊
            .mainInfo{
                width: 1024px;
                height: auto;
                h1{
                    width: 1004px;
                    margin-bottom: 15px;
                    font-size: 24px;
                    font-weight: bold;
                    color: $black-14;
                }
                p{
                    width: 1004px;
                    line-height: 200%;
                    font-size: 20px;
                }
            }

            .wrapperTop{
                margin-bottom: 100px;
            }

            .cardWrapper{
                // border: 1px solid red;
                position: relative;
                display: flex;
                justify-content: center;
                align-items: center;
                width: 1024px;
                height: 632px;
                background-color: $ivory-gray-100;
                border-radius: 16px;
                margin-bottom: 100px;
            }
            
            // 下方卡片詳細
            .infoCard{
                display: flex;
                justify-content: center;
                align-items: flex-start;
            }

            .infoList{
                width: 100%;
                height: 100%;
                margin-top: 64px;
                margin-bottom: 71px;
                margin-left: 80px;
                // flex: 1;
                h1{
                    font-size: 20px;
                    font-weight: bold;
                    color: $black-14;
                    margin-bottom: 8px;
                }
                .infoListH2{
                    display: flex;
                    align-items: flex-end;

                    h2{
                        font-size: 36px;
                        margin-right: 16px;
                    }
                    .time{
                        font-size: 36px;
                    }
                    .ampm{
                        font-size: 20px;
                    }
                }
            }
            .infoListP{
                font-size: 20px;
                font-weight: bolder;
                margin-top: 8px;
                margin-bottom: 80px;
            }

            .leftMain{            
                margin-bottom: 80px;  
                p{
                    line-height: 120%;
                    font-size: 20px;
                    font-weight: bold;
                    margin: 8px 0;
                }
                .lefiMainCH,.mmkm{
                    font-size: 20px;
                    font-weight: bold;
                }
                .leftMainMM{
                    font-size: 36px;
                }
            }
            .leftFooter{
                p{
                    font-size: 20px;
                    font-weight: bold;
                    margin-bottom: 8px;
                }
                .lefiMainCH2,.kms{
                    font-size: 20px;
                    font-weight: bold;
                }
                .leftMainMM2{
                    font-size: 36px;
                }
            }

            .rightInfoCard{
                margin-left: 179px;
                img{
                    display: block;
                    width: 400px;
                    height: auto;
                    margin-top: 64px;
                }
            }
            .rightInfo{
                margin-top: 135px;
                margin-bottom: 18px;
                margin-left: 190px;
                .rightInfoNA,.rightInfoNB{
                    font-size: 24px;
                    font-weight: bold;
                    color: $black-14;
                }
                .rightInfoNA{
                    margin-right: 12px;
                }
            }
            .rightInfoBNA,.rightInfoBT,.rightInfoBBNA{
                font-size: 24px;
                font-weight: bold;
                margin-right: 12px;
            }
            .rightInfoBNA{
                margin-left: 190px;
            }
            .rightInfoBBNA{
                color: #E13535;
            }
            .wooniInfo{
                font-size: 20px;
                font-weight: bold;
                line-height: 200%;
                padding: 30px;
                margin-left: 220px;
                align-self: flex-start;
                .wooniUl{
                    list-style: disc;
                }
            }
            button{
                width: 342px;
                height: 56px;
                background-color: $black-14;
                border-radius: 30px;
                color: #ffffff;
                font-size: 24px;
                font-weight: bold;
                cursor: pointer;
            }
            

            </style>