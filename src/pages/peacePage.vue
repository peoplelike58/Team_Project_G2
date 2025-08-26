<template>
<NavMenu/>
  <div class="wrapper">
    <!-- 遊戲 -->
    <div class="game">
      <br>
      <p>Comming Soon!!!</p>
      <br>
      <p>遊戲難產中</p>
    </div>

    <!-- 按鈕列表，由 tabs 做陣列動態產生 -->
    <div class="iconList">
      <button
        v-for="tab in tabs"
        :key="tab.key"
        :class="{ active: tab.key === activeTab }"
        @click="selectTab(tab.key)"
        aria-pressed="tab.key === activeTab"
      >
        <img :src="tab.icon" :alt="tab.label" />
        <p>{{ tab.label }}</p>
      </button>
    </div>

    <!-- 下方內容區 -->
    <div class="content">
      <!-- 使用v-if 有陣列才顯示 -->
      <ul v-if="activeTabData.length">
        <li v-for="(line, index) in activeTabData" :key="index">
          {{ line }}
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import Woni   from '@/assets/images/peaceCard/woniPhotoroom.png'
import Back   from '@/assets/images/peaceCard/back.png'
import People from '@/assets/images/peaceCard/people.png'
import Info   from '@/assets/images/peaceCard/info.png'
import NavMenu from '@/components/An/navMenu.vue'

// 1. 定義 tabs 時直接把 content 變陣列，每項都是一句
const tabs = [
  {
    key: 'woni',
    label: '安全小知識',
    icon: Woni,
    content: [
      '出發前記得申請入山／入園證（如雪霸、玉山等需申請）。',
      '詳細查詢天氣與地形預報（如中央氣象局、風向圖）。',
      '不單獨登山，至少2人以上同行。',
      '告知家人／朋友行程與回程時間。',
      '下載離線地圖與備用電池。',
      '務必備妥足夠水源與高熱量食物。',
      '遵守「山林七不」原則（不留垃圾、不驚擾動物等）。',
      '學習辨認山徑路標與布條指示。',
      '設定「登山預計下山通知」提醒(LINE、APP等)。',
      '如遇天候不佳，應果斷下撤避免風險。'
    ]
  },
  {
    key: 'back',
    label: '裝備資訊',
    icon: People,
    content: [
      '登山鞋／防滑鞋（依難度選擇）',
      '頭燈與備用電池',
      '防風、防水外套與保暖層',
      '水袋／水壺（至少2公升）',
      '高熱量乾糧／能量棒／泡麵',
      '地圖與指南針（或GPS裝置）',
      '急救包（內含繃帶、消毒水、止痛藥等）',
      '登山杖（減少膝蓋壓力）',
      '口哨與反光布條（便於求援）',
      '登山背包（依行程日數選容量）'
    ]
  },
  {
    key: 'people',
    label: '緊急聯絡',
    icon: Info,
    content: [
      '消防署緊急電話：119（可定位通報）',
      '警政署報案電話：110（可代通報山難）',
      '台灣山難救援協會（TMRA）',
      '國家公園管理處緊急聯絡資訊（各園區不同）',
      '登山通報專線：1991 行政院農委會山域服務平台',
      'App 推薦：山林日誌、健行筆記、登山小幫手',
      '提供個人ICE（In Case of Emergency）聯絡人資料',
      '記得下載「登山入山申請單副本」紙本以備查驗',
      '中華電信「緊急定位服務」開啟方法',
      '無訊號區域，學會以手勢、鏡面、布條發送求救訊號'
    ]
  },
  {
    key: 'info',
    label: '山災情報',
    icon: Back,
    content: [
      '中央氣象署即時天氣預警（豪雨、落雷）',
      '水保局土石流警戒查詢平台',
      '林務局山林封閉公告（如落石封路、坍方管制）',
      '最新地震速報與路況警示',
      '颱風警報期間暫停進入山區',
      '熱門山區即時影像監測（玉山、雪山等）',
      '高山症預警與應對方式',
      '山火災發生警示（尤其乾季）',
      '登山口、林道施工封閉資訊（如能高越嶺道）',
      '政府發布山區限時封閉公告彙整（如防疫、災後重建）'
    ]
  },
]

// 預設為第一筆 key
const activeTab = ref(tabs[0]?.key || '')

// 用 computed 直接回傳 content 陣列，如果找不到則回空陣列
const activeTabData = computed(() => {
  const tab = tabs.find(t => t.key === activeTab.value)
  return Array.isArray(tab?.content) ? tab.content : []
})

// 切換 tab
function selectTab(key) {
  activeTab.value = key
}
</script>

<style lang="scss" scoped>
@import '@/assets/styles/main.scss';

.wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.game {
  width: 1421px;
  height: 550px;
  border: 1px solid red;
  text-align: center;
  font-size: 100px;
  margin-bottom: 90px;
}

.iconList {
  display: flex;

  button {
    border: none;
    margin-left: 30px;
    cursor: pointer;
    background: transparent;

    &:first-child {
      margin-left: 0;
    }

    img {
      width: 142px;
      height: 142px;
      object-fit: cover;
      margin-bottom: 17px;
    }

    p {
      font-size: 24px;
    }

    &.active {
      transform: scale(1.05);
      p {
        color: #007bff;
      }
    }
  }
}

.content {
  margin-top: 40px;

  ul {
    list-style: inside decimal;
    padding: 0;
    margin-top: 60px;

    li {
      margin-bottom: 0.5rem;
      line-height: 1.6;
      font-size: 20px;
    }
  }

  p {
    font-size: 20px;
    color: #888;
  }
}
</style>
