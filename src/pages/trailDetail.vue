<script setup>
import { ref, onMounted, computed, watch } from "vue";
import { useRoute } from "vue-router";
import axios from 'axios';
import { useUserStore } from '@/stores/user'

// import trailsData from '@/assets/json/trails.json';

import navMenu from '@/components/An/navMenu.vue';
import CarouselPic from '@/components/Pei/CarouselPic.vue';
import APIweather from '@/components/Pei/APIweather.vue';
import btnPage from '@/components/Pei/btnPage.vue';
import Comments from '@/components/Pei/Comments.vue';
import brandFooter from '@/components/An/footer.vue';




// ----------------PHP---------------------------
// API 基本路徑
const trails = ref([])
const API_URL = `${import.meta.env.VITE_AJAX_URL}/trailDetail.php`
const fetchTrails = async () => {
  
  try {
    const resp = await axios.get(API_URL)
    trails.value = resp.data
    // console.log(trails.value);

  } catch (err) {
    console.log(err.message);
    
  }
}

const user = useUserStore()

// onMounted(() => {                                           
//   fetchTrails()       
  
// }) 

onMounted(async () => {
  // 先把登入狀態與後端 Session 對齊（避免 F5 後 Pinia 還沒回灌）
  try { await user.hydrateFromSession() } catch {}

  // 再撈山（不需要登入）
  await fetchTrails()
})


const route = useRoute()

// ===== baseUrl 與路徑轉換工具 =====
const baseUrl = import.meta.env.BASE_URL   
// const toUrl = (p) => (p ? `${baseUrl}${String(p).replace(/^\/+/, '')}` : '')

// 取得 URL 上的 id 將其轉成數字 int
const id = computed(() => parseInt(route.params.MOUNTAIN_ID)) 

// 只有在經緯度是「有效數字」時才渲染地圖
const hasLatLng = computed(() => {
  const lat = Number(trail.value?.LATITUDE)
  const lng = Number(trail.value?.LONGITUDE)
  return Number.isFinite(lat) && Number.isFinite(lng)
})

// 找出對應的山資料
// const trail = computed(() => trails.value.find(trail => trail.MOUNTAIN_ID === id.value))
// console.log(trails.value);
const trail = computed(() =>                                                          
  trails.value.find(trail => Number(trail.MOUNTAIN_ID) === id.value)                         
)   

watch(trail, (t) => {                                                     
  const name = (t && typeof t.MOUNTAIN_NAME === 'string')  
    ? t.MOUNTAIN_NAME.trim()                              
    : ''                                    
  document.title = name                    
    ? `山上見｜路線規劃｜${name}`      
    : '山上見｜路線規劃'             
}, { immediate: true })         

</script>


<template>
    <div class="wrapper "v-if="trail">
    <navMenu/>

    

    <nav class="breadcrumb">
        <router-link to="/homepage">首頁</router-link>
        <span> &gt; </span>
        <router-link to="/routes">路線規劃</router-link>  
        <span> &gt; </span>
        <span class="current">{{ trail.MOUNTAIN_NAME }}</span>  


    </nav>

    <CarouselPic :trail="trail" />

    <APIweather 
    
    :town="trail.TOWN"/>

    <!-- v-if="hasLatLng" -->
    <!--只有經緯度是有效數字才渲染-->
    <btnPage 
    :trail="trail"/>

    <Comments 
    :id="trail.MOUNTAIN_ID"
    :mountainName="trail.MOUNTAIN_NAME"/>

    <brandFooter/>
    </div>

    <div v-else>
        <p>資料載入中或找不到資料...</p>
    </div>
    
</template>

<style lang="scss" scoped>
@import '@/assets/styles/main.scss';
@import '@/assets/styles/mixins';
.wrapper{
    background-color: $bg-gray;

    .breadcrumb{
        width: 100%;
        max-width: 1200px;
        margin: 32px auto;
        letter-spacing: 1.5px;

        @include m(){
            max-width: 768px;
            padding: 0 20px;
            box-sizing: border-box;
        }

        a{
            text-decoration: none;
            color: $tag;
        }

        a:last-child,
        .current{
            font-weight:$medium;
        }
        
    }
    
}
</style>