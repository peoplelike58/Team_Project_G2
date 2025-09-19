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
              <!-- <img :src="profileData.tempAvatarPreview || user.profile.avatarUrl || `${BASE}images/Products/default-avatar.jpg`" alt="頭像" /> -->
              <img :src="profileData.tempAvatarPreview || (user.profile.avatarUrl? `${BASE}uploads/avatars/${user.profile.avatar}`:`${BASE}uploads/avatars/default-avatar.jpg`)" alt="頭像" />
            </div>
            <div v-if="user.loading.uploadingAvatar" class="upload-loading">
              上傳中...
            </div>
            <button 
              v-if="isEditing" 
              class="upload-btn" 
              @click="handleAvatarUpload"
              :disabled="user.loading.uploadingAvatar"
            >
              {{ user.loading.uploadingAvatar ? '上傳中...' : '上傳檔案' }}
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
            :disabled="!isEditing " 
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
        <button class="save-btn" @click="saveProfile">儲存變更</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useUserStore } from '@/stores/user'

const BASE = import.meta.env.BASE_URL;
const user = useUserStore()
// 編輯模式狀態
const isEditing = ref(false)
// const canEdit = ref(false)

// 個人資料數據
const profileData = reactive({
  avatar: '',
  nickname: '',
  about: '',
  birthday: '',
  phone: '',
  address: '',
  // 新增：暫存的頭像檔案和預覽URL
  tempAvatarFile: null,      // 暫存檔案物件
  tempAvatarPreview: ''      // 暫存預覽URL
})

// 切換編輯模式
const toggleEditMode = () => {
  if (isEditing.value) {
    // 如果正在編輯中，取消時清除暫存資料
    profileData.tempAvatarFile = null
    profileData.tempAvatarPreview = ''
    // 可選：重新載入原始資料
    getProfile()
  }
  isEditing.value = !isEditing.value
}
// 處理頭像上傳（不立刻上傳）
const handleAvatarUpload = () => {
  // 創建文件輸入元素
  const input = document.createElement('input')       //用 DOM（文件物件模型）動態建立一個 <input> HTML 元素。
  input.type = 'file'                                 //把這個 <input> 的型別設成 file，讓使用者可以選檔案。
  input.accept = 'image/*'                            //限制可選檔案的 MIME 類型為圖片（image/* 表示各種圖片格式都可）
  
  input.onchange = async (event) => {                       //當使用者選擇檔案後會觸發 change 事件；這裡註冊事件處理器，參數 event 裝著事件資訊。
    const file = event.target.files[0]                //從事件來源的檔案清單（files 是一個 FileList）抓第一個檔案物件（File）。
    if (file) {
      // 暫存檔案
      profileData.tempAvatarFile = file
      // 產生預覽URL
      const reader = new FileReader()                  //建立 FileReader（瀏覽器內建的檔案讀取器，用來把本機選到的檔案讀成可用的資料）
      reader.onload = (e) => {                         //當 FileReader 讀取完成會觸發 load 事件；這裡註冊完成後要做的事。
        profileData.tempAvatarPreview  = e.target.result           //把讀到的結果（通常是 Data URL（資料網址，如 data:image/png;base64,...））指定給 profileData.avatar，前端就能立刻顯示預覽。 
      }
      reader.readAsDataURL(file)                       // 將檔案轉為 base64 格式預覽,叫 FileReader 以 Data URL 的形式把檔案讀進來（適合做圖片預覽）。

      await uploadAvatar(file)
    }
  }
  
  input.click()                                        //程式主動觸發這個隱形的 <input type="file"> 的點擊，跳出系統檔案選擇視窗。
}

// 上傳頭像到伺服器的函式
const uploadAvatar = async (file) => {
  // 檢查是否已登入
  if (!user.isLoggedIn || !user.id) {
    alert('請先登入')
    return
  }

  // 設定上傳中狀態
  user.loading.uploadingAvatar = true

   try {
    // 建立 FormData 物件來傳送檔案
    const formData = new FormData()
    formData.append('avatar', file)           // 添加檔案
    formData.append('member_id', user.id)     // 添加會員 ID

    // 發送上傳請求
    const response = await fetch(import.meta.env.VITE_AJAX_URL + '/LoginPage_uploadAvatar.php', {
      method: 'POST',
      credentials: 'include',    // 包含 cookie
      body: formData            // 不設定 Content-Type，讓瀏覽器自動設定
    })

    const result = await response.json()

    if (result.success) {
      // 上傳成功後，更新資料庫中的頭像檔名
      await updateAvatarInDatabase(result.data.filename)
      console.log(result.data.filename)
      
      // 更新 Pinia store 中的頭像狀態
      user.updateAvatar(result.data.filename)
      
      alert('頭像上傳成功！')
    } else {
      throw new Error(result.message || '上傳失敗')
    }
  } catch (error) {
    console.error('頭像上傳失敗:', error)
    alert('頭像上傳失敗：' + error.message)
  } finally {
    // 結束上傳狀態
    user.loading.uploadingAvatar = false
  }

}

// 更新資料庫中的頭像檔名
const updateAvatarInDatabase = async (filename) => {
  try {
    const response = await fetch(import.meta.env.VITE_AJAX_URL + '/LoginPage_updateAvatarDB.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({ 
        member_id: user.id,
        avatar_filename: filename 
      })
    })

    const result = await response.json()
    if (!result.success) {
      throw new Error(result.message || '更新資料庫失敗')
    }
  } catch (error) {
    console.error('更新資料庫頭像失敗:', error)
    throw error  // 重新拋出錯誤，讓上層處理
  }
}

// 儲存個人資料
const saveProfile = async () => {
  console.log('準備儲存的資料:', profileData) // 調試用

  // 處理生日空值
  if (profileData.birthday === '') {
    profileData.birthday = null
  }
  console.log('實際的 birthday 值:', profileData.birthday)
  console.log('birthday 的型別:', typeof profileData.birthday)
  
  try {
    // 先上傳頭像（如果有新的頭像檔案）
    if (profileData.tempAvatarFile) {
      await uploadAvatar(profileData.tempAvatarFile)
    }
    // API 呼叫儲存資料
    const res = await fetch (import.meta.env.VITE_AJAX_URL + '/LoginPage_updateProfile.php',{
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify(profileData)
    })
    // 檢查 HTTP 狀態
    if (!res.ok) {
      throw new Error(`HTTP 錯誤: ${res.status}`)
    }
    const data = await res.json()

    
    // 模擬 API 請求
    // await new Promise(resolve => setTimeout(resolve, 1000))
    if(data.success){
      console.log('儲存個人資料:', profileData)
            
      profileData.tempAvatarFile = null             // 清除暫存的頭像資料
      profileData.tempAvatarPreview = ''
      isEditing.value = false                       // 關閉編輯模式
      user.updateProfile(profileData)               // 更新 Pinia store 中的個人資料
      getProfile()
      alert('個人資料更新成功！')
    }else {
      // 處理伺服器回傳的錯誤訊息
      console.error('伺服器錯誤:', data.message)
      alert(data.message || '儲存失敗')
    }
  } catch (error) {
    console.error('儲存失敗:', error)
    alert('儲存失敗，請重試')
  }
}

// 取得個人資料 
const getProfile = async () => {
  console.log('開始取得個人資料...') // 調試用
  try {
    // API 呼叫儲存資料
    const res = await fetch (import.meta.env.VITE_AJAX_URL + '/LoginPage_getProfile.php',{
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({})
    })
    if (!res.ok) {
      throw new Error(`HTTP 錯誤: ${res.status}`)
    }
    
    const data = await res.json()
    
    if(data.success && data.profileData && data.profileData.length > 0){
      const profile = data.profileData[0]  // 取第一筆資料
       console.log('解析的個人資料:', profile) // 調試用
      // 將 PHP 回傳的欄位名稱對應到前端
      profileData.nickname = profile.NICKNAME || ''
      profileData.birthday = profile.BIRTHDAY || ''
      profileData.phone = profile.PHONE || ''
      profileData.address = profile.ADDRESS || ''
      profileData.about = profile.ABOUT_ME || ''  // PHP 是 ABOUTME
      profileData.avatar = profile.IMAGE || 'default-avatar.jpg'
      console.log('成功取得的儲存個人資料:', profileData)
       
      if (profile.IMAGE) {                       // 處理頭像資料
        profileData.avatar = profile.IMAGE
        // 更新 Pinia store 中的頭像狀態
        user.updateAvatar(profile.IMAGE)
      }
      isEditing.value = false
      console.log(user.profile.avatarUrl)
    }else {
      console.error('取得資料失敗:', data.message)
      alert(data.message || '無法取得個人資料')
    }
  } catch (error) {
    console.error('儲存失敗:', error)
    alert('儲存失敗，請重試')
  }}

onMounted(() => {
  console.log('元件已載入，開始取得個人資料') // 調試用
  // 載入個人資料的API呼叫
  user.profile.avata
  getProfile()
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