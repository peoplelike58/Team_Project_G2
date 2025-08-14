<template>
    <section ref="wrapEl" class="h-wrap">
      <div ref="trackEl" class="h-track">
        <section
          v-for="(it, i) in items"
          :key="i"
          class="h-panel"
          :style="{ zIndex: i + 1 }"
          :data-index="i"
        >
          <!-- 背景圖 -->
          <div class="bg" :style="{ backgroundImage: `url(${it.img})` }"></div>
          <!-- 暗化 / 模糊層（非當前頁會顯示） -->
          <div class="dim"></div>
  
          <!-- 左側垂直色條＋直書標題 -->
          <aside class="side-rail" :style="{ background: it.barColor }">
            <div class="rail-inner">
              <span class="rail-title">{{ it.label }}</span>
              <span class="rail-sub">AREA</span>
              <span class="rail-note">SCROLL</span>
            </div>
          </aside>
  
          <!-- 內容區（示意卡片） -->
          <div class="content">
            <h1 class="hero-title">
              {{ it.label }} <small>Area</small>
            </h1>
            <div class="cards">
              <article class="card" v-for="n in 3" :key="n">
                <div class="thumb"></div>
                <h3 class="caption">項目 {{ n }} ＞</h3>
              </article>
            </div>
          </div>
        </section>
      </div>
    </section>
  
    <section class="after">下段內容</section>
  </template>
  
  <script setup>
  import { ref, onMounted, onUnmounted, nextTick } from "vue";
  import gsap from "gsap";
  import { ScrollTrigger } from "gsap/ScrollTrigger";
  gsap.registerPlugin(ScrollTrigger);
  
  /** Demo 資料：改成你的圖片與色條色彩 */
  const items = [
    {
      label: "STAY",
      img: "https://images.unsplash.com/photo-1505692794403-34d4982f88aa?q=80&w=1600",
      barColor: "#1e90ff",
    },
    {
      label: "EAT",
      img: "https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=1600",
      barColor: "#ffbe3c",
    },
    {
      label: "PLAY",
      img: "https://images.unsplash.com/photo-1512428559087-560fa5ceab42?q=80&w=1600",
      barColor: "#ef476f",
    },
    {
      label: "RELAX",
      img: "https://images.unsplash.com/photo-1519455953755-af066f52f1ea?q=80&w=1600",
      barColor: "#06d6a0",
    },
  ];
  
  const wrapEl = ref(null);
  const trackEl = ref(null);
  
  let hTween;
  let triggers = [];
  
  onMounted(async () => {
    await nextTick();
  
    const panels = Array.from(trackEl.value.querySelectorAll(".h-panel"));
    const total = panels.length;
  
    // 抗閃爍：強制走合成層
    gsap.set([trackEl.value, panels], { force3D: true, z: 0 });
  
    // 主橫向位移動畫：固定外層，讓 track 橫移 (total - 1) 個視窗寬
    hTween = gsap.to(trackEl.value, {
      xPercent: -100 * (total - 1),
      ease: "none",
      force3D: true,
      scrollTrigger: {
        trigger: wrapEl.value,
        start: "top top",
        end: () => "+=" + window.innerWidth * (total - 1),
        scrub: true,
        pin: true,
        pinType: "transform",
        anticipatePin: 1,
      },
    });
  
    // 逐面板：到左邊時 pin 住、不留空隙 → 疊放；同時控制「當前頁」樣式
    panels.forEach((panel, i) => {
      const st = ScrollTrigger.create({
        trigger: panel,
        containerAnimation: hTween,
        start: "left left",
        end: "+=100%",
        pin: true,
        pinSpacing: false,
        anticipatePin: 1,
        onEnter: () => setActive(i, true),
        onEnterBack: () => setActive(i, true),
        onLeave: () => setActive(i, false),
        onLeaveBack: () => setActive(i - 1, true), // 回捲時上一張成為 active
      });
      triggers.push(st);
    });
  
    // 預設第一張為 active
    setActive(0, true);
  });
  
  onUnmounted(() => {
    hTween?.scrollTrigger?.kill();
    hTween?.kill?.();
    triggers.forEach((t) => t.kill());
    triggers = [];
  });
  
  /** 切換 active 狀態，影響 dim/blur 與內容顯示強度 */
  function setActive(index, isActive) {
    const p = trackEl.value?.querySelector(`.h-panel[data-index="${index}"]`);
    if (!p) return;
    p.classList.toggle("is-active", !!isActive);
  }
  </script>
  
  <style scoped>
  /* 版面骨架 */
  .h-wrap {
    position: relative;
    height: 100dvh;
    overflow: hidden;
    -webkit-transform: translateZ(0);
    transform: translateZ(0);
  }
  .h-track {
    display: flex;
    height: 100%;
    will-change: transform;
    -webkit-backface-visibility: hidden;
    backface-visibility: hidden;
  }
  .h-panel {
    position: relative;
    min-width: 100vw;
    height: 100dvh;
    overflow: hidden;
  }
  
  /* 背景圖 */
  .bg {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    transform: translateZ(0) scale(1.07); /* 微放大避免移動時邊緣露白 */
  }
  
  /* 非當前頁的暗化與模糊（當前頁 .is-active 會移除） */
  .dim {
    position: absolute;
    inset: 0;
    background: rgba(8, 12, 20, 0.45);
    backdrop-filter: blur(2px);
    transition: 300ms ease;
  }
  .h-panel.is-active .dim {
    background: rgba(8, 12, 20, 0.2);
    backdrop-filter: blur(0.5px);
  }
  
  /* 左側垂直色條＋直書文字 */
  .side-rail {
    position: absolute;
    left: 0;
    top: 0;
    width: clamp(56px, 6vw, 88px);
    height: 100%;
    display: grid;
    place-items: center;
    box-shadow: 0 0 0 1px rgba(0,0,0,.08) inset;
  }
  .rail-inner {
    display: grid;
    align-items: center;
    gap: 10vh;
    writing-mode: vertical-rl;     /* 直書 */
    text-orientation: mixed;
    letter-spacing: 0.08em;
    font-family: Inter, system-ui, -apple-system, "Noto Sans TC", sans-serif;
    color: #0b0b0b;
    filter: drop-shadow(0 0 2px rgba(255,255,255,.3));
  }
  .rail-title {
    font-weight: 800;
    font-size: clamp(16px, 2.6vw, 28px);
  }
  .rail-sub {
    font-weight: 700;
    opacity: 0.8;
    font-size: clamp(12px, 1.4vw, 14px);
  }
  .rail-note {
    margin-top: auto;
    font-weight: 700;
    letter-spacing: 0.3em;
    font-size: clamp(11px, 1.2vw, 12px);
  }
  
  /* 內容層：置中大標題 + 三張卡片 */
  .content {
    position: relative;
    height: 100%;
    padding-left: clamp(72px, 7vw, 112px); /* 避開側欄 */
    display: grid;
    grid-template-rows: 1fr auto 1fr;
    align-items: center;
  }
  .hero-title {
    margin: 0;
    padding-left: 6vw;
    font: 800 clamp(32px, 9vw, 132px)/1 Inter, system-ui, "Noto Sans TC", sans-serif;
    color: #fff;
    text-shadow: 0 2px 12px rgba(0,0,0,.35);
  }
  .hero-title small {
    font: 700 clamp(18px, 3vw, 36px)/1.2 Inter, system-ui;
    color: #ffe66d;
    margin-left: .6ch;
  }
  
  /* 卡片列（示意，可換成你自己的卡片元件） */
  .cards {
    display: grid;
    grid-auto-flow: column;
    grid-auto-columns: minmax(260px, 28vw);
    gap: clamp(16px, 2vw, 24px);
    padding: 0 6vw 6vh 6vw;
  }
  .card {
    background: rgba(255,255,255,.1);
    border-radius: 16px;
    padding: 12px;
    box-shadow: 0 6px 24px rgba(0,0,0,.25);
    backdrop-filter: blur(4px);
    border: 1px solid rgba(255,255,255,.25);
  }
  .card .thumb {
    height: clamp(120px, 18vw, 220px);
    border-radius: 12px;
    background: rgba(255,255,255,.6);
  }
  .card .caption {
    margin: 10px 8px 6px;
    color: #eaf2ff;
    font: 700 clamp(14px, 1.6vw, 20px)/1.3 Inter, system-ui, "Noto Sans TC", sans-serif;
  }
  
  /* 後續內容 */
  .after {
    height: 100dvh;
    display: grid;
    place-items: center;
    font: 700 clamp(24px, 6vw, 72px)/1 Inter, system-ui;
  }
  </style>