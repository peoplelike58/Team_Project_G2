<script setup>
import NavMenu from '@/components/An/navMenu.vue';
import Checkout_stepup from '@/components/Irene/ShopPage/Checkout_stepup.vue';
import brandFooter from '@/components/An/footer.vue'
import { useRouter } from 'vue-router'
const router = useRouter()
function goShop(){ router.push('/Shop') }

</script>

<template>
    <NavMenu/>
    <main>
        <section>
            <Checkout_stepup :current="3"/>
        </section>

        <div class="page">
            <el-card class="center" shadow="never">
            <el-result icon="success" title="付款完成" sub-title="您的訂單編號為:12345678,稍後可在〈會員中心〉查詢詳細資訊" />
            <!-- 這裡的訂單編號要改成動態的 -->
            </el-card>

            <div class="actions center">
            <el-button @click="goShop" style="background-color: black;color: #fff; padding: 8px 16px;">返回商城</el-button>
            </div>

            <el-card shadow="never" class="panel">
            <template #header>📦出貨提醒與注意事項</template>
            <el-descriptions :column="1" border>
                <el-descriptions-item>
                    🔸 出貨時間
                    <br>
                    • 訂單確認後 1–3 個工作天內出貨（不含假日）
                    <br>
                    • 若含預購商品，將待全單備齊後一併出貨，出貨日依商品頁公告為準
                </el-descriptions-item>
                <el-descriptions-item>
                    🔸 配送方式與時程
                    <br>
                    • 超商取貨（7-11／全家）：寄出後約 2–3 天到店
                    <br>
                    • 宅配到府：寄出後約 1–2 天送達（依物流公司實際配送為準）
                    <br>
                    • 離島地區與偏遠山區配送需另加 1–2 工作天
                </el-descriptions-item>
                <el-descriptions-item>
                    🔸 注意事項
                    <br>
                    • 請務必填寫正確收件資訊，若因資料錯誤導致配送延誤，將不另行補寄
                    <br>
                    • 超商取貨請於簡訊通知後 7 日內取件，逾期未取將自動退回，恕不再次寄送
                    <br>
                    • 商品到貨後如有外箱破損，請於開箱時錄影存證並與客服聯繫
                </el-descriptions-item>
            </el-descriptions>
            </el-card>

        </div>
    </main>
    <brandFooter/>
</template>

<style lang="scss" scoped>
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';
.page { max-width: 980px; margin: 0 auto; background-color: transparent;}
.center { text-align:center;padding: 24px 0; }
.panel { margin: 20px 0; }
.actions.center{ display:flex; justify-content:center; margin:24px 0 60px; }
:deep(.el-card__header){
    font-size: $pcFont-H4;
    font-weight: $bold;
    line-height: $lineHeight-p-200;
}
:deep(.el-result){
    gap: 8px;
}
</style>