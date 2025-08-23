<template>
    <section class="panel hero">
        <img class="layer back" alt="" style="background-color: blue; opacity: 1;">
        <img class="layer mid" alt="" style="background-color: red; opacity: 1;">
        <img class="layer front" alt="" style="background-color: yellow; opacity: 1;">
        <h1 class="title">Parallax Demo</h1>
    </section>

    <section class="panel content">
        <p>下方內容...</p>
    </section>
</template>
  
<script setup>
    import { onMounted, onUnmounted } from "vue";
    import gsap from "gsap";
    import { ScrollTrigger } from "gsap/ScrollTrigger";
  
    gsap.registerPlugin(ScrollTrigger);
  
    onMounted(() => {
        const triggers = [];
  
        // 將每一層設置不同的滾動位移
        triggers.push(
            gsap.to(".hero .back",  {
                y: "-20vh",
                scrollTrigger: { trigger: ".hero", start: "top top", end: "bottom top", scrub: true }
            })
        );
        triggers.push(
            gsap.to(".hero .mid", {
                y: "-40vh",
                scrollTrigger: { trigger: ".hero", start: "top top", end: "bottom top", scrub: true }
            })
        );
        triggers.push(
            gsap.to(".hero .front", {
                y: "-60vh",
                scrollTrigger: { trigger: ".hero", start: "top top", end: "bottom top", scrub: true }
            })
        );
        triggers.push(
            gsap.to(".hero .title", {
                y: "-30vh", opacity: 0.2,
                scrollTrigger: { trigger: ".hero", start: "top top", end: "bottom top", scrub: true }
            })
        );
  
        onUnmounted(() => {
            triggers.forEach(gsapTo => gsapTo.scrollTrigger?.kill());
        });
    });
</script>
  
<style scoped>
    .panel { 
        position: relative; 
        height: 100vh; 
        overflow: hidden;
        display: flex;
    }
    .hero .layer { 
        /* position: absolute;  */
        /* inset: 0;   */
        width: 300px;
        height: 400px;
        will-change: transform;
        border-radius: 8px;
    }
    .title { 
        position: absolute; 
        left: 8vw; 
        bottom: 10vh; 
        font: 700 8vw/1 Inter, sans-serif; 
        color: white; 
    }
</style>
  