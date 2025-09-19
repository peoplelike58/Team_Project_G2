<script setup>
import { ref, defineProps, onMounted, watch, computed } from 'vue'
import axios from 'axios'
import { useRouter } from "vue-router"
import { useUserStore } from "@/stores/user";


// ===== 路徑工具（保留你的寫法） =====
const baseUrl = import.meta.env.BASE_URL
const toUrl = (p) => {
  if (!p) return ''
  const s = String(p).trim()
  if (/^(?:https?:)?\/\//i.test(s) || s.startsWith('data:') || s.startsWith('/assets/')) return s
  return `${baseUrl}${encodeURI(s.replace(/^\/+/, ''))}`
}

// ===== 父層傳入 MOUNTAIN_ID、MOUNTAIN_NAME =====
const props = defineProps({
  id: { type: Number, required: true },
  mountainName: { type: String }
})

// ===== 路由與使用者狀態 =====
const router = useRouter()
const user = useUserStore() // 含 (isLoggedIn/name/email）

// ===== UI 狀態 =====
const messages = ref([])
const msgMaxLen = 500
const newMessageText = ref('')
const countMsgLen = computed(() => Array.from(newMessageText.value).length)
const noOverMaxLen = (e) => {
  const chars = Array.from(e.target.value)
  if (chars.length > msgMaxLen){
    newMessageText.value = chars.slice(0, msgMaxLen).join('')
  }else{
    newMessageText.value = e.target.value
  }
}

const showPopup = ref(false)
const newPhotoFile = ref(null)
const newPhotoPreview = ref('')

const isImageViewerVisible = ref(false)
const imageViewerUrl = ref('')



// 只打你自己的 PHP 根路徑，例如 http://localhost/TeamProject/public/PHP
const API_BASE = import.meta.env.VITE_AJAX_URL

// 不帶 Cookie（公開用）
const apiPublic = axios.create({
  baseURL: API_BASE,
  withCredentials: false,
})

// 需要 Cookie / Session（會員操作用）
const apiAuth = axios.create({
  baseURL: API_BASE,
  withCredentials: true,
})



// ===== 上傳檔案對外 URL 基底：把 /PHP 拿掉 → 變成 /public =====
const API_ROOT = import.meta.env.VITE_AJAX_URL.replace(/\/PHP\/?$/,'')
const UPLOADS_BASE = `${API_ROOT}/uploads`
// console.log(user.profile.avatar); // 印出會員avatar檔名

// ===== 從後端一列資料 → 轉成前端需要的物件 =====
function mapRowToMessage(row){
  // 後端可能用別名：MESSAGE_IMAGE / MEMBER_IMAGE
  const msgImageKey = row.MESSAGE_IMAGE ?? null
  const avatarKey   = row.MEMBER_IMAGE ?? null
 
  
  
  return {
    msgId:Number(row.MESSAGE_ID),       
    memId:Number(row.MEMBER_ID),         
    mountainId:Number(row.MOUNTAIN_ID),
    msgId: row.MESSAGE_ID,
    name: row.NICKNAME ?? row.MEMBER_NAME ?? `會員#${row.MEMBER_ID}`,
    mountain: row.MOUNTAIN_NAME || '',
    avatar: `${UPLOADS_BASE}/avatars/${avatarKey}` || 'images/myChallenge/head4.png',
    time: row.CREATED_AT || row.CREATE_AT || row.CREATE_TIME || '',
    content: row.CONTENT || '',
    photo: msgImageKey ? `${UPLOADS_BASE}/${msgImageKey}` : '',
    canDelete : user.isLoggedIn && user.id === row.MEMBER_ID
    
  }
}

// ===== 讀留言：GET /CommentsGet.php?MOUNTAIN_ID=... =====
async function fetchComments(){
  try{
    const resp = await apiPublic.get('/CommentsGet.php', {
      params: { MOUNTAIN_ID: props.id }
    })
    const body = resp.data

    // 讓兩種格式都能吃
    const rows = Array.isArray(body) ? body
               : (body && Array.isArray(body.data)) ? body.data
               : null

    if (!rows) {
      console.error('CommentsGet 回傳非預期：', body)
      throw new Error(body?.message || '取得留言失敗')
    }

    messages.value = rows.map(mapRowToMessage)
  }catch(err){
    console.error('fetchComments error:', err)
    messages.value = []
  }
}


// ===== 新增留言：POST /CommentsAdd.php（multipart） =====
async function submitComment(){
  const txt = (newMessageText?.value || '').trim()
  if (!txt) return

  const form = new FormData()
  form.append('MOUNTAIN_ID', String(props.id))
  form.append('CONTENT', txt)
  if (newPhotoFile.value) form.append('image', newPhotoFile.value)

  try{
    const { data } = await apiAuth.post('/CommentsAdd.php', form, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    if (data?.success) {
      // 有回單筆就塞進列表，否則重撈
      if (data.data) messages.value.unshift(mapRowToMessage(data.data))
      else await fetchComments()
      closePopup()
    } else {
      alert(data?.message || '留言失敗')
    }
  }catch(err){
    alert(err.message || '留言時發生錯誤')
  }
}

// ===== 刪除留言：POST /CommentsDelete.php =====

async function deleteMessageById(msgId){
  if (!msgId) return
  if (!confirm('確定要刪除此留言嗎？')) return

  try{
    const { data } = await apiAuth.post('/CommentsDelete.php', { MESSAGE_ID: msgId })
    if (data?.success){
      const i = messages.value.findIndex(m => m.msgId === msgId)
      if (i > -1) messages.value.splice(i, 1)
    }else{
      alert(data?.message || '刪除失敗')
    }
  }catch(err){
    const msg =
      err?.response?.data?.message ||
      (typeof err?.response?.data === 'string' ? err.response.data : '') ||
      err?.message || '刪除時發生錯誤'
    alert(msg)
  }
}


// ==== 點擊「撰寫評論」按鈕時，先用 pinia 檢查登入狀態，再決定導頁或彈窗 ====
async function checkLogin () {
  if (!user.isLoggedIn) {
    try { await user.hydrateFromSession() } catch {}
  }
  if (!user.isLoggedIn) {
    alert('請先登入會員唷！')
    router.push('/loginregister')
    return
  }
  openPopup()
}



// ===== UI：彈窗 / 圖片上傳 / 圖片放大 =====
function openPopup(){ 
  showPopup.value = true 
}

function closePopup(){
  showPopup.value = false
  newMessageText.value = ''
  newPhotoFile.value = null
  newPhotoPreview.value = ''
}

function handleImageUpload(event){
  const file = event?.target?.files?.[0]
  if (!file) return
  newPhotoFile.value = file
  newPhotoPreview.value = URL.createObjectURL(file)
}

function openImageViewer(photoUrl){
  imageViewerUrl.value = photoUrl
  isImageViewerVisible.value = true
}

function closeImageViewer(){
  isImageViewerVisible.value = false
  imageViewerUrl.value = ''
}

// ===== 掛載／山別變動 → 從後端撈資料（取代 localStorage 版） =====
onMounted(
  fetchComments
)




watch(() => props.id, (n,o) => { if (n && n !== o) fetchComments() })
</script>

<template>
  <div class="comments">
    <h1>留言板</h1>
    <span class="h1Tag">Comments</span>

    <button class="writeBtn" @click="checkLogin">撰寫評論</button>
    <span class="noRude">
      <img src="../../../public/images/icon/alert.svg" alt="警示icon" />
      禁止輸入不雅字眼
    </span>

    <ul class="commentList">
      <li class="noComment" v-if="messages.length === 0">
        目前還沒有人留言喔～～～快來成為第一個留下足跡的人吧^_<;;
      </li>

      <li v-for="message in messages" :key="message.msgid" class="commentCard">
        <div class="member">
          <div class="avatar">
            <img :src="toUrl(message.avatar)" alt="使用者頭像" />
          </div>
          <p class="name">{{ message.name }}</p>
        </div>

        <span class="time">{{ message.time }}</span>

        <p class="message">{{ message.content }}</p>

        <div class="listBottom">
          <div class="photo" v-if="message.photo !== ''">
            <img
              :src="toUrl(message.photo)"
              alt="上傳的照片"
              @click="openImageViewer(message.photo)"
            />
          </div>

          <button
            class="trashBtn"
            v-if="message.canDelete"
            @click="deleteMessageById(message.msgId)"
            title="刪除留言"
            aria-label="刪除留言"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              width="24"
              height="24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.8"
              stroke-linecap="round"
              stroke-linejoin="round"
              role="img"
              aria-labelledby="trashTitle"
            >
              <path d="M3 6h18" />
              <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
              <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
              <path d="M10 11v6" />
              <path d="M14 11v6" />
            </svg>
            刪除留言
          </button>
        </div>
      </li>
    </ul>
  </div>

  <!-- 撰寫留言彈窗 -->
  <div class="popupMask" v-if="showPopup" @click.self="closePopup">
    <div class="showPopup">
      <button class="closeBtn" @click="closePopup">×</button>

      <h2 class="popupTitle">{{ props.mountainName }}</h2>

      <div class="popupUser">
        <div class="popupAvatar">
          <img :src="`${UPLOADS_BASE}/avatars/${user.profile.avatar}`" alt="使用者頭像" />
        </div>
        <p class="popupName">{{ user.profile.nickname ?? user.name ?? `會員#${user.id}` }}</p>
      </div>

      <textarea
        class="popupTextarea"
        v-model="newMessageText"
        @input="noOverMaxLen"
        rows="5"
        placeholder="想說些什麼呢？"
      ></textarea>

      <div class="maxWords">
        <span> {{ countMsgLen }} / {{ msgMaxLen }}</span>
      </div>

      <label
        class="photoUploadBtn"
        :class="{ hasphotoUploadBtn: newPhotoFile && newPhotoPreview }"
      >
        新增照片 ( {{ newPhotoFile && newPhotoPreview ? 1 : 0 }} / 1 )
        <input type="file" accept="image/*" hidden @change="handleImageUpload" />
      </label>

      <div class="previewBox" v-if="newPhotoPreview">
        <img :src="newPhotoPreview" alt="預覽圖片" />
        <button class="removePreviewBtn" @click="newPhotoPreview = ''">×</button>
      </div>

      <button class="submitCommentBtn" @click="submitComment">發布評論</button>
    </div>
  </div>

  <!--  照片放大檢視（所有使用者可用） -->
  <div
    class="imageViewerMask"
    v-if="isImageViewerVisible"
    @click.self="closeImageViewer"
  >
    <div class="imageViewerBox">
      <button class="imageViewerCloseBtn" @click="closeImageViewer">×</button>
      <img :src="imageViewerUrl" alt="放大檢視" />
    </div>
  </div>
</template>

<style lang="scss" scoped>
@import '@/assets/styles/main.scss';
@import '@/assets/styles/mixins';

/* 以下樣式保留你的原始版本 */
.comments {
  width: 100%;
  max-width: 1200px;
  margin: 48px auto;
  display: flex;
  flex-direction: column;
  align-items: center;
  box-sizing: border-box;
  @include m(){
    max-width:768px;
    padding: 0 20px;
  }

  h1 {
    width:100%;
    max-width: 1200px;
    font-size: $pcFont-H1-m;
    font-weight: $semiBold;
    line-height: $lineHeight-title-120;
    letter-spacing: 2px;
    margin-right: auto;
    padding: 0 30px;
      @include m(){
        max-width: 768px;
        padding: 0 ;
      }
  }

  .h1Tag {
    width:100%;
    max-width: 1200px;
    margin-right: auto;
    padding: 0 30px;
    font-size: $pcFont-H4;
    font-weight: $semiBold;
    line-height: $lineHeight-title-120;
    letter-spacing: 1.5px;
      @include m(){
        max-width: 768px;
        padding: 0 ;
      }
  }

  .writeBtn {
    font-size: $pcFont-p-s;
    width: 240px;
    height: 40px;
    margin: 40px 0 8px auto;
    padding: 10px;
    box-sizing: border-box;
    border-radius: 20px 16px 16px 0px;
    border: none;
    background-color: $tag;
    color: white;
    cursor: pointer;
    @include m(){
      width: 220px;
    }
  }

  .noRude {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 12px;
    margin-left: auto;
    margin-right: 50px;

    img {
      display: block;
      width: 16px;
      height: auto;
    }
  }

  .commentList {
    width: 95%;
    max-width: 1140px;
    margin-top: 40px;
    box-sizing: border-box;
    @include m(){
      width: 100%;
    }

    .noComment {
      text-align: center;
      font-size: $pcFont-H3;
      font-weight: $medium;
      line-height: 1.2;
      color: #ccc;
      margin: 50px auto 200px;
    }

    .commentCard {
      padding: 20px 0;
      box-sizing: border-box;
      display: flex;
      flex-direction: column;
      gap: 24px;
      border-bottom: 0.5px solid #999;
      letter-spacing: 1.5px;
      line-height: $lineHeight-p-150;

      &:last-of-type {
        border-bottom: none;
      }

      .member {
        display: flex;
        align-items: center;
        gap: 16px;

        .avatar {
          width: 80px;
          height: 80px;
          overflow: hidden;
          border-radius: 50%;

          img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
          }
        }

        .name { }
      }

      .time {
        font-size: 14px;
        color: #666;
      }

      .message {
        background-color: white;
        width: 100%;
        min-height: 130px;
        border-radius: 0px 40px 40px 45px;
        padding: 20px;
        box-sizing: border-box;

        white-space: pre-wrap;
        overflow-wrap: anywhere;
        word-break: break-word;
        max-width: 100%;
        min-width: 0;
      }

      .listBottom {
        display: flex;
        justify-content: space-between;

        .photo {
          width: 150px;
          height: 150px;
          overflow: hidden;

          img {
            width: 100% !important;
            height: 100% !important;
            border-radius: 8px;
            object-fit: cover;
            object-position: center;
            display: block;
            cursor: zoom-in;
          }
        }

        .trashBtn {
          color: #999;
          padding: 10px;
          display: flex;
          justify-content: center;
          align-items: center;
          height: 40px;
          margin-top: auto;
          margin-left: auto;
          cursor: pointer;
          background-color: transparent;
          border: none;

          &:hover {
            color: $tag;
          }
        }
      }
    }
  }
}

/* 撰寫彈窗 */
.popupMask {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;

  overflow-x: hidden;
  overflow-y: auto;

  @include m(){
    max-width: 768px;
  }

  .showPopup {
    background-color: $ivory-gray-100;
    width: 90%;
    max-width: 500px;
    height: 85%;
    max-height: 550px;
    padding: 24px;
    border-radius: 16px;
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 10px;

    @include m(){
      width: 350px
    }

    .closeBtn {
      margin-left: auto;
      font-size: 20px;
      background: transparent;
      border: none;
      border-radius: 50%;
      cursor: pointer;

      &:hover {
        background-color: rgba(255, 255, 255, 0.5);
      }
    }

    .popupTitle {
      font-size: $pcFont-H4;
      font-weight: $semiBold;
      text-align: center;
      letter-spacing: 1.5px;
    }

    .popupUser {
      display: flex;
      align-items: center;
      gap: 12px;

      .popupAvatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        overflow: hidden;
        object-fit: cover;

        img {
          width: 100%;
          height: 100%;
          display: block;
          object-fit: cover;
        }
      }

      .popupName {
        font-weight: $medium;
        letter-spacing: 1.5px;
      }
    }

    .popupTextarea {
      width: 100%;
      height: 100px;
      border-radius: 0px 40px 40px 40px;
      padding: 12px;
      border: 1px solid #ccc;
      height: 20%;
      resize: none;
      letter-spacing: 1.5px;
      font-size: $pcFont-p-s;
      line-height: $lineHeight-p-150;
    }

    .photoUploadBtn {
      align-self: center;
      border: 1.5px solid $tag;
      padding: 8px 24px;
      border-radius: 8px;
      cursor: pointer;
      box-sizing: border-box;
      font-size: 14px;
      width: 35%;
      text-align: center;

      @include m(){
        width: 200px;
      }
    }

    .hasphotoUploadBtn {
      background-color: $tag;
      color: white;
    }

    .previewBox {
      position: relative;
      width: 150px;
      height: 150px;
      object-fit: cover;


      img {
        width: 100%;
        height: 100%;
        border-radius: 8px;
        object-fit: cover;
      }

      .removePreviewBtn {
        position: absolute;
        top: 4px;
        right: 4px;
        background: rgba(255, 255, 255, 0.2);
        border: 0.5px solid #666;
        color: #666;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 14px;
        cursor: pointer;

        &:hover {
          background-color: rgba(255, 255, 255, 0.6);
          color: $tag;
          border: 0.5px solid $tag;
        }
      }
    }

    .submitCommentBtn {
      width: 35%;
      text-align: center;
      margin-top: auto;
      align-self: center;
      padding: 8px 24px;
      background-color: $ash-olive-400;
      border: none;
      border-radius: 8px;
      cursor: pointer;

      @include m(){
        width: 200px;
      }

      &:hover {
        background-color: rgba(186, 186, 171, 0.6);
      }
    }

    .maxWords{
      // outline: 1px solid red;
      display: flex;
      span{
      // outline: 1px solid blue;
      margin-left: auto;
      margin-right: 10px;
      color: #999;
      font-size: 12px;
      }
    }
  }
}

/* 照片放大檢視樣式 */
.imageViewerMask {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.7);
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;

  .imageViewerBox {
    position: relative;
    max-width: 90vw;
    max-height: 85vh;

    img {
      max-width: 90vw;
      max-height: 85vh;
      display: block;
      border-radius: 8px;
    }

    .imageViewerCloseBtn {
      position: absolute;
      top: -12px;
      right: -12px;
      width: 24px;
      height: 24px;
      border-radius: 50%;
      border: none;
      background: rgba(255, 255, 255, 0.5);
      cursor: pointer;
      font-size: 16px;
      line-height: 1;

      &:hover{
        background: rgba(255, 255, 255, 0.8);
      }
    }
  }
}
</style>
