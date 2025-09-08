<script setup>
import { onMounted, onBeforeUnmount, ref } from 'vue'

const vantaRef = ref(null)
let vantaEffect = null

onMounted(() => {
    // 初始化 Vanta 效果
    vantaEffect = VANTA.CLOUDS({
        el: vantaRef.value,
        mouseControls: true,
        touchControls: true,
        gyroControls: false,
        minHeight: 200.00,
        minWidth: 200.00,
        speed: 0.80,
        THREE,
    })
})

onBeforeUnmount(() => {
    // 銷毀
    if (vantaEffect) vantaEffect.destroy()
})

</script>

<template>
    <div ref="vantaRef" class="wrapper">
        <div class="btn-row">
            <RouterLink to="/homepage" class="view-btn has-badge">
                <span class="label">前台</span>
                <svg class="badge-svg" viewBox="0 0 200 200" aria-hidden="true">
                    <defs>
                        <path id="circlePath-home" d="M100,100 m-80,0 a80,80 0 1,1 160,0 a80,80 0 1,1 -160,0"/>
                    </defs>
                    <g class="badge-text">
                        <text>
                            <textPath href="#circlePath-home" startOffset="0%">
                                MOUNTAIN PEAK • SEE YOU UP THERE •
                            </textPath>
                        </text>
                    </g>
                </svg>
            </RouterLink>
            <RouterLink to="/backlogin" class="view-btn has-badge">
                <span class="label">後台</span>
                <svg class="badge-svg" viewBox="0 0 200 200" aria-hidden="true">
                    <defs>
                        <path id="circlePath-login" d="M100,100 m-80,0 a80,80 0 1,1 160,0 a80,80 0 1,1 -160,0"/>
                    </defs>
                    <g class="badge-text">
                        <text>
                            <textPath href="#circlePath-login" startOffset="0%">
                                MOUNTAIN PEAK • SEE YOU UP THERE •
                            </textPath>
                        </text>
                    </g>
                </svg>
            </RouterLink>
        </div>
        <div class="bot-container">
            <img class="logo" src="/images/Logo.png">
            <div class="copy-right">
                本網站為 緯育TibaMe_前端工程師班 第96期 學員專題成果作品，本平台僅供學習，展示之用，若有抵觸有關著作權，或有第三人主張侵害智慧財產權等情事，均由學員負法律上責任，緯育公司概不負責。若有侵權疑慮，您可以私訊 [ 緯育TibaMe ]，後續會由專人協助處理。
            </div>
        </div>
    </div>
</template>


<style scoped lang="scss">
@import '@/assets/styles/main.scss';
.wrapper{
    display: flex;
    align-items: center;
    position: relative;

    width:100%;
    height:100vh;
}

.btn-row{
    display: flex;
    gap: 20vw;
    margin: 0 auto;
}

.bot-container{
    position: absolute;
    bottom: 40px;

    display: flex;
    justify-content: space-between;
    width: 100%;
    padding: 0 40px;
    box-sizing: border-box;     
}

.logo{
    width: 80px;
    height: 80px;
    aspect-ratio: 1/1;
}

.view-btn{
    position: relative;

    color: aliceblue;
    font-size: $pcFont-H3;
    font-weight: $bold;
    text-decoration: none;
    text-shadow: 0 1px 8px rgba(0,0,0,0.25);
}

.copy-right{
    width: 50%;
    color: aliceblue;
    font-size: 14px;
    line-height: $lineHeight-p-200;
}

//---------------------------------

/* 基本按鈕 */
.view-btn {
    position: relative;
    display: grid;
    place-items: center;
    width: 200px;
    aspect-ratio: 1/1;
    border-radius: 50%;
    text-decoration: none;
}

/* 中央文字 */
.view-btn .label {
    position: relative;
    z-index: 1;
    font-size: $pcFont-H3;
    font-weight: $bold;
}

/* 外圈文字 */
.badge-svg {
    position: absolute;
    inset: -6px;
    pointer-events: none;
    opacity: 0;
    transform: scale(0.9);
    transition: opacity 0.4s ease, transform 0.4s ease;
}

/* hover */
.view-btn:hover .badge-svg {
    opacity: 1;
    transform: scale(1);
}

.badge-text {
    animation: spin 12s linear infinite;
    transform-origin: 100px 100px;
}

.badge-text text {
    font: $bold 20px 'Inter';
    letter-spacing: 3px;
    text-transform: uppercase;
    fill: aliceblue; /* 外圈顏色 */
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

@media (max-width: 430px) {
    .wrapper{
        box-sizing: border-box;
        align-items: flex-start;
    }
    .btn-row{
        flex-direction: column;
        gap: 64px;
        margin-top: 10dvh;
    }
    .bot-container {
        flex-direction: column;
        gap: 32px;
        padding: 0 24px;
    }
    .copy-right {
        width: 100%;
    }
    .badge-svg {
        opacity: 1;
    }
}
</style>