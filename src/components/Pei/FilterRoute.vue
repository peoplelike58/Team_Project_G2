<script setup>

import { ref, computed } from 'vue'
import trailsData from '@/assets/json/trails.json'

const loading = ref(false) // 資料讀取狀態
const error = ref('') // 錯誤訊息
const trails = ref(trailsData) // 原始資料

// --------- 篩選條件按鈕資料與目前狀態 ---------
const regionBtns = ['全部','北部','中部','南部','東部'] // 區域選項
const trafficBtns = ['全部','可乘大眾運輸','須開車前往'] // 交通選項
const timeBtns = ['全部','3小時內','3-6小時','6-12小時','12小時-2天','2天以上'] // 時間選項
const typeBtns = ['全部','百岳','小百岳','其他山岳','必訪步道'] // 類型選項

//預設一開始篩選吧都為「全部」
const regionNow = ref(regionBtns[0]) // 當前選取區域
const trafficNow = ref(trafficBtns[0]) // 當前選取交通
const timeNow = ref(timeBtns[0]) // 當前選取時間
const typeNow = ref(typeBtns[0]) // 當前選取類型



// 1 條件過濾
const filteredTrails = computed(() => {
  return trails.value.filter((trail) => {
    const matchRegion = regionNow.value === '全部' || trail.filter.includes(regionNow.value)
    const matchTraffic = trafficNow.value === '全部' || trail.filter.includes(trafficNow.value)
    const matchTime = timeNow.value === '全部' || trail.filter.includes(timeNow.value)
    const matchType = typeNow.value === '全部' || trail.type === typeNow.value

    return matchRegion && matchTraffic && matchTime && matchType
  })
})

// 2 關鍵字過濾（在已經篩選過的資料上做）
const searchText = ref('') // 使用者輸入的字
const finalResults = computed(() => {
  const keyword = searchText.value.trim() // 去頭尾空白
  if (!keyword) return filteredTrails.value // 如果沒有輸入關鍵字,直接跑條件過濾結果

  return filteredTrails.value.filter((trail) => {
    return (
      trail.name.includes(keyword) || 
      trail.region.includes(keyword) ||
      trail.type.includes(keyword)||
      trail.filter.some(tag => tag.includes(keyword))
      
    )
  })
})

// 3 分頁處理（從最終結果中取出某一頁）
const page = ref(1)
const perPage = 8

const totalPages = computed(() => {
  return Math.max(1, Math.ceil(finalResults.value.length / perPage))
})

const pagedTrails = computed(() => {
  const start = (page.value - 1) * perPage
  const end = start + perPage
  return finalResults.value.slice(start, end)
})

function goPage(p) {
  if (p < 1 || p > totalPages.value) return
  page.value = p
}


</script>




<template> <!-- 畫面模板開始 -->
  <div class="filterBar"> <!-- 篩選工具列（目前為靜態示意） -->
    <span>區域 ｜ <!-- 區域標籤 -->
      <button 
      v-for="regionBtn in regionBtns" 
      :key="regionBtn"
      :class="{ active : regionNow === regionBtn }"
      @click="() => { regionNow = regionBtn; page = 1 }"
      >
        {{ regionBtn }} <!--北/中/南-->

      </button>
      
    </span>


    <br /> <!-- 換行（自閉合） -->

    <span>交通 ｜ <!-- 交通標籤 -->
      <button 
      v-for="trafficBtn in trafficBtns"
      :key="trafficBtn"
      :class="{ active : trafficNow === trafficBtn }"
      @click="() => { trafficNow = trafficBtn; page = 1 }"
      >
        {{ trafficBtn }} <!--可乘大眾運輸/須開車前往-->
      </button>
    </span>

    <br /> <!-- 換行（自閉合） -->

    <span>時間 ｜ <!-- 時間標籤 -->
      <button 
      v-for="timeBtn in timeBtns"
      :key="timeBtn"
      :class="{ active : timeBtn === timeNow }"
      @click=" () => { timeNow = timeBtn; page = 1 }"
      >
        {{ timeBtn }} <!--3小時內/3~6小時/6~12小時...-->
      </button>
    </span>

    <br /> <!-- 換行（自閉合） -->

    <span>類型 ｜ <!-- 類型標籤 -->
      <button
      v-for="typeBtn in typeBtns"
      :key="typeBtn"
      :class="{ active : typeBtn === typeNow }"
      @click="() => { typeNow = typeBtn; page = 1 }"
      >
        {{ typeBtn }}
      </button>
    </span>
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
        <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"> <!-- 放大鏡圖示（SVG 容器） -->
          <path
            fill="currentColor"
            d="M10.5 2a8.5 8.5 0 106.02 14.52l4.24 4.24a1 1 0 001.42-1.42l-4.24-4.24A8.5 8.5 0 0010.5 2zm0 2a6.5 6.5 0 110 13 6.5 6.5 0 010-13z" 
          /> <!-- 自閉合 path，避免缺結束標籤 -->
        </svg> <!-- 結束 SVG -->
      </div> <!-- 搜尋輸入容器結束 -->
    </div> <!-- 關鍵字搜尋區結束 -->

    <div class="mapSearch"> <!-- 地圖搜尋區 -->
      <button>
        地圖搜尋
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <polygon points="1 6 1 22 8 20 16 22 23 20 23 4 16 6 8 4 1 6"></polygon>
          <line x1="8" y1="4" x2="8" y2="20"></line>
          <line x1="16" y1="6" x2="16" y2="22"></line>
        </svg>
      </button> <!-- 按鈕：地圖搜尋（之後可觸發地圖 Modal） -->
    </div> <!-- 地圖搜尋區結束 -->
  </div> <!-- 其他工具列結束 -->

  <div class="result"> <!-- 搜尋結果區塊 -->
    <h2>搜尋結果</h2> <!-- 標題 -->
    <p>有 {{ finalResults.length }} 路線資料</p>
    <div class="headLine"></div> <!-- 分隔線 -->

    <p v-if="loading">資料載入中…</p> <!-- 載入中顯示 -->
    <p v-else-if="error">{{ error }}</p> <!-- 發生錯誤顯示 -->
    <div v-else> <!-- 正常顯示區 -->
      <p v-if="finalResults.length === 0" class="noResult">查無符合的路線，<br>換個條件試試吧QQ</p> <!-- 無資料提示 -->

      <ul class="totalCard" v-else> <!-- 有資料才顯示卡片清單 -->
        <li class="card" v-for="trail in pagedTrails" :key="trail.id"> <!-- 只渲染當前頁的8張卡片 -->
          <img :src="trail.img" :alt="trail.name" /> <!-- 圖片（自閉合） -->
          <div class="meta"> <!-- 卡片文字資訊 -->
            <h4 class="name">{{ trail.name }}</h4> <!-- 路線名稱 -->
            <span>{{ trail.region }}</span> <!-- 所在區域 -->
          </div> <!-- meta 結束 -->
          <div class="tags"> <!-- 標籤列 -->
            <span >{{ trail.type }}</span> <!-- 類型標籤 -->
            <span>{{ trail.difficulty }}</span> <!-- 難度標籤 -->
          </div> <!-- tags 結束 -->
        </li> <!-- 單一卡片結束 -->
      </ul> <!-- 卡片清單結束 -->

      <div class="pager" v-if="finalResults.length > 0"> <!-- 分頁器（有符合的結果就會出現） -->
        <button :disabled="page === 1" @click="goPage(page - 1)">上一頁</button> <!-- 上一頁 -->
        <button
          v-for="p in totalPages"
          :key="p"
          :class="{ active: p === page }"
          @click="goPage(p)"
        >{{ p }}</button> <!-- 頁碼按鈕 -->
        <button :disabled="page === totalPages" @click="goPage(page + 1)">下一頁</button> <!-- 下一頁 -->
      </div> <!-- 分頁器結束 -->
    </div> <!-- 正常顯示區結束 -->
  </div> <!-- 搜尋結果區塊結束 -->
</template>

<style lang="scss"> /* 全域樣式（非 scoped） */
body { // 全頁背景色
  background-color: #EFF1F2; // 淺灰底
}
</style>

<style scoped lang="scss"> /* 元件私有樣式 */
@import '../../assets/styles/main.scss';

.filterBar{ // 篩選列容器
  width: 100%; // 滿版寬
  max-width: 1200px; // 最大寬
  margin: 0 auto; // 水平置中
  background-color: rgba(255,255,255,0.5); // 半透明白底
  // border: 1px solid #000;
  border-radius: 10px; // 圓角
  padding: 10px 20px; // 內距
  box-sizing: border-box;
  display: flex; // 彈性排版
  flex-direction: column; // 垂直排列
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


.otherBar{ // 關鍵字＋地圖搜尋外框
  width: 100%;
  max-width: 1200px;
  margin: auto;
  margin-top: 28px; // 與上方間距
  display: flex; // 彈性排版
  justify-content: space-between; // 左右分散
  padding-right: 20%; // 左右內距
  padding-left: 20px;
  // border: 2px solid black;
  box-sizing: border-box;
}

.keywords{ // 關鍵字搜尋行
  display: flex; // 橫向排列0
  align-items: center; // 垂直置中
  gap: 12px; // 元素間距
}


.searchWrap{ // 搜尋輸入容器
  position: relative; // 讓圖示能絕對定位
  display: inline-block; // 寬度包內容

  input{ // 搜尋輸入框
    font-size: 16px;
    padding: 6px 12px; // 內距
    border: 2px solid gray; // 邊框
    border-radius: 8px; // 圓角
    width: 380px; // 寬度
    box-sizing: border-box; // 邊框計入寬度
  }

  .icon{ // 放大鏡圖示
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

.mapSearch{ // 地圖搜尋區

  button{
    
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;

    svg{
      width: 20px; /* SVG 寬 */
      height: 20px;  /* SVG 高 */
      stroke-width: 1.5; 
    }
  }


}







.result{ // 搜尋結果外框
  width: 100%;
  max-width: 1200px;
  margin: auto;
  margin-top: 60px; // 與上方間距

  // border:2px solid #ff0404;

  h2{ //搜尋結果標題
    font-size: $pcFont-H3;
    font-weight: $medium;
    }

  h2+p{
    margin-top: 10px;
    color: #666;
    font-size: 14px;
  }
  .noResult{ // 沒有任何符合條件的山
    // border: 2px solid red;
    margin-top: 100px;
    text-align: center;
    font-weight: $medium;
    font-size: $pcFont-p-m;
    line-height: $linHeight-p-150;
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
  list-style: none; // 移除項目符號
  padding: 0; // 移除預設內距（解決像 padding-left 的縮排）
  margin: 0; // 移除預設外距
  width: 100%; // 滿版寬
  max-width: 1200px;
  margin: auto;
  min-height: 600px; // 最小高度避免跳動
  margin-top: 52px; // 與標題間距
  // border:2px solid #1115e7; 

  display: flex; // 彈性排列
  flex-wrap: wrap; // 自動換行
  gap: 24px; // 卡片間距
  

  
}

.card{ /* 單一卡片（li） */
  width: calc(25% - 24px); // 4 欄等分（扣除gap視覺修正）
  // max-width: 250px; // 最大寬度
  height: 280px;
  background: #fff; // 白底
  border-radius: 8px; // 圓角
  overflow: hidden; // 超出隱藏
  border: 1px solid #ddd; // 邊框
  box-sizing: border-box; // 邊框計入寬度

  display: flex;
  flex-direction: column;//為了把tags靠底部貼邊

  img{ // 卡片圖片
    display: block; // 移除底部空隙
    width: 100%; // 滿寬
    height: 60%; // 固定高度
    max-height: 168px;
    object-fit: cover; // 充滿容器並裁切
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
  margin-top: 32px; // 與卡片清單距離
  // border: 1px solid yellow;
  text-align: center;
}

</style>