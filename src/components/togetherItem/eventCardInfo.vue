<template>
  <NavMenu/>
  <div class="wrapper">
    <!-- Loading 狀態 -->
    <div v-if="isLoading" class="loading">
      <div class="spinner"></div>
      <p>載入中...</p>
    </div>
    
    <!-- 錯誤訊息 -->
    <div v-else-if="error" class="error-message">
      <p>{{ error }}</p>
      <button @click="fetchEventData">重試</button>
    </div>
    
    <!-- 主要內容 - 只在資料載入完成後顯示 -->
    <div v-else-if="eventData && !isLoading" class="wrapperTop">
      <div class="top">
        <div class="topInfoImg">
          <img :src="eventData.imageUrl || '/images/eventCard/cardimg1.jpg'" 
               :alt="eventData.title || '活動圖片'">
        </div>

        <div class="topInfo">
          <div class="status-header">
            <h1>{{ eventData.status }}</h1>
            <div class="closeBtn" @click="goBack">✕</div>
          </div>
          <h2>{{ eventData.title || '活動標題' }}</h2>
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

    <!-- 卡片區域  -->
    <div class="cardWrapper" v-if="eventData && !isLoading && !error">
      <!-- 桌面版 -->
      <div class="infoCard desktop-version">
        <div class="infoList">
          <div>
            <h1>[集合時間與地點]</h1>
            <div class="infoListH2">
              <h2>{{ formatDate(eventData.startDate)}}</h2> 
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
          <img :src="eventData.imageUrl || '/images/eventCard/cardimg1.jpg'" 
               :alt="eventData.title || '活動圖片'">
          
          <div class="rightInfo">
            <span class="rightInfoNA">報名人數</span>
            <span class="rightInfoNB">{{ eventData.joinQty || 0 }}</span>
          </div>
          <div class="deadline-info">
            <span class="rightInfoBNA">{{ formatDate(eventData.registrationDeadlineDate) || '8/13' }}</span>
            <span class="rightInfoBT">{{ formatTime(eventData.registrationDeadlineTime) || '12:00' }}</span>
            <span class="rightInfoBBNA">截止</span>
          </div>
        </div>
      </div>

      <!-- 手機版輪播 -->
<div class="mobile-carousel">
    <div class="carousel-wrapper" 
         @touchstart="handleTouchStart" 
         @touchend="handleTouchEnd">
        
        <!-- 詳細資訊（第一頁） -->
        <div class="carousel-slide" :class="{ active: currentSlide === 0 }">
            <div class="infoList mobile-layout">
                <div>
                    <h1>[集合時間與地點]</h1>
                    <div class="infoListH2">
                        <h2>{{ formatDate(eventData.startDate) }}</h2> 
                        <span class="time">{{ formatTime(eventData.startTime) }}</span>
                        <span class="ampm">{{ eventData.startTime ? (parseInt(eventData.startTime.split(':')[0]) >= 12 ? 'pm' : 'am') : 'am' }}</span>
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

        <!-- 報名資訊（第二頁） -->
        <div class="carousel-slide" :class="{ active: currentSlide === 1 }">
            <div class="rightInfoCard mobile-layout">
                <img :src="eventData.imageUrl || '/images/eventCard/cardimg1.jpg'" 
                     :alt="eventData.title ">
                
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
            @click="goToSlide(index)">
        </span>
    </div>
</div>
    </div>

    <!-- 注意事項 -->
    <div class="wooniInfo" v-if="eventData && eventData.notes">
      <ul class="wooniUl">注意事項：
        <li v-for="(note, index) in parseNotes(eventData.notes)" :key="index">
          {{ note }}
        </li>
      </ul>
    </div>

    <!-- 按鈕區域 -->
    <div class="button-wrapper" v-if="!isLoading && !error">
      <button class="join-btn" @click="handleJoin" 
              :disabled="eventData.status === '已截止'">
        {{ eventData.status === '已截止' ? '報名已截止' : '報名參加' }}
      </button>
    </div>
    
    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import NavMenu from '../An/navMenu.vue';
import Footer from '@/components/An/footer.vue';

const route = useRoute();
const router = useRouter();

const eventData = ref({
  id: '',
  title: '載入中...',
  date: '',
  time: '',
  startDate: '',
  startTime: '',
  joinQty: 0,
  meetingPlace: '',
  distance: 0,
  route: '',
  duration: 0,
  content: '',
  notes: '',
  imageUrl: '/images/eventCard/cardimg1.jpg', // 預設圖片
  registrationDeadlineDate: '',
  registrationDeadlineTime: '',
  status: '揪團中'
});

const isLoading = ref(true);
const error = ref(null);
const currentSlide = ref(0);

// API 設定
const API_BASE_URL = `${import.meta.env.VITE_AJAX_URL}/eventCardInfo.php`

// 取得活動資料
const fetchEventData = async () => {
    try {
        isLoading.value = true;
        error.value = null;
        
        const eventId = route.params.id || 1;
        
        const response = await axios.get(API_BASE_URL, {
            params: { id: eventId },
            headers: {
                'Content-Type': 'application/json'
            }
        });
                
        if (response.data.success) {
            // ⭐ 合併資料，保留預設值
            eventData.value = {
                ...eventData.value,  // 保留預設值
                ...response.data.data  // 覆蓋新資料
            };
            // console.log('活動資料載入成功:', eventData.value);
        } else {
            throw new Error(response.data.message || '載入失敗');
        }
        
    } catch (err) {
        // console.error('載入活動資料失敗:', err);
        
        if (err.response) {
            error.value = `伺服器錯誤: ${err.response.data?.message || '未知錯誤'}`;
        } else if (err.request) {
            error.value = '無法連接到伺服器，請檢查網路連線';
        } else {
            error.value = err.message;
        }
    } finally {
        isLoading.value = false;
    }
};

// 格式化日期
const formatDate = (dateString) => {
    if (!dateString) return '';
    try {
        const date = new Date(dateString);
        return `${date.getMonth() + 1}/${date.getDate()}`;
    } catch (e) {
        return dateString;
    }
};

// 格式化時間
const formatTime = (timeString) => {
    if (!timeString) return '';
    try {
        const [hours, minutes] = timeString.split(':');
        const hour = parseInt(hours);
        const period = hour >= 12 ? 'pm' : 'am';
        const displayHour = hour > 12 ? hour - 12 : hour;
        return `${displayHour}:${minutes} ${period}`;
    } catch (e) {
        return timeString;
    }
};

// 解析注意事項
const parseNotes = (notesString) => {
    if (!notesString) return [];
    return notesString.split(/[;\\n]/).filter(note => note.trim());
};

// 報名處理
const handleJoin = async () => {
    if (eventData.value.status === '已截止') {
        alert('報名已截止');
        return;
    }
    
    alert('報名功能開發中...');
    // TODO: 實作報名功能
};

// 返回上一頁
const goBack = () => {
    router.back();
};

// 輪播控制
let touchStartX = 0;
let touchEndX = 0;

const goToSlide = (index) => {
    currentSlide.value = index;
};

const handleTouchStart = (e) => {
    touchStartX = e.touches[0].clientX;
};

const handleTouchEnd = (e) => {
    touchEndX = e.changedTouches[0].clientX;
    handleSwipe();
};

const handleSwipe = () => {
    const swipeThreshold = 50;
    const diff = touchStartX - touchEndX;
    
    if (Math.abs(diff) > swipeThreshold) {
        if (diff > 0 && currentSlide.value < 1) {
            currentSlide.value++;
        } else if (diff < 0 && currentSlide.value > 0) {
            currentSlide.value--;
        }
    }
};

// 元件載入時取得資料
onMounted(() => {
    fetchEventData();
});

</script>

<style lang="scss" scoped>
@import '../../assets/styles/main.scss';

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
    
    img {
        display: block;
        width: 400px;
        height: auto;
        margin-top: 64px;
        border-radius: 10px;
        object-fit: cover;
    }
}

.rightInfo {
    margin-top: 135px;
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

    &:hover {
        background-color: darken($black-14, 10%);
        transform: translateY(-2px);
    }

    &:active {
        transform: translateY(0);
    }
}


@media screen and (max-width: 768px) {
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

        img {
            width: 100%;
            max-width: 280px;
            margin-top: 0;
            margin-bottom: 120px;
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
</style>