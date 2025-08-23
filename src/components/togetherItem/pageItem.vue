<template>
    <div class="pager" v-if="totalPages > 1">
        <button :disabled="page === 1" @click="goPage(page - 1)">上一頁</button>
        <button
            v-for="p in totalPages"
            :key="p"
            :class="{ active: p === page }"
            @click="goPage(p)">{{ p }}</button>
        <button :disabled="page === totalPages" @click="goPage(page + 1)">下一頁</button>
    </div>
</template>

<script setup="setup">
    //--------- 頁碼 -------------
    const page = ref(1) // 目前頁碼（從1開始）
    const perPage = 8 // 每頁顯示8張卡片

    const totalPages = computed(
        () => Math.max(1, Math.ceil(trails.value.length / perPage))
    ) // 總頁數（依資料動態計算，至少1）

    const pagedTrails = computed(() => { // 計算當前頁要顯示的資料切片
        const start = (page.value - 1) * perPage // 這一頁的起始索引
        const end = start + perPage // 這一頁的結束索引（不含 end）
        return trails
            .value
            .slice(start, end) // 回傳該頁的最多8筆資料
    }) // 結束 pagedTrails

    function goPage(p) { // 切換到第 p 頁
        if (p < 1 || p > totalPages.value) 
            return // 超出範圍則不處理
        page.value = p // 設定新頁碼
    } // 結束 goPage
</script>

<style lang="scss" scoped="scoped">
    .pager {
        // 分頁器外框
        margin-top: 16px; // 與卡片清單距離
        // border: 1px solid yellow;
        text-align: center;
    }
</style>