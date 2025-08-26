<script setup>
import { computed } from "vue";
import { useRoute } from "vue-router";

import trailsData from '@/assets/json/trails.json';

import navMenu from '@/components/An/navMenu.vue';
import CarouselPic from '@/components/Pei/CarouselPic.vue';
import APIweather from '@/components/Pei/APIweather.vue';
import btnPage from '@/components/Pei/btnPage.vue';
import Comments from '@/components/Pei/Comments.vue';

const route = useRoute()

// 取得 URL 上的 id 將其轉成數字 int
const id = computed(() => parseInt(route.params.id)) 

// 找出對應的山資料
const trail = computed(() => trailsData.find(trail => trail.id === id.value))
// console.log(thisTrail.value);

</script>


<template>
    <div class="wrapper "v-if="trail">
    <navMenu/>

    

    <nav class="breadcrumb">
        <router-link to="/homepage">首頁</router-link>
        <span> &gt; </span>
        <router-link to="/routes">路線規劃</router-link>  
        <span> &gt; </span>
        <router-link >{{ trail.name }}</router-link>  


    </nav>

    <CarouselPic :trail="trail" />
    <APIweather :town="trail.town"/>
    <btnPage :trail="trail"/>
    <Comments :id="trail.id"/>
    </div>

    <div v-else>
        <p>資料載入中或找不到資料...</p>
    </div>
    
</template>

<style lang="scss" scoped>
@import '../assets/styles/main.scss';
.wrapper{
    background-color: $bg-gray;

    .breadcrumb{
        width: 100%;
        max-width: 1200px;
        margin: 32px auto;
        letter-spacing: 1.5px;

        a{
            text-decoration: none;
            color: $tag;
        }

        a:last-child{
            font-weight:$medium;
        }
        
    }
    
}
</style>