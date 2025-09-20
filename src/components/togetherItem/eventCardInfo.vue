<template>
  <NavMenu/>

  <div class="wrapper">
    <!-- 載入中 -->
    <div v-if="isLoading" class="loading">
      <div class="spinner"></div>
      <p>載入中...</p>
    </div>

    <!-- 錯誤訊息 -->
    <div v-else-if="error" class="error-message">
      <p>{{ error }}</p>
      <button @click="fetchEventData">重試</button>
    </div>

    <!-- 主要內容 -->
    <div v-else class="wrapperTop">
      <div class="top">
        <div class="topInfoImg">
          <!-- 用 computed 的 imgSrc -->
          <img :src="imgSrc" :alt="eventData.title" />
        </div>
        <div class="topInfo">
          <div class="status-header">
            <h1>{{ eventData.status }}</h1>
            <div class="closeBtn" @click="goBack">✕</div>
          </div>
          <h2>{{ eventData.title }}</h2>
          <div class="topInfoP">
            <p>日期</p>
            <p>{{ eventData.date }}</p>
          </div>
          <div class="topInfoP-2">
            <p>時間</p>
            <p>{{ eventData.time }}</p>
          </div>
        </div>
      </div>

      <div class="mainInfo">
        <h1>活動簡介</h1>
        <p>{{ eventData.content }}</p>
      </div>
    </div>

    <!-- 桌面版卡片 -->
    <div class="cardWrapper" v-if="!isLoading && !error">
      <div class="infoCard desktop-version">
        <div class="infoList">
          <div>
            <h1>[集合時間與地點]</h1>
            <div class="infoListH2">
              <h2>{{ formatDate(eventData.startDate) }}</h2>
              <span class="time">{{ formatTime(eventData.startTime) }}</span>
            </div>
            <p class="infoListP">{{ eventData.meetingPlace }}</p>
          </div>
          <div>
            <div class="leftMain">
              <p>[路程]</p>
              <span class="leftMaiMM">{{ eventData.distance }}</span>
            </div>
            <div class="leftFooter">
              <p>[花費時間]</p>
              <span class="lefiMainCH2">約</span>
              <span class="leftMainMM2">{{ eventData.duration }}</span>
              <span class="hms">小時</span>
            </div>
          </div>
        </div>

        <div class="rightInfoCard">
          <!-- Leaflet 地圖 -->
          <div ref="mapContainer" class="map-container"></div>
          <div class="rightInfo">
            <span class="rightInfoNA">報名人數</span>
            <span class="rightInfoNB">{{ eventData.joinQty }}</span>
          </div>
          <div class="deadline-info">
            <span class="rightInfoBNA">{{ formatDate(eventData.registrationDeadlineDate) }}</span>
            <span class="rightInfoBT">{{ formatTime(eventData.registrationDeadlineTime) }}</span>
            <span class="rightInfoBBNA">截止</span>
          </div>
        </div>
      </div>

      <!-- 手機版輪播 -->
      <div class="mobile-carousel">
        <div
          class="carousel-wrapper"
          @touchstart="handleTouchStart"
          @touchend="handleTouchEnd"
        >
          <!-- 第一頁：集合資訊 -->
          <div class="carousel-slide" :class="{ active: currentSlide === 0 }">
            <div class="infoList mobile-layout">
              <div>
                <h1>[集合時間與地點]</h1>
                <div class="infoListH2">
                  <h2>{{ formatDate(eventData.startDate) }}</h2>
                  <span class="time">{{ formatTime(eventData.startTime) }}</span>
                  <span class="ampm">
                    {{
                      eventData.startTime
                        ? (parseInt(eventData.startTime.split(':')[0]) >= 12
                            ? 'pm'
                            : 'am')
                        : 'am'
                    }}
                  </span>
                </div>
                <p class="infoListP">{{ eventData.meetingPlace }}</p>
              </div>
              <div>
                <div class="leftMain">
                  <p>[路程]</p>
                  <span class="leftMainMM">{{ eventData.distance }}</span>
                </div>
                <div class="leftFooter">
                  <p>[花費時間]</p>
                  <span class="lefiMainCH2">約</span>
                  <span class="leftMainMM2">{{ eventData.duration }}</span>
                  <span class="hms">小時</span>
                </div>
              </div>
            </div>
          </div>

          <!-- 第二頁：報名資訊 -->
          <div class="carousel-slide" :class="{ active: currentSlide === 1 }">
            <div class="rightInfoCard mobile-layout">
              <!-- Leaflet 地圖 -->
              <div ref="mobileMapContainer" class="map-container mobile-map"></div>
              <div class="rightInfo">
                <span class="rightInfoNA">報名人數</span>
                <span class="rightInfoNB">{{ eventData.joinQty }}</span>
              </div>
              <div class="deadline-info">
                <span class="rightInfoBNA">{{ formatDate(eventData.registrationDeadlineDate) }}</span>
                <span class="rightInfoBT">{{ formatTime(eventData.registrationDeadlineTime) }}</span>
                <span class="rightInfoBBNA">截止</span>
              </div>
            </div>
          </div>
        </div>

        <!-- 圓點指示器 -->
        <div class="carousel-dots">
          <span
            v-for="(_, index) in 2"
            :key="index"
            :class="['dot', { active: currentSlide === index }]"
            @click="goToSlide(index)"
          ></span>
        </div>
      </div>
    </div>

    <!-- 注意事項 -->
    <div class="wooniInfo" v-if="eventData.notes">
      <ul class="wooniUl">注意事項：
        <li v-for="(note, index) in parseNotes(eventData.notes)" :key="index">
          {{ note }}
        </li>
      </ul>
    </div>

    <!-- 報名按鈕 -->
    <div class="button-wrapper" v-if="!isLoading && !error">
      <button
        class="join-btn"
        :class="{ 
          'btn-registered': hasUserRegistered,
          'btn-disabled': eventData.status === '已截止'
        }"
        @click="handleJoinEvent"
        :disabled="eventData.status === '已截止' || hasUserRegistered"
      >
        {{ getButtonText }}
      </button>
    </div>

    <Footer/>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useUserStore } from '@/stores/user';  // Pinia
import axios from 'axios';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import NavMenu from '../An/navMenu.vue';
import Footer from '@/components/An/footer.vue';

const AJAX_URL = import.meta.env.VITE_AJAX_URL; 
const BASE_URL = AJAX_URL.replace(/\/PHP$/, '/');


// 修改 Leaflet 默認圖標
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
  iconUrl:      `${BASE_URL}images/icon/markerIcon.png`,
  iconRetinaUrl:`${BASE_URL}images/icon/markerIcon2x.png`,
  shadowUrl: `${BASE_URL}images/icon/marker-shadow.png`, // 有陰影檔再開
  iconSize: [48, 64],                                             // 圖示顯示大小：寬64×高64
  iconAnchor: [24, 64],                                           // 錨點在底部中央：寬/2=32, 高=64
  popupAnchor: [0, -64], 
});

const route = useRoute();
const router = useRouter();
const userStore = useUserStore();

// 初始資料結構
const eventData = ref({
  mountainId: null,
  imageName: null,
  id: '',
  title: '載入中…',
  date: '',
  time: '',
  startDate: '',
  startTime: '',
  joinQty: 0,
  meetingPlace: '',
  distance: 0,
  duration: 0,
  content: '',
  notes: '',
  registrationDeadlineDate: '',
  registrationDeadlineTime: '',
  status: '未開團中'
});

const imageMap = ref({});
const isLoading = ref(true);
const error = ref(null);
const currentSlide = ref(0);
const hasUserRegistered = ref(false);
const mapContainer = ref(null);
const mobileMapContainer = ref(null);
const mountainData = ref(null);
let desktopMap = null;
let mobileMap = null;


// 動態按鈕文字
const getButtonText = computed(() => {
  if (eventData.value.status === '已截止') {
    return '報名已截止';
  }
  
  if (userStore.isLoggedIn && hasUserRegistered.value) {
    return '已完成報名';
  }
  
  return '報名參加';
});

// img src
const imgSrc = computed(() => {
  const mid  = eventData.value.mountainId;
  const name = eventData.value.imageName;
  if (mid && name) {
    return `${BASE_URL}images/Mountain/${mid}/${name}`;
  }
  return `${BASE_URL}images/eventCard/cardimg1.jpg`;
});

// 獲取山岳資料
const fetchMountainData = async (mountainId) => {
  try {
    const response = await axios.get(`${AJAX_URL}/getMountainData.php`, {
      params: { mountainId: mountainId }
    });
    
    if (response.data.success) {
      mountainData.value = response.data.data;
      return response.data.data;
    } else {
      throw new Error('無法獲取山岳位置資料');
    }
  } catch (err) {
    console.error('獲取山岳資料失敗:', err);
    return null;
  }
};

// 初始化桌面版地圖
const initDesktopMap = () => {
  if (!mapContainer.value || !mountainData.value) return;
  
  try {
    // 如果地圖已存在，先移除
    if (desktopMap) {
      desktopMap.remove();
    }
    
    const { LATITUDE, LONGITUDE, MOUNTAIN_NAME } = mountainData.value;
    
    desktopMap = L.map(mapContainer.value).setView([LATITUDE, LONGITUDE], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(desktopMap);
    
    L.marker([LATITUDE, LONGITUDE])
      .addTo(desktopMap)
      .bindPopup(`<b>${MOUNTAIN_NAME}</b>`)
      .openPopup();
      
  } catch (err) {
    console.error('初始化桌面版地圖失敗:', err);
  }
};

// 初始化手機版地圖
const initMobileMap = () => {
  if (!mobileMapContainer.value || !mountainData.value) return;
  
  try {
    // 如果地圖已存在，先移除
    if (mobileMap) {
      mobileMap.remove();
    }
    
    const { LATITUDE, LONGITUDE, MOUNTAIN_NAME } = mountainData.value;
    
    mobileMap = L.map(mobileMapContainer.value).setView([LATITUDE, LONGITUDE], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(mobileMap);
    
    L.marker([LATITUDE, LONGITUDE])
      .addTo(mobileMap)
      .bindPopup(`<b>${MOUNTAIN_NAME}</b>`)
      .openPopup();
      
  } catch (err) {
    console.error('初始化手機版地圖失敗:', err);
  }
};

// 檢查使用者是否已報名此活動
const checkUserRegistration = async () => {
  if (!userStore.isLoggedIn) {
    hasUserRegistered.value = false;
    return;
  }

  try {
    const response = await axios.get(`${AJAX_URL}/checkEventRegistration.php`, {
      params: {
        userId: userStore.id,
        eventId: eventData.value.id
      }
    });

    if (response.data.success) {
      hasUserRegistered.value = response.data.hasRegistered || false;
    }
  } catch (err) {
    console.error('檢查報名狀態失敗:', err);
    hasUserRegistered.value = false;
  }
};

// 呼叫兩支 API：文字 & 圖片
const fetchEventData = async () => {
  isLoading.value = true;
  error.value = null;

  try {
    const eventId = route.params.id || 1;

    const [resInfo, resImg] = await Promise.all([
      axios.get(`${AJAX_URL}/eventCardInfo.php`, { params: { id: eventId } }),
      axios.get(`${AJAX_URL}/eventCardImg.php`)
    ]);

    // 處理活動文字資料
    if (resInfo.data.success) {
      Object.assign(eventData.value, resInfo.data.data);
      await checkUserRegistration();
      
      // 獲取山岳資料
      if (eventData.value.mountainId) {
        await fetchMountainData(eventData.value.mountainId);
      }
    } else {
      throw new Error(resInfo.data.message || '活動資料載入失敗');
    }

    // 處理圖片資料映射
    if (resImg.data.success) {
      imageMap.value = resImg.data.data;
      const mid = eventData.value.mountainId;
      if (imageMap.value[mid]) {
        eventData.value.imageName = imageMap.value[mid].imageName;
      }
    } else {
      throw new Error(resImg.data.message || '圖片資料載入失敗');
    }

  } catch (err) {
    if (err.response) {
      error.value = `伺服器錯誤：${err.response.data?.message}`;
    } else if (err.request) {
      error.value = '無法連線至伺服器，請檢查網路';
    } else {
      error.value = err.message;
    }
  } finally {
    isLoading.value = false;
  }
};

// 處理報名點擊事件
const handleJoinEvent = async () => {
  if (eventData.value.status === '已截止' || hasUserRegistered.value) {
    return;
  }

  if (!userStore.isLoggedIn) {
    alert('請先登入才能報名活動！');
    sessionStorage.setItem('redirectAfterLogin', route.fullPath);
    router.push('/loginregister');
    return;
  }

  try {
    const confirmJoin = confirm(`確定要報名「${eventData.value.title}」嗎？`);
    if (!confirmJoin) return;

    const response = await axios.post(`${AJAX_URL}/registerEvent.php`, {
      userId: userStore.id,
      eventId: eventData.value.id,
      userName: userStore.name,
      userEmail: userStore.email
    });

    if (response.data.success) {
      hasUserRegistered.value = true;
      eventData.value.joinQty += 1;
      alert('報名成功！');
    } else {
      alert(response.data.message || '報名失敗，請稍後再試');
    }
  } catch (err) {
    console.error('報名過程發生錯誤:', err);
    alert('系統錯誤，請稍後再試');
  }
};

// 返回上一頁
const goBack = () => router.back();

// 輪播邏輯
let touchStartX = 0;
const handleTouchStart = (e) => (touchStartX = e.touches[0].clientX);
const handleTouchEnd = (e) => {
  const diff = touchStartX - e.changedTouches[0].clientX;
  if (Math.abs(diff) > 50) {
    currentSlide.value = diff > 0
      ? Math.min(currentSlide.value + 1, 1)
      : Math.max(currentSlide.value - 1, 0);
  }
};
const goToSlide = (idx) => (currentSlide.value = idx);

// 格式化日期、時間、notes
const formatDate = (s) => {
  if (!s) return '';
  const d = new Date(s);
  return `${d.getMonth()+1}/${d.getDate()}`;
};
const formatTime = (t) => {
  if (!t) return '';
  const [h,m] = t.split(':');
  const hh = parseInt(h);
  const period = hh >= 12 ? 'pm' : 'am';
  const disp = hh > 12 ? hh - 12 : hh;
  return `${disp}:${m} ${period}`;
};
const parseNotes = (str) => str ? str.split(/[;\\n]/).filter(n => n.trim()) : [];

// 監聽當前滑動頁面變化，初始化對應的地圖
watch(currentSlide, async (newSlide) => {
  if (newSlide === 1 && mountainData.value) {
    await nextTick();
    initMobileMap();
  }
});

// 元件掛載後執行
onMounted(async () => {
  await fetchEventData();
  
  // 初始化桌面版地圖
  if (mountainData.value) {
    await nextTick();
    initDesktopMap();
  }
  
  // 檢查是否有登入後重定向的需求
  const redirectPath = sessionStorage.getItem('redirectAfterLogin');
  if (redirectPath && route.fullPath === redirectPath) {
    sessionStorage.removeItem('redirectAfterLogin');
    if (userStore.isLoggedIn) {
      console.log('歡迎回來！您現在可以報名活動了');
    }
  }
});
</script>

<style lang="scss" scoped>
@import '../../assets/styles/main.scss';
@import 'leaflet/dist/leaflet.css';

.wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    background-color: #fff;
}

.wrapper > footer{
    align-self: stretch;
}

.status-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
    width: 100%;

    h1 {
        display: flex;
        width: 98px;
        height: 43px;
        font-size: 16px;
        font-weight: bold;
        justify-content: center;
        align-items: center;
        border-radius: 999px;
        background-color: #01685E;
        color: #ffffff;
        margin: 0;
    }

    .closeBtn {
        font-size: 28px;
        font-weight: bold;
        color: $black-14;
        cursor: pointer;
        transition: 0.2s ease;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        
        &:hover {
            color: #E13535;
            transform: scale(1.1);
            background-color: rgba(225, 53, 53, 0.1);
        }
    }
}

// top資訊區
.topInfoImg {
    img {
        width: 500px;
        height: 500px;
        border-radius: 10px;
        object-fit: cover;
    }
}

.top {
    display: flex;
    margin-bottom: 100px;
    max-width: 1024px;
    width: 100%;
}

.topInfo {
    margin-left: 80px;
    flex: 1;

    h2 {
        width: 420px;
        height: auto;
        font-size: 24px;
        font-weight: bold;
        color: $black-14;
        line-height: 1.5;
        margin-bottom: 20px;
    }
}

.topInfoP {
    width: 260px;
    height: auto;
    display: flex;
    margin: 20px 0;
    border-top: solid 1px $black-14;
    
    p {
        margin-top: 20px;
        color: $black-14;
        font-size: 20px;
        font-weight: bold;
        
        &:last-child {
            margin-left: 16px;
        }
    }
}

.topInfoP-2 {
    width: 260px;
    height: auto;
    display: flex;
    border-bottom: solid 1px $black-14;
    
    p {
        margin-bottom: 20px;
        color: $black-14;
        font-size: 20px;
        font-weight: bold;
        
        &:last-child {
            margin-left: 16px;
        }
    }
}

// 中間活動資訊
.mainInfo {
    width: 100%;
    max-width: 1024px;
    height: auto;
    padding: 0 20px;
    
    h1 {
        width: 100%;
        margin-bottom: 15px;
        font-size: 24px;
        font-weight: bold;
        color: $black-14;
    }
    
    p {
        width: 100%;
        line-height: 2;
        font-size: 20px;
    }
}

.wrapperTop {
    margin-bottom: 100px;
    width: 100%;
    max-width: 1024px;
}

.cardWrapper {
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    max-width: 1024px;
    height: 500px;
    background-color: $ivory-gray-100;
    border-radius: 16px;
    margin-bottom: 100px;
    padding: 0 20px;
}

// 桌面版顯示，手機版隱藏
.desktop-version {
    display: flex;
}

.mobile-carousel {
    display: none;
}

// 下方卡片詳細
.infoCard {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    width: 100%;
}

.infoList {
    width: 100%;
    height: 100%;
    margin-top: 64px;
    margin-bottom: 71px;
    margin-left: 80px;
    
    h1 {
        font-size: 20px;
        font-weight: bold;
        color: $black-14;
        margin: 15px 0;
    }
    
    .infoListH2 {
        display: flex;
        align-items: flex-end;
        margin-bottom: 15px;

        h2 {
            font-size: 36px;
            margin-right: 16px;
        }
        
        .time {
            font-size: 36px;
        }
        
        .ampm {
            font-size: 20px;
        }
    }
}

.infoListP {
    font-size: 20px;
    font-weight: bolder;
    margin-top: 8px;
    margin-bottom: 60px;
}

.leftMain {            
    margin-bottom: 60px;  
    
    p {
        line-height: 1.2;
        font-size: 20px;
        font-weight: bold;
        margin: 15px 0;
    }
    
    .leftMaiMM {
        line-height: 1.2;
        font-weight: bold;
        font-size: 20px;
    }
}

.leftFooter {
    p {
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 15px;
    }
    
    .lefiMainCH2, .hms {
        font-size: 20px;
        font-weight: bold;
    }
    
    .leftMainMM2 {
        font-size: 36px;
    }
}

.rightInfoCard {
    margin-left: 179px;
    
    // 地圖容器樣式
    .map-container {
        display: block;
        width: 400px;
        height: 260px;
        margin-top: 64px;
        border-radius: 10px;
        overflow: hidden;
        border: 2px solid #ddd;
    }
}

.rightInfo {
    margin-top: 65px;
    margin-bottom: 18px;
    margin-left: 150px;
    
    .rightInfoNA, .rightInfoNB {
        font-size: 24px;
        font-weight: bold;
        color: $black-14;
    }
    
    .rightInfoNA {
        margin-right: 12px;
    }
}

.deadline-info {
    margin-left: 150px;
    margin-right: 80px;
    width: 250px;
}

.rightInfoBNA, .rightInfoBT, .rightInfoBBNA {
    font-size: 24px;
    font-weight: bold;
    margin-right: 12px;
}

.rightInfoBBNA {
    color: #E13535;
}

.wooniInfo {
    font-size: 20px;
    font-weight: bold;
    line-height: 2;
    padding: 30px;
    margin-left: 220px;
    margin-bottom: 50px;
    align-self: flex-start;
    width: 100%;
    max-width: 1024px;
    
    .wooniUl {
        list-style: disc inside;
    }
}

.button-wrapper {
    width: 100%;
    display: flex;
    justify-content: center;
    margin-bottom: 50px;
}

// 報名按鈕樣式
.join-btn {
    width: 342px;
    height: 56px;
    background-color: $black-14;
    border: none;
    border-radius: 30px;
    color: #ffffff;
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;

    &:hover:not(:disabled) {
        background-color: darken($black-14, 10%);
        transform: translateY(-2px);
    }

    &:active:not(:disabled) {
        transform: translateY(0);
    }

    &.btn-registered {
        background-color: #4CAF50;
        cursor: default;
        
        &:hover {
            background-color: #4CAF50;
            transform: none;
        }
    }

    &.btn-disabled {
        background-color: #9E9E9E;
        cursor: not-allowed;
        opacity: 0.7;
        
        &:hover {
            background-color: #9E9E9E;
            transform: none;
        }
    }

    &:disabled {
        cursor: not-allowed;
    }
}

// 手機版響應式樣式
@media screen and (max-width: 1200px) {
    .wrapper {
        padding: 0 15px;
    }

    .top {
        flex-direction: column;
        align-items: center;
        margin-bottom: 50px;
    }

    .topInfoImg {
        margin-bottom: 30px;

        img {
            width: 100%;
            max-width: 350px;
            height: 250px;
            border-radius: 8px;
        }
    }

    .topInfo {
        margin-left: 0;
        width: 100%;

        .status-header {
            flex-direction: column;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;

            h1 {
                width: 80px;
                height: 35px;
                font-size: 14px;
                order: 1;
            }

            .closeBtn {
                order: 0;
                align-self: flex-end;
                font-size: 24px;
                margin-bottom: 10px;
            }
        }

        h2 {
            font-size: 20px;
            width: 100%;
            text-align: center;
            margin-bottom: 25px;
        }

        .topInfoP, .topInfoP-2 {
            width: 200px;
            margin: 15px auto;
            justify-content: space-between;

            p {
                font-size: 16px;

                &:last-child {
                    margin-left: 0;
                }
            }
        }
    }

    // 主要資訊區手機版
    .mainInfo {
        padding: 0;
        text-align: center;

        h1 {
            font-size: 20px;
        }

        p {
            font-size: 16px;
            line-height: 1.6;
        }
    }

    .wrapperTop {
        margin-bottom: 50px;
    }

    // 卡片區域 - 顯示輪播，隱藏桌面版
    .cardWrapper {
        padding: 20px 15px;
        min-height: auto;
        margin-bottom: 50px;
    }

    .desktop-version {
        display: none;
    }

    .mobile-carousel {
        display: block;
        width: 100%;
        position: relative;
    }

    .carousel-wrapper {
        position: relative;
        overflow: hidden;
        width: 100%;
    }

    .carousel-slide {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        opacity: 0;
        visibility: hidden;
        transform: translateX(100%);
        transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);

        &.active {
            opacity: 1;
            visibility: visible;
            transform: translateX(0);
            position: relative;
        }
    }

    // 手機版資訊列表樣式
    .infoList.mobile-layout {
        margin: 0 auto;
        width: 90%;
        padding: 20px;
        line-height: 1.3;

        h1 {
            font-size: 18px;
            text-align: center;
            margin-bottom: 20px;
        }

        .infoListH2 {
            justify-content: center;
            margin-bottom: 10px;

            h2 {
                font-size: 28px;
            }

            .time {
                font-size: 28px;
            }

            .ampm {
                font-size: 16px;
            }
        }

        .infoListP {
            font-size: 16px;
            text-align: center;
            margin-bottom: 30px;
        }

        .leftMain {
            margin-bottom: 30px;
            text-align: center;

            p {
                font-size: 18px;
                margin: 5px 0;
            }

            .lefiMainCH, .mmkm {
                font-size: 16px;
            }

            .leftMainMM {
                font-size: 28px;
            }
        }

        .leftFooter {
            text-align: center;

            p {
                font-size: 18px;
            }

            .hms, .lefiMainCH2 {
                font-size: 16px;
            }

            .leftMainMM2 {
                font-size: 28px;
            }
        }
    }

    // 手機版右側資訊卡樣式
    .rightInfoCard.mobile-layout {
        margin: 0 auto;
        width: 90%;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px;

        // 手機版地圖容器
        .map-container.mobile-map {
            width: 100%;
            max-width: 280px;
            height: 200px;
            margin-top: 0;
            margin-bottom: 50px;
            border-radius: 8px;
        }

        .rightInfo {
            margin: 0 0 18px;
            text-align: center;

            .rightInfoNA, .rightInfoNB {
                font-size: 20px;
            }

            .rightInfoNA {
                margin-right: 12px;
            }
        }

        .deadline-info {
            margin: 0;
            text-align: center;
        }

        .rightInfoBBNA, .rightInfoBNA, .rightInfoBT {
            font-size: 20px;
            font-weight: bold;
            margin-right: 12px;
        }

        .rightInfoBBNA {
            color: #E13535;
            margin-right: 0;
        }
    }

    // 圓點樣式
    .carousel-dots {
        display: flex;
        justify-content: center;
        gap: 12px;
        margin-top: 30px;

        .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: linear-gradient(135deg, #e0e0e0, #c0c0c0);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            position: relative;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);

            &::before {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 0;
                height: 0;
                border-radius: 50%;
                background: linear-gradient(135deg, #01685E, #007B6F);
                transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            }

            &:hover {
                transform: scale(1.1);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            }

            &.active {
                background: linear-gradient(135deg, #01685E, #007B6F);
                transform: scale(1.2);
                box-shadow: 0 4px 12px rgba(1, 104, 94, 0.3);

                &::before {
                    width: 4px;
                    height: 4px;
                    background: rgba(255, 255, 255, 0.9);
                }
            }
        }
    }

    // 注意事項手機版
    .wooniInfo {
        padding: 20px 0;
        font-size: 16px;
        margin-left: 0;
        text-align: left;

        .wooniUl {
            padding-left: 15px;

            li {
                margin-bottom: 8px;
            }
        }
    }

    // 按鈕手機版
    .join-btn {
        width: 100%;
        max-width: 300px;
        height: 50px;
        font-size: 20px;
    }

    .button-wrapper {
        margin-bottom: 30px;
        padding: 0 15px;
    }
}

// 載入和錯誤狀態樣式
.loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 200px;
    
    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #01685E;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-bottom: 20px;
    }
    
    p {
        font-size: 18px;
        color: $black-14;
    }
}

.error-message {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 200px;
    text-align: center;
    
    p {
        font-size: 18px;
        color: #E13535;
        margin-bottom: 20px;
    }
    
    button {
        background-color: #01685E;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        
        &:hover {
            background-color: darken(#01685E, 10%);
        }
    }
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>