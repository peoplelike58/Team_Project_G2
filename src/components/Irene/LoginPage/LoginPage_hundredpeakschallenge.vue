<template>
  <div class="memberMessages">
    <!-- 頁面標題 -->
    <div class="pageHeader">
      <h1 class="pageTitle">我的挑戰</h1>
    </div>

    <!-- 挑戰記錄表格 -->
    <div class="messagesTable">
      <div class="tableHeader">
        <div class="headerName" data-label="山名：">山名</div>
        <div class="headerName" data-label="想法：">想法記錄</div>
        <div class="headerName" data-label="時間：">上傳時間</div>
        <div class="headerName" data-label="統計：">詳細資料</div>
        <div class="headerName" data-label="操作：">操作</div>
      </div>
      
      <div class="tableBody">
        <div 
          v-for="record in challengeRecords" 
          :key="record.id"
          class="tableRow"
        >
          <div class="bodyName routeName">
            {{ record.name }}
            <span v-if="record.isClimbed" class="summitBadge">🏔️ 登頂</span>
          </div>
          
          <div class="bodyName messageContent">
            <div v-if="!record.isEditing" class="contentText">
              {{ record.content || '無想法記錄' }}
            </div>
            <textarea 
              v-else 
              v-model="record.editContent"
              class="editTextarea"
              maxlength="500"
              @keydown.enter.ctrl="saveEdit(record)"
            ></textarea>
          </div>
          
          <div class="bodyName dateTime" data-label="時間：">
            <div class="datePart">{{ formatDate(record.date).date }}</div>
            <div class="timePart">{{ formatDate(record.date).time }}</div>
          </div>
          
          <div class="bodyName challengeStats">
            <div class="statItem">高度: {{ record.height }}m</div>
            <div class="statItem">距離: {{ record.kilo }}km</div>
            <div class="statItem">時間: {{ record.time }}hr</div>
          </div>
          
          <div class="bodyName actions">
            <button 
              v-if="!record.isEditing" 
              class="editBtn" 
              @click="startEdit(record)"
              title="編輯想法"
            >
              <i class="editIcon">✏️</i>
            </button>
            <div v-else class="editActions">
              <button class="saveBtn" @click="saveEdit(record)" title="儲存">💾</button>
              <button class="cancelBtn" @click="cancelEdit(record)" title="取消">❌</button>
            </div>
            
            <!-- <button 
              class="deleteBtn" 
              @click="deleteRecord(record.id)"
              title="刪除記錄"
            >
              <i class="deleteIcon">🗑️</i>
            </button> -->
          </div>
        </div>
      </div>
    </div>

    <!-- 空狀態 -->
    <div v-if="challengeRecords.length === 0 && !isLoading" class="emptyState">
      <p class="emptyMessage">目前沒有任何挑戰記錄</p>
      <p class="emptyHint">開始你的登山挑戰吧！</p>
    </div>

    <!-- 載入狀態 -->
    <div v-if="isLoading" class="loadingState">
      <p>載入中...</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const challengeRecords = ref([])
const isLoading = ref(false)

const HISTORY_API = `${import.meta.env.VITE_AJAX_URL}/mychallenge_history.php`
const EDIT_API = `${import.meta.env.VITE_AJAX_URL}/LoginPage_hundredpeakschallenge_edit.php`

const loadChallengeHistory = async () => {
  isLoading.value = true
  try {
    const response = await axios.get(HISTORY_API, {
      withCredentials: true
    })
    
    if (response.data.success) {
      challengeRecords.value = response.data.data.map(record => ({
        ...record,
        isClimbed: record.isClimbed == 1,
        isEditing: false,
        editContent: ''
      }))
    }
  } catch (error) {
    console.error('載入挑戰記錄失敗:', error)
    alert('載入失敗，請重新整理頁面')
  } finally {
    isLoading.value = false
  }
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  const dateStr = date.toLocaleDateString('zh-TW', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit'
  })
  const timeStr = date.toLocaleTimeString('zh-TW', {
    hour: '2-digit',
    minute: '2-digit',
    hour12: false
  })
  
  return  { date: dateStr, time: timeStr }
}

const startEdit = (record) => {
  record.isEditing = true
  record.editContent = record.content || ''
}

const cancelEdit = (record) => {
  record.isEditing = false
  record.editContent = ''
}

// 儲存
const saveEdit = async (record) => {
  try {
    const response = await axios.post(EDIT_API, {
      action: 'edit',
      id: record.id,
      content: record.editContent
    }, {
      withCredentials: true
    })
    
    if (response.data.success) {
      record.content = record.editContent
      record.isEditing = false
      alert('想法更新成功！')
    } else {
      alert('更新失敗：' + response.data.message)
    }
  } catch (error) {
    console.error('更新失敗:', error)
    alert('更新失敗，請稍後再試')
  }
}

// // 刪除
// const deleteRecord = async (recordId) => {
//   if (!confirm('確定要刪除這筆挑戰記錄嗎？此操作無法復原。')) {
//     return
//   }
  
//   try {
//     const response = await axios.delete(EDIT_API, {
//       data: { id: recordId },
//       withCredentials: true
//     })
    
//     if (response.data.success) {
//       challengeRecords.value = challengeRecords.value.filter(record => record.id !== recordId)
//       alert('記錄刪除成功！')
//     } else {
//       alert('刪除失敗：' + response.data.message)
//     }
//   } catch (error) {
//     console.error('刪除失敗:', error)
//     alert('刪除失敗，請稍後再試')
//   }
// }

// 初始化
onMounted(() => {
  loadChallengeHistory()
})
</script>

<style scoped lang="scss">
@import '@/assets/styles/main.scss';

.memberMessages {
  .pageHeader {
    margin-bottom: 40px;
    
    .pageTitle {
      font-size: $pcFont-H2;
      font-weight: $semiBold;
      color: $black-14;
    }
  }
  
  .messagesTable {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    
    .tableHeader {
      display: grid;
      grid-template-columns: 1.5fr 2fr 1fr 1.5fr 1fr;
      background: $ivory-gray-100;
      
      .headerName {
        padding: 20px 16px;
        font-size: $pcFont-p-s;
        font-weight: $semiBold;
        color: $black-14;
        text-align: center;
      }
    }
    
    .tableBody {
      .tableRow {
        display: grid;
        grid-template-columns: 1.5fr 2fr 1fr 1.5fr 1fr;
        border-bottom: 1px solid #eee;
        
        &:last-child {
          border-bottom: none;
        }
        
        .bodyName {
          padding: 20px 16px;
          font-size: $pcFont-p-s;
          color: $black-14;
          display: flex;
          align-items: center;
          justify-content: center;
          text-align: center;
          
          &.routeName {
            justify-content: flex-start;
            flex-direction: column;
            align-items: flex-start;
            
            .summitBadge {
              font-size: 12px;
              color: #10b981;
              font-weight: $semiBold;
              margin-top: 4px;
            }
          }
          
          &.messageContent {
            justify-content: flex-start;
            align-items: flex-start;
            
            .contentText {
              line-height: 1.5;
              text-align: left;
              word-break: break-word;
            }
            
            .editTextarea {
              width: 100%;
              min-height: 80px;
              padding: 8px;
              border: 1px solid #ddd;
              border-radius: 4px;
              resize: vertical;
            }
          }
          
          &.challengeStats {
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
            
            .statItem {
              font-size: 12px;
              color: #666;
            }
          }
          
          &.actions {
            flex-direction: row;
            gap: 8px;
            
            .editBtn, .deleteBtn, .saveBtn, .cancelBtn {
              background: none;
              border: none;
              cursor: pointer;
              padding: 4px;
              border-radius: 4px;
              transition: background-color 0.2s;
              
              &:hover {
                background-color: #f3f4f6;
              }
            }
            
            .editActions {
              display: flex;
              gap: 4px;
            }
          }

          &.dateTime {
            flex-direction: column;
            align-items: center;
            gap: 4px;
            
            .datePart, .timePart {
              font-size: $pcFont-p-s;
            }
          }
        }
      }
    }
  }
  
  .emptyState {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 60px 20px;
    text-align: center;
    
    .emptyMessage {
      font-size: $pcFont-p-m;
      color: $ash-olive-400;
      margin-bottom: 8px;
    }
    
    .emptyHint {
      font-size: $pcFont-p-s;
      color: #999;
    }
  }
  
  .loadingState {
    text-align: center;
    padding: 40px;
    color: #666;
  }
}

// 手機版樣式
@media (max-width: 812px) {
  .messagesTable {
    .tableHeader {
      display: block !important;
      background: $ivory-gray-100;
      text-align: center;
      padding: 16px 20px;
      border-radius: 8px 8px 0 0;
      
      .headerName {
        display: none; // 隱藏所有原始標題
        
        &:first-child {
          display: block;
          font-size: $pcFont-p-m;
          font-weight: $semiBold;
          color: $black-14;
          
          // 用偽元素替換內容
          &:before {
            content: '挑戰紀錄';
          }
          
          // 隱藏原本的文字
          font-size: 0;
          
          &:before {
            font-size: $pcFont-p-m;
          }
        }
      }
    }
    
    .tableBody {
      .tableRow {
        display: flex !important;
        flex-direction: column;
        gap: 0;
        padding: 24px 20px;
        border-bottom: 1px solid #BABAAB !important;
        margin-bottom: 0;
        border-radius: 0;
        
        .bodyName {
          display: block !important;
          margin-bottom: 16px;
          padding: 0 !important;
          justify-content: flex-start;
          align-items: flex-start;
          text-align: left !important;
          
          &:last-child {
            margin-bottom: 0;
          }
          
          &.routeName {
            &:before { 
              content: '山名'; 
              font-weight: $semiBold;
              font-size: 12px;
              color: #666;
              margin-bottom: 6px;
              display: block;
              text-transform: uppercase;
            }
            
            .summitBadge {
              margin-top: 6px;
              margin-left: 0;
              font-size: 11px;
              padding: 2px 6px;
              background: #e8f5e8;
              border-radius: 12px;
              display: inline-block;
            }
          }
          
          &.messageContent {
            &:before { 
              content: '想法記錄'; 
              font-weight: $semiBold;
              font-size: 12px;
              color: #666;
              margin-bottom: 6px;
              display: block;
              text-transform: uppercase;
            }
            
            .contentText, .editTextarea {
              width: 100%;
              line-height: 1.6;
              color: #333;
              display: block;
            }
          }
          
          &.dateTime {
            flex-direction: column;
            
            &:before { 
              content: '上傳時間'; 
              font-weight: $semiBold;
              font-size: 12px;
              color: #666;
              margin-bottom: 6px;
              display: block;
              text-transform: uppercase;
            }
            
            .datePart {
              display: block;
              margin-bottom: 2px;
            }
            
            .timePart {
              display: block;
              font-size: 13px;
            }
          }
          
          &.challengeStats {
            flex-direction: column;
            align-items: flex-start;
            
            &:before { 
              content: '詳細資料'; 
              font-weight: $semiBold;
              font-size: 12px;
              color: #666;
              margin-bottom: 6px;
              display: block;
              text-transform: uppercase;
            }
            
            .statItem {
              display: inline-block;
              background: #f8f9fa;
              padding: 4px 8px;
              border-radius: 4px;
              font-size: 12px;
              color: #555;
              border: 1px solid #e9ecef;
              margin-right: 8px;
              margin-bottom: 4px;
            }
          }
          
          &.actions {
            flex-direction: column;
            align-items: flex-start;
            
            &:before { 
              content: '操作'; 
              font-weight: $semiBold;
              font-size: 12px;
              color: #666;
              margin-bottom: 8px;
              display: block;
              text-transform: uppercase;
            }
            
            .editBtn, .saveBtn, .cancelBtn {
              background: #f8f9fa;
              border: 1px solid #dee2e6;
              padding: 6px 12px;
              border-radius: 4px;
              font-size: 13px;
              margin-right: 8px;
              display: inline-block;
              
              &:hover {
                background: #e9ecef;
              }
            }
            
            .editActions {
              display: inline-block;
            }
          }
        }
      }
    }
  }
}
</style>