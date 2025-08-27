<!-- 精選商品輪播 -->
<script setup>

import { ref,computed } from 'vue'
import sunglasses1 from '@/assets/images/Products/products/墨鏡_1.jpg'

const carouselproducts = ref([
    {"id": 1,"name":"時尚墨鏡","pic":sunglasses1},
    {"id": 2,"name":"時尚墨鏡","pic":sunglasses1},
    {"id": 3,"name":"時尚墨鏡","pic":sunglasses1},
    {"id": 4,"name":"時尚墨鏡","pic":sunglasses1},
    {"id": 5,"name":"時尚墨鏡","pic":sunglasses1},
    {"id": 6,"name":"時尚墨鏡","pic":sunglasses1},
    {"id": 7,"name":"時尚墨鏡","pic":sunglasses1},
    {"id": 8,"name":"時尚墨鏡","pic":sunglasses1},
    {"id": 9,"name":"時尚墨鏡","pic":sunglasses1},
])


// 目前顯示的商品索引（從 0 開始）
const currentIndex = ref(0)
const showcount = 6;

const goPre=()=>{
 // 如果不是第一個商品，就讓 currentIndex 減 1
  if (currentIndex.value > 0) {
    currentIndex.value = currentIndex.value - 1
  }
}

const goNext=()=>{
  // 如果還沒到最後一個商品，就讓 currentIndex 加 1
  if (currentIndex.value < productList.value.length - 1) {
    currentIndex.value = currentIndex.value + 1
  }
}




</script>

<template>
    <section class="featured_products">
        <h2>精選商品</h2>
        <div class="carousel_box">
            <button class="products_pre" @click="goPre"><</button>
            <div class="carousel_content">
                <div class="carousel_item" v-for="(item,index) in carouselproducts" :key="item.index">
                    <div class="item_pic"><img :src="item.pic" alt=""></div>
                    <p>{{ item.name }}</p>
                </div>
            </div>
            <button class="products_next" @click="goNext">></button>
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
.carousel_box ,.carousel_content{
    @include flexcenter(20px,row);
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
.carousel_box{
    button{
        @include btn(0);
        padding: 10px;
        font-size: $pcFont-H3;
    }
}

.carousel_content{
    max-width: 1100px;
    overflow: hidden;
}

</style> 
