<template>
  <div class="member-activities">
    <!-- 頁面標題 -->
    <div class="page-header">
      <h1 class="page-title">我的活動</h1>
    </div>

    <!-- 載入中狀態 -->
    <div v-if="isLoading" class="loading-state">
      <div class="spinner"></div>
      <p>載入中...</p>
    </div>

    <!-- 錯誤狀態 -->
    <div v-else-if="error" class="error-state">
      <p class="error-message">{{ error }}</p>
      <button @click="fetchActivities" class="retry-btn">重試</button>
    </div>

    <!-- 活動列表表格 -->
    <div v-else-if="activities.length > 0" class="activities-table">
      <div class="table-header">
        <div class="header-cell">活動名稱</div>
        <div class="header-cell">報名時間</div>
        <div class="header-cell">出發日期</div>
        <div class="header-cell">操作</div>
      </div>
      
      <div class="table-body">
        <div 
          v-for="activity in activities" 
          :key="activity.id"
          class="table-row"
        >
          <div class="body-cell activity-name">
            <router-link 
              :to="`/together/activities/${activity.id}`" 
              class="activity-link"
            >
              {{ activity.name }}
            </router-link>
          </div>
          <div class="body-cell">{{ activity.registerDate }}</div>
          <div class="body-cell">
            {{ activity.departureDate }}
            <span v-if="activity.departureTime" class="departure-time">
              {{ activity.departureTime }}
            </span>
          </div>
          <div class="body-cell">
            <button 
              class="cancel-btn" 
              @click="cancelActivity(activity)"
              :disabled="cancellingId === activity.id"
            >
              {{ cancellingId === activity.id ? '處理中...' : '取消報名' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- 空狀態 -->
    <div v-else class="empty-state">
      <div class="empty-icon">📅</div>
      <p class="empty-message">目前沒有報名任何活動</p>
      <router-link to="/eventCard" class="explore-btn">
        探索更多活動
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useUserStore } from '@/stores/user'
import { useRouter } from 'vue-router'
import axios from 'axios'

const AJAX_URL = import.meta.env.VITE_AJAX_URL

// Store 和 Router
const userStore = useUserStore()
const router = useRouter()

// 響應式資料
const activities = ref([])
const isLoading = ref(true)
const error = ref(null)
const cancellingId = ref(null)

// 計算屬性：當前登入的會員 ID
const currentMemberId = computed(() => userStore.id)

// 獲取會員活動列表
const fetchActivities = async () => {
  isLoading.value = true
  error.value = null

  try {
    // 檢查是否已登入
    if (!userStore.isLoggedIn || !currentMemberId.value) {
      throw new Error('請先登入')
    }

    // 呼叫 API 獲取會員的活動列表
    const response = await axios.get(`${AJAX_URL}/getMemberEvents.php`, {
      params: {
        memberId: currentMemberId.value
      }
    })

    if (response.data.success) {
      // 格式化活動資料
      activities.value = response.data.data.map(event => ({
        id: event.id,
        name: event.name,
        registerDate: event.registerDate,
        registerTime: event.registerTime,
        departureDate: event.departureDate,
        departureTime: event.departureTime,
        status: event.status,
        registrationStatus: event.registrationStatus,
        content: event.content,
        meetingPlace: event.meetingPlace,
        distance: event.distance,
        mountainId: event.mountainId
      }))
    } else {
      throw new Error(response.data.message || '獲取活動失敗')
    }
  } catch (err) {
    console.error('獲取活動失敗:', err)
    
    if (err.response?.status === 401 || err.message === '請先登入') {
      error.value = '請先登入以查看您的活動'
      // 3秒後跳轉到登入頁面
      setTimeout(() => {
        router.push('/loginregister')
      }, 3000)
    } else if (err.response) {
      error.value = `伺服器錯誤：${err.response.data?.message || '未知錯誤'}`
    } else if (err.request) {
      error.value = '無法連線至伺服器，請檢查網路連線'
    } else {
      error.value = err.message
    }
  } finally {
    isLoading.value = false
  }
}

// 取消活動報名
const cancelActivity = async (activity) => {
  // 確認對話框
  const confirmed = confirm(`確定要取消報名「${activity.name}」嗎？`)
  if (!confirmed) return

  cancellingId.value = activity.id

  try {
    // 呼叫取消報名 API
    const response = await axios.post(`${AJAX_URL}/cancelEventRegistration.php`, {
      memberId: currentMemberId.value,
      eventId: activity.id
    })

    if (response.data.success) {
      // 成功取消，從列表中移除該活動
      activities.value = activities.value.filter(a => a.id !== activity.id)
      
      // 顯示成功訊息
      alert(`已成功取消「${activity.name}」的報名`)
    } else {
      throw new Error(response.data.message || '取消報名失敗')
    }
  } catch (err) {
    console.error('取消報名失敗:', err)
    
    if (err.response) {
      alert(`取消報名失敗：${err.response.data?.message || '伺服器錯誤'}`)
    } else if (err.request) {
      alert('無法連線至伺服器，請檢查網路連線')
    } else {
      alert(`取消報名失敗：${err.message}`)
    }
  } finally {
    cancellingId.value = null
  }
}

// 組件掛載時執行
onMounted(() => {
  // 檢查登入狀態
  if (!userStore.isLoggedIn) {
    error.value = '請先登入以查看您的活動'
    isLoading.value = false
    setTimeout(() => {
      router.push('/loginregister')
    }, 2000)
    return
  }
  
  // 獲取活動列表
  fetchActivities()
})
</script>

<style lang="scss" scoped>
@import '@/assets/styles/main.scss';

.member-activities {
  min-height: 500px;
  padding: 20px;
  
  .page-header {
    margin-bottom: 40px;
    
    .page-title {
      font-size: 32px;
      font-weight: 600;
      color: $black-14;
    }
  }
  
  // 載入狀態
  .loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 300px;
    
    .spinner {
      width: 50px;
      height: 50px;
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
  
  // 錯誤狀態
  .error-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 300px;
    text-align: center;
    
    .error-message {
      font-size: 18px;
      color: #E13535;
      margin-bottom: 20px;
    }
    
    .retry-btn {
      background-color: #01685E;
      color: white;
      border: none;
      padding: 10px 30px;
      border-radius: 25px;
      cursor: pointer;
      font-size: 16px;
      transition: all 0.3s ease;
      
      &:hover {
        background-color: darken(#01685E, 10%);
        transform: translateY(-2px);
      }
    }
  }
  
  // 活動表格
  .activities-table {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    
    .table-header {
      display: grid;
      grid-template-columns: 2fr 1.5fr 1.5fr 1fr;
      background: #F2F2E9;
      border-bottom: 2px solid #e0e0e0;
      
      .header-cell {
        padding: 20px 24px;
        font-size: 16px;
        font-weight: 600;
        color: $black-14;
        text-align: center;
        
        &:first-child {
          text-align: left;
        }
      }
    }
    
    .table-body {
      .table-row {
        display: grid;
        grid-template-columns: 2fr 1.5fr 1.5fr 1fr;
        border-bottom: 1px solid #e0e0e0;
        transition: background-color 0.2s ease;
        
        &:hover {
          background-color: #f9f9f9;
        }
        
        &:last-child {
          border-bottom: none;
        }
        
        .body-cell {
          padding: 20px 24px;
          font-size: 16px;
          color: $black-14;
          display: flex;
          align-items: center;
          justify-content: center;
          text-align: center;
          
          &.activity-name {
            justify-content: flex-start;
            text-align: left;
            
            .activity-link {
              color: #01685E;
              text-decoration: none;
              font-weight: 500;
              transition: color 0.2s ease;
              
              &:hover {
                color: darken(#01685E, 15%);
                text-decoration: underline;
              }
            }
          }
          
          .departure-time {
            margin-left: 8px;
            color: #666;
            font-size: 14px;
          }
          
          .cancel-btn {
            padding: 8px 20px;
            background-color: #fff;
            color: #E13535;
            border: 1px solid #E13535;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            
            &:hover:not(:disabled) {
              background-color: #E13535;
              color: white;
              transform: translateY(-2px);
            }
            
            &:disabled {
              opacity: 0.6;
              cursor: not-allowed;
            }
          }
        }
      }
    }
  }
  
  // 空狀態
  .empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 80px 20px;
    text-align: center;
    
    .empty-icon {
      font-size: 64px;
      margin-bottom: 20px;
    }
    
    .empty-message {
      font-size: 20px;
      color: #666;
      margin-bottom: 30px;
    }
    
    .explore-btn {
      padding: 12px 40px;
      background-color: #01685E;
      color: white;
      text-decoration: none;
      border-radius: 25px;
      font-size: 16px;
      font-weight: 500;
      transition: all 0.3s ease;
      
      &:hover {
        background-color: darken(#01685E, 10%);
        transform: translateY(-2px);
      }
    }
  }
}

// 動畫
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

// 響應式設計
@media (max-width: 768px) {
  .member-activities {
    padding: 15px;
    
    .page-header {
      margin-bottom: 25px;
      
      .page-title {
        font-size: 24px;
      }
    }
    
    .activities-table {
      .table-header {
        display: none;
      }
      
      .table-body {
        .table-row {
          grid-template-columns: 1fr;
          gap: 10px;
          padding: 20px;
          border-bottom: 2px solid #e0e0e0;
          
          .body-cell {
            padding: 8px 0;
            justify-content: flex-start;
            text-align: left;
            
            &:before {
              content: attr(data-label);
              font-weight: 600;
              width: 100px;
              margin-right: 15px;
              color: #666;
            }
            
            &:nth-child(1):before { content: '活動名稱：'; }
            &:nth-child(2):before { content: '報名時間：'; }
            &:nth-child(3):before { content: '出發日期：'; }
            &:nth-child(4) {
              justify-content: center;
              margin-top: 15px;
              
              &:before { content: ''; }
            }
            
            .cancel-btn {
              width: 100%;
              max-width: 200px;
            }
          }
        }
      }
    }
    
    .empty-state {
      padding: 60px 15px;
      
      .empty-icon {
        font-size: 48px;
      }
      
      .empty-message {
        font-size: 18px;
      }
    }
  }
}
</style>