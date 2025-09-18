<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useUserStore } from "@/stores/user";

// 留言資料
// const messages = ref([
//   {
//     id: 1,
//     routeName: '玉山新手友善',
//     content: '風景超美，新手爬也不會覺得太難',
//     updateTime: '2025/08/04'
//   },
//   {
//     id: 2,
//     routeName: '玉山新手友善',
//     content: '風景超美，新手爬也不會覺得太難',
//     updateTime: '2025/08/04'
//   },
//   {
//     id: 3,
//     routeName: '玉山新手友善',
//     content: '風景超美，新手爬也不會覺得太難',
//     updateTime: '2025/08/04'
//   },
//   {
//     id: 4,
//     routeName: '玉山新手友善',
//     content: '風景超美，新手爬也不會覺得太難',
//     updateTime: '2025/08/04'
//   }
// ])

const user = useUserStore()

// 只打你自己的 PHP 根路徑，例如 http://localhost/TeamProject/public/PHP
const API_BASE = import.meta.env.VITE_AJAX_URL

// 需要 Cookie / Session（會員操作用）
const apiAuth = axios.create({
  baseURL: API_BASE,
  withCredentials: true,
})

const messages = ref([])

// ==== 將後端回傳欄位對應到前端 ====
function phpToVue(row){
  const mountainId = row.MOUNTAIN_ID
  const msgId = row.MESSAGE_ID
  const mountain = row.MOUNTAIN_NAME
  const content = row.CONTENT
  const craateTime = row.CREATED_AT
  // 時間轉成 年/月/日 顯示
  const date = (craateTime || '').slice(0,10).replace(/-/g,'/')
  
  return{ msgId, mountain, content, date }

}

// ==== 讀留言,從 session 判斷 member id ====
async function fetchComments(){

  try{
    const resp = await apiAuth.post('/CommentsMember.php',{})
    const data = resp.data || {}
    if(!data.success){
      console.log(data.message || '讀取留言失敗');   
    }
    const rows = Array.isArray(data.data) ? data.data : []             // 取得資料陣列                                                // 取 rows
    messages.value = rows.map(phpToVue)      
  }catch(err){
    console.error('fetchComments error:', err)
    messages.value = []
  }
}






// 刪除留言
const deleteMessage = (messageId) => {
  // 刪除留言功能
  if (confirm('確定要刪除留言嗎？')) {
    messages.value = messages.value.filter(message => message.id !== messageId)
  }
}

onMounted(() => {
  // 載入留言資料的API呼叫
  
  fetchComments()

})
</script>

<template>
    <div class="member-messages">
    <!-- 頁面標題 -->
    <div class="page-header">
      <h1 class="page-title">我的留言</h1>
    </div>

    <!-- 留言列表表格 -->
    <div class="messages-table">
      <div class="table-header">
        <div class="header-cell">留言路線</div>
        <div class="header-cell">留言內容</div>
        <div class="header-cell">留言時間</div>
      </div>
      
      <div class="table-body">
        <div 
          v-if="messages.length > 0"
          v-for="message in messages" 
          :key="message.msId"
          class="table-row"
        >
          <div class="body-cell route-name">
            <router-link :to="`/routes/${message.mountainId}`" >{{ message.mountain }}</router-link>  
           山的ID: {{ message.mountainId }}
            
          </div>
          <div class="body-cell message-content">
            <div class="content-text">{{ message.content }}</div>
            <button class="delete-btn" @click="deleteMessage(message.msgId)">
              <i class="edit-icon"><svg  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024"><path fill="currentColor" d="M160 256H96a32 32 0 0 1 0-64h256V95.936a32 32 0 0 1 32-32h256a32 32 0 0 1 32 32V192h256a32 32 0 1 1 0 64h-64v672a32 32 0 0 1-32 32H192a32 32 0 0 1-32-32zm448-64v-64H416v64zM224 896h576V256H224zm192-128a32 32 0 0 1-32-32V416a32 32 0 0 1 64 0v320a32 32 0 0 1-32 32m192 0a32 32 0 0 1-32-32V416a32 32 0 0 1 64 0v320a32 32 0 0 1-32 32"></path></svg></i>
            </button>
          </div>
          <div class="body-cell">{{ message.date }}</div>
        </div>
      </div>
    </div>

    <!-- 空狀態 -->
    <div v-if="messages.length === 0 " class="empty-state">
      <p class="empty-message">目前沒有任何留言</p>
    </div>
  </div>
</template>

<style scoped lang="scss">
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';

.member-messages {
  .page-header {
    margin-bottom: 40px;
    
    .page-title {
      font-size: $pcFont-H2;
      font-weight: $semiBold;
      color: $black-14;
    }
  }
  
  .messages-table {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    
    .table-header {
      display: grid;
      grid-template-columns: 1fr 2fr 1fr;
      background: $ivory-gray-100;
      
      @media (max-width: 768px) {
        grid-template-columns: 1fr;
      }
      
      .header-cell {
        padding: 20px 24px;
        font-size: $pcFont-p-s;
        font-weight: $semiBold;
        color: $black-14;
        text-align: center;
        
        &:first-child {
          text-align: left;
        }
        
        @media (max-width: 768px) {
          display: none;
          
          &:first-child {
            display: block;
            text-align: center;
          }
        }
      }
    }
    
    .table-body {
      .table-row {
        display: grid;
        grid-template-columns: 1fr 2fr 1fr;
        @include border($ash-olive-400);
        border-left: none;
        border-right: none;
        border-bottom: none;
        
        &:last-child {
          border-bottom: 1px solid $ash-olive-400;
        }
        
        @media (max-width: 768px) {
          grid-template-columns: 1fr;
          gap: 8px;
          padding: 16px;
        }
        
        .body-cell {
          padding: 20px 24px;
          font-size: $pcFont-p-s;
          color: $black-14;
          @include flexcenter(0, row);
          align-items: center;
          text-align: center;
          
          &.route-name {
            justify-content: flex-start;
            text-align: left;
          }
          
          &.message-content {
            @include flexcenter(12px, row);
            justify-content: space-between;
            
            .content-text {
              flex: 1;
              text-align: left;
              line-height: $lineHeight-p-150;
            }
            
            .delete-btn {
              @include btn(4px);
              width: 32px;
              height: 32px;
              background-color: $bg-gray;
              @include flexcenter(0, row);
              flex-shrink: 0;
              transition: background-color 0.3s ease;
              @include border($ash-olive-400);
              
              &:hover {
                background-color: $ash-olive-400;
              }
              
              .edit-icon {
                width: 16px;
                height: 16px;
                background: url('/path/to/edit-icon.svg') no-repeat center;
                background-size: contain;
              }
            }
          }
          
          @media (max-width: 768px) {
            padding: 4px 0;
            justify-content: flex-start;
            text-align: left;
            
            &:before {
              content: attr(data-label);
              font-weight: $semiBold;
              width: 100px;
              margin-right: 10px;
            }
            
            &.message-content {
              flex-direction: column;
              align-items: flex-start;
              gap: 8px;
              
              .content-text {
                width: 100%;
              }
              
              .delete-btn {
                align-self: flex-end;
              }
            }
          }
        }
      }
    }
  }
  
  .empty-state {
    @include flexcenter(0, column);
    padding: 60px 20px;
    text-align: center;
    
    .empty-message {
      font-size: $pcFont-p-m;
      color: $ash-olive-400;
    }
  }
}

// Mobile 專用樣式
@media (max-width: 768px) {
  .messages-table {
    .table-body {
      .table-row {
        .body-cell {
          &:nth-child(1):before { content: '留言路線：'; }
          &:nth-child(2):before { content: '內容：'; }
          &:nth-child(3):before { content: '更新時間：'; }
        }
      }
    }
  }
}
</style>