<script setup>
import Checkout_stepup from '@/components/Irene/ShopPage/Checkout_stepup.vue';
import { useRouter } from 'vue-router'
const router = useRouter()
function goShop(){ router.push('/Shop') }

</script>

<template>
    <main>
        <section>
            <Checkout_stepup :current="3"/>
        </section>

        <div class="page">
            <el-card class="center" shadow="never">
            <el-result icon="success" title="付款完成" sub-title="您的訂單編號為：12345678，稍後可在〈會員中心〉查詢詳細資訊" />
            </el-card>

            <el-card shadow="never" class="panel">
            <template #header>出貨提醒與注意事項</template>
            <el-descriptions :column="1" border>
                <el-descriptions-item>訂單確認後 1–3 個工作天內出貨（不含假日）。</el-descriptions-item>
                <el-descriptions-item>含預購商品將待全單備齊後一併出貨，依商品公告為準。</el-descriptions-item>
                <el-descriptions-item>超商 2–3 天到店；宅配 1–2 天送達（偏遠地區另加 1–2 天）。</el-descriptions-item>
                <el-descriptions-item>請填正確資料，若因錯誤造成配送延誤，將不另行補寄。</el-descriptions-item>
                <el-descriptions-item>超商取貨請於簡訊通知後 7 日內取件，逾期自動退回。</el-descriptions-item>
                <el-descriptions-item>到貨如有外箱破損，請開箱錄影存證並與客服聯繫。</el-descriptions-item>
            </el-descriptions>
            </el-card>

            <div class="actions center">
            <el-button @click="goShop">返回商城</el-button>
            </div>
        </div>
    </main>

</template>

<style lang="scss" scoped>
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';
.page { max-width: 980px; margin: 0 auto; }
.center { text-align:center; }
.panel { margin-top: 16px; }
.actions.center{ display:flex; justify-content:center; margin:24px 0 60px; }
</style>