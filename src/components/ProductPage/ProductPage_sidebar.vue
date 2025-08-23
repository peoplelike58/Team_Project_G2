<template>
       <!-- 側邊篩選欄 -->
    <aside>
         <!-- 搜尋欄 -->
        <div class="search">
            <input type="text" placeholder="請輸入你想找的商品">
            <button class="icon_search"><img src="/public/Products/icons/icon_search.svg" alt=""></button>
         </div>
        <!-- 商品分類 -->
        <div class="classification">
            <!-- 健行裝備 -->
             <div class="hiking_equipment">
                <button><span>登山健行裝備</span><img src="/public/Products/icons/icon_predown.svg" alt=""></button>
                <ul class="">
                    <li><button>登山杖</button></li>
                    <li><button>水瓶</button></li>
                    <li><button>望遠鏡</button></li>
                    <li><button>手電筒</button></li>
                </ul>
             </div>
            <!-- 過夜裝備 -->
             <div class="overnight_equipment">
                <button><span>登山過夜裝備</span><img src="/public/Products/icons/icon_predown.svg" alt=""></button>
                <ul class="">
                    <li><button>帳篷</button></li>
                    <li><button>睡袋</button></li>
                    <li><button>廚具</button></li>
                </ul>
             </div>
             <div class="cloth">
                <button>登山服飾</button>
             </div>
             <div class="shoes">
                <button>登山鞋</button>
             </div>
             <div class="backpack">
                <button>登山包</button>
             </div>
        </div>
        <!-- 性別篩選 -->
         <div class="sex">
            <h3>篩選 性別</h3>
            <ol>
                <li><input type="checkbox" name="sex" id="male"><span>男性</span></li>
                <li><input type="checkbox" name="sex" id="female"><span>女性</span></li> 
            </ol>
         </div>
    </aside>
</template>

<script setup>

</script>

<style scoped lang="scss">
@import '@/assets/styles/main.scss';

/* 搜尋欄 */
.search{
    border: 1px solid #ccc;
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
      img{
         // @include img;
      }
    }
}

.search:focus-within{
   outline: 1px solid $ash-olive-400;
}

/* 商品分類 */
.classification{
   // @include flexcenter(0,column);
   font-size: $pcFont-H4;
   line-height: $linHeight-p-200;
   font-weight: $semiBold;
   display: flex;
   flex-direction: column;
   padding: 10px;

   // 主分類按鈕樣式
   .hiking_equipment,.overnight_equipment,.cloth,.shoes,.backpack{
      >button{
         font-weight: $semiBold;
         display: flex;
         justify-content: space-between;
         align-items: center;
         border-bottom: 1px solid #ccc;
         padding: 8px 16px;

         font-size: $pcFont-p-s;
         font-weight: $semiBold;
         line-height: $linHeight-p-200;

         &:hover {
         // background-color: $ash-olive-400;
         // color: white;
         }
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
        border: 1px solid #e0e0e0;
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
.sex{
   padding-left: 10px;
   h3{
      padding: 10px;
      font-size: $pcFont-H4;
      font-weight: $semiBold;
      line-height: $linHeight-p-200;
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