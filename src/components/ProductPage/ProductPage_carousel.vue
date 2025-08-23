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

h2{
    font-size : $pcFont-H2;
    color : $black-14;
    font-weight : $semiBold;
    line-height : $lineHeight-title-120;
    padding: 40px 100px;

}
.carousel_box ,.carousel_content{
    // display: flex;
    // justify-content: center;
    // align-items: center;
    // gap: 20px;
    @include flexcenter(20px,row);

    .item_pic{
        // width: 240px;
        // height: 240px;
        // border-radius: 10px;
        // overflow: hidden;
        @include product_card_img(240px,240px,10px);
        img{
            // width: 100%;
            @include img
        }
       
    }
    p{
    font-size : $pcFont-H4;
    color : $black-14;
    font-weight : $semiBold;
    line-height : $linHeight-p-150;
    padding: 10px 0;
    text-align: center;
    }
}
.carousel_box{
    button{
        background-color: #fff;
        border: none;
        cursor: pointer;
        padding: 10px;
        font-size: $pcFont-H3;
    }
}



</style>