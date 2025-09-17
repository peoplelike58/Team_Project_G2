<script setup>
import navMenu from '@/components/An/navMenu.vue';
import filterRoute from '@/components/Pei/FilterRoute.vue';
import adCard from '@/components/Pei/adCard.vue';
import footer from '@/components/An/footer.vue';
import brandFooter from '@/components/An/footer.vue';



</script>



<template>
    <div class="wrapper">
        
            <navMenu />
        

       
            <nav class="breadcrumb">
                <router-link to="/homepage">首頁</router-link>
                <span> &gt; </span>
                <router-link to="/routes">路線規劃</router-link>         
            </nav>

            
            <filterRoute/>
            <adCard />
            <brandFooter/>
            
        
    
    </div>


</template>

<style lang="scss" scoped>
@import '@/assets/styles/main.scss';
@import '@/assets/styles/mixins';

.wrapper{
  background-color: $bg-gray !important; // 整頁背景色
  width: 100%; // 撐滿
  min-height: 100vh; // 高度滿版

    .breadcrumb{
        width: 100%;
        max-width: 1200px;
        margin: 32px auto;
        letter-spacing: 1.5px;
        padding: 0 20px;
        box-sizing: border-box;
        @include m(){
            max-width:768px;
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