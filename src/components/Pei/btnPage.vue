<script setup>
import { ref, onMounted, watch } from "vue"
import L from "leaflet"
import "leaflet/dist/leaflet.css"

const goPage = ref("detailPage")
const showPopup = ref(false)
const mapBox = ref(null)
let map // 宣告在外面，讓後面可以存取

onMounted(() => {
  // 初始化地圖
  map = L.map(mapBox.value).setView([24.457743, 121.258031], 15) // 要串接trail.longitude 和 trail.latitude
  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "&copy; OpenStreetMap"
  }).addTo(map)
  L.marker([24.457743, 121.258031]).addTo(map).bindPopup("大霸尖山")
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
                    <p>新竹縣尖石鄉／苗栗縣泰安鄉</p> <!-- 要串接 trail.region -->
                </li>

                <li>
                    <span>
                        難易度
                        
                        <img 
                            src="../../../public/img/icons/difficulty.svg" 
                            alt="問號icon"
                            @click="showPopup = !showPopup"
                            style="cursor: pointer"
                        />
                        
                    </span>
                    <p>難</p> <!-- 要串接 trail.level -->
                </li>

                <li>
                    <span>交通</span>
                    <p>需開車前往</p> <!-- 要串接 trail.traffic -->
                </li>
                <li>
                    <span>里程</span>
                    <p>單程13公里</p> <!-- 要串接 trail.long -->
                </li>
                <li>
                    <span>建議時間</span>
                    <p>3天2夜</p> <!-- 要串接 trail.time -->
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
                    </div>
                    <span>登山鞋</span>
                </li>
                <li>
                    <div>
                        <!--到時候會放商品圖-->
                    </div>
                    <span>登山杖</span>
                </li>
                <li>
                    <div>
                        <!--到時候會放商品圖-->
                    </div>
                    <span>手套</span>
                </li>
                <li>
                    <div>
                        <!--到時候會放商品圖-->
                    </div>
                    <span>頭燈</span>
                </li>
                <li>
                    <div>
                        <!--到時候會放商品圖-->
                    </div>
                    <span>登山爐具</span>
                </li>
                <li>
                    <div>
                        <!--到時候會放商品圖-->
                    </div>
                    <span>睡袋&帳篷</span>
                </li>
            </ul>

            <button>前往山腳雜貨店↗</button>
        
        
        </div>

    </div>  
</template>



<style scoped lang="scss"> 
@import '../../assets/styles/main.scss';

.btnpages{ /* 最外面的框 */
    padding: 64px 0;
    width: 100%;
    max-width: 1200px;
    margin:0 auto;
    
    .btns{/* ul 切換按鈕 */
        // border: 1px solid red;
        display: flex;

        justify-content: center; /* li 置中 */
        align-items: center;
        gap: 48px;

        li{

            button{
                cursor: pointer;
                width: 192px;
                padding: 8px 52px;
                border-radius: 8px;
                border: none;
                background-color: $ivory-gray-100;

                font-size: $pcFont-p-m;

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
        
            li{
                border-bottom: 1px solid #999;
                padding: 8px 15px;
                display: flex;
                margin-bottom: 40px;
                font-size: $pcFont-p-m;
                font-weight: $medium;
                
                gap: 7rem;

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
        height: 600px;
        .mapBox{
            height: 500px;
            width: 100%;
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
            


        }

        .equipment{ /* ul */
            // border: 1px solid ;
            width: 90%;
            margin: 0 auto;
            display: flex;
            flex-grow: 2;


            li{
            // border: 1px solid red;
            flex-basis: 0;
            flex-grow: 1;

            display: flex;
            flex-direction: column;
            // justify-content: center;
            align-items: center;
            gap:40px;

        
                div{ /* 裝備圖片 */ 
                    width: 120px;
                    height: 120px;
                    border-radius: 50%;
                    background-color: #666;
                }
                span{/* 裝備名稱 */

                font-size: $pcFont-p-m;
                    

                }

            }

        }

        button{
            

            width: 25%;
            max-width: 240px;
           
            padding: 10px;
            margin-bottom: 40px;
            
            border-radius: 8px;
            border: none;
            background-color:$tag;
            color: white;

            font-size: $pcFont-p-m;
            cursor: pointer;

            

        }
    }


}


.page{

    // outline: 1px solid black;
    margin-top: 32px;
    background-color: white;
    width: 95%;
    max-width: 1140px;
    border-radius: 18px;
    height: 600px;

    padding: 32px;
    box-sizing: border-box;

    h2{
            font-size: $pcFont-H2;
            font-weight: $semiBold;
            text-align: center;
            margin-top: 32px;
        }

    .popupMask {
        position: fixed; // 蓋住整個畫面
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.3); // 半透明背景
        
        .showPopup{ /* 彈窗的框 */
            width: 30%;
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





</style>