<template>

    <!-- 上方輪播圖 -->
    <header>
        <div class="cardLoop">
            <ul :style="sliderStyle">
                <li v-for="(image, index) in images" :key="index">
                    <img :src="image" alt=""/>
                </li>
            </ul>
        </div>
    </header>

</template>



<script setup>

    import {ref, computed, onMounted, onBeforeUnmount} from 'vue'

    const images = [
        new URL('@/assets/images/eventCard/cardimg1.jpg', import.meta.url).href,
        new URL('@/assets/images/eventCard/cardimg2.jpg', import.meta.url).href,
        new URL('@/assets/images/eventCard/cardimg3.jpg', import.meta.url).href
    ]

    const currentIndex = ref(0)
    let timer = null

    // 計算滑動位置
    const sliderStyle = computed(() => ({
        transform: `translateX(-${currentIndex.value * 100}%)`,
        transition: 'transform 0.8s ease'
    }))

    const nextSlide = () => {
        currentIndex.value = (currentIndex.value + 1) % images.length
    }

    onMounted(() => {
        timer = setInterval(nextSlide, 5000) //每5秒切換一張
    })

    onBeforeUnmount(() => {
        clearInterval(timer)
    })

</script>



<style lang="scss" scoped>

    @import '@/assets/styles/main.scss';

    // 上方輪播圖
    .cardLoop {
        width: 1200px;
        max-height: 389px;
        margin: 0 auto;
        overflow: hidden;

        ul {
            width: 100%;
            height: 100%;
            padding: 0;
            margin: 0;
            display: flex;
            list-style: none;

            li {
                flex: 0 0 100%;
                height: 100%;

                img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    display: block;
                }
            }
        }
    }

</style>