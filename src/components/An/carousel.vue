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
                <RouterLink to="/peaks" class="cta" href="#">走訪山岳之書</RouterLink>
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
  
    import Patungkuonu from '@/assets/images/百岳/玉山/516347556_24961926796729060_2046895774293410155_n.jpg'
    import Sekoan from '@/assets/images/百岳/雪山/Syue.png'
    import Pisayhe from '@/assets/images/百岳/南湖/S__103170057.jpg'
    import Guanshan from '@/assets/images/百岳/關山/497800866_30073497982241602_125545256976496051_n.jpg'
    import Kangkuwan from '@/assets/images/百岳/秀姑巒山/S__103153668.jpg'
    import Hehuan from '@/assets/images/百岳/合歡主鋒/hehuan.jpg'
    import Nenggao from '@/assets/images/百岳/能高/482981083_8784364731668597_4120280109385300698_n.jpg'
    import PapakWaqa from '@/assets/images/百岳/大霸尖山/S__103153671.jpg'
    import Pintian from '@/assets/images/百岳/品田山/119779207_3379500632085022_4848165980483849107_n.jpg'
    import Parusan from '@/assets/images/百岳/奇萊/S__5382274.jpg'
  
    const slides = [
        { src: Patungkuonu, badge: '01', name: '玉山', height: '3952', latin: 'Patungkuonu', subtitle: '台灣之巔' },
        { src: Sekoan, badge: '02', name: '雪山', height: '3886', latin: 'Sekoan', subtitle: '巍峨雪峰' },
        { src: Pisayhe, badge: '03', name: '南湖大山', height: '3742', latin: 'Pisayhe', subtitle: '帝王之山' },
        { src: Guanshan, badge: '04', name: '關山', height: '3668', latin: 'Guanshan', subtitle: '蒼茫雲嶺' },
        { src: Kangkuwan, badge: '05', name: '秀姑巒山', height: '3805', latin: 'Kangkuwan', subtitle: '峻拔雄峰' },
        { src: Hehuan, badge: '06', name: '合歡主峰', height: '3417', latin: 'Hehuan Main Peak', subtitle: '雪舞高嶺' },
        { src: Nenggao, badge: '07', name: '能高主鋒', height: '3262', latin: 'Nenggao Main Peak', subtitle: '蒼翠長嶺' },
        { src: PapakWaqa, badge: '08', name: '大霸尖山', height: '3492', latin: 'Papak Waqa', subtitle: '孤高巨嶺' },
        { src: Pintian, badge: '09', name: '品田山', height: '3524', latin: 'Pintian Mountain', subtitle: '峭壁奇峰' },
        { src: Parusan, badge: '10', name: '奇萊主峰', height: '3560', latin: 'Parusan', subtitle: '黑色奇萊' },
    ]

    const index = ref(0);
    const currentSlide = computed(() => slides[index.value]);

</script>
  
<style scoped lang="scss">
    @import '@/assets/styles/main.scss';
    
    .hero {
        display: grid;
        grid-template-areas: "canvas";

        width: 100%;
        height: calc(100dvh - 52px);

        box-sizing: border-box;
    }

    .hero > * { grid-area: canvas; }

    .hero-bg {
        width: 100%;
        height: 100%;
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
  
    .hero-content .left {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .badge {
        opacity: 0.9;
        font-weight: $semiBold;
        font-size: clamp(20px, 3vw, 32px);
        text-shadow: 0 1px 8px rgba(0,0,0,0.25);
    }

    .latin {
        opacity: 0.9;
        font-size: clamp(32px, 6vw, 56px);
        font-weight: $bold;
        line-height: $lineHeight-title-120;
        text-shadow: 0 1px 8px rgba(0,0,0,0.25);
    }

    .subtitle {
        opacity: 0.9;
        font-size: clamp(20px, 3vw, 32px);
        font-weight: $semiBold;
        line-height: $lineHeight-title-120;
        text-shadow: 0 1px 8px rgba(0,0,0,0.25);
    }

    .cta {
        display: inline-block;

        width: fit-content;
        margin-top: 80px;

        color: #fff;
        opacity: 0.9;
        font-size: 24px;
        font-weight: 900;

        text-decoration: 1px underline;
        text-underline-offset: 10px;
        text-shadow: 0 1px 8px rgba(0,0,0,0.25);
    }
  
    .hero-content .right {
        display: flex;
        align-items: center;
        gap: 24px;
    }
  
    .title-vertical {
        opacity: 0.9;
        font-weight: 900;
        writing-mode: vertical-rl;
        letter-spacing: 0.5rem;
        font-size: clamp(72px, 16vw, 144px);
        text-shadow: 0 1px 8px rgba(0,0,0,0.25);
    }
  
    .altitude {
        width: max-content;
        opacity: 0.9;
        margin: 0;
        font-weight: $bold;
        font-size: clamp(24px, 4vw, 40px);
        text-shadow: 0 1px 8px rgba(0,0,0,0.25);
    }

    @media (max-width: 430px) {
        .hero {
            height: calc(100dvh - 60px);
            position: relative;
        }
        .hero-content {
            display: flex;
            justify-content: space-between;
            padding: 0 24px;
        }
        .hero-content .left {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .hero-content .right { 
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }
        .cta{
            position: absolute;
            left: 50%;
            bottom: 40px;
            transform: translateX(-50%);

            font-size: 20px;
            text-underline-offset: 8px;
        }
    }
</style>  