<!-- src/components/FooterSection.vue -->
<template>
    <footer class="site-footer">
        <div class="footer-main">
            <!-- 左側：logo + 標語 -->
            <div class="footer-left">
                <router-link to="/homepage">
                    <img src="@/assets/images/Logo.png" class="logo"/>
                </router-link>
                <div class="brand-text">
                    <div class="copyright">© 2025 MountainPeak.</div>
                </div>
            </div>

            <!-- 右側：日文簡介 -->
            <div class="footer-right">
                <p>山は距離ではなく、出会いと旅の始まり。「山で待ってるよ！いっしょに行こう！」それは私たちの合言葉。山であなたを待ち、共に歩き、新しい景色を分かち合う。初めての一歩でも、挑戦を求める旅でも、山上見はいつもあなたの隣に。</p>
                <div class="icon-wrapper">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3.33337 16.0002C3.33337 10.0295 3.33337 7.04283 5.18804 5.18816C7.04271 3.3335 10.028 3.3335 16 3.3335C21.9707 3.3335 24.9574 3.3335 26.812 5.18816C28.6667 7.04283 28.6667 10.0282 28.6667 16.0002C28.6667 21.9708 28.6667 24.9575 26.812 26.8122C24.9574 28.6668 21.972 28.6668 16 28.6668C10.0294 28.6668 7.04271 28.6668 5.18804 26.8122C3.33337 24.9575 3.33337 21.9722 3.33337 16.0002Z" stroke="#253237" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M23.344 8.66699H23.3307M22 16.0003C22 17.5916 21.3679 19.1177 20.2426 20.243C19.1174 21.3682 17.5913 22.0003 16 22.0003C14.4087 22.0003 12.8826 21.3682 11.7574 20.243C10.6321 19.1177 10 17.5916 10 16.0003C10 14.409 10.6321 12.8829 11.7574 11.7577C12.8826 10.6325 14.4087 10.0003 16 10.0003C17.5913 10.0003 19.1174 10.6325 20.2426 11.7577C21.3679 12.8829 22 14.409 22 16.0003Z" stroke="#253237" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M30.0534 8.56016C29.8868 7.93654 29.5597 7.36745 29.1047 6.90963C28.6497 6.4518 28.0826 6.12122 27.46 5.95083C25.1734 5.3335 16 5.3335 16 5.3335C16 5.3335 6.82671 5.3335 4.54004 5.95083C3.91745 6.12122 3.35039 6.4518 2.89537 6.90963C2.44035 7.36745 2.11326 7.93654 1.94671 8.56016C1.33337 10.8642 1.33337 15.6668 1.33337 15.6668C1.33337 15.6668 1.33337 20.4695 1.94671 22.7735C2.11306 23.3974 2.44006 23.9667 2.89509 24.4248C3.35012 24.8829 3.91729 25.2137 4.54004 25.3842C6.82804 26.0002 16 26.0002 16 26.0002C16 26.0002 25.1734 26.0002 27.46 25.3842C28.0828 25.2137 28.65 24.8829 29.105 24.4248C29.56 23.9667 29.887 23.3974 30.0534 22.7735C30.6667 20.4722 30.6667 15.6668 30.6667 15.6668C30.6667 15.6668 30.6667 10.8642 30.0534 8.56016ZM13 20.0282V11.3068L20.6667 15.6682L13 20.0282Z" stroke="#253237" stroke-width="1.5" stroke-miterlimit="10" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="footer-note">© 本網站為緯育TibaMe【第102期】前端工程師專業技術養成班學員作品，僅供學習、展示之用途。</div>
    </footer>
</template>
  
<script setup>
    import { RouterLink } from 'vue-router';
</script>
  
<style scoped lang="scss">
@import '@/assets/styles/main.scss';

.site-footer {
    display: flex;
    flex-direction: column;
    align-items: center;

    background: #fff;
}
  
.footer-main {
    display: flex;
    justify-content: space-between;
    width: 100%;
    max-width: 1200px;
    padding: 64px;
    margin: 0 auto;
    box-sizing: border-box;
}
  
/* 左側 */
.footer-left {
    display: flex;
    flex-direction: column;
    gap: 40px;
}
.logo {
    width: 160px;
    aspect-ratio: 1/1;
}
.brand-text {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.copyright {
    font-size: $pcFont-H4;
}

.footer-note {
    width: 100%;
    padding: 20px;
    font-size: 14px;
    color: #666;
    text-align: center;
    border-top: 1px dashed #666;
    box-sizing: border-box;
}
  
/* 右側 */
.footer-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 56px;
    max-width: 480px;
    font-size: $pcFont-p-s;
    font-weight: $semiBold;
    line-height: $lineHeight-p-200;
}

.icon-wrapper{
    display: flex;
    gap: 40px;
}

.icon-wrapper svg{
    cursor: pointer;
}
  
  /* RWD */
  @media (max-width: 768px) {
    .footer-main {
      flex-direction: column;
      gap: 24px;
    }
    .footer-left {
      justify-content: flex-start;
    }
  }
 </style>  