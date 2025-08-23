<script setup>
import { useRouter } from 'vue-router'
const router = useRouter()
// const id = router.params.id

//關閉回到商品頁
const close = () => router.push('/Shop')

</script>

<template>
    <!-- 遮罩（純切版：只負責背景，不做任何狀態） -->
  <div class="mask" @click="close" ></div>

  <!-- 彈窗本體（Teleport 避免受父層影響） -->
  <teleport to="body">
    <div class="product_modal"  @click.stop> <!-- stop是vue的修飾符，讓遮罩的click事件在這個區域停止 -->
      <!-- 右上角關閉 -->
      <button  class="close" @click="close" >×</button>
      <!-- 卡片上半區塊 -->
      <div class="modal_up">
        <!-- 左：主圖 -->
        <div class="product_show">
            <div class="product_image">
                <img src="/public/Products/products/望遠鏡_3.png" alt="折疊雙筒望遠鏡"/>
            </div> 
          <!-- 標籤 -->
          <div class="product_tags">
            <span class="tag">熱銷</span>
            <span class="tag">新手必備</span>
            <span class="tag">建議難度 低</span>
            <span class="tag">女性特製</span>
          </div>
        </div>
        <!-- 右：規格 -->
        <div class="product_info">
          <h2 class="product_title">折疊雙筒望遠鏡</h2>
          <!-- 價格&icon -->
          <div class="product_price_icon">
            <div class="product_price">NT$ 3,200</div>
            <div class="share-like">
                <button class="icon_btn">🔗</button>
                <button class="icon_btn">🤍</button>
            </div>
          </div>
          <!-- 顏色 -->
          <div class="product_row">
            <div class="product_label">顏色</div>
            <div class="product_options">
              <button class="opt">黑</button>
              <button class="opt">白</button>
              <button class="opt">紅</button>
            </div>
          </div>
          <!-- 尺寸 -->
          <div class="product_row">
            <div class="product_label">尺寸</div>
            <div class="product_options">
              <button class="opt">S</button>
              <button class="opt">M</button>
              <button class="opt">L</button>
            </div>
          </div>
          <!-- 數量 -->
          <div class="product_row">
            <div class="product_label">數量</div>
            <div class="product_quality">
              <button>-</button>
              <input type="number" value="20" readonly />
              <button>+</button>
            </div>
          </div>

          <!-- 行動按鈕 -->
          <div class="product_actions">
            <button class="btn-addcart">加入購物車</button>
            <button class="btn-paynow">立即購買</button>
          </div>
        </div>
      </div>
        <!-- 卡片下半區塊 -->
        <div class="modal_down">
          <!-- 詳細資訊（不同商品內容不同） -->
        <div class="product_detail">
            <h3>商品詳情</h3>
            <p>高透光鍍膜，攜帶方便。</p>
        </div>
            <!-- 配送資訊（永遠不變） -->
        <div class="product_accordions">
            <div class="product_accordion">
                <div class="product_acc-title">付款方式</div>
                <ul>
                  <li>信用卡：VISA / Master / JCB</li>
                  <li>LINE Pay</li>
                  <li>超商取貨付款（限額 $2,000 以下）</li>
                </ul>
            </div>
            <div class="product_accordion">
                <div class="product_acc-title">運送方式</div>
                <ul>
                    <li>7-11 取貨：長邊 ≤ 45cm，重量 ≤ 5kg</li>
                    <li>離島配送：運費另計</li>
                </ul>
            </div>
        <!-- /product_accordions -->
         </div>
        </div>
    </div>
  </teleport>
</template>

<style lang="scss" scoped>
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';

/* 遮罩 */
.mask {
  position: fixed;
  inset: 0;//top: 0;right: 0;bottom: 0;left: 0;的縮寫
  background: rgba(0,0,0,.35);
  z-index: 90;
}

/* 彈窗本體 */
.product_modal {
  position: fixed;
  inset: 40px 24px;//top:40px; right:24px; bottom:40px; left:24px;
  max-width: 1080px;
  margin: auto;
  background: #fff;
  border-radius: 16px;
  padding: 24px;
  z-index: 100;
  box-shadow: 0 20px 60px rgba(0,0,0,.2);
  max-height:80vh; 
  overflow:auto;

  display: flex;
  flex-direction: column;
}

/* 關閉按鈕 */
.close {
  @include btn(0);
  position: absolute;
  right: 12px;
  top: 10px;
  font-size: 24px;
  padding: 6px 10px;
  // align-self: flex-end;
}

/* 上半部版面 */
.modal_up {
  @include flexcenter(100px,row);
  padding: 12px;
}
.product_show{
    @include flexcenter(10px,column);
    align-items: flex-start;
    flex: 0 0 400px;
    .product_image{
        @include product_card_img(400px,400px,16px);
    }
    .product_tags{
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        .tag {
            background: $tag;
            color: #fff;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
        }
    }
}
/*右邊內容區*/
.product_info{
  padding-top: 6px;
  flex: 0 0 400px;
  .product_title { 
      font-size: $pcFont-H3; 
      font-weight: $bold; 
      margin: 6px 0 10px; 
  }
  .product_price_icon {
      display: flex; 
      justify-content: space-between; 
      align-items: center;
      margin-bottom: 16px;
      .product_price { font-size: 36px; font-weight: 800; }
      .share-like {
          display: flex; gap: 10px;
          .icon_btn{
          width: 36px; 
          height: 36px; 
          @include flexcenter(0,row);
          @include btn(0);
          }
      }
  }
  /* 規格列 */
  .product_row{ 
    @include flexcenter(16px,row);
    justify-content:flex-start;
    margin: 18px 0;}
    .product_label { 
        width: 44px; 
        font-weight: $bold; 
        color:$black-14; 
    }
    .product_options {
        display: flex; 
        gap: 12px; 
        flex-wrap: wrap;
        .opt {
            min-width: 56px; 
            height: 36px; 
            padding: 0 14px;
            @include btn(8px);
            @include border(#bbb);
            }
  }
  /* 數量區（底線風格） */
  .product_quality {
    @include flexcenter(12px,row);
    padding: 6px 12px 12px;
    width: 220px;
    button { 
      width: 32px; 
      height: 32px; 
      font-size: 24px;
      @include border(#bbb);
      @include btn(6px);
    .number { 
      width: 174px; 
      height: 64px; 
      text-align: center; 
      border: none; 
      outline: none; }
      }
  }

  /* 行動按鈕 */
  .product_actions {
    display: flex; 
    gap: 12px; 
    margin-top: 22px;
    .btn-addcart {
      flex: 1; 
      @include btn(10px);
      @include border(#111);
      height: 48px; 
      font-weight: $bold;
    }
    .btn-paynow {
      flex: 1; 
      @include btn(10px);
      @include border(#111);
      height: 48px; 
      background: #111; 
      color: #fff;
      font-weight: 800;
    }
  }
}


/* 下半區塊：左文案、右折疊 */
.modal_down {
 @include flexcenter(100px,row);
 align-items: self-start;
 margin: 28px;
}
.product_detail{
  flex: 0 0 400px;
  height: 100%;
  h3 { font-size: 16px; font-weight: 800; margin-bottom: 8px; }
  p  { color: #333; margin: 8px 0 0; line-height: 1.6; }
}

.product_accordions{
  flex: 0 0 400px;
  .product_accordion { 
    border-top: 1px solid #e5e5e5;
    padding: 16px 0; }
  .product_acc-title {
    font-weight: 800;
    position: relative;
    padding-right: 24px;
    margin-bottom: 10px;
    .product_acc-title::after {
      content: "";
      position: absolute;
      right: 0; 
      top: 50%;
      width: 10px; 
      height: 10px; 
      border-right: 2px solid #222; 
      border-bottom: 2px solid #222;
      transform: translateY(-50%) rotate(-45deg);
      }
    }
  .product_accordion ul { margin: 0 0 0 16px; line-height: 1.8; color: #333; }
}


</style>

