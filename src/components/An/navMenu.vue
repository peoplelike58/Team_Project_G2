<template>
    <header class="site-header">
        <div class="menu-toggle" @click="toggle">
            <span class="menu-text">MENU</span>
            <span class="burger" :class="{ open: isOpen }">
                <span class="line"></span>
                <span class="line"></span>
                <span class="line"></span>
            </span>
        </div>
    </header>
    <div class="overlay" v-show="isOpen" @click="close" ref="overlayRef"></div>
    <aside class="menu-panel" ref="panelRef" v-show="true">
        <div class="panel-close" @click="close">
            <span class="menu-text">MENU</span>
            <span class="burger" :class="{ open: isOpen }">
                <span class="line"></span>
                <span class="line"></span>
                <span class="line"></span>
            </span>
        </div>
        <div class="menu-content">
            <div class="left-col">
                <img class="logo" src="./img/Logo.png" alt="">
                <div class="about">
                    <p>山上見為每一個想親近山林的人而生，我們相信，登山不該只是經驗者的專利，而是每個人都能享受的生活方式。<br><br>輕鬆開始你的第一步，一起走入山林，重新連結自己與大自然，我們山上見！</p>
                </div>
            </div>
            <nav class="right-col">
                <ul class="menu-list">
                    <li v-for="(item, index) in menuItems" :key="index" class="menu-item">
                        <div class="menu-link" type="button" @click="go(item);routerTo(item)">{{ item.label }}</div>
                        <!--  -->
                    </li>
                </ul>
                <div class="copyright">© 2025 MountainPeak.</div>
            </nav>
        </div>
    </aside>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'//
import gsap from 'gsap'
const router = useRouter() //

const isOpen = ref(false)
const panelRef = ref(null)
const overlayRef = ref(null)
function routerTo(item){  //
  router.push(`/${item.path}`);
}

const menuItems = [
    { label: '百岳之書', path: 'peaks' },
    { label: '揪上山', path: 'together' },
    { label: '路線規劃', path: 'routes' },
    { label: '山腳雜貨店', path: 'shop' },
    { label: '會員登入', path: 'login' },
]

let gsapTimeline

onMounted(() => {
    const panel = panelRef.value
    const overlay = overlayRef.value
    const items = panel.querySelectorAll('.menu-item')

    // 初始狀態：面板不可點、透明；遮蓋層覆蓋；清單待入場
    gsap.set(panel,   { opacity: 0, pointerEvents: 'none' })
    gsap.set(overlay, { autoAlpha: 0 })
    gsap.set(items,   { x: 20, autoAlpha: 0 })

    gsapTimeline = gsap.timeline({ paused: true, defaults: { ease: 'power2.out' } })
        .set(panel,  { pointerEvents: 'auto' }, 0)
        .to(overlay, { autoAlpha: 1, duration: 0.4 }, 0)
        .to(panel,   { opacity: 1, duration: 0.4 }, 0)
        .to(items,   { x: 0, autoAlpha: 1, duration: 0.4, stagger: 0.1 }, 0.16)

    gsapTimeline.eventCallback('reverseComplete', () => {
        gsap.set(panel, { opacity: 0, pointerEvents: 'none' }) // 關閉後不吃點擊
    })
})


function toggle() {
    isOpen.value = !isOpen.value
    if (gsapTimeline) (isOpen.value ? gsapTimeline.play() : gsapTimeline.reverse())
}
function close() {
    isOpen.value = false
    if (gsapTimeline) gsapTimeline.reverse()
}
function go(item) {
    close()
}
</script>

<style scoped lang="scss">
@import '../../assets/styles/main.scss';

/* Header */
.site-header{ 
    display: flex; 
    justify-content: end; 
    align-items: center;

    position: sticky;
    top: 0; 
    z-index: 20; 
    padding: 16px 40px;

    background: transparent; 
}
.menu-toggle{ 
    display: inline-flex; 
    align-items: center; 
    gap: 16px;

    font-size: $pcFont-H4;
    font-weight: $medium;
    letter-spacing: 2px;

    background: transparent;

    cursor: pointer;
}
.burger{ 
    position: relative; 
    width: 64px; 
    height: 20px; 
}
.burger .line{ 
    position: absolute; 
    left: 0; 
    right: 0; 
    height: 1px; 
    background: $black-14;
    transform-origin: center; 
    transition: transform 0.3s, opacity 0.3s; 
}
.burger .line:nth-child(1){ top: 0; } 
.burger .line:nth-child(2){ top: 50%; } 
.burger .line:nth-child(3){ bottom: 0; }

.burger.open .line:nth-child(1){ transform: translateY(10px) rotate(20deg); }
.burger.open .line:nth-child(2){ opacity: 0; }
.burger.open .line:nth-child(3){ transform: translateY(-9px) rotate(-20deg); }

/* Overlay */
.overlay{ 
    position: fixed; 
    inset: 0;
    z-index: 28; 
}

/* Panel */
.menu-panel{
    display: flex; 
    flex-direction: column;
    
    position: fixed;
    inset: 0; 
    z-index: 30;
    
    background: $ivory-gray-100;

    overflow: hidden;
    pointer-events: none;
}

/* 右上角 Menu 選單 */
.panel-close{
    display: inline-flex;
    align-items: center;
    gap: 16px;

    position: absolute;
    top: 16px;
    right: 40px;
    z-index: 2;

    background: transparent;
    font-size: $pcFont-H4;
    font-weight: $medium;
    letter-spacing: 2px;
    cursor: pointer;
}

/* 內容 */
.menu-content{ 
    display: flex; 
    justify-content: space-between; 
    align-items: flex-end; 
    gap: auto; 
    
    width: 100%; 
    height: 100%;
    padding:0 160px 100px 120px;
    
    box-sizing: border-box;
    z-index: 0;
}

/* 左右欄 */
.left-col{ 
    display: flex; 
    align-items: center; 
    gap: 64px;
}

.logo{
    width: 160px;
    height: auto;
}

.about {
    max-width: 480px;
}
.about p{  
    font-weight: $medium;
    line-height: $lineHeight-p-200;
}
.copyright{ 
    font-size: 14px;
}

.right-col{ 
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 80%;
}

.menu-list{ 
    display: flex;
    flex-direction: column;
    gap: 32px;

    list-style: none;
}

.menu-link{
    font-weight: $bold;
    transition: opacity 0.3s ease;
    cursor: pointer;
}

.menu-link:hover{ opacity: 0.7; }

/* RWD */
@media (max-width:1024px){
  .menu-content{ padding-top:80px; flex-direction:column-reverse; gap:32px; }
  .right-col{ justify-content:flex-start; }
  .left-col{ gap:16px; }
  .stamp .seed{ width:78px; height:98px; font-size:11px; }
}
</style>