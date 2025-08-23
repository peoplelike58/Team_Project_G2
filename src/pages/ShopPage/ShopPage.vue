<script setup>
import ShopPage_carousel from '@/components/Irene/ShopPage/ShopPage_carousel.vue';
import ShopPage_couponBanner from '@/components/Irene/ShopPage/ShopPage_couponBanner.vue';
import ShopPage_pagination from '@/components/Irene/ShopPage/ShopPage_pagination.vue';
import ShopPage_productslist from '@/components/Irene/ShopPage/ShopPage_productslist.vue';
import ShopPage_sidebar from '@/components/Irene/ShopPage/ShopPage_sidebar.vue';
import { reactive } from 'vue';

// 篩選的方式管理
const filters = reactive({
  search: '',     // 儲存搜尋關鍵字
  category: '',   //儲存篩選的分類
  genders: []     //儲存篩選的性別[male,female]
})

//分頁相關狀態
const pagination = reactive({
  currentPage: 1,
  pageSize: 20,
  total: 0
})

// 處理關鍵字搜尋事件
const handleSearch = (searchKeyword) => {
  filters.search = searchKeyword      // 更新搜尋狀態
  pagination.currentPage = 1          // 搜尋後回到第一頁
}

//處理性別篩選事件
const handleGenderFilter = (genders) => {
    filters.genders = genders         // 更新篩選性別（條件）
    pagination.currentPage = 1        // 搜尋後回到第一頁
}

//處理分類篩選的事件
const handleCategoryFilter = (category) => {
    filters.category = category  // 更新篩選分類（條件)
    pagination.currentPage = 1        // 搜尋後回到第一頁
}

//處理總數變化（從商品列表組件接收）
const handleTotalChange = (total) => {
  pagination.total = total
}

//處理頁面變化
const handlePageChange = (page) => {
  pagination.currentPage = page
}


</script>

<!-- 這是商品首頁 -->
<template>
    <main>
        <section class="featured_products">
            <ShopPage_carousel/>
        </section>
        <section class="coupon">
            <ShopPage_couponBanner/>
        </section>
        <section class="products">
            <ShopPage_sidebar 
                @search="handleSearch" 
                @gender-filter="handleGenderFilter"
                @category-filter="handleCategoryFilter" />
            <!-- 建立事件聆聽接收子層傳來是時間，用handleSearch接收 -->
            <ShopPage_productslist 
                :filters="filters"
                :current-page="pagination.currentPage"
                :page-size="pagination.pageSize"
                @total-change="handleTotalChange"/>
        </section >
        
        <!-- 把filters傳給子組件 -->
        <section class="pagination">
            <ShopPage_pagination 
                :total="pagination.total"
                :page-size="pagination.pageSize"
                :current-page="pagination.currentPage"
                @page-change="handlePageChange"/>
        </section>
    </main>
    <RouterView/>
    
</template>

<style scoped lang="scss">
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';

main{
    section{
    padding-bottom: 40px;
    width: 1200px;
    margin: auto;
    }
}

.coupon{
    text-align: center;
}

.products{
    @include flexcenter(50px,row);
    align-items: flex-start; /* 避免側欄與清單被垂直置中而高度不齊 */
}

</style>