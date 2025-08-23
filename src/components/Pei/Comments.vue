<script setup>
import { ref } from 'vue'

const messages = ref([
  {
    id: 'cmt-1001',
    name: '今晚上山',
    avatarUrl: '../../../public/images/myChallenge/head1.png',
    time: '15分鐘前',
    message: '今天天氣很好，非常適合爬山！',
    photoUrl: '../../../public/img/trails/1.jpg',
    canDelete: true
  },
  {
    id: 'cmt-1002',
    name: '明晚下山',
    avatarUrl: '../../../public/images/myChallenge/head2.png',
    time: '14小時前',
    message: '爬到腿軟了.....再也不敢去了==',
    photoUrl: '',
    canDelete: false
  },
  {
    id: 'cmt-1003',
    name: '久久爬一次山',
    avatarUrl: '../../../public/images/myChallenge/head3.png',
    time: '3天前',
    message: '差點餓倒在山上，還好路過的阿姨分我吃他的饅頭，又平安度過了一天！^0^',
    photoUrl: '../../../public/img/trails/2.jpg',
    canDelete: false
  }
])

// 撰寫彈窗邏輯
const showPopup = ref(false)
const newMessageText = ref('')
const newPhotoFile = ref(null)
const newPhotoPreview = ref('')

// 撰寫留言按鈕彈窗
function openPopup() {
  showPopup.value = true
}
function closePopup() {
  showPopup.value = false
  newMessageText.value = ''
  newPhotoFile.value = null
  newPhotoPreview.value = ''
}

// 新增照片功能
function handleImageUpload(event) {
  const file = event.target.files[0]
  if (file) {
    newPhotoFile.value = file
    newPhotoPreview.value = URL.createObjectURL(file)
  }
}

// 送出留言功能 (假資料)
function submitComment() {
  if (!newMessageText.value.trim()) return
  const newComment = {
    id: 'cmt-' + Date.now(),
    name: '會員ID',
    avatarUrl: '../../../public/images/myChallenge/head4.png',
    time: '剛剛',
    message: newMessageText.value,
    photoUrl: newPhotoPreview.value || '',
    canDelete: true
  }
  messages.value.unshift(newComment)
  closePopup()
}

// 照片放大檢視
const isImageViewerVisible = ref(false)
const imageViewerUrl = ref('')

function openImageViewer(photoUrl) {
  imageViewerUrl.value = photoUrl
  isImageViewerVisible.value = true
}
function closeImageViewer() {
  isImageViewerVisible.value = false
  imageViewerUrl.value = ''
}

// 會員刪除留言功能
function deleteMessageById(messageId) {
  // 這裡先加個保護：確認後再刪
  const isConfirmed = window.confirm('確定要刪除這則留言嗎？')
  if (!isConfirmed) return

  const targetIndex = messages.value.findIndex(item => item.id === messageId)
  if (targetIndex !== -1) {
    messages.value.splice(targetIndex, 1)
  }
}
</script>

<template>
  <div class="comments">
    <h1>留言板</h1>
    <span class="h1Tag">Comments</span>

    <button class="writeBtn" @click="openPopup">撰寫評論</button>
    <span class="noRude">
      <img src="../../../public/img/icons/alert.svg" alt="警示icon" />
      禁止輸入不雅字眼
    </span>

    <ul class="commentList">
      <li class="noComment" v-if="messages.length === 0">
        還沒有人留言喔～～～快來成為第一個留下足跡的人吧！！！
      </li>

      <li v-for="message in messages" :key="message.id" class="commentCard">
        <div class="member">
          <div class="avatar">
            <img :src="message.avatarUrl" alt="使用者頭像" />
          </div>
          <p class="name">{{ message.name }}</p>
        </div>

        <span class="time">{{ message.time }}</span>

        <p class="message">{{ message.message }}</p>

        <div class="listBottom">
          <div class="photo" v-if="message.photoUrl !== ''">
            <!-- 點圖放大 -->
            <img
              :src="message.photoUrl"
              alt="上傳的照片"
              @click="openImageViewer(message.photoUrl)"
            />
          </div>

          <button
            class="trashBtn"
            v-if="message.canDelete"
            @click="deleteMessageById(message.id)"
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

      <h2 class="popupTitle">大霸尖山</h2>

      <div class="popupUser">
        <div class="popupAvatar">
          <img src="../../../public/images/myChallenge/head4.png" alt="使用者頭像" />
        </div>
        <p class="popupName">會員ID</p>
      </div>

      <textarea
        class="popupTextarea"
        v-model="newMessageText"
        rows="5"
        placeholder="想說些什麼呢？"
      ></textarea>

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

  <!-- ① 照片放大檢視（所有使用者可用） -->
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
@import '../../assets/styles/main.scss';

.comments {
  width: 100%;
  max-width: 1200px;
  margin: 48px auto;
  display: flex;
  flex-direction: column;
  align-items: center;

  h1 {
    font-size: $pcFont-H1-m;
    font-weight: $semiBold;
    line-height: $lineHeight-title-120;
    letter-spacing: 2px;
    margin-right: auto;
  }

  .h1Tag {
    margin-right: auto;
    font-size: $pcFont-H4;
    font-weight: $semiBold;
    line-height: $lineHeight-title-120;
    letter-spacing: 1.5px;
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

    .noComment {
      text-align: center;
      font-size: $pcFont-H3;
      font-weight: $medium;
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

        .name {
        }
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
            width: 100%;
            height: 100%;
            border-radius: 8px;
            object-fit: cover;
            object-position: center;
            display: block;
            cursor: zoom-in; /* 提示可放大 */
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
  z-index: 999;

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
    gap: 20px;

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
      width: 95%;
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
    }

    .hasphotoUploadBtn {
      background-color: $tag;
      color: white;
    }

    .previewBox {
      position: relative;
      width: 150px;
      height: 150px;

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

      &:hover {
        background-color: rgba(186, 186, 171, 0.6);
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
