<template>
    <div class="stack-wrap" ref="wrapRef">
        <article class="card card1" ref="card1Ref">
            <div class="badge">BRAND PHILOSOPHY</div>
            <div class="card-inner-1">
                    <div class="card1-left-col">
                        <h1 class="title">山上見</h1>
                        <h3 class="subtitle">SEE YOU UP THERE</h3>
                    </div>
                    <div class="card1-right-col">
                        <h3 class="content-title">山上見，讓登山更親近</h3>
                        <p class="content-text">為每一個想親近山林的人而生，我們相信，登山不該只是經驗者的專利，而是每個人都能享受的生活方式。從完整的山岳介紹，帶你了解山林故事與自然之美，到智慧化的路線規劃，幫助你選擇最適合的難度與行程，甚至揪團功能，讓你不再孤單，一起找同伴共享登山的樂趣。不論你是初學者，還是想挑戰更高峰的老手，我們都為你準備好一切。輕鬆開啟你的第一步，一起走入山林，重新連結自己與大自然，我們山上見！</p>
                    </div>
            </div>
        </article>
        <article class="card card2" ref="card2Ref">
            <div class="badge">SEE YOU UP THERE</div>
            <div class="card-inner-2">
                <figure class="polaroid left-tilt">
                    <img src="./img/Mahamayan.jpg"/>
                </figure>

                <!-- 中間直書文案 -->
                <div class="vertical-text">
                    上山去，留下你與山的合影
                </div>

                <!-- 右側拍立得 -->
                <figure class="polaroid right-tilt">
                    <img src="./img/patungkuonu.png"/>
                </figure>
            </div>
        </article>
        <article class="card card3" ref="card3Ref">
            <div class="badge">PLANT YOUR FLAG</div>
            <div class="card-inner-3">
                <h3 class="content-title">「 插上你的旗幟，寫下你的專屬百岳篇章 」</h3>
            </div>
        </article>
    </div>
</template>
  
<script setup>
    import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue'
    import gsap from 'gsap'
    import ScrollTrigger from 'gsap/ScrollTrigger'
    gsap.registerPlugin(ScrollTrigger)

    const wrapRef = ref(null)
    const card1Ref = ref(null)
    const card2Ref = ref(null)
    const card3Ref = ref(null)
  
    let scrollTimeline = null
    let resizeHandler = null
  
    onMounted(async () => {
        await nextTick()
  
        const cardsInOrder = [card1Ref.value, card2Ref.value, card3Ref.value]
  
        // 初始：第一張保持原位；第二、三張先放在右側螢幕外
        gsap.set([card2Ref.value, card3Ref.value], { xPercent: 100, force3D: true })
        gsap.set(cardsInOrder, { willChange: 'transform' })
  
        // 時間軸：pin 容器；總捲動距離 = 需要滑入的卡片數（2 張） × 視窗寬
        scrollTimeline = gsap.timeline({
            defaults: { ease: 'none' },
            scrollTrigger: {
            trigger: wrapRef.value,
            start: 'top top',
            end: () => '+=' + (window.innerWidth * (cardsInOrder.length - 1)),
            scrub: true,
            pin: true,
            anticipatePin: 1
            }
        })
  
        // 第二張、第三張控制
        scrollTimeline
            .fromTo(card2Ref.value, { xPercent: 88 }, { xPercent: 6 })
            .fromTo(card3Ref.value, { xPercent: 94 }, { xPercent: 12 })
  
        // 視窗尺寸變更時，刷新計算，確保 end 正確
        resizeHandler = () => {
            scrollTimeline?.scrollTrigger?.refresh()
        }
         window.addEventListener('resize', resizeHandler, { passive: true })
    })
  
    onBeforeUnmount(() => {
        window.removeEventListener('resize', resizeHandler)
        scrollTimeline?.scrollTrigger?.kill()
        scrollTimeline?.kill()
    })
</script>
  
<style scoped lang="scss">
@import '@/assets/styles/main.scss';

/* 主要容器 */
.stack-wrap {
    position: relative;

    width: 100%;
    max-width: 1200px;
    height: 100vh;
    margin: 0 auto 160px;
    
    border-radius: 8px;
    box-shadow: 0 8px 33px rgba(0,0,0,0.1);
    overflow: hidden;
}

/* 卡片層 */
.card {
    position: absolute;
    inset: 0;

    display: grid;
    place-items: center;
    
    width: 100%;
    height: 100%;
    border-radius: 8px;
    
    backface-visibility: hidden;
    transform: translateZ(0);
    overflow: hidden;
}

.title {
    font-size: $pcFont-bigTitle-l;
    font-weight: $bold;
}

.subtitle {
    font-size: $pcFont-H3;
    font-weight: $bold;
}

.content-title{ 
    font-size: $pcFont-H3;
    font-weight: $bold;
}

.content-text {
    font-weight: $semiBold;
    line-height: $lineHeight-p-200;
}

.card-inner-1 {
    position: relative;
    display: flex;
    align-self: stretch;

    padding: 120px;
    padding-right: 200px;
    justify-content: space-between;
}

.card-inner-2{
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;

    width: 100%;
    padding: 0 240px 0 160px;

    box-sizing: border-box;
}

.card-inner-3{
    position: relative;
    width: 100%;
    height: 100%;
}

.card-inner-3 .content-title{
    position: absolute;
    right: 20%;
    bottom: 12%;

    color: aliceblue;
}

.badge{
    position: absolute;
    top: 120px;
    left: 20px;

    writing-mode: vertical-lr;

    font-size: $pcFont-H3;
    font-weight: $semiBold;
    font-style: italic;
}

.card1-left-col {
    display: flex;
    flex-direction: column;
    align-self: flex-start;
    gap: 16px;

    width: fit-content;
}

.card1-right-col {
    display: flex;
    flex-direction: column;
    align-self: flex-end;
    gap: 16px;

    width: 50%;
}

/* 拍立得卡片 */
.polaroid {
    width: 280px;
    background: #fff;
    border-radius: 8px;
    padding: 12px 12px 72px; /* 下方留白像拍立得 */
    box-shadow:
      0 16px 32px rgba(0,0,0,0.15),
      0 4px 8px rgba(0,0,0,0.08);
    display: grid;
    place-items: center;
    user-select: none;
}
.polaroid img {
    display: block;
    width: 100%;
    height: 320px;
    border-radius: 4px;
    object-fit: cover;
}

.left-tilt  { transform: rotate(-8deg); }
.right-tilt { transform: rotate(10deg); }

.vertical-text {
    writing-mode: vertical-lr;
    text-orientation: upright;
    font-weight: $bold;
    font-size: $pcFont-H3;
    letter-spacing: 0.5rem;
    line-height: $lineHeight-p-200;
    user-select: none;
}

/* 每張的背景與層級 */
.card1 {
    background-color: $bg-gray;
    z-index: 1;
}
.card2 {
    background: #fff;
    z-index: 2;
}
.card3 {
    background-image: url(./img/FlagMan_1.png);
    background-repeat: no-repeat;
    background-position: center;
    background-size: cover;
    z-index: 3;
}

@media (max-width: 768px) {
    .stack-wrap{
        box-shadow: none;
        height: 100dvh;
    }
    .stack-wrap .badge {
        display: none;
    }
    .stack-wrap .card {
        margin-top: 60px;
    }
    .card-inner-1 {
        flex-direction: column;
        justify-content: center;
        gap: 32px;

        padding: 0 40px;
    }
    .card1-left-col .title {
        font-size: $pcFont-bigTitle-m;
    }
    .card1-right-col {
        align-self: flex-start;
        width: 86%;
    }
    .card1-right-col .content-title {
        display: none;
    }
    .card-inner-2 {
        flex-direction: column;
        gap: 48px;
        width: 80%;
        padding: 0 64px 0 24px;
    }
    .card-inner-2 .vertical-text {
        writing-mode: horizontal-tb;
        font-size: $pcFont-H4;
        letter-spacing: 0.2rem;
    }
    .card-inner-2 .polaroid {
        width: 168px;
        padding: 8px 8px 48px;

        border-radius: 4px;
    }
    .card-inner-2 .polaroid img {
        height: 192px;
    }
    .card-inner-2 .left-tilt {
        align-self: flex-start;
    }
    .card-inner-2 .right-tilt {
        align-self: flex-end;
    }
    .card-inner-3 .content-title {
        font-size: $pcFont-H4;
        line-height: $lineHeight-p-150;
        padding: 0 24px;
        bottom: 20%;
    }
}
@media (max-width: 430px) {
    .stack-wrap .card {
        height: 86%;
    }
    .card-inner-3 .content-title {
        right: 12%;
        bottom: 12%;
    }
}
</style>