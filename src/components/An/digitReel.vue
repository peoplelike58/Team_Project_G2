<template>
    <div class="reels">
        <div
            class="reel"
            v-for="(targetNumber, index) in digits"
            :key="index"
            :style="{ '--height': '64px' }"
            :ref="reelElement => trackRefs[index] = reelElement">
            <div class="track">
                <!-- 先滾滿 1 圈（0~9） -->
                <div
                    v-for="loopCount in (10)"
                    :key="'loop-' + index + '-' + loopCount"
                    class="digit">
                    {{ (loopCount - 1) % 10 }}
                </div>
                <!-- 再補到 targetNumber -->
                <div
                    v-for="loopCount in (targetNumber + 1)"
                    :key="'tail-' + index + '-' + loopCount"
                    class="digit">
                    {{ loopCount - 1 }}
                </div>
            </div>
        </div>
    </div>
</template>
  
<script setup>
    import { ref, onMounted, nextTick, watch, computed } from 'vue'
    import gsap from 'gsap'
  
    const props = defineProps({
        to: { type: Number, required: true },
        stagger: { type: Number, default: 0 },
        easeArr: { type: Array, default: () => ['power4.out', 'back.out(1.2)', 'power2.out', 'power1.out'] },
        delayArr: { type: Array, default: () => [0, 0, 0, 0.05] }
    })
  
    //把目標數字拆成陣列 Ex:[3,9,5,2]
    const digits = computed(() => {
        return props.to.toString().split('').map(number => Number(number))
    })
  
    const trackRefs = ref([])
  
    function playReel () {
        const gsapTimeLine = gsap.timeline()
        digits.value.forEach((targetNumber, index) => {
            const reelElement = trackRefs.value[index]
            if (!reelElement) return
            const trackElement = reelElement.querySelector('.track')
  
            const loops = 1
            const height = 64
            const duration = 1.7
            const distance = -((loops * 10) + targetNumber) * height
  
            gsapTimeLine.fromTo(
                trackElement,
                { y: 0 },
                {
                    y: distance,
                    duration: duration,
                    ease: props.easeArr[index]
                },
                index * props.stagger + props.delayArr[index]
            )
        })
        return gsapTimeLine
    }
  
    onMounted(() => nextTick(playReel))
    watch(() => props.to, () => nextTick(playReel))
    watch(() => [props.easeArr, props.delayArr, props.stagger], () => nextTick(playReel), { deep: true })
</script>
  
<style scoped lang="scss">
    @import '../../assets/styles/main.scss';
  
    .reels {
        display: inline-flex;
    }
  
    .reel {
        width: fit-content;
        padding: 0 2px;
        height: var(--height);
        overflow: hidden;
        display: inline-block;
        text-align: center;
        font-variant-numeric: tabular-nums;
    }
  
    .track {
        will-change: transform;
    }
  
    .digit {
        height: var(--height);
        font-size: 64px;
        line-height: var(--height);
    }
</style>  