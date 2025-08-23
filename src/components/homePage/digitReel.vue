<template>
    <div class="reels">

        <!-- reel是包住一個滾輪的外框 -->
        <!-- digits 是一個陣列（例如 [3, 9, 5, 2]），代表千位、百位、十位、個位的目標數字。
        targetNumber → 當前位數的目標數字（0–9）
        index → 這是第幾位（0=千位、1=百位...） -->

        <!-- Step 2 -->
        <div
        class="reel"
        v-for="(targetNumber, index) in digits"
        :key="index"
        :style="{ '--height': heightFor(index) + 'px' }"
        :ref="reel => trackRefs[index] = reel"
        > <!-- reel 共 4 個（千、百、十、個） -->

            <div class="track">
                <!-- 用途：滾動的內層容器
                 .reel 是外框（固定高度，裁掉超出的內容）
                 .track 裡面放一堆.digit，靠.track 的 y 位移來做動畫 -->

                <div v-for="loopCount in (loopsFor(index) * 10)" :key="'loop-'+index+'-'+loopCount" class="digit">{{ (loopCount-1) % 10 }}</div>
                <div v-for="loopCount in (targetNumber + 1)" :key="'tail-'+index+'-'+loopCount" class="digit">{{ loopCount-1 }}</div>
                <!-- key 由三部分組成：
                1. 'loop-'：純字串前綴
                2. index："外層 v-for" 的 "位數" 索引
                   0 → 千位、1 → 百位、2 → 十位、3 → 個位

                3. loopCount：內層 v-for 的第幾格數字（0, 1, 2, 3...） -->

                <!-- 
                第一段：先讓滾輪完整滾 N 圈（全是 0~9 循環）
                第二段：多滾幾格，把目標數字滾到視窗中
                例如：
                loops = 2 圈
                targetNumber = 7
                內部 .track 會長這樣：
                0 1 2 3 4 5 6 7 8 9   ← 第 1 圈
                0 1 2 3 4 5 6 7 8 9   ← 第 2 圈
                0 1 2 3 4 5 6 7       ← 最後補到 targetNumber -->
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, onMounted, nextTick, watch, computed } from 'vue'
    import gsap from 'gsap'

    const props = defineProps({
        to: { type: Number }, //放目標數字

        loopsArr: { type: Array, default: () => [] },
        durationArr: { type: Array, default: () => [] },
        easeArr: { type: Array, default: () => [] },
        heightArr: { type: Array, default: () => [] },
        delayArr: { type: Array, default: () => [] }
    })

    //Step 1
    const digits = computed(() => {
        const propsToString = Math.floor(props.to).toString() //解析 props.to 接收到的目標數字
        return propsToString.split('').map(splitString => Number(splitString)) //Ex:[3,9,5,2]  
    })
    
    const loopsFor = index => props.loopsArr[index]
    const durationFor = index => props.durationArr[index]
    const easeFor = index => props.easeArr[index]
    const heightFor = index => props.heightArr[index]
    const DelayFor = index => props.delayArr[index]
    
    const trackRefs = ref([])

    function playReel() {
        const gsapTimeLine = gsap.timeline()
        // console.log(digits.value);
        
        digits.value.forEach((targetNumber, index) => {
            const reel = trackRefs.value[index]
            if (!reel) return
            const track = reel.querySelector('.track')

            const loops = Number(loopsFor(index)) || 0
            const height = Number(heightFor(index)) || 0
            const distance = -((loops * 10) + targetNumber) * height

            gsapTimeLine.fromTo(
                track,
                { y: 0 },
                { y: distance,
                  duration: durationFor(index),
                  ease: easeFor(index) },
                index * (props.stagger) + DelayFor(index)
            )
        })
        return gsapTimeLine
    }

    onMounted(() => nextTick(playReel))
    watch(() => props.to, () => nextTick(playReel))
    watch(() => [props.loopsArr, props.durationArr, props.easeArr, props.heightArr, props.delayArr], () => nextTick(playReel), { deep: true })
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
        font-variant-numeric: tabular-nums; //tabular-nums：每個數字佔的空間等寬
    }

    .track {
        will-change: transform;
    }

    .digit {
        height: var(--height);
        font-size: 64px;
    }
</style>