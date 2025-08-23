<template>
    <section class="hero">
        <!-- 底層：輪播背景 -->
        <carouselPlayController
            class="hero-bg"
            v-model:index = "index"
            :slides="slides"
            :autoPlay="true"
            :autoPlayIntervalMs="5000"
        />
  
        <!-- 中層：遮罩 -->
        <div class="hero-scrim"></div>
  
        <!-- 上層：內容文字 -->
        <div class="hero-content">
            <div class="left">
                <p class="badge"># {{ currentSlide.badge }} / {{ slides.length }}</p>
                <h1 class="latin">{{ currentSlide.latin }}</h1>
                <p class="subtitle">{{ currentSlide.subtitle }}</p>
                <a class="cta" href="#">走訪山岳之書</a>
            </div>
  
            <div class="right">
                <h2 class="title-vertical">{{ currentSlide.name }}</h2>
                <p class="altitude">
                    <digitReel
                    :to="Number(currentSlide.height)"
                    :stagger="0"
                    :easeArr="['power4.out', 'back.out(1.2)', 'power2.out', 'power1.out']"
                    :delayArr="[0, 0, 0, 0.05]"
                    />
                    m
                </p>
            </div>
        </div>
    </section>
</template>
  
<script setup>
    import { ref, computed } from 'vue'
    import carouselPlayController from './carouselPlayController.vue'
    import digitReel from './digitReel.vue'
  
    import mountain01 from '@/assets/images/百岳/玉山/516347556_24961926796729060_2046895774293410155_n.jpg'
    import mountain02 from '@/assets/images/百岳/雪山/511002789_10238811707489777_4111256882028328282_n.jpg'
    import mountain03 from '@/assets/images/百岳/南湖/S__103170057.jpg'
  
    const slides = [
        { src: mountain01, badge: '01', name: '玉山', height: '3952', latin: 'Patungkuonu', subtitle: '台灣之巔' },
        { src: mountain02, badge: '02', name: '雪山', height: '3886', latin: 'Hseuhshan',    subtitle: '巍峨雪峰' },
        { src: mountain03, badge: '03', name: '南湖大山', height: '3742', latin: 'Nanhu Dashan', subtitle: '帝王之山' }
    ]

    const index = ref(0);
    const currentSlide = computed(() => slides[index.value]);

</script>
  
<style scoped lang="scss">
    @import '../../assets/styles/main.scss';
    
    .hero {
        display: grid;
        grid-template-areas: "canvas";

        width: 100%;
        height: 100dvh;

        box-sizing: border-box;
    }

    .hero > * { grid-area: canvas; }

    .hero-bg {
        width: 100%;
        height: 100%;
        
        // opacity: 0;
    }
    
    .hero-bg :deep(img) {
        width: 100%;
        height: 100%;
        
        object-fit: cover;
    }
  
    .hero-scrim {
        pointer-events: none;
        background: linear-gradient(
            180deg,
            rgba(0,0,0,0.45) 0%,
            rgba(0,0,0,0.25) 40%,
            rgba(0,0,0,0.10) 70%,
            rgba(0,0,0,0.00) 100%
        );
    }
  
    .hero-content {
        display: grid;
        grid-template-columns: 3fr 1fr;
        align-items: center;

        padding: 0 80px;
        color: #fff;
    }
  
    .left {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .badge {
        opacity: 0.9;
        font-weight: $semiBold;
        font-size: 32px;
    }

    .latin {
        opacity: 0.9;
        font-size: 64px;
        font-weight: $bold;
        line-height: $lineHeight-title-120;
    }

    .subtitle {
        opacity: 0.9;
        font-size: 32px;
        font-weight: $semiBold;
        line-height: $lineHeight-title-120;
    }

    .cta {
        display: inline-block;

        width: fit-content;
        margin-top: 80px;

        text-decoration: 1px underline;
        text-underline-offset: 10px;
        
        color: #fff;
        opacity: 0.9;
        font-size: 24px;
        font-weight: 900;
    }
  
    .right {
        display: flex;
        align-items: center;
        gap: 24px;
    }
  
    .title-vertical {
        opacity: 0.9;
        font-weight: 900;
        writing-mode: vertical-rl;
        letter-spacing: 0.5rem;
        font-size: 160px;
        text-shadow: 0 1px 10px rgba(0,0,0,0.25);
    }
  
    .altitude {
        width: max-content;
        opacity: 0.9;
        margin: 0;
        font-weight: $bold;
        font-size: 40px;
        text-shadow: 0 1px 8px rgba(0,0,0,0.25);
    }

    @media (max-width: 1024px) {
        .hero-content {
            grid-template-columns: 1fr;
            align-content: end;
            gap: 24px;
            padding: 0 40px;
        }
        .right { justify-items: start; }
        .zh-vertical {
            font-size: clamp(56px, 20vw, 140px);
        }
    }
</style>  