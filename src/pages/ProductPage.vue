<template>
    <main>
        <section class="featured_products">
            <ProductPage_carousel/>
        </section>
        <section class="coupon">
            <ProductPage_couponBanner/>
        </section>
        <section class="products">
            <ProductPage_sidebar/>
            <ProductPage_productslist/>
        </section class="pagination">
        <ProductPage_pagination/>
    </main>
</template>

<script setup>

import  ProductPage_carousel from '@/components/ProductPage/ProductPage_carousel.vue';
import  ProductPage_couponBanner  from '@/components/ProductPage/ProductPage_couponBanner.vue';
import  ProductPage_pagination from '@/components/ProductPage/ProductPage_pagination.vue';
import ProductPage_productslist from '@/components/ProductPage/ProductPage_productslist.vue';
import ProductPage_sidebar from '@/components/ProductPage/ProductPage_sidebar.vue';

</script>


<style scoped lang="scss">
@import '../assets/styles/main.scss';

section{
    padding-bottom: 40px;
    width: 100%;
}

.coupon{
    text-align: center;
}

.products{
    @include flexcenter(50px,row)
}
.pagination{
    margin: auto;
}
</style>