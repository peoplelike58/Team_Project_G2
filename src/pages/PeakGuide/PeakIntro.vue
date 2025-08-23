<script setup>
    // Hero Title
    const heroTitle = "百岳之書"
    const heroSubTitle = "翻開台灣百岳，插旗每座山峰！"
    // Images
    const images = {
        hero:"/images/PeaksOfTaiwan/Yushan/Yushan_main01.jpg",
        intro:"/images/PeaksOfTaiwan/Yushan/Yushan_main02.png",
        about1:"/images/PeaksOfTaiwan/Yushan/Yushan_main03.png",
        about2:"/images/PeaksOfTaiwan/Yushan/Yushan_main04.png",
        guide1:"/images/PeaksOfTaiwan/Yushan/Yushan_main05.png",
        guide2:"/images/PeaksOfTaiwan/Yushan/Yushan_main06.png",
        guide3:"/images/PeaksOfTaiwan/Yushan/Yushan_main07.jpg",
    }
    // Content
    const content ={
        intro:"台灣百岳，是一本寫滿自然與勇氣的書。\n每座山峰，都是大地的詩篇，\n等待你用腳步書寫傳奇，用旗幟記錄你的足跡！",
        about:"台灣百岳，是指台灣海拔3,000公尺以上的100座山峰，\n由登山先驅選定，象徵勇氣與自然的完美結合。",
        guide:"從玉山的「亞洲第一高峰」到南湖大山的「帝王之氣」，\n每座百岳都是台灣的驕傲。你準備好打開這本「山岳之書」了嗎？",
    }
</script>

<template>
    <main class="hero">
        <div class="hero-left">
            <div class="hero-slogan">
                <h1>{{ heroTitle}}</h1>
                <p>{{ heroSubTitle}}</p>
            </div>
        </div>
        <div class="hero-right">
            <img :src="images.hero" 
            alt="PeaksOfTaiwan-01" 
            loading="lazy">
        </div>
    </main>
    <section class="intro">
        <div class="intro-content">
            <div class="intro-text">
                <p v-html="content.intro.replace(/\n/g, '<br>')"></p>   <!-- 將斷行"/n"替換成<br> -->  
            </div>
            <div class="intro-media">
                <img :src="images.intro" alt="PeaksOfTaiwan-02" loading="lazy">
            </div>
        </div>
    </section>
    <section class="about">
        <div class="about-text">
            <p v-html="content.about.replace(/\n/g, '<br>')"></p>
        </div>
        <div class="about-media">
            <div class="media-right"><img :src="images.about1" alt="PeaksOfTaiwan-03" loading="lazy"></div>
            <div class="media-left"><img :src="images.about2" alt="PeaksOfTaiwan-04" loading="lazy"></div>
        </div>
    </section>
    <section class="guide">
        <div class="guide-text">
            <p v-html="content.guide.replace(/\n/g, '<br>')"></p>
        </div>
        <div class="guide-photo">
            <div class="photo-left"><img :src="images.guide1" alt="PeaksOfTaiwan-05" loading="lazy"></div>
            <div class="photo-center"><img :src="images.guide2" alt="PeaksOfTaiwan-06" loading="lazy"></div>
            <div class="photo-right"><img :src="images.guide3" alt="PeaksOfTaiwan-07" loading="lazy"></div>
        </div>
    </section>
</template>

<style scoped lang="scss">
    @import '@/assets/styles/mixins.scss';
    @import '@/assets/styles/main.scss';
    
    *{
        box-sizing: border-box;
    }
    .hero{
        @include container;
        display: grid;
        grid-template-columns: 1fr minmax(180px, 560px);
        margin: 50px auto;
        align-items: center;
        gap: clamp(16px, 4vw, 48px);
    }
    .hero-slogan{
        padding-top: 56px;
        position: fixed;
        top: 24px;
        z-index: 10;
        h1{
            font-size: $pcFont-bigTitle-l;
            font-weight: $bold;
            line-height: $lineHeight-title-120;
            @include m(){
                font-size:$pcFont-bigTitle-m;

            }
        }
        p{
                font-size: $pcFont-H1-m;
                line-height: $lineHeight-p-200;
                @include m(){
                font-size:$pcFont-H1-l;

            }
            }
    }
    .hero-right{
        img{
            @include peakguide-img;
        }
    }
    .intro{
        @include container;
    }
    .intro-content{
        padding: 40px 20px;
        display: flex;
        flex-direction: column;
        gap: 24px;

        @include m() {
            padding: 20px 16px;
        }
            .intro-text{
                p{
                    font-size: $pcFont-H4;
                    line-height: $lineHeight-p-200;
                    text-align: right;
                    @include m() {
                        text-align: center;
                }
            }
        }
        .intro-media {
            padding-top: 54px;

            img {
            @include peakguide-img;
            }   
        }
    }
    .about{
        @include container;
        margin-top: 24px;
    }
    .about-text{
        padding-left: 150px;
        p{
            font-size: $pcFont-H4;
            line-height: $lineHeight-p-200;
            text-align: left;
        }
    }
    .about-media{
        display: grid;
        grid-template-columns: 1.5fr 2fr;
        gap: 64px;
        padding-top: 24px;
        height: 500px;

        @include m() {
            grid-template-columns: 1fr;
            height: auto;
            gap: 24px;

        }
    }
    .media-right{
        display: flex;
        align-self: end;
        justify-content: center;

        img{
            @include peakguide-img;
            width: 300px;
            height: 300px;
            @include m() {
                width: 250px;
                height: 250px;
            }
         }
    }
    .media-left{
        display: flex;
        align-self: start;
        justify-content: center;
        img{
            @include peakguide-img;
            max-width: 500px;

            @include m() {
                max-width: 300px;
            }
        }
    }
    .guide{
        @include container;
        display: block;
        gap: 20px;
        margin-top: 24px;
    }
    .guide-text{
        p{
            font-size: $pcFont-H4;
            line-height: $lineHeight-p-200;
            text-align: center;
            margin-bottom: 24px;
        }
    }
    .guide-photo{
        height: 600px;
        display: flex;
        margin-top: 24px;
        justify-content: center;
        @include m() {
            flex-direction: column;
            height: auto;
            align-items: center;
        }
    }
    .photo-left , .photo-center , .photo-right{
        display: flex;
        margin: 24px;
        img{
            @include peakguide-img;
            height: 300px;
            width: 300px;
            @include m() {
                height: 250px;
                width: 250px;
                margin: 12px;
            }
        }
    }
    .photo-left{
        align-items: flex-end;
        @include m() {
            align-items: center;
        }
    }
    .photo-center {
        align-items: center;
        @include m() {
            align-items: center;
        }
    }
    .photo-right {
        align-items: flex-start;
        @include m() {
            align-items: center;
        }
    }
</style>