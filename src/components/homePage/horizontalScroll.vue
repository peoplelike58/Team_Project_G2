<template>
    <div class="stack-wrap" ref="wrapRef">
        <article
            v-for="(card, i) in items"
            :key="i"
            class="card"
            :class="card.class"
            :style="{ zIndex: i + 1 }"
            ref="cardRefs"
        >
            <div class="card-inner">
                <h2>{{ card.title }}</h2>
                <p>{{ card.text }}</p>
            </div>
        </article>
    </div>
</template>
  
<script setup>
    import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue'
    import gsap from 'gsap'
    import ScrollTrigger from 'gsap/ScrollTrigger'
    gsap.registerPlugin(ScrollTrigger)
  
    const props = defineProps({
    
        items: {
            type: Array,
            default: () => ([
                { title: '卡片 A', text: '滑到最左側時保持原位。', class: 'c1' },
                { title: '卡片 B', text: '向左滑動並疊在 A 上面。', class: 'c2' },
                { title: '卡片 C', text: '最後疊到最上層（在 B 上面）。', class: 'c3' },
            ])
        },
        height: { type: String, default: '90vh' } // 方便外部改高度
    })
  
    const wrapRef = ref(null)
    const cardRefs = ref([]) // 會自動收集 v-for 的 DOM
    let tl = null //timeline
    let onResize = null
  
    onMounted( async () => {

        await nextTick()
        const cards = cardRefs.value
  
        // 對卡片進行初始設置（好像寫不寫這段都沒差）
        /* for (let i = 0; i < cards.length; i++) {
            gsap.set(cards[i], { xPercent: 100, force3D: true })
        } */
  
        // 時間軸：pin 住容器；每一張卡片各用 1 段捲動距離
        tl = gsap.timeline({
            defaults: { ease: 'none' },
            scrollTrigger: {
                trigger: wrapRef.value,
                start: 'top top',
                end: () => '+=' + (window.innerWidth * cards.length),
                /* 這段計算在幹嘛
                '+=' + 數字 → GSAP 語法，意思是「相對於 start 再多滑這麼多 px 才結束
                window.innerWidth → 取得當前視窗的寬度（單位是 px）
                cards.length → 卡片的數量
                window.innerWidth * cards.length →
                代表「要橫向滑動的總距離」= 每張卡片一個視窗寬 × 要滑動的卡片數量 */
                scrub: true,
                pin: true,
                anticipatePin: 1
            }
        })
  
        // 讓第 2、3 張依序從右滑到左（0%），並保持堆疊
        for (let i = 0; i < cards.length; i++) {
            tl.fromTo(cards[i], { xPercent: 100 - ((3-i)*10) }, { xPercent: (0 + i*10) })
        }
  
        // 視窗尺寸變更時刷新（避免旋轉或改寬度導致 end 不準）
        onResize = () => {
            tl?.scrollTrigger?.refresh()
        }

        window.addEventListener('resize', onResize, { passive: true })
    })
  
    onBeforeUnmount(() => {
        window.removeEventListener('resize', onResize)
        tl?.scrollTrigger?.kill()
        tl?.kill()
    })
</script>
  
<style scoped>
    :root { --h: 70vh; }
    .spacer {
        height: 80vh;
        display: grid;
        place-items: center;
        color: #666;
    }
  
    /* 主要容器 */
    .stack-wrap {
        position: relative;
        height: v-bind('height');     /* 允許用 prop 調整 */
        overflow: hidden;             /* 防止溢出與閃爍 */
        background: #0f172a;
        border-radius: 20px;
        margin: 8vh auto;
        width: min(1100px, 92vw);
        box-shadow: 0 10px 30px rgba(0,0,0,.2);
        will-change: transform;
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
        transform: translateZ(0); /* 減少滾動時的閃爍 */
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

    .card h2 { margin: 0; font-size: clamp(24px, 4vw, 36px); }
    .card p  { margin: 0; color: #334155; line-height: 1.6; }
  
    .c1 { background: linear-gradient(135deg,#60a5fa,#a78bfa); }
    .c2 { background: linear-gradient(135deg,#34d399,#22d3ee); }
    .c3 { background: linear-gradient(135deg,#f59e0b,#ef4444); }
</style>  