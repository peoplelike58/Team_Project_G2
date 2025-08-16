<template>
  <div class="modal-overlay" v-if="showModal" @click="closeModal">
    <div class="modal-container" @click.stop>
      <!-- 關閉按鈕 -->
      <button class="close-btn" @click="closeModal">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
          <path d="M15 5L5 15M5 5L15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
      </button>

      <!-- 恭喜圖標和橫幅 -->
      <div class="congratulations-section">
        <div class="congratulations_pic"><img src="" alt=""></div>
          <div class="congratulations-text">Congratulations!</div>
      </div>

      <!-- 主要內容 -->
      <div class="content-section">
        <h2 class="main-title">恭喜獲得精選【註冊禮】</h2>
        <p class="description">
          稍後您可以再「會員專區 > 我的優惠」看到優惠內容
        </p>
        
        <button class="action-btn" @click="viewCoupons">
          立即查看
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

// 響應式數據
const showModal = ref(false)

// 方法
const closeModal = () => {
  showModal.value = false
}

const viewCoupons = () => {
  // 這裡可以添加跳轉到優惠券頁面的邏輯
  console.log('前往查看優惠券')
  // 例如：router.push('/member/coupons')
  closeModal()
}

// 生命週期
onMounted(() => {
  // 組件載入時顯示彈窗
  showModal.value = true
})

// 暴露方法給父組件使用（可選）
defineExpose({
  showModal: () => showModal.value = true,
  hideModal: () => showModal.value = false
})
</script>

<style scoped>
/* 彈窗遮罩 */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

/* 彈窗容器 */
.modal-container {
  position: relative;
  background: white;
  border-radius: 16px;
  padding: 40px 30px 30px;
  max-width: 400px;
  width: 90%;
  text-align: center;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

/* 關閉按鈕 */
.close-btn {
  position: absolute;
  top: 15px;
  right: 15px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 5px;
  border-radius: 50%;
  transition: background-color 0.2s;
  color: #666;
}

.close-btn:hover {
  background-color: #f5f5f5;
}

/* 恭喜區域 */
.congratulations-section {
  margin-bottom: 30px;
}

.banner-wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
}

.banner-icon {
  margin-bottom: 5px;
}

.congratulations-text {
  font-family: 'Arial', sans-serif;
  font-style: italic;
  font-weight: bold;
  color: #333;
  font-size: 14px;
}

/* 內容區域 */
.content-section {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.main-title {
  font-size: 20px;
  font-weight: bold;
  color: #333;
  margin: 0;
  line-height: 1.4;
}

.description {
  font-size: 14px;
  color: #666;
  margin: 0;
  line-height: 1.6;
}

/* 按鈕樣式 */
.action-btn {
  background: #333;
  color: white;
  border: none;
  padding: 12px 40px;
  font-size: 16px;
  font-weight: 500;
  cursor: pointer;
  border-radius: 0;
  transition: all 0.2s;
  border-bottom: 2px solid #333;
  margin-top: 10px;
}

.action-btn:hover {
  background: #555;
  border-bottom-color: #555;
}

.action-btn:active {
  transform: translateY(1px);
}

/* 響應式設計 */
@media (max-width: 480px) {
  .modal-container {
    margin: 20px;
    padding: 30px 25px 25px;
  }
  
  .main-title {
    font-size: 18px;
  }
  
  .action-btn {
    padding: 10px 35px;
    font-size: 15px;
  }
}
</style>