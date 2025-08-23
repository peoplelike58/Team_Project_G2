<script setup>
import{ref,defineEmits} from 'vue';


const searchKeyword = ref('');// 儲存使用者輸入的關鍵字
const selectedGenders = ref([]);// 儲存使用者選擇的性別 
const selectedSubCategory  = ref('');//儲存點擊的分類,初始狀態設為空字串，代表選中「全部商品」
const activeCategory = ref('');

// 控制手風琴開關狀態
const openCategories = ref({
  hiking: false,      // 登山健行裝備是否展開
  overnight: false    // 登山過夜裝備是否展開
})

const emit = defineEmits(['search','gender-filter','category-filter'])//定義要傳輸的事件名稱，命名事件為'search'


// 搜尋處理函數（傳遞的動作）
const handleSearch = () => {
  emit('search', searchKeyword.value);  // 發送搜尋事件給父組件('事件名稱'，值)，這裡的值是要傳遞輸入的關鍵字或者分類給父層
}

//性別篩選（傳遞的動作）
const handleGenderFilter = () => {
  emit('gender-filter', selectedGenders.value);
}

//分類篩選
const selectCategory = (category, type) =>{
  if (type === 'main') {
        // 主分類：控制展開/收合
        activeCategory.value = activeCategory.value === category ? '' : category;
    } else if (type === 'sub' || type === 'single') {
        // 子分類或單一分類：實際篩選商品
        selectedSubCategory.value = category;
        emit('category-filter', selectedSubCategory.value);
    } 
  // 如果是單一分類（如全部商品），關閉所有展開的主分類
    if (type === 'single') {
      activeCategory.value = '';
    }
}

//打開分類（手風琴）
const toggleCategory = (category) => {
  openCategories.value[category] = !openCategories.value[category]
}

// 初始化時觸發一次分類篩選，確保「全部商品」被選中
import { onMounted } from 'vue'
onMounted(() => {
  emit('category-filter', selectedSubCategory.value); // 發送空字串代表顯示全部商品
})
</script>

<template>
  <!-- 側邊篩選欄 -->
  <aside class="filters"><!-- 搜尋欄 -->
    <div class="search">
        <input type="text" v-model="searchKeyword" @input="handleSearch"  placeholder="請輸入你想找的商品" >
        <!-- 監聽input進來的值 -->
        <button class="icon_search" @click="handleSearch"><img src="/public/Products/icons/icon_search.svg" alt=""></button>
      </div>
    <div class="classification"><!-- 商品分類 -->
      <div class="all_products">
        <button 
          @click="selectCategory('', 'single')" 
          :class="{ active: selectedSubCategory === '' }">
          全部商品
        </button>
      </div>
      <div class="hiking_equipment"><!-- 健行裝備 -->
        <!-- 加入手風琴切換功能 -->
        <button @click="toggleCategory('hiking');selectCategory('登山健行裝備', 'main')"  
        :class="{ active: openCategories.hiking }">
          <span>登山健行裝備</span><img src="/public/Products/icons/icon_predown.svg" alt="" 
          :style="{ transform: openCategories.hiking ? 'rotate(180deg)' : 'rotate(0deg)' }">
        </button>
        <!--  根據狀態顯示/隱藏子選單 -->
        <ul :class="{ active: openCategories.hiking }">
            <li><button @click="selectCategory('登山杖', 'sub')">登山杖</button></li>
            <li><button @click="selectCategory('水瓶', 'sub')">水瓶</button></li>
            <li><button @click="selectCategory('望遠鏡', 'sub')">望遠鏡</button></li>
            <li><button @click="selectCategory('手電筒', 'sub')">手電筒</button></li>
        </ul>
      </div>
      <div class="overnight_equipment"><!-- 過夜裝備 -->
        <button @click="toggleCategory('overnight');selectCategory('登山過夜裝備', 'main')"
                :class="{ active: openCategories.overnight }">
          <span>登山過夜裝備</span><img src="/public/Products/icons/icon_predown.svg" alt=""
          :style="{ transform: openCategories.overnight ? 'rotate(180deg)' : 'rotate(0deg)' }">
        </button>
        <ul :class="{ active: openCategories.overnight}">
            <li><button @click="selectCategory('帳篷', 'sub')">帳篷</button></li>
            <li><button @click="selectCategory('睡袋', 'sub')">睡袋</button></li>
            <li><button @click="selectCategory('廚具', 'sub')">廚具</button></li>
        </ul>
      </div>
      <div class="cloth">
        <button @click="selectCategory('登山服飾', 'single')">登山服飾</button>
      </div>
      <div class="shoes">
        <button @click="selectCategory('登山鞋', 'single')">登山鞋</button>
      </div>
      <div class="backpack">
        <button @click="selectCategory('登山包', 'single')">登山包</button>
      </div>
    </div>
    <!-- 性別篩選 -->
    <div class="filter_sex">
        <h3>篩選 性別</h3>
        <ol>
            <li><input 
              type="checkbox" 
              name="sex" 
              id="male" 
              value="male"   
              v-model="selectedGenders"
              @change="handleGenderFilter">
              <label for="male">男性</label></li>
            <li><input 
              type="checkbox" 
              name="sex" 
              id="female" 
              value="female"
              v-model="selectedGenders"
              @change="handleGenderFilter">
              <label for="female">女性</label></li> 
        </ol>
    </div>
  </aside>
</template>

<style scoped lang="scss">
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';
aside{
  flex: 0 0 240px; /* 固定側邊欄寬度 */
}
/* 搜尋欄 */
.search{
    @include border(#ccc);
    border-radius: 12px;
    padding: 10px;
    display: flex;

    input{
      font-size: $pcFont-p-s;
      flex:1;
      border: none;
    }
    input:focus {
      outline: none; /* 取消外框 */
      box-shadow: none; /* 取消陰影（像藍色光暈） */
      }
    button{
      @include btn(0);
      // img{
      //    @include img;
      // }
    }
}

.search:focus-within{
   outline: 1px solid $ash-olive-400;
}

/* 商品分類 */
.classification{
   // @include flexcenter(0,column);
   font-size: $pcFont-H4;
   line-height: $lineHeight-p-200;
   font-weight: $semiBold;
   display: flex;
   flex-direction: column;
   padding: 10px;

   // 主分類按鈕樣式
   .hiking_equipment,.overnight_equipment,.cloth,.shoes,.backpack,.all_products{
      >button{
         font-weight: $semiBold;
         display: flex;
         justify-content: space-between;
         align-items: center;
         border-bottom: 1px solid #ccc;
         padding: 8px 16px;

         font-size: $pcFont-p-s;
         font-weight: $semiBold;
         line-height: $lineHeight-p-200;

        //  &:hover {
        //  background-color: $ash-olive-400;
        //  color: white;
        //  }
         &.active {
      //   background-color: #2c5530;
      //   color: white;
        img {
          transform: rotate(180deg);
        }
      }

      }  
   }
   button{
      @include btn(0);
      width: 100%;
   }

  
    // 子分類列表
    ul {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.3s ease;
      background-color: white;
      border-radius: 6px;

      
      &.active {
        max-height: 200px;
        // border: 1px solid #e0e0e0;
      }
      
      li {
        
        button {
         @include btn(0);
          width: 100%;
          padding: 10px 20px;
          text-align: left;
          font-size: $pcFont-p-s;
          color: #666;
          border-bottom: 1px solid #f0f0f0;

          &:hover {
            // background-color: #f8f8f8;
            color: #2c5530;
          }
          
          &:last-child {
            border-bottom: none;
          }
          
          &.selected {
            background-color: #2c5530;
            color: white;
            font-weight: 500;
          }
        }
      }
    }
   }
/* 篩選 */
.filter_sex{
   padding-left: 10px;
   h3{
      padding: 10px;
      font-size: $pcFont-H4;
      font-weight: $semiBold;
      line-height: $lineHeight-p-200;
   }
   ol{
      padding: 0 10px;
      li{
         padding: 0  0 10px 10px;
      }
   }

}



// 響應式設計
@media (max-width: 768px) {
  .classification {
    gap: 6px;
    
    .hiking_equipment, .overnight_equipment, .cloth, .shoes, .backpack {
      > button {
        padding: 10px 12px;
        font-size: 14px;
        
        span {
          font-size: 13px;
        }
        
        img {
          width: 14px;
          height: 14px;
        }
      }
      
      ul li button {
        padding: 8px 16px;
        font-size: 13px;
        
        &:hover {
          padding-left: 20px;
        }
      }
    }
  }
}

</style>