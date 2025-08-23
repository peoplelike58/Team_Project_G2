<template>
    <div class="stack-wrap" ref="wrapRef">
        <article class="card card1" ref="card1Ref">
            <div class="card-inner">
                <h2>卡片 A</h2>
                <p>滑到最左側時保持原位。</p>
            </div>
        </article>
        <article class="card card2" ref="card2Ref">
            <div class="card-inner">
                <h2>卡片 B</h2>
                <p>向左滑動並疊在 A 上面。</p>
            </div>
        </article>
        <article class="card card3" ref="card3Ref">
            <div class="card-inner">
                <h2>卡片 C</h2>
                <p>最後疊到最上層（在 B 上面）。</p>
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
  
        // 初始：第一張保持原位；第二、三張先放在右側螢幕外，開啟 3D 可避免閃爍
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
  
        // 依序把第二張、第三張從右滑入到 0%，疊在上層（z-index 由 CSS 控制）
        scrollTimeline
            .fromTo(card2Ref.value, { xPercent: 80 }, { xPercent: 0 })
            .fromTo(card3Ref.value, { xPercent: 90 }, { xPercent: 0 })
  
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
@import '../../assets/styles/main.scss';

:root {
    --h: 70vh;
}

/* 主要容器 */
.stack-wrap {
    position: relative;
    height: 100vh;
    /* 允許用 prop 調整 */
    overflow: hidden;
    /* 防止溢出與閃爍 */
    background: #0f172a;
    border-radius: 20px;
    margin: 8vh auto;
    width: min(1100px, 92vw);
    box-shadow: 0 10px 30px rgba(0,0,0,.2);
}

/* 卡片層（全部絕對定位疊在一起） */
.card {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    display: grid;
    place-items: center;
    padding: 24px;
    border-radius: 20px;
    backface-visibility: hidden;
    transform: translateZ(0);
}

.card-inner {
    width: min(920px, 86%);
    height: 80%;
    border-radius: 16px;
    background: rgba(255,255,255,.92);
    box-shadow: 0 10px 24px rgba(0,0,0,.15);
    display: grid;
    gap: 12px;
    padding: 28px;
    text-align: center;
}

.card h2 {
    margin: 0;
    font-size: clamp(24px, 4vw, 36px);
}
.card p {
    margin: 0;
    color: #334155;
    line-height: 1.6;
}

/* 依需求調整每張的背景與層級：後面的覆蓋前面的 */
.card1 {
    background-color: $bg-gray;
    z-index: 1;
}
.card2 {
    background-color: $bg-pink-100;
    z-index: 2;
}
.card3 {
    background-color: $ivory-gray-100;
    z-index: 3;
}

</style>