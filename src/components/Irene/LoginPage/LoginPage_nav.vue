<!-- LoginPage_nav.vue 商城頁側邊欄 -->
<script setup>

</script>

<template>
    <nav class="menu" >
        <ul class="menu__list">
          <li class="menu__item">
            <RouterLink class="nav-item " to="/Member/profile">
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
              <span  class="nav-text">個人資料</span>
            </RouterLink>
          </li>
          <li class="menu__item">
            <RouterLink class="nav-item " to="/Member/orders">
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <span  class="nav-text">我的訂單</span>
            </RouterLink>
          </li>
          <li class="menu__item">
            <RouterLink class="nav-item " to="/Member/collections">  
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
              </svg>
              <span  class="nav-text">商品收藏</span>
            </RouterLink>
          </li>
          <li class="menu__item">
            <RouterLink class="nav-item " to="/Member/messages">
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
              </svg>
              <span  class="nav-text">我的留言</span>
            </RouterLink>
          </li>
          <li class="menu__item">
            <RouterLink class="nav-item " to="/Member/activitys">
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
              <span  class="nav-text">我的活動</span>
            </RouterLink>
          </li>
          <li class="menu__item">
            <RouterLink class="nav-item " to="/Member/coupons">
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z"></path>
              </svg>
              <span  class="nav-text">我的優惠</span>
            </RouterLink>
          </li>
          <li class="menu__item">
            <RouterLink class="nav-item " to="/Member/challenges">
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12l5-5 4 4 6-6m-6 6v9"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 17l3-3 4 4 8-8"></path>
              </svg>
              <span  class="nav-text">百岳挑戰</span>
            </RouterLink>
          </li>
        </ul>
    </nav>
</template>

<style lang="scss" scoped>
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';

.menu__list { 
  @include flexcenter(16px,column);
  :deep(a){ //:deep(a) 的意思是「穿透 scoped 的邊界，往子元件裡router自動產生的深層的 <a> 套樣式」。所以這一條能確保連 <RouterLink> 產生的 <a> 也會吃到字色、粗細、去底線等設定
  color:$black-14; 
  text-decoration:none; 
  font-weight:$semiBold; 
  }
  /* 啟用中的路由高亮標示 */
  :deep(.router-link-active){ 
    color:$black-14; 
    position:relative; 
  }
  :deep(.router-link-active)::before{//在這個位置時掛在active的屬性，產生偽元素的亮標
    content:''; 
    position:absolute; 
    left:-12px; 
    top:6px; 
    width:4px; 
    height:12px; 
    background:$black-14; 
    border-radius:2px;
  }
  :deep(a svg){
  width: 20px;               /* 等效於 Tailwind w-5 */
  height: 20px;              /* 等效於 Tailwind h-5 */
  margin-right: 12px;        /* 等效於 Tailwind mr-3 */
  flex-shrink: 0;            /* 避免被壓縮到 0 */
  display: inline-block;
  vertical-align: middle;
  /* 不直接指定 color，讓它沿用 a 的 color，配合 currentColor */
}

}

.nav-item {
            position: relative;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            :deep(a){
            color: $black-14 !important;  /* 保險，避免其他規則把 color 蓋掉 */
            }   
            :deep(a svg){
              width: 20px; height: 20px;    /* 再次保險指定尺寸 */
              margin-right: 0;              /* 你小螢幕把文字藏起來就不需要間距 */
            }      
        }
        
        .nav-item:hover {
            background-color: #f1f5f9;
            border-radius: 8px;
        }
        
        .nav-item.active {
            color: #1f2937;
            font-weight: 600;
        }
        
        .nav-item.active::before {
            content: '';
            position: absolute;
            left: -12px;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 12px;
            background: #1f2937;
            border-radius: 2px;
        }
 @media (max-width: 768px) {
            .member-layout {
                grid-template-columns: 100px 1fr !important;
            }
            
            .nav-text {
                display: none;
            }
            
            .nav-item {
                justify-content: center;
            }
        }
</style>