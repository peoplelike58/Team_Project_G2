<script setup>
import Checkout_stepup from '@/components/Irene/ShopPage/Checkout_stepup.vue';
import { reactive, ref, computed, watch } from 'vue'
import { ArrowDown } from '@element-plus/icons-vue'
import { ElMessage } from 'element-plus'
import { useRouter } from 'vue-router'

const router = useRouter()
const items = reactive([
  { id: 1, name: '折疊雙節望遠鏡', size: 'S', color: '紅', price: 3200, qty: 1, image: 'https://images.unsplash.com/photo-1516223725307-6f76b9ec8742?w=600&fit=crop' },
  { id: 2, name: '折疊雙節望遠鏡', size: 'S', color: '紅', price: 3200, qty: 1, image: 'https://images.unsplash.com/photo-1519681393784-d120267933ba?w=600&fit=crop' },
])

// 勾選
const checkedMap = reactive(Object.fromEntries(items.map(i => [i.id, true])))
const allChecked = ref(true)
watch(() => Object.values(checkedMap), vals => allChecked.value = vals.every(Boolean), { deep: true })
function toggleAll(){ Object.keys(checkedMap).forEach(k => (checkedMap[k] = allChecked.value)) }
function remove(id){ const idx = items.findIndex(i => i.id === id); if (idx>-1){ items.splice(idx,1); delete checkedMap[id] } }
function removeChecked(){
  const ids = Object.entries(checkedMap).filter(([,v])=>v).map(([k])=>+k)
  ids.forEach(remove)
  if(!ids.length) ElMessage.info('請先勾選要刪除的商品')
}

// 優惠券
const coupons=[{ id:'A', title:'新朋友 $200 折扣', amount:200 }]
const chosenCoupon=ref(null)
function applyCoupon(c){ chosenCoupon.value=c }

// 小計
const subtotal = computed(()=> items.reduce((s,i)=> s + i.price*i.qty, 0))
const totalQty = computed(()=> items.reduce((s,i)=> s + i.qty, 0))
const discount = computed(()=> chosenCoupon.value?.amount ?? 0)
const total = computed(()=> Math.max(subtotal.value - discount.value, 0))

// 導頁
function goBack(){ router.push('/Shop') }
function goNext(){ router.push('/Shop/info') }

</script>

<template>
    <main>
        <section>
            <Checkout_stepup :current="1"/>
        </section>
        <div class="toolbar">
            <el-checkbox v-model="allChecked" @change="toggleAll">全選</el-checkbox>
            <el-button link type="info"  @click="removeChecked">全部刪除</el-button>
        </div>

        <el-table :data="items"  stripe class="cart-table">
            <el-table-column width="54" align="center">
                <template #default="{ row }"><el-checkbox v-model="checkedMap[row.id]" /></template>
            </el-table-column>

            <el-table-column label="" width="140">
                <template #default="{ row }">
                <el-image :src="row.image" fit="cover" style="width:120px;height:80px;border-radius:6px;" />
                </template>
            </el-table-column>

            <el-table-column prop="name" label="商品" min-width="220">
                <template #default="{ row }">
                <div class="name">{{ row.name }}</div>
                <div class="sku">尺寸：{{ row.size }}　顏色：{{ row.color }}</div>
                </template>
            </el-table-column>

            <el-table-column label="數量" width="160" align="center">
                <template #default="{ row }"><el-input-number v-model="row.qty" :min="1" /></template>
            </el-table-column>

            <el-table-column label="單價" width="120" align="right">
                <template #default="{ row }">NT${{ row.price }}</template>
            </el-table-column>

            <el-table-column width="64" align="center">
                <template #default="{ row }"><el-button link type="danger" @click="remove(row.id)">✕</el-button></template>
            </el-table-column>
        </el-table>

        <div class="coupon">
        <el-dropdown @command="applyCoupon">
            <el-button>
            選擇優惠券
            <el-icon class="ml-1"><ArrowDown /></el-icon>
            </el-button>
            <template #dropdown>
            <el-dropdown-menu>
                <el-dropdown-item v-for="c in coupons" :key="c.id" :command="c">{{ c.title }}</el-dropdown-item>
            </el-dropdown-menu>
            </template>
        </el-dropdown>
        <span v-if="chosenCoupon" class="coupon-tag">已套用：{{ chosenCoupon.title }}</span>
        </div>

        <el-card shadow="never" class="summary">
        <div class="line"><span>共 {{ totalQty }} 件商品</span><span>商品金額</span><strong> $ {{ subtotal.toLocaleString() }}</strong></div>
        <div class="line"><span></span><span>活動優惠</span><strong class="discount">- $ {{ discount.toLocaleString() }}</strong></div>
        <el-divider />
        <div class="line total"><span></span><span>小計</span><strong>$ {{ total.toLocaleString() }}</strong></div>
        </el-card>

        <div class="actions">
        <el-button @click="goBack">上一步</el-button>
        <el-button type="primary" :disabled="!items.length" @click="goNext">下一步</el-button>
        </div>
    </main>
</template>

<style lang="scss" scoped>
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';

main{
    max-width: 1200px;
    margin: auto;
    section{
        padding: 40px 0;
       
        :deep(element.style){
            text-align: center;
        }
    }
}

.toolbar{
    display: flex;
    justify-content: space-between;
    padding: 20px;
    :deep(.el-checkbox__label,.el-button>span){
        font-size: 20px;



    }

}

</style>