<template>
    <div
      class = "image-carousel"
    >
        <!-- 單張圖片 -->
        <transition name="fade" mode="out-in">
            <img
                v-if = "slides.length"
                class = "image"
                :key = "index"
                :src = "slides[index].src"
                loading = "lazy"
                decoding = "async"
            />
        </transition>
  
        <!-- 指示點 -->
        <ol class="dots" v-if = "slides.length > 1">
            <li class = "dot-wrap" v-for = "(slide, slideIndex) in slides"
                :key = "`dot-${slideIndex}`">
                <button
                    class = "dot"
                    :class = "{ active: slideIndex === index }"
                    @click = "goTo(slideIndex)"
                >
                </button>
            </li>
        </ol>
    </div>
</template>
  
<script setup>
    import { ref, onMounted, onBeforeUnmount } from 'vue'
  
    const props = defineProps({
        index: { type: Number, default: 0 },

        slides: { type: Array, required: true },
        autoPlay: { type: Boolean, default: true },
        autoPlayIntervalMs: { type: Number, default: 5000 }
    })
  
    const emit = defineEmits(['update:index'])
    let autoPlayTimer = null
    
    //下一張
    function showNext () {
        if (!props.slides.length) return
        emit('update:index', (props.index + 1) % props.slides.length)
    }
    
    //去到指定索引
    function goTo (slideIndex) {
        if (slideIndex < 0 || slideIndex >= props.slides.length) return
        emit('update:index', slideIndex)
        // 歸零計時器並重新計時
        if (autoPlayTimer) {
            clearInterval(autoPlayTimer)
            autoPlayTimer = null
        }
        startAutoPlay()
    }
    
    //自動播放
    function startAutoPlay () {
        if (!props.autoPlay || autoPlayTimer || props.slides.length <= 1) return
        autoPlayTimer = setInterval(showNext, props.autoPlayIntervalMs)
    }
  
    onMounted(startAutoPlay)
    onBeforeUnmount(() => {if(autoPlayTimer){clearInterval(autoPlayTimer)}})

</script>
  
<style scoped lang="scss">
    @import '../../assets/styles/main.scss';

    .image-carousel {
        position: relative;

        aspect-ratio: 16 / 9;
        overflow: hidden;
        background: #141414;
    }
  
    .image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
  
    /* ⭐️淡入淡出動畫⭐️ */
    .fade-enter-active, .fade-leave-active {
        transition: opacity 0.6s ease;
    }
    .fade-enter-from, .fade-leave-to { 
        opacity: 0; 
    }
  
    /* 指示點 */
    .dots {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 40px;
        display: flex;
        justify-content: center;
        gap: 8px;
        
        // border: 1px solid maroon;
    }

    .dot {
        display: block;
        width: 40px;
        height: 4px;
        border: none;
        cursor: pointer;
        background: rgba(255,255,255,.5);
    }
    
    .dot.active { background: #fff; }
</style>
  