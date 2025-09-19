<script setup>
import { ref, onMounted, watch, defineProps, computed } from "vue"
import { useRouter } from 'vue-router';

import L from "leaflet"
import "leaflet/dist/leaflet.css"
import markerShadow from 'leaflet/dist/images/marker-shadow.png'
// 取消預設取圖 marker
delete L.Icon.Default.prototype._getIconUrl

// 合併新預設，之後 new L.Marker() 就會用這三個檔案                 // 全域套用新圖示
const baseUrl = import.meta.env.BASE_URL
L.Icon.Default.mergeOptions({                                    // 設定圖示路徑
  iconUrl:      `${baseUrl}images/icon/markerIcon.png`,
  iconRetinaUrl:`${baseUrl}images/icon/markerIcon2x.png`,
  shadowUrl: `${baseUrl}images/icon/marker-shadow.png`, // 有陰影檔再開
  iconSize: [48, 64],                                             // 圖示顯示大小：寬64×高64
  iconAnchor: [24, 64],                                           // 錨點在底部中央：寬/2=32, 高=64
  popupAnchor: [0, -64],                                          // 泡泡往上偏移一個圖示高

  // 若你沿用 Leaflet 原陰影，記得一起調整大小與錨點：               // 陰影大小對齊
  shadowSize: [64, 64],                                           // 陰影顯示大小（依你的陰影圖而定）
  shadowAnchor: [24, 64],                                         // 陰影錨點（通常與 iconAnchor 對齊）

})

const router = useRouter()  
const goShopping = () => router.push('/shop')




// 設定分頁按鈕
const goPage = ref("detailPage")
const showPopup = ref(false)
const mapBox = ref(null)

//接收父層
const props = defineProps({
  trail: {
    type: Object,
    // required: true
  }
})


// ======= 設定leaflet經緯度 =======
// console.log('LATITUDE raw =', props.trail?.LATITUDE, 'type=', typeof props.trail?.LATITUDE)
const latitude = computed(() => Number(props.trail?.LATITUDE))
// console.log(latitude.value);
const longitude = computed(() => Number(props.trail?.LONGITUDE))
// console.log(longitude.value);
const name = props.trail.MOUNTAIN_NAME

let map // 宣告在外面，讓後面可以存取

onMounted(() => {
  // 初始化地圖
  map = L.map(mapBox.value).setView([latitude.value, longitude.value], 15) // 要串接trail.longitude 和 trail.latitude
  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "&copy; OpenStreetMap"
  }).addTo(map)
  L.marker([latitude.value, longitude.value]).addTo(map).bindPopup(`${name}`)
})

// 監聽 goPage 切換，防止map用v-show切換到路線地圖分頁時抓不到高度
watch(goPage, (newPage) => {
  if (newPage === "mapPage") {
    // 當切到地圖分頁時，重新計算大小
    setTimeout(() => {
      map.invalidateSize()
    }, 200) // 給點時間讓 v-show 動畫/渲染完成
  }
})
</script>



<template>
    <div class="btnpages"><!--最外層的框框-->
        <ul class="btns">
            <li>
                <button @click="goPage = 'detailPage'">
                    山的資訊
                </button>
            </li>

            <li>
                <button @click="goPage = 'mapPage'">
                    路線地圖
                </button>
            </li>

            <li>
                <button @click="goPage = 'equipmentPage'">
                    裝備建議
                </button>
            </li>
        </ul>

        <!--山的資訊的框-->
        <div class="page detailPage" v-show="goPage === 'detailPage'"> 
            <h2>山的資訊</h2>
            <ul class="detail">
                <li>
                    <span>地區</span>
                    <p>{{ props.trail.REGION }}</p> <!-- 要串接 trail.region -->
                </li>

                <li>
                    <span>
                        難易度
                        
                        <img 
                            src="../../../public/images/icon/difficulty.svg" 
                            alt="問號icon"
                            @click="showPopup = !showPopup"
                            style="cursor: pointer"
                        />
                        
                    </span>
                    <p>{{ props.trail.LEVEL }}</p> <!-- 要串接 trail.level -->
                </li>

                <li>
                    <span>交通</span>
                    <p>{{ props.trail.TRAFFIC }}</p> <!-- 要串接 trail.traffic -->
                </li>
                <li>
                    <span>里程</span>
                    <p>{{ props.trail.DISTANCE }}</p> <!-- 要串接 trail.long -->
                </li>
                <li>
                    <span>建議時間</span>
                    <p>{{ props.trail.TIME }}</p> <!-- 要串接 trail.time -->
                </li>
            </ul>

        <!---彈窗:難易度表格彈窗------>
        <div 
        class="popupMask" 
        v-if="showPopup" 
        @click.self="showPopup = false"
        >
            <div class="showPopup">
                <button @click="showPopup = false">x</button>
                <table>
                <thead>
                    <tr>
                    <th>易</th>
                    <th>中</th>
                    <th>難</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    <td>親子<br>一般大眾</td>
                    <td>體力較佳者</td>
                    <td>體力佳<br>具地圖判斷能力<br>具野外求生能力</td>
                    </tr>
                    <tr>
                    <td>1天內</td>
                    <td>1~2天</td>
                    <td>2天以上</td>
                    </tr>
                    <tr>
                    <td>裝備<br><br>水<br>適量糧食</td>
                    <td>裝備<br><br>水及糧食<br>炊煮設備<br>睡袋等登山裝備</td>
                    <td>裝備<br><br>水及糧食<br>炊煮設備<br>睡袋等登山裝備</td>
                    </tr>
                </tbody>
                </table>
            </div>
        </div>

        </div>


        <!--路線地圖的框-->
        <div class="page mapPage" v-show="goPage === 'mapPage'"> 
            
            <div ref="mapBox" class="mapBox">
            <!--要串leaflet地圖API-->

            </div>

        </div>


        <!--建議裝備的框-->
        <div class="page equipmentPage" v-show="goPage === 'equipmentPage'">
            <h2>裝備建議</h2>
            <p>＊必備: 水、乾良、垃圾袋、遮陽帽、健保卡。</p>
            <ul class="equipment">
                <li>
                    <div>
                        <!--到時候會放商品圖-->
                        <img
                        src="../../../public/images/Equipment/登山鞋.png" 
                        alt="登山鞋圖片">
                    </div>
                    <span>登山鞋</span>
                </li>
                <li>
                    <div>
                        <img 
                        src="../../../public/images/Equipment/登山杖.png" 
                        alt="登山杖圖片">
                        <!--到時候會放商品圖-->
                    </div>
                    <span>登山杖</span>
                </li>
                <li>
                    <div>
                        <img 
                        src="../../../public/images/Equipment/手套.png" 
                        alt="手套圖片"
                        :class="{ grayImg: ('易').includes(props.trail.LEVEL) }">
                       
                    </div>
                    <span :class="{ graySpan: ('易').includes(props.trail.LEVEL) }">
                        手套
                    </span>
                </li>
                <li>
                    <div>
                        <img
                        src="../../../public/images/Equipment/頭燈.png"
                        alt="頭燈圖片"
                        :class="{ grayImg: ('易').includes(props.trail.LEVEL) }">
                    </div>
                    <span :class="{ graySpan: ('易').includes(props.trail.LEVEL) }">
                        頭燈
                    </span>
                </li>
                <li>
                    <div>
                        <!--到時候會放商品圖-->
                        <img
                        src="../../../public/images/Equipment/爐具.png"
                        alt="登山爐具圖片"
                        :class="{ grayImg: ['中', '易'].includes(props.trail.LEVEL) }">

                    </div>
                    <span :class="{ graySpan: ['中', '易'].includes(props.trail.LEVEL) }">
                        登山爐具
                    </span>
                </li>
                <li>
                    <div>
                        <!--到時候會放商品圖-->
                        <img
                        src="../../../public/images/Equipment/帳篷.png"
                        alt="帳篷圖片"
                        :class="{ grayImg: ['中', '易'].includes(props.trail.LEVEL) }">

                    </div>
                    <span :class="{ graySpan: ['中', '易'].includes(props.trail.LEVEL) }">
                        睡袋&帳篷
                    </span>
                </li>
            </ul>

            <!-- <button
            @click="goShopping"
            >
                前往山腳雜貨店↗
            </button> -->

            <!-- 底部 CTA -->
            <footer class="section-footer">
                <RouterLink to="/shop" class="view-all">前往山腳雜貨店</RouterLink>
                <button class="diag-btn" aria-label="open">
                    <svg class="arrow" viewBox="0 0 24 24" aria-hidden="true">
                        <line x1="5" y1="19" x2="18" y2="6" class="shaft"/>
                        <polyline points="8,5 19,5 19,16" class="head"/>
                    </svg>
                </button>
            </footer>
        
        
        </div>

    </div>  
</template>



<style scoped lang="scss"> 
@import '../../assets/styles/main.scss';
@import '../../assets/styles/mixins';

.btnpages{ /* 最外面的框 */
    padding: 64px 0;
    width: 100%;
    max-width: 1200px;
    margin:0 auto;

    @include m(){
        max-width: 768px;
        padding: 50px 20px;
        font-size: 14px;
        box-sizing: border-box;
    }

    @include s(){
        max-width: 430px;
    }
    
    .btns{/* ul 切換按鈕 */
        // border: 1px solid red;
        display: flex;

        justify-content: center; /* li 置中 */
        align-items: center;
        gap: 48px;
        margin: 0 auto;

        @include m(){
            max-width: 728px;
            gap:28px;
        }

        @media (max-width: 648px){
            max-width: 608px;
            gap:20px;
        }

        @media (max-width: 590px){
            max-width: 550px;
       
        }

        @include s(){
            max-width: 390px;
            
        }

        li{

            button{
                cursor: pointer;

                padding: 8px 52px;
                border-radius: 8px;
                border: none;
                background-color: $ivory-gray-100;

                font-size: $pcFont-p-m;

                @include m(){                 
                    box-sizing: border-box;
                }

                @media (max-width: 648px){
                    padding: 8px 45px;        
                }

                @media (max-width: 590px){
                    padding: 8px 30px;        
                }

                @media (max-width: 500px){
                    padding: 8px 25px;  
                    font-size: $pcFont-p-s;
                }

                @include s(){               
                    padding: 8px 20px;                    
                }

                @media (max-width: 392px){
                    padding: 8px 15px;  
                    font-size: $pcFont-p-s;
                }

                &:focus{
                    background-color:white;
                    font-weight: $semiBold;
                }
            }
            
        }

    
    }

    .detailPage{ /* 山的資訊的框 */
        
        ul{
        width: 80%;
        max-width: 912px;
        margin:64px auto 0;
        // border: 1px solid red;

        @media (max-width: 566px) {
            width: 95%;

        }

        @include s(){
            width: 100%;
            
        }
        
            li{
                border-bottom: 1px solid #999;
                padding: 8px 15px;
                display: flex;
                margin-bottom: 40px;
                font-size: $pcFont-p-m;
                font-weight: $medium;
                box-sizing: border-box;
                
                gap: 7rem;

                @include m(){
                    gap:0;
                    justify-content: space-between;
                    padding: 8px;
                }

                @include s(){
                    font-size: $pcFont-p-s;
                }

                p{ /* 內容 */

                    
                }

                span{ /* 欄位 */ 
                    width: 100px;
                    color: #666;
                    img{
                        
                        width: 1.125rem;
                        height: auto;
                    }
 

                }
            }
        }
    }

    .mapPage{ /* 路線地圖的框 */ 
        padding: 50px 20px;
        box-sizing: border-box;
        
        .mapBox{
            
            height: 500px;
            width: 100%;
            border-radius: 16px;
        }
        
    }

    .equipmentPage{ /* 推薦裝備的框 */
        display: flex;
        flex-direction: column;

        align-items: center;

        h2{
            flex-grow: 1;

        }
        
        
        p{ /* 必備文字提示*/
            align-self: flex-start;
            margin: 0 80px;
            flex-grow: 1;
            // width: 100%;

            @include m(){
                align-self: center;
                margin: 0 auto;
            }


        }

        .equipment{ /* ul */
            // border: 1px solid ;
            width: 90%;
            margin: 0 auto;
            display: flex;
            flex-grow: 2;

            @include m(){
            
            flex-wrap: wrap;

            }


            li{
            // border: 1px solid red;
            flex-basis: calc(( 100% - 16px) / 3);
            flex-grow: 1;

            display: flex;
            flex-direction: column;
            // justify-content: center;
            align-items: center;
            gap:40px;

            @include m(){
                gap:16px;
            }

            @include m(){
                gap:16px;
            }

        
                div{ /* 裝備圖片 */ 
                    width: 120px;
                    height: 120px;
                    border-radius: 50%;
                    background-color: #666;
                    overflow: hidden;
                    // border:0.3px solid #ccc;

                    @include m(){
                        width: 100px;
                        height: 100px;
                    }

                    @include s(){
                        width: 90px;
                        height: 90px;
                    }

                    img{
                        object-fit: cover;
                        width: 100%;
                        height: 100%;
                    }

                    .grayImg {
                        filter: grayscale(100%) brightness(1.2) contrast(80%);
                        opacity: .6;                         /* 再淡一點 */
                        transition: filter .25s ease, opacity .25s ease;
                    }
                }

                span{/* 裝備名稱 */
                    font-size: $pcFont-p-m;
                    font-weight:$medium;

                    @include m(){
                        font-size: 14px;
                    }
                }

                .graySpan{
                    color: #999;
                    font-weight:$regular;

                }

            }

        }

        // button{
            

        //     width: 25%;
        //     max-width: 240px;
           
        //     padding: 10px;
        //     margin-bottom: 40px;
            
        //     border-radius: 8px;
        //     border: none;
        //     background-color:$tag;
        //     color: white;

        //     font-size: $pcFont-p-m;
        //     cursor: pointer;

        //     @include m(){
        //         width: 90%;
        //         font-size: $pcFont-p-s;
        //         margin-bottom: 0;
        //     }

            

        // }
    }


}


.page{

    // outline: 1px solid black;
    margin: 0 auto;
    margin-top: 32px;
    background-color: white;
    width: 95%;
    max-width: 1140px;
    border-radius: 18px;
    height: 600px;

    padding: 32px;
    box-sizing: border-box;

    @include m(){
        max-width: 728px;
        font-size: 14px;
        padding: 36px 20px;
    }


    h2{
            font-size: $pcFont-H2;
            font-weight: $semiBold;
            text-align: center;
            
        }

    .popupMask {
        // position: fixed; // 蓋住整個畫面
        // top: 0;
        // left: 0;
        // width: 100vw;
        // height: 100vh;
        // background-color: rgba(0, 0, 0, 0.7); // 半透明背景
        
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.7);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow-y: auto;
        overflow-x: hidden;

        .showPopup{ /* 彈窗的框 */
            width: 80%;
            max-width: 500px;

            background-color:$ivory-gray-100;         
            border-radius: 16px;
            padding: 30px ;
            
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 24px;

            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%); 

            @include m(){
                width: 100%;
                max-width: 400px;
                padding: 20px;
            }

            @include s(){
                max-width: 350px;
            }
            

            button{
                margin-left: auto;
                border-radius: 50%;
                font-weight:$medium;
                font-size: 18px;
                border: none;
                background-color: transparent;
                cursor: pointer;

                &:hover{
                    background-color: rgba(255, 255, 255, 0.5);
                }
                
            }

            table{
            
            width: 98%;
            text-align: center;
            line-height:$lineHeight-p-150;
            border-radius: 8px;
            overflow: hidden;
            background-color: white;
            table-layout: fixed;

            border-collapse: collapse;
            border-spacing: 0;

            @include m(){
                font-size: 12px;
            }
            

                
                
                th, td {
                    border: none !important;   // 強制清除所有邊框 ✅
                    outline: none;             // 防止某些瀏覽器邊框殘影 ✅
                    box-shadow: none;          // 清除格線殘影 ✅
                    }

                th {
                    background-color: $ash-olive-400;
                    padding: 10px;
                    font-weight: bold;
                    color: white;
                    border-bottom: 15px solid $ivory-gray-100;
                    
                    
                }
                

                td {
                    background-color: #fff;
                    padding: 12px 8px;
                    border-top: 1px solid #c40e0e;
                }

                

                tr:nth-child(1) td, 
                tr:nth-child(2) td 
                {
                    border-bottom: 15px solid $ivory-gray-100;
                }

                

            
            }
        }

 

    }   

}


.section-footer { 
    display: inline-flex;
    // align-items: center;
    // align-items: baseline;
    width: max-content;
    margin: 0 auto;
    gap: 8px;
    flex-wrap: nowrap;        /* 不允許換行 */
    white-space: nowrap;      /* 內容不斷行（中文也不會在字與字之間折） */

    transition: gap 0.4s ease;
}
.section-footer:hover {
    gap: 16px;
}
.section-footer .view-all {  
    color: $black-14;
    font-size: $pcFont-H3;
    font-weight: $bold;
    text-decoration: 1px underline;
    text-underline-offset: 10px;
    line-height: 150%;
    display: inline-block;    /* 保持在同一行 */
    white-space: nowrap;      /* 文字本身也不斷行 */
    cursor: pointer;
}

.section-footer .diag-btn{
    --size: 40px;        /* 按鈕尺寸 */
    --icon: 24px;        /* 箭頭大小 */
    --fly: 24px;         /* 飛出距離（右上 / 左下） */
    --dur: 720ms;        /* 動畫時間 */
  
    position: relative;
    width: var(--size);
    height: var(--size);
    background-color: $tag;
    border: 0;
    border-radius: 4px;
    overflow: hidden;
}
  
.section-footer .arrow{
    width: var(--icon);
    height: var(--icon);
    position: absolute;
    left: 4px;
    bottom: 4px;
    will-change: transform, opacity;
}
  
.section-footer .shaft, .head{
    stroke: #fff;
    stroke-width: 2px;
    stroke-linecap: square;
    stroke-linejoin: square;
    fill: none;
}

.section-footer:hover .arrow,
.section-footer:focus-visible .arrow{
    animation: boomerang45 var(--dur) cubic-bezier(.2,.7,.2,1) 1;
}
  
@keyframes boomerang45 {
    0% {
        transform: translate(0, 0);
        opacity: 1;
    }
    55% {
        transform: translate(var(--fly), calc(var(--fly) * -1)); /* 右上 */
        opacity: 0;
    }
    56% {
        transform: translate(calc(var(--fly) * -1), var(--fly));  /* 左下 */
        opacity: 0;
    }
    100% {
        transform: translate(0, 0);
        opacity: 1;
    }
}

@media (max-width: 768px) {
    .activity-section{
        padding: 0;
    }
    .activity-section .content{
        width: auto;
    }
    .activity-section .section-header{
        margin-left: 24px;
    }
    .activity-section .view-all{
        font-size: $pcFont-H4;
        text-underline-offset: 8px;
    }
    .activity-section .card-container{
        width: 100%;
        padding: 0 24px;
        box-sizing: border-box;

        overflow-x: auto;
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
    }
}


</style>