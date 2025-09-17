<script setup>
import { ref,computed,onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import axios from 'axios';


// import trailsJson from '@/assets/json/trails.json';
// const trailsTen = trailsJson.slice(0,10);
// 只取十座百岳
// console.log(trailsTen);

// 先把 BASE_URL 存成變數，避免在 template 直接寫 import.meta
// 取得部署子目錄，如 '/tjd102/g2/' 
const baseUrl = import.meta.env.BASE_URL                              

// 小工具：把 JSON 裡的相對路徑拼成可用網址
// 回傳拼好的完整路徑
// const toUrl = (p) => `${baseUrl}${p}`  


// ----------------PHP---------------------------
// API 基本路徑
const API_URL = `${import.meta.env.VITE_AJAX_URL}/filterCard.php`
const trailsTen = ref([])

const adTrails = async () => {
  
  try {
    const resp = await axios.get(API_URL)
    trailsTen.value = resp.data.slice(0,10)
    

    // console.log(trailsTen.value);


    
    

  } catch (err) {
    console.log(err.message);
    
  }
}

onMounted(() => {                                           
  adTrails()       
  
}) 



</script>



<template>
  <div class="cardWrapper">
    <h3>精選山岳推薦</h3>
    <h4>Top peaks</h4>

    <div class="marquee-wrapper">
      <div class="marquee-track">
        <ul>
          <li 
            class="activity-card" 
            v-for="trail in trailsTen"
            :key="trail.MOUNTAIN_ID"
          >
            <div class="top-content">
              <div>
                <p class="ac-date">{{ trail.REGION }}</p>
                
              </div>

              <!--:src="trail.img"-->
              <div name="image">
                <img class="ac-img" 
                :src="`${baseUrl}images/Mountain/${trail.MOUNTAIN_ID}/${trail.IMAGE}`" 
                :alt="trail.MOUNTAIN_NAME"
                 
                loading="lazy" />
              </div>

              <div name="title">
                <h3 class="ac-title">{{ trail.MOUNTAIN_NAME }}</h3>
              </div>

              <div class="ac-tags">
                <h3 class="tag">{{ trail.DISTANCE }}</h3>
                <h3 class="tag">所花時間{{ trail.TIME }}</h3>
              </div>
            </div>
            <RouterLink class="ac-cta" :to="`/routes/${trail.MOUNTAIN_ID}`">查看路線</RouterLink>
          </li>
        </ul>

                <ul>
          <li 
            class="activity-card" 
            v-for="trail in trailsTen"
            :key="trail.MOUNTAIN_ID"
          >
            <div class="top-content">
              <div>
                <p class="ac-date">{{ trail.REGION }}</p>
                
              </div>

              <!--:src="trail.img"-->
              <div name="image">
                <img class="ac-img" 
                :src="`${baseUrl}images/Mountain/${trail.MOUNTAIN_ID}/${trail.IMAGE}`" 
                :alt="trail.MOUNTAIN_NAME"
                 
                loading="lazy" />
              </div>

              <div name="title">
                <h3 class="ac-title">{{ trail.MOUNTAIN_NAME }}</h3>
              </div>

              <div class="ac-tags">
                <h3 class="tag">{{ trail.DISTANCE }}</h3>
                <h3 class="tag">所花時間{{ trail.TIME }}</h3>
              </div>
            </div>
            <RouterLink class="ac-cta" :to="`/routes/${trail.MOUNTAIN_ID}`">查看路線</RouterLink>
          </li>
        </ul>


      </div>
    </div>
  </div>
</template>

<style scoped="scoped" lang="scss">
    @import '@/assets/styles/main.scss';
    @import '@/assets/styles/mixins';

    .cardWrapper{

        background-color:rgba(255, 255, 255, 0.5);
        padding: 50px 0 60px;

        @include m(){
          font-size: 14px;
        }

        >h3{
            font-size: $pcFont-H3;
            font-weight: $medium;
            padding-left: 120px;

            @include m(){
            padding-left: 20px;

            }


        }

        >h4{
            font-size: $pcFont-H4;
            font-weight: $medium;
            letter-spacing: 1px;
            padding-left: 120px;
            margin: 8px 0px 32px;;

            @include m(){
            padding-left: 20px;

            }

        }
    }

    /* ========== card ========== */
    ul{
        display:flex;
        gap:12px;
        overflow: hidden;
        box-sizing: border-box;
        
        // border:1px solid red;
       
    }

    .activity-card {
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
        justify-content: space-between;

        width: 100%;
        max-width: 290px;
        
        padding: 20px;
        border: 1px dashed $black-14;
        border-radius: 8px;
        background: #fff;
        box-sizing:border-box;
      
        transition: background-color 0.3s ease, color 0.3s ease;
      
        @include m(){
          max-width: 210px;
        }
      }
    .activity-card:hover{
        background-color: #77b5d6;
        color: white;

        .ac-cta{
          color: white;
        }
    }

    .top-content{
        display: flex;
        flex-direction: column;
        gap: 20px;

        @include m(){
          gap:10px
        }
    }

    .ac-date {
        font-size: $pcFont-p-s;
        font-weight: $medium;
    }

    /* image block */
    .ac-img {
        display: block;
        width: 100%;
        height: auto;
        aspect-ratio: 16/10;
        object-fit: cover;
        border-radius: 8px;
        transition: border-radius 0.3s ease;
    }
    .activity-card:hover .ac-img {
        border-radius: 56px 100px 56px 100px;
    }

    /* title */
    .ac-title {
        font-size: $pcFont-H4;
        font-weight: $bold;
        line-height: $lineHeight-title-120;
    }

    /* tags */
    .ac-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        list-style: none;

        @include m(){
          gap:4px;
        }
    }
    .tag {
        display: inline-block;
        padding: 8px 8px;
        color: #fff;
        font-size:$mbFont-label;
        font-weight: $medium;
        background-color: $tag;
        border-radius: 4px;
        white-space: nowrap;

        transition: background-color 0.3s ease;
        @include m(){
          font-size: 12px;
        }
    }
    .activity-card:hover .tag{
        background-color: $bg-pink-100;
        color: $tag;
    }

    /* CTA */
    .ac-cta {
        display: flex;
        justify-content: end;
        align-items: center;
        gap: 8px;

       margin-top:30px;

        font-size: $pcFont-p-s;
        font-weight: $semiBold;
        color: $black-14;
        text-decoration: 1px underline;
        text-underline-offset: 6px;

        cursor: pointer;
        
        

        @include m(){
          margin-top: 10px;
        }
    }

/* === 以下是跑馬燈用樣式，完全不動你原本的樣式 === */
.marquee-wrapper {
  overflow: hidden; // 裁掉多出來的
}

.marquee-track {
  display: flex;
  gap:12px;
  width: max-content; // 關鍵！內容有多寬就滾多遠
  animation: scroll 80s linear infinite; // 速度可自行調整秒數
}

.marquee-wrapper:hover .marquee-track {
  animation-play-state: paused; // hover 時暫停動畫
}

@keyframes scroll {
  from {
    transform: translateX(0);
  }
  to {
    transform: translateX(-50%); // 向左滾動
  }
}

/* 最後一層保險（真的還有莫名元素撐破時） */
:root, body {
  overflow-x: hidden;   /* 可選 */
}
    
</style>