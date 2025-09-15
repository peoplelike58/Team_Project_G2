 <template>
  <div class="member-profile">
    <!-- 頁面標題 -->
    <div class="page-header">
      <h1 class="page-title">個人資料</h1>
      <button class="edit-btn" @click="toggleEditMode">
        {{ isEditing ? '取消' : '編輯' }}
      </button>
    </div>

    <!-- 個人資料表單 -->
    <div class="profile-form">
      <div class="form-section">
        <!-- 頭像上傳區域 -->
        <div class="avatar-section">
          <label class="form-label">頭像</label>
          <div class="avatar-upload">
            <div class="avatar-preview">
              <img :src="profileData.avatar || '/images/Products/default-avatar.jpg'" alt="頭像" />
            </div>
            <button 
              v-if="isEditing" 
              class="upload-btn" 
              @click="handleAvatarUpload"
            >
              上傳檔案
            </button>
          </div>
        </div>

        <!-- 暱稱 -->
        <div class="form-group">
          <label class="form-label">暱稱</label>
          <input 
            v-model="profileData.nickname" 
            type="text" 
            class="form-input"
            :disabled="!isEditing"
          />
        </div>

        <!-- 關於我 -->
        <div class="form-group">
          <label class="form-label">關於我</label>
          <textarea 
            v-model="profileData.about" 
            class="form-textarea"
            :disabled="!isEditing"
            rows="4"
          ></textarea>
        </div>

        <!-- 生日 -->
        <div class="form-group">
          <label class="form-label">生日</label>
          <input 
            v-model="profileData.birthday" 
            type="date" 
            class="form-input"
            :disabled="!isEditing"
          />
        </div>

        <!-- 聯絡電話 -->
        <div class="form-group">
          <label class="form-label">聯絡電話</label>
          <input 
            v-model="profileData.phone" 
            type="tel" 
            class="form-input"
            :disabled="!isEditing"
          />
        </div>

        <!-- 聯絡地址 -->
        <div class="form-group">
          <label class="form-label">聯絡地址</label>
          <input 
            v-model="profileData.address" 
            type="text" 
            class="form-input"
            :disabled="!isEditing"
          />
        </div>
      </div>

      <!-- 儲存按鈕 -->
      <div v-if="isEditing" class="form-actions">
        <button class="save-btn" @click="saveProfile">儲存異更</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'

// 編輯模式狀態
const isEditing = ref(false)

// 個人資料數據
const profileData = reactive({
  avatar: '',
  nickname: '',
  about: '',
  birthday: '2025/08/03',
  phone: '',
  address: ''
})

// 切換編輯模式
const toggleEditMode = () => {
  isEditing.value = !isEditing.value
}

// 處理頭像上傳
const handleAvatarUpload = () => {
  // 創建文件輸入元素
  const input = document.createElement('input')
  input.type = 'file'
  input.accept = 'image/*'
  
  input.onchange = (event) => {
    const file = event.target.files[0]
    if (file) {
      // 這裡可以加入圖片上傳邏輯
      const reader = new FileReader()
      reader.onload = (e) => {
        profileData.avatar = e.target.result
      }
      reader.readAsDataURL(file)
    }
  }
  
  input.click()
}

// 儲存個人資料
const saveProfile = async () => {
  try {
    // API 呼叫儲存資料
    console.log('儲存個人資料:', profileData)
    
    // 模擬 API 請求
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    isEditing.value = false
    alert('個人資料更新成功！')
  } catch (error) {
    console.error('儲存失敗:', error)
    alert('儲存失敗，請重試')
  }
}

onMounted(() => {
  // 載入個人資料的API呼叫
})
</script>

<style lang="scss" scoped>
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';

.member-profile {
  .page-header {
    @include flexcenter(0, row);
    justify-content: space-between;
    margin-bottom: 40px;
    
    .page-title {
      font-size: $pcFont-H2;
      font-weight: $semiBold;
      color: $black-14;
    }
    
    .edit-btn {
      @include btn(8px);
      padding: 8px 16px;
      background-color: $ivory-gray-100;
      color: $black-14;
      font-size: $pcFont-label;
      font-weight: $regular;
      @include border($ash-olive-400);
      transition: all 0.3s ease;
      
      &:hover {
        background-color: $ash-olive-400;
        color: white;
      }
    }
  }
  
  .profile-form {
    background: white;
    border-radius: 12px;
    padding: 40px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    
    @media (max-width: 768px) {
      padding: 24px;
    }
    
    .form-section {
      .avatar-section {
        margin-bottom: 32px;
        
        .form-label {
          display: block;
          font-size: $pcFont-p-s;
          font-weight: $medium;
          color: $black-14;
          margin-bottom: 12px;
        }
        
        .avatar-upload {
          @include flexcenter(16px, row);
          align-items: flex-start;
          
          .avatar-preview {
            @include product_card_img(100px, 100px, 50%);
            & > img{
              height: 100%;
            }
            flex-shrink: 0;
            background-color: $bg-gray;
          }
          
          .upload-btn {
            @include btn(8px);
            padding: 10px 20px;
            background-color: $bg-gray;
            color: $black-14;
            font-size: $pcFont-label;
            @include border($ash-olive-400);
            transition: background-color 0.3s ease;
            
            &:hover {
              background-color: $ash-olive-400;
              color: white;
            }
          }
        }
      }
      
      .form-group {
        margin-bottom: 24px;
        .form-label {
          display: block;
          font-size: $pcFont-p-s;
          font-weight: $medium;
          color: $black-14;
          margin-bottom: 8px;
        }
        
        :deep(.form-input) {
          width: 100%;
          padding: 12px 16px;
          font-size: $pcFont-p-s;
          color: $black-14;
          background: white;
          @include border($ash-olive-400);
          border-radius: 6px;
          transition: border-color 0.3s ease;
          
          &:focus {
            outline: none;
            border-color: $tag;
          }
          
          &:disabled {
            background-color: $bg-gray;
            color: $ash-olive-400;
            cursor: not-allowed;
          }
        }
        
        .form-textarea {
          width: 100%;
          padding: 12px 16px;
          font-size: $pcFont-p-s;
          color: $black-14;
          background: white;
          @include border($ash-olive-400);
          border-radius: 6px;
          resize: vertical;
          min-height: 100px;
          font-family: inherit;
          line-height: $lineHeight-p-150;
          transition: border-color 0.3s ease;
          box-sizing: border-box;
          &:focus {
            outline: none;
            border-color: $tag;
          }
          
          &:disabled {
            background-color: $bg-gray;
            color: $ash-olive-400;
            cursor: not-allowed;
          }
        }
      }
    }
    
    .form-actions {
      @include flexcenter(0, row);
      justify-content: flex-end;
      margin-top: 32px;
      padding-top: 24px;
      
      .save-btn {
        @include btn(8px);
        padding: 12px 24px;
        background-color: $tag;
        color: white;
        font-size: $pcFont-label;
        font-weight: $medium;
        transition: background-color 0.3s ease;
        
        &:hover {
          background-color: darken($tag, 10%);
        }
      }
    }
  }
}

:deep(.form-input){
  box-sizing: border-box;
}

</style>