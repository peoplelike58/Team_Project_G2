<template>
    <div id="qaList">
        <h1>Q & A</h1>
        <div v-for="(item ,index) in qnaList" :key="index" class="textList"
             :class="{ active: activeIndex === index }" @click="toggle(index)">
            <h2>{{ item.qtitle }}</h2>
            <p>{{ item.atitle }}</p>
            <div class="indexList"></div>
        </div>
    </div>
</template>

<script setup>

import { ref } from 'vue'

const qnaList = [
    {
        qtitle: '我要怎麼加入一個登山團？',
        atitle: '請先註冊帳號，進入「揪團」頁面，篩選符合你時間、地點與等級的登山活動，點擊「我要參加」即可提出參加申請'
    },
    {
        qtitle: '我可以匿名參加嗎？',
        atitle: '為確保安全與聯繫順利，參團需使用真實姓名與聯絡方式，發起人與參團人僅能查看彼此必要資訊，平台保障個資隱私。'
    },
    {
        qtitle: '有沒有推薦的新手路線？',
        atitle: '在「揪團」頁面可用「新手友善」篩選條件，常見推薦路線如陽明山、郊山步道、合歡山主峰等，適合首次登山體驗。'
    },
    {
        qtitle: '怎麼知道這個揪團是不是安全可靠？',
        atitle: '本站活動皆有嚴格篩選，平台也會審核爬山路線難度，提醒適合的參加者。'
    },
    {
        qtitle: '可以取消參加嗎？',
        atitle: '可在活動開始前「於會員中心取消」內退出團隊；若逾期取消或臨時爽約，可能會影響你的信用評價，並遭限制參加其他揪團。'
    },
    {
        qtitle: '揪團會有保險嗎？',
        atitle: '平台鼓勵發起人提供登山意外保險選項，但是否保險由該團隊自行決定，請務必自行確認。平台也會提供相關保險資訊連結供參考。'
    }
]

const activeIndex = ref(null)

function toggle (index) {
    activeIndex.value = activeIndex.value === index ? null : index
}
</script>

<style lang="scss" scoped>
// 問與答區塊
.qaList {
//   border: 2px solid red;
//   width: 1080px;
//   margin: 0 auto;
//   overflow-y: auto;

  h1 {
    width: 1030px;
    font-size: 40px;
    font-weight: bold;
    text-align: center;
    margin-left: 20px;
    margin-top: 10px;
    padding-bottom: 30px;
    border-bottom: 3px dashed gray;
  }
}

 // Q&A 內容
.textList {
  width: 1030px;
  margin-left: 24px;
  margin-top: 20px;
  cursor: pointer;

  h2 {
    width: 823px;
    font-size: 24px;
    font-weight: bold;
    margin: 0;
  }

  p {
    width: 823px;
    font-size: 20px;
    font-weight: bold;
    margin: 0;
    margin-top: 20px;
    display: block;
    opacity: 0;
    transform: translateY(-10px);
    max-height: 0;
    overflow: hidden;
    transition:
      max-height 0.3s ease,
      opacity 0.3s ease,
      transform 0.3s ease;
  }

  .indexList {
    margin-top: 20px;
    border-bottom: 3px dashed gray;
  }

  /* 展開狀態 */
  &.active {
    p {
      max-height: 500px;
      opacity: 1;
      transform: translateY(0);
    }
  }
}
</style>