<script setup>
import navMenu from '@/components/An/navMenu.vue';
import filterRoute from '@/components/Pei/FilterRoute.vue';
import adCard from '@/components/Pei/adCard.vue';

import trailsData from '@/assets/json/trails.json';



</script>



<template>
    <div class="wrapper">
        <header>
            <!-- <navMenu /> -->
        </header>

        <body>
            <nav class="breadcrumb">
                <router-link to="/homepage">首頁</router-link>
                <span> &gt; </span>
                <router-link to="/routes">路線規劃</router-link>         
            </nav>

            
            <filterRoute/>
            <adCard />
        </body>
    
    </div>


</template>

<style lang="scss" scoped>
@import '../assets/styles/main.scss';
@import '../assets/styles/mixins';

.wrapper{
    background-color: $bg-gray;

    .breadcrumb{
        width: 100%;
        max-width: 1200px;
        margin: 32px auto;
        letter-spacing: 1.5px;
        padding: 0 20px;
        @include m(){
            max-width: 430px;
        }

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