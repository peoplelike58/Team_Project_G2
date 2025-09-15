<template>
  <NavMenu/>

  <div class="wrapper">
    <!-- 遊戲區塊 -->
    <div class="game">
      <GamePage @fail="handleGameFail" />
    </div>

    <!-- 按鈕列表 -->
    <div class="iconList">
      <button
        v-for="tab in tabs"
        :key="tab.key"
        :class="{ 
          'active': tab.key === currentSelectedIcon && !isMobileView,
          'icon-hidden': tab.key === currentSelectedIcon && isMobileView
        }"
        @click="handleIconClick(tab.key)"
        aria-pressed="tab.key === currentSelectedIcon"
      >
        <img :src="tab.icon" :alt="tab.label" />
        <p>{{ tab.label }}</p>
      </button>
    </div>

    <!-- 手機版被選中的 icon -->
    <div v-if="isMobileView && currentSelectedIcon" class="selected-icon-area">
      <button 
        class="selected-icon"
        @click="handleIconClick(currentSelectedIcon)"
      >
        <img :src="getSelectedIconData.icon" :alt="getSelectedIconData.label" />
        <p>{{ getSelectedIconData.label }}</p>
      </button>
    </div>

    <!-- 內容區塊 -->
    <div class="content">
      <ul v-if="currentIconContent.length">
        <li v-for="(line, index) in currentIconContent" :key="index">
          {{ line }}
        </li>
      </ul>
    </div>
  </div>

  <Footer/>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import NavMenu  from '@/components/An/navMenu.vue'
import Footer   from '@/components/An/footer.vue'
import GamePage from './gamePage.vue'
import Woni     from '@/assets/images/peaceCard/woniPhotoroom.png'
import People   from '@/assets/images/peaceCard/people.png'
import Info     from '@/assets/images/peaceCard/info.png'
import Back     from '@/assets/images/peaceCard/back.png'

// 四個 tab，content 填入原始資料
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
  }
]

const currentSelectedIcon = ref(tabs[0].key)
const isMobileView = ref(false)

function handleIconClick(key) {
  currentSelectedIcon.value = key
}

function handleGameFail(level) {
  const idx = level - 1
  if (idx >= 0 && idx < tabs.length) {
    currentSelectedIcon.value = tabs[idx].key
    const el = document.querySelector('.content')
    el && el.scrollIntoView({ behavior: 'smooth' })
  }
}

const currentIconContent = computed(() => {
  const tab = tabs.find(t => t.key === currentSelectedIcon.value)
  return tab ? tab.content : []
})

const getSelectedIconData = computed(() => {
  return tabs.find(t => t.key === currentSelectedIcon.value) || {}
})

function checkScreenSize() {
  isMobileView.value = window.innerWidth <= 768
}

onMounted(() => {
  checkScreenSize()
  window.addEventListener('resize', checkScreenSize)
})

onUnmounted(() => {
  window.removeEventListener('resize', checkScreenSize)
})
</script>

<style lang="scss" scoped>
@import '@/assets/styles/main.scss';

.wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 0 20px;
}

.game {
  width: 100%;
  max-width: 1000px;
  height: 400px;
  text-align: center;
  margin-bottom: 250px;

  @media (max-width: 1200px) {
    display: none;
  }
}

.iconList {
  display: flex;
  justify-content: center;
  gap: 90px;
  width: 100%;

  button {
    border: none;
    background: transparent;
    cursor: pointer;
    transition: all 0.3s ease;

    img {
      width: 142px;
      height: 142px;
      margin-bottom: 17px;
      transition: all 0.3s ease;
    }

    p {
      font-size: 24px;
      transition: color 0.3s ease;
    }

    &.active {
      transform: scale(1.05);

      p {
        color: #007bff;
      }
    }

    &.icon-hidden {
      @media (max-width: 768px) {
        opacity: 0;
        visibility: hidden;
        width: 0;
        margin: 0;
        padding: 0;
        overflow: hidden;
      }
    }

    @media (max-width: 768px) {
      img {
        width: 80px;
        height: 80px;
      }

      p {
        font-size: 14px;
      }
    }
  }

  @media (max-width: 768px) {
    gap: 15px;
    padding: 0 10px;
  }
}

.selected-icon-area {
  display: none;
  width: 100%;
  justify-content: center;
  margin: 30px 0;
  animation: slideDown 0.3s ease;

  @media (max-width: 768px) {
    display: flex;
  }

  .selected-icon {
    border: none;
    background: transparent;
    cursor: pointer;
    transform: scale(1.3);
    animation: scaleUp 0.3s ease;

    img {
      width: 120px;
      height: 120px;
      margin-bottom: 10px;
      border-radius: 10px;
    }

    p {
      font-size: 20px;
      color: #007bff;
      font-weight: bold;
    }
  }
}

@keyframes slideDown {
  from {
    transform: translateY(-20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

@keyframes scaleUp {
  from {
    transform: scale(1);
  }
  to {
    transform: scale(1.3);
  }
}

.content {
  margin-top: 20px;
  width: 100%;
  max-width: 1200px;
  text-align: center;

  ul {
    list-style: inside decimal;
    padding: 0 20px;
    margin-top: 20px;

    li {
      margin-bottom: 0.5rem;
      line-height: 1.6;
      font-size: 20px;
      overflow-wrap: break-word;

      @media (max-width: 768px) {
        font-size: 16px;
        line-height: 1.8;
      }
    }
  }

  @media (max-width: 1200px) {
    margin-top: 60px;
  }

  @media (max-width: 768px) {
    margin-top: 0;
  }
}
</style>
