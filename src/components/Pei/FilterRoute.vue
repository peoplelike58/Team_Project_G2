<script setup>

import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router';
import axios from 'axios';




//-------------------JSON-------------------------------------
// import trailsData from '@/assets/json/trails.json'

// 先把 BASE_URL 存成變數，避免在 template 直接寫 import.meta
// 取得部署子目錄，如 '/tjd102/g2/' 
const baseUrl = import.meta.env.BASE_URL                             

// 小工具：把 JSON 裡的相對路徑拼成可用網址
// 回傳拼好的完整路徑
// const toUrl = (p) => `${baseUrl}${p}`  
//------------------------------------------------------------







// 引導至詳細頁面
const router = useRouter()  
const goDetail = MOUNTAIN_ID => router.push({name:'trailDetail' , params:{MOUNTAIN_ID}})

// 資料讀取狀態,是否正在載入
const loading = ref(false) 
// 錯誤訊息
const error = ref('') 
 // 原始資料
// const trails = ref(trailsData)
const trails = ref([])



// ----------------PHP---------------------------
// API 基本路徑
const API_URL = `${import.meta.env.VITE_AJAX_URL}/filterCard.php`

const fetchTrails = async () => {
  
  try {
    const resp = await axios.get(API_URL)
    trails.value = resp.data
    // console.log(trails.value);
    
    

  } catch (err) {
    console.log(err.message);
    
  }
}








// --------- 篩選條件按鈕資料與目前狀態 ---------
const areaBtns = ['全部','北部','中部','南部','東部'] 
const trafficBtns = ['全部','可乘大眾運輸','須開車前往'] 
const timeBtns = ['全部','3小時內','3-6小時','6-12小時','12小時-2天','2天以上'] 
const typeBtns = ['全部','大百岳','小百岳','其他山岳','必訪步道'] 

//預設一開始篩選吧都為「全部」
const areaNow = ref(areaBtns[0]) 
const trafficNow = ref(trafficBtns[0]) 
const timeNow = ref(timeBtns[0]) 
const typeNow = ref(typeBtns[0]) 






// 1 條件過濾
const filteredTrails = computed(() => {
  return trails.value.filter((trail) => {
     
    const matchArea = areaNow.value === '全部' || trail.AREA.includes(areaNow.value)
    const matchTraffic = trafficNow.value === '全部' || trail.TRAFFIC.includes(trafficNow.value)
    const matchTime = timeNow.value === '全部' || trail.TIME.includes(timeNow.value)
    const matchType = typeNow.value === '全部' || trail.TYPE === typeNow.value

    return matchArea && matchTraffic && matchTime && matchType
  })
})

// 2 關鍵字過濾（在已經篩選過的資料上做）
const searchText = ref('') // 使用者輸入的字
const finalResults = computed(() => {
  const keyword = searchText.value.trim() // 去頭尾空白
  if (!keyword) return filteredTrails.value // 如果沒有輸入關鍵字,直接跑條件過濾結果

  return filteredTrails.value.filter((trail) => {
    return (
      trail.MOUNTAIN_NAME.includes(keyword) || 
      trail.AREA.includes(keyword) ||
      trail.TYPE.includes(keyword)
      
      
    )
  })
})

// 3 分頁處理（從最終結果中取出某一頁）
const page = ref(1)
const perPage = ref(8)

function rwdPerPage(){
  perPage.value = window.matchMedia('(max-width: 768px)').matches ? 6 : 8
}

const totalPages = computed(() => {
  return Math.max(1, Math.ceil(finalResults.value.length / perPage.value))
})

const pagedTrails = computed(() => {
  const start = (page.value - 1) * perPage.value
  const end = start + perPage.value
  return finalResults.value.slice(start, end)
})

function goPage(p) {
  if (p < 1 || p > totalPages.value) return
  page.value = p
}

onMounted(() => {                                           
  fetchTrails()
  window.addEventListener('resize',rwdPerPage)       
  
}) 

onUnmounted(() => {
  window.removeEventListener('resize',rwdPerPage)
})


</script>




<template> <!-- 畫面模板開始 -->
<div class="flexWrapper">
  <div class="filterBar"> <!-- 篩選工具列（目前為靜態示意） -->
    
    <div class="choose">
      <span>
        區域 ｜ 
      </span><!-- 區域標籤 -->
      <div class="btnGroup">
        <button 
        v-for="areaBtn in areaBtns" 
        :key="areaBtn"
        :class="{ active : areaNow === areaBtn }"
        @click="() => { areaNow = areaBtn; page = 1 }"
        >
          {{ areaBtn }} <!--北/中/南-->

        </button>
      </div>
    </div>
      
    <br /> 

  <div class="choose">
    <span>
      交通 ｜
    </span><!-- 交通標籤 -->
    <div class="btnGroup">
        <button 
        v-for="trafficBtn in trafficBtns"
        :key="trafficBtn"
        :class="{ active : trafficNow === trafficBtn }"
        @click="() => { trafficNow = trafficBtn; page = 1 }"
        >
          {{ trafficBtn }} <!--可乘大眾運輸/須開車前往-->
        </button>
    </div>
  </div>
    

    <br /> 

  <div class="choose">
    <span>
      時間 ｜
    </span><!-- 時間標籤 -->
    <div class="btnGroup">
      <button 
      v-for="timeBtn in timeBtns"
      :key="timeBtn"
      :class="{ active : timeBtn === timeNow }"
      @click=" () => { timeNow = timeBtn; page = 1 }"
      >
        {{ timeBtn }} <!--3小時內/3~6小時/6~12小時...-->
      </button>
    </div>
  </div>

    <br /> 

  <div class="choose">
    <span>
      類型 ｜ 
    </span><!-- 類型標籤 -->
    
    <div class="btnGroup">
      <button
      v-for="typeBtn in typeBtns"
      :key="typeBtn"
      :class="{ active : typeBtn === typeNow }"
      @click="() => { typeNow = typeBtn; page = 1 }"
      >
        {{ typeBtn }}
      </button>
    </div> 
  </div>
  
    
  </div> <!-- 篩選工具列結束 -->

  <div class="otherBar"> <!-- 其他工具列（關鍵字＋地圖搜尋） -->
    <div class="keywords"> <!-- 關鍵字搜尋區 -->
      <span>關鍵字搜尋</span> <!-- 區塊標題 -->
      <div class="searchWrap"> <!-- 搜尋輸入容器 -->
        <input 
        v-model="searchText"
        type="text" 
        placeholder="想去哪一座山..." 
        />
        <svg class="search-icon" viewBox="0 0 24 24" aria-hidden="true"> <!-- 放大鏡圖示（SVG 容器） -->
          <path
            fill="currentColor"
            d="M10.5 2a8.5 8.5 0 106.02 14.52l4.24 4.24a1 1 0 001.42-1.42l-4.24-4.24A8.5 8.5 0 0010.5 2zm0 2a6.5 6.5 0 110 13 6.5 6.5 0 010-13z" 
          /> 
        </svg>
      </div> 
    </div> 
    
    <!-- 地圖搜尋區 -->
    <!-- <div class="mapSearch"> 
      <button>
        地圖搜尋
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <polygon points="1 6 1 22 8 20 16 22 23 20 23 4 16 6 8 4 1 6"></polygon>
          <line x1="8" y1="4" x2="8" y2="20"></line>
          <line x1="16" y1="6" x2="16" y2="22"></line>
        </svg>
      </button> 
    </div>  -->
  </div> 

  <div class="result"> <!-- 搜尋結果區塊 -->
    <h2>搜尋結果</h2> <!-- 標題 -->
    <p>有 {{ finalResults.length }} 筆路線資料</p>
    <div class="headLine"></div> <!-- 分隔線 -->

    <p v-if="loading">資料載入中…</p> 
    <p v-else-if="error">{{ error }}</p> 
    <div v-else> 
      
      <p v-if="finalResults.length === 0" class="noResult">查無符合的路線，<br>換個條件試試吧QQ</p> 
      
      <ul class="totalCard" >  <!--v-else-->
        <li 
        class="card" 
        @click="goDetail(trail.MOUNTAIN_ID)" 
        v-for="trail in pagedTrails" 
        :key="trail.MOUNTAIN_ID"
        > <!-- 只渲染當前頁的8張卡片 -->
          <div class="imgBox">
            <img :src="`${baseUrl}images/Mountain/${trail.MOUNTAIN_ID}/${trail.IMAGE}`" :alt="trail.MOUNTAIN_NAME" />
          
          </div>

          <!-- <img :src="trail.img" :alt="trail.name" />  -->
          <div class="meta">
            <h4 class="name">{{ trail.MOUNTAIN_NAME }}</h4> 
            <span>{{ trail.REGION }}</span> 
          </div>
          <div class="tags"> 
            <span >{{ trail.TYPE }}</span> 
            <span>{{ trail.DIFF }}</span> 
          </div> 
        </li> 
      </ul> 

      
      <div class="pager" v-if="finalResults.length > 0"> 
      
        <button :disabled="page === 1" @click="goPage(page - 1)">上一頁</button> 
        <button
          v-for="p in totalPages"
          :key="p"
          :class="{ active: p === page }"
          @click="goPage(p)"
        >{{ p }}</button> <!-- 頁碼按鈕 -->
        <button :disabled="page === totalPages" @click="goPage(page + 1)">下一頁</button> 
      </div> <!-- 分頁器結束 -->
    </div> <!-- 正常顯示區結束 -->
  </div> <!-- 搜尋結果區塊結束 -->

</div>



</template>

<style scoped lang="scss"> /* 元件私有樣式 */
@import '@/assets/styles/main.scss';
@import '@/assets/styles/mixins';



.choose{
  
  display: flex;
  // border: 1px solid red;
  align-items: center;
  span{
    width: 70px;
  }

  .btnGroup{
    display: flex;
    width: 100%;
    flex-wrap: wrap;


  }
  

  @include m(){
    flex-direction: column;
    span{
        // border: 1px solid red;
        width: 100%;
        margin-bottom:5px ;
    }

    button{
      width: 112px;
      border: 1px solid $tag;
      text-align: center;
      justify-content: space-between;
      margin: 8px;
      @include m(){
        width: 87px;
        margin: 4px;

      }

    }
    
  }
}

.flexWrapper{
  display: flex;
  flex-direction: column;
  background-color: $bg-gray;
  @include m(){
    font-size: 14px;
  }
  
}


.filterBar{ // 篩選列容器
  width: 100%; 
  max-width: 1200px; 
  margin: 0 auto; 

  background-color: rgba(255,255,255,0.5); 
  // border: 1px solid #000;
  border-radius: 10px; 
  padding: 40px; 
  box-sizing: border-box;
  display: flex; 
  flex-direction: column; 

  @include m(){
    order: 1;
    max-width: 708px;
    margin-top: 20px;
    padding: 20px;
  }
}

button{ // 通用按鈕樣式
  all: unset; // 清除預設樣式
  cursor: pointer; // 指標為手
  padding: 5px 15px; // 內距
  border-radius: 8px; // 圓角
  margin-right: 10px; // 右邊距


  &:focus,
  &.active{ // 鍵盤聚焦樣式
  background-color: #141414; // 深色底
  color: #fff; // 白字
}
}


.otherBar{ // 關鍵字搜尋外框
  width: 100%;
  max-width: 1200px;
  margin: auto;
  margin-top: 28px; // 與上方間距
  display: flex; // 彈性排版
  justify-content: space-between; // 左右分散
  padding:0 20px;

  // border: 2px solid black;
  box-sizing: border-box;

  @include m(){
   
    max-width: 768px;
  }


}

.keywords{ // 關鍵字搜尋行

  display: flex; // 橫向排列0
  align-items: center; // 垂直置中
  gap: 12px; // 元素間距

  >span{
    width: 80px;

    @include m(){
      display: none;
    }
  }
  
}


.searchWrap{ // 搜尋輸入容器

  position: relative; // 讓圖示能絕對定位
  display: inline-block; // 寬度包內容
  



  input{ // 搜尋輸入框
    font-size: 16px;
    padding: 6px 12px; 
    border: 1.5px solid gray; 
    border-radius: 8px; 
    width: 450px; 
    box-sizing: border-box; 

    @include m(){
      width: 708px;
    }

  }

  .search-icon{ // 放大鏡圖示
    position: absolute; // 絕對定位
    right: 20px; // 右距
    top: 50%; // 垂直置中基準
    width: 18px; // 圖示寬
    height: auto; // 高度自動
    transform: translateY(-50%); // 真正垂直置中
    pointer-events: none; // 不阻擋輸入框點擊
    color: #666; // 顏色（可用 currentColor 控制）
  }
}

// 地圖搜尋區
// .mapSearch{ 

//   button{
    
//     display: flex;
//     align-items: center;
//     justify-content: center;
//     gap: 8px;

//     svg{
//       width: 20px; /* SVG 寬 */
//       height: 20px;  /* SVG 高 */
//       stroke-width: 1.5; 
//     }
//   }


// }







.result{ // 搜尋結果外框
  width: 100%;
  max-width: 1200px;
  margin: auto;
  margin-top: 60px; // 與上方間距
  @include m(){
    order: 3;
    max-width: 768px;
  }

  // border:2px solid #ff0404;

  h2{ //搜尋結果標題
    font-size: $pcFont-H3;
    font-weight: $medium;

    @include m(){
    padding-left: 20px;
    box-sizing: border-box;
    }

  }

  h2+p{
    margin-top: 10px;
    color: #666;
    font-size: 14px;

    @include m(){
    padding-left: 20px;
    }

  }
  .noResult{ // 沒有任何符合條件的山
    // border: 2px solid red;
    margin-top: 100px;
    text-align: center;
    font-weight: $medium;
    font-size: $pcFont-p-m;
    line-height: $lineHeight-p-150;
    color: #999;
    height: 250px;
  }

  .headLine{ // 分隔線
    height: 0.5px; // 高度
    width: 90%; // 寬度
    background-color: #333; // 顏色
    margin:20px 0; // 上下間距
    float: right; // 右浮動
    overflow: hidden; // 隱藏溢出
  }
}

.totalCard{ /* 卡片清單（ul） */
 
  padding: 20px; 

  width: 100%; 
  max-width: 1200px;
  margin: auto;
  min-height: 600px; 
  margin-top: 52px; 
  box-sizing: border-box;
  // border:2px solid #1115e7; 

  display: flex; // 彈性排列
  flex-wrap: wrap; // 自動換行
  gap: 12px; // 卡片間距

  @include m(){
   
    max-width: 768px;
    gap: 8px;
  }
  

  
}

.card{ /* 單一卡片（li） */
  flex-basis: calc((100% - 36px) / 4);
  
  // max-width: 250px; // 最大寬度
  height: 280px;
  background: #fff; // 白底
  border-radius: 8px; // 圓角
  overflow: hidden; // 超出隱藏
  border: 1px solid #ddd; // 邊框
  box-sizing: border-box; // 邊框計入寬度

  display: flex;
  flex-direction: column;//為了把tags靠底部貼邊

  cursor: pointer;

  &:hover{
    border: 1px dashed #ddd;
  }

  @include m(){
   
  // flex-basis: calc((100% - 8px) / 2);
  flex-basis: calc((100% - 16px) / 3);
    
  }

  /* 新增圖片外層容器，負責裁切與固定高度 */
  .imgBox {
    flex: 0 0 60%;         // 讓圖片區佔卡片高度 60%
    max-height: 168px;     // 保持你原本的上限
    overflow: hidden;      // 關鍵：裁掉放大後超出的部分
  }

  /* 把圖片規則搬到 .imgBox 內，並加上平滑動畫 */
  .imgBox img {
    display: block;        // 移除底部空隙
    width: 100%;           // 滿寬
    height: 100%;          // 撐滿容器高度（等同你原本的 60%）
    object-fit: cover;     // 充滿並裁切
    transition: transform 0.4s ease;  // 放大時平滑
  }

  /* 改成整張卡片 hover 時，圖片才放大 */
  &:hover .imgBox img {
    transform: scale(1.15);
  }

  .meta{ // 文字區
    margin: 8px 8px; // 內距
    white-space: pre-line; // 支援\n讓跨區顯示區域時斷行
    height: 30%;
    // border: 1px solid pink;
    font-weight: $medium;

    display: flex;
    flex-direction: column;
    gap:8px;
    
  

    h4{
      font-size: $pcFont-H4;
      
    }
    span{ // 區域文字
      color: #999; // 次要色
      line-height: 1.3;
      font-size: 14px;

      @include m(){
        font-size: 13px;
      }

    }
  }
  .tags{ // 標籤列
    
    font-size: 12px; // 小字
    margin: 8px 8px 16px; // 內距
    margin-top: auto; //把tags靠底部貼邊
     
    
    display: flex; // 橫向
    gap: 8px; // 間距
    span{ // 單一標籤
      font-weight: $semiBold;
      color: $tag;
      border: 1.5px solid $tag; // 邊框
      padding: 2px 8px; // 內距
      border-radius: 4px; // 圓角
      white-space: nowrap; // 避免換行
    }
  }
}

.pager{ // 分頁器外框
  margin-top: 32px; 
  margin-bottom: 48px;
  // border: 1px solid yellow;
  text-align: center;
}
/* 兩欄卡片的計算 OK，但再補一個保險，避免任何子元素撐破容器 */
.result,
.totalCard {
  overflow-x: hidden;   /* 可選保險，不影響正常排版 */
}

/* 最後一層保險（真的還有莫名元素撐破時） */
:root, body {
  overflow-x: hidden;   /* 可選 */
}
</style>