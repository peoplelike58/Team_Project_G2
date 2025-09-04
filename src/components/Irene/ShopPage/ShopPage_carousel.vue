<!-- 精選商品輪播 -->
<script setup>

import { ref,computed } from 'vue'
import sunglasses1 from '@/assets/images/Products/products/墨鏡_1.jpg'
import sunglasses2 from '@/assets/images/Products/products/手套1.jpg'
import sunglasses3 from '@/assets/images/Products/products/水壺_1.jpg'
import sunglasses4 from '@/assets/images/Products/products/指南針_1.jpg'
import sunglasses5 from '@/assets/images/Products/products/登山包_1.jpg'
import sunglasses6 from '@/assets/images/Products/products/登山杖_1.jpg'
import sunglasses7 from '@/assets/images/Products/products/望遠鏡_2.png'
import sunglasses8 from '@/assets/images/Products/products/水壺_3.jpg'
import sunglasses9 from '@/assets/images/Products/products/睡袋_1.jpg'


const carouselproducts = ref([
    {"id": 1,"name":"時尚墨鏡","pic":sunglasses1},
    {"id": 2,"name":"時尚墨鏡","pic":sunglasses2},
    {"id": 3,"name":"時尚墨鏡","pic":sunglasses3},
    {"id": 4,"name":"時尚墨鏡","pic":sunglasses4},
    {"id": 5,"name":"時尚墨鏡","pic":sunglasses5},
    {"id": 6,"name":"時尚墨鏡","pic":sunglasses6},
    {"id": 7,"name":"時尚墨鏡","pic":sunglasses7},
    {"id": 8,"name":"時尚墨鏡","pic":sunglasses8},
    {"id": 9,"name":"睡袋","pic":sunglasses9},
])


// 目前顯示的商品索引（從 0 開始）
const currentIndex = ref(0)  // 目前顯示的第一個商品索引
const showCount = 4;
const cardWidth = 240;
const gap = 40;

//計算最大索引（防止超出範圍
const maxIndex = computed(() =>
  Math.max(0, carouselproducts.value.length - showCount)   //意思是最多可以從第5個商品開始顯示（索引0-5）,例如：9個商品，一次顯示3個，最大索引 = 9 - 4 = 5
)
//計算輪播容器的位移距離
const translateX =computed(()=>{
    return -(currentIndex.value * (cardWidth + gap))
})


const goPre=()=>{
 // 如果不是第一個商品，就讓 currentIndex 減 1
  if (currentIndex.value > 0) {
    currentIndex.value = currentIndex.value - 1
  }
}

const goNext=()=>{
  // 如果還沒到最後一個商品，就讓 currentIndex 加 1
  if (currentIndex.value < carouselproducts.value.length - 1) {
    currentIndex.value = currentIndex.value + 1
  }
}
// 檢查按鈕是否應該禁用
const isPrevDisabled = computed(() => currentIndex.value === 0)
const isNextDisabled = computed(() => currentIndex.value >= maxIndex.value)


</script>


<template>
    <section class="featured_products">
        <h2>精選商品</h2>
        <div class="carousel_box">
            <button class="products_pre" @click="goPre" :disabled="isPrevDisabled"><</button>
            <div class="carousel_content" >
                <div class="carousel_item" v-for="(item,index) in carouselproducts" :key="item.id " :style="{ transform: `translateX(${translateX}px)` }">
                    <div class="item_pic"><img :src="item.pic" alt=""></div>
                    <p>{{ item.name }}</p>
                </div>
            </div>
            <button class="products_next" @click="goNext" :disabled="isNextDisabled">></button>
        </div>
    </section>
</template>


<style scoped lang="scss">
 
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';

h2{
    font-size : $pcFont-H2;
    color : $black-14;
    font-weight : $semiBold;
    line-height : $lineHeight-title-120;
    padding: 40px 100px;

}
.carousel_box {
    @include flexcenter(20px,row);
    button{
    @include btn(0);
    padding: 10px;
    font-size: $pcFont-H3;
    background-color: transparent;
    font-weight: $bold;
    line-height: $lineHeight-p-200;
    transform: scale(1.5);
    &:hover{
        color: #ccc;
    }}
    .carousel_content{
        @include flexcenter(40px,row);
        max-width: 1100px;
        overflow: hidden;
        justify-content: flex-start; 
        transition: transform 0.3s ease;
        .carousel_item{
            .item_pic{
                @include product_card_img(240px,240px,10px);
                img{
                    @include img
                }
            }
            p{
            font-size : $pcFont-H4;
            color : $black-14;
            font-weight : $semiBold;
            line-height : $lineHeight-p-150;
            padding: 10px 0;
            text-align: center;
            }
        }
    }
}

</style> 
