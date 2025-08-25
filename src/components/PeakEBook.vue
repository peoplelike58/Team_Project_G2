<template>
    <div class="ebook-overlay" @click="$emit('close')">
      <div class="ebook-container" @click.stop>
        <button class="close-btn" @click="$emit('close')">✕</button>
  
        <div ref="book" class="book">
          <!-- EBook 封面 ＋ 標題 -->
          <div class="page cover-page"> 
            <div class="cover-wrap">
              <img class="cover-book" src="/images/EBook/EBookCover.png" alt="Cover of Taiwan Peak">
              <div class="cover-title">
                <h1>百岳之書：{{ mountain.name }}</h1>
                <p class="subtitle">The Mountain Book of {{ mountain.eName }}</p>
              </div>
            </div>
          </div>
  
          <!-- Page 2: 照片滿版(left) -->
          <div class="page spread-left">
            <img class="spread-photo-left" :src="mountain.fullImage" :alt="mountain.name" />
            <div class="slogan">
                <p>{{ mountain.slogan }}</p>
                <p>—— {{ mountain.name }}{{ mountain.subSlogan }}</p>
            </div>
          </div>

          <!-- Page 2: 照片滿版(right) -->
          <div class="page spread-right">
            <img class="spread-photo-right" :src="mountain.fullImage" :alt="mountain.name" />
          </div>

          <!-- Page3：左頁只有照片 -->
          <div class="page photo-only">
            <img :src="mountain.leftImage" :alt="mountain.name"  />
          </div>

          <!-- Page4：圖文 -->
          <div class="page photo-text" >
            <img class="icon" src="/images/icon/Ebook-icon.png" alt="Ebook_icon">
            <h2>{{ mountain.title }}</h2>
            <p>{{ mountain.content1 }}</p>
            <p class="title2">{{ mountain.title2 }}</p>
            <p>{{ mountain.content2 }}</p>
            <img class="photo-buttom" :src="mountain.rightImage" :alt="mountain.name" />
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, onUnmounted } from 'vue'
  import { PageFlip } from 'page-flip'
  
  const props = defineProps({
    mountain: { 
      type: Object, 
      required: true }
  })

  const emit = defineEmits(['close'])
  
  const book = ref(null)
  let flip = null
  
  
  onMounted(() => {
    if (!book.value) return
    
  // 初始化 PageFlip
  flip = new PageFlip(book.value, {
    width: 420,
    height: 600,
    showCover: true,
    maxShadowOpacity: 0.5,
    flippingTime: 900,
    usePortrait: false, //封面後雙頁展開
    mobileScrollSupport: false,
    showPageCorners: false,
  })
  
  // 從DOM載入頁面
  flip.loadFromHTML(book.value.querySelectorAll('.page'))
  
})

onUnmounted(() => { 
  if (flip) flip.destroy() 
})
</script>

<style scoped lang="scss">
    @import '@/assets/styles/mixins.scss';
    @import '@/assets/styles/main.scss';

*{
  box-sizing: border-box;
}
/* 外層 Modal */
.ebook-overlay{
  position:fixed; 
  inset:0; 
  background:#262626;
  display:flex; 
  align-items:center; 
  justify-content:center; 
  padding:20px; 
  z-index:2000;
  backdrop-filter: blur(8px);
}

.ebook-container{ 
  position:relative; 
  width:100%; 
  height:100%; 
  max-width:1100px; 
  max-height:720px; 
  display:flex; 
  align-items:center; 
  justify-content:center; 
}

.close-btn{
  position:absolute; 
  top:16px; 
  right:16px; 
  width:40px; 
  height:40px;
  border-radius:50%;
  border:none; 
  background:#EFF1F2; 
  cursor:pointer; 
  font-size:18px; 
  line-height:40px;
  transition: all 0.2s ease;
}

.close-btn:hover {
  background:#DBCBCB;
  transform: scale(1.1);
}

/* 書本初始尺寸 */
.book{ 
  width:840px; 
  height:600px; 
  overflow:hidden;
}

/* 共同頁面樣式 */
.page{
  background:#EFF1F2; 
  position:relative; 
  overflow:hidden;
}

/* 封面 */
.cover-page{
  color:#F2F2E9;
}

.cover-wrap{ 
  width:100%; 
  height:100%; 
  position:relative; 
  display:flex; 
  align-items:center; 
  justify-content:center; 
  img{
      position: absolute;
      width: 100%;
      height: 100%;
      object-fit: cover;
  }
}

.cover-title{ 
  position:absolute; 
  bottom:50px; 
  right:30px; 
  text-align: center;
  line-height: $lineHeight-title-120;
  h1{
      font-size: $pcFont-H4;
      font-weight: 600;
      text-align: right;
  }
  p{
      font-size: 10px;
  }
}


/* 滿版照片頁 */
.spread-left, .spread-right{
  position: relative;
  overflow: hidden;
  color:#F2F2E9;
  font-size: $pcFont-H4;
  img{
    width: 200%;
    height: 100%;
    object-fit: cover;
  }
  .slogan{
    position: absolute;
    left: 40px;
    bottom: 40px;
  }
  p{
    line-height: $lineHeight-p-150;
  }
}
.spread-photo-left{
  object-position: left center;
}
.spread-photo-right{
  object-position: right center;
  margin-left: -86%;
}


.photo-only{
  overflow: hidden;
  img{
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}

.photo-text{
  // color: #141414;
  text-align: center;
  padding: 24px;
  .icon{
    padding-top: 8px;
    width: 40px;
  }
  h2{
    line-height: $lineHeight-p-200;
    font-weight: 600;
    padding-top: 12px;
  }
  p{
    text-align: left;
    line-height: $lineHeight-p-150;
  }
  .title2{
    padding-top: 12px;
    padding-bottom: 8px;
    font-weight: 500;
  }
  .photo-buttom{
    // padding-top: 36px;
    margin-top: 36px;
    width: 300px;
    height: 200px;
    object-fit: cover;
    border-radius: 12px;
  }

}

  </style>