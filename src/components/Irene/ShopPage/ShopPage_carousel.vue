<!-- 精選商品輪播 -->
<template>
    <section class="featured_products">
        <h2>精選商品</h2>
        <div class="carousel_box">
            <button class="products_pre"><</button>
            <div class="carousel_content">
                <!-- 精選商品項目 -->
                <div class="carousel_item">
                    <div class="item_pic"><img src="/Products/products/墨鏡_1.jpg" alt=""></div>
                    <p>時尚墨鏡</p>
                </div>
                <div class="carousel_item">
                    <div class="item_pic"><img src="/public/Products/products/墨鏡_1.jpg" alt=""></div>
                    <p>時尚墨鏡</p>
                </div>
                <div class="carousel_item">
                    <div class="item_pic"><img src="/public/Products/products/墨鏡_1.jpg" alt=""></div>
                    <p>時尚墨鏡</p>
                </div>
                <div class="carousel_item">
                    <div class="item_pic"><img src="/public/Products/products/墨鏡_1.jpg" alt=""></div>
                    <p>時尚墨鏡</p>
                </div><div class="carousel_item">
                    <div class="item_pic"><img src="/public/Products/products/墨鏡_1.jpg" alt=""></div>
                    <p>時尚墨鏡</p>
                </div>
            </div>
            <button class="products_next">></button>
        </div>
    </section>
</template>

<script setup>

</script>

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



</style>