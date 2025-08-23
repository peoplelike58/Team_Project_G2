<script setup>
import Checkout_stepup from '@/components/Irene/ShopPage/Checkout_stepup.vue';
import { reactive, ref, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

// 表單
const formRef = ref()
const form = reactive({ name: '', phone: '', addr: '' })
const rules = {
  name: [{ required: true, message: '必填', trigger: 'blur' }],
  phone: [{ required: true, message: '必填', trigger: 'blur' }],
  addr: [{ required: true, message: '必填', trigger: 'blur' }],
}

// 付款
const pay = reactive({ method: 'card', cardNo: '', exp: '', cvc: '' })

// 配送
const shipOpts = [
  { id: '711', label: '7-11 取貨', fee: 60, needStore: true },
  { id: 'family', label: '全家 取貨', fee: 60, needStore: true },
  { id: 'home', label: '宅配到府', fee: 60, needStore: false },
]
const ship = ref('711')
function chooseStore(id){ alert(id==='711'?'開啟 7-11 門市（示意）':'開啟全家門市（示意）') }

// 提交條件
const canSubmit = computed(() => {
  const basicOk = form.name && form.phone && form.addr
  return pay.method === 'card'
    ? basicOk && pay.cardNo && pay.exp && pay.cvc
    : basicOk
})

function goBack(){ router.push('/Shop/cart') }
function submitNext(){ formRef.value?.validate?.((ok)=> { if (ok) router.push('/Shop/success') }) }



</script>

<template>
    <main>
        <section>
            <Checkout_stepup :current="2"/>
        </section>
        <div class="page">
            <el-card shadow="never" class="panel">
            <template #header>填寫資料</template>
            <el-form :model="form" :rules="rules" ref="formRef" label-width="88px">
                <el-row :gutter="16">
                <el-col :span="12"><el-form-item label="收件人" prop="name"><el-input v-model="form.name" /></el-form-item></el-col>
                <el-col :span="12"><el-form-item label="聯絡電話" prop="phone"><el-input v-model="form.phone" /></el-form-item></el-col>
                <el-col :span="24"><el-form-item label="收貨地址" prop="addr"><el-input v-model="form.addr" /></el-form-item></el-col>
                </el-row>
            </el-form>
            </el-card>

            <el-card shadow="never" class="panel">
            <template #header>付款方式</template>
            <el-form :model="pay" label-width="120px">
                <el-form-item label="選擇付款方式">
                <el-select v-model="pay.method" style="width:240px">
                    <el-option label="信用卡" value="card" />
                    <el-option label="貨到付款" value="cod" />
                </el-select>
                </el-form-item>
                <template v-if="pay.method==='card'">
                <el-form-item label="Credit Card Number"><el-input v-model="pay.cardNo" placeholder="xxxx xxxx xxxx xxxx" maxlength="19" /></el-form-item>
                <el-row :gutter="16">
                    <el-col :span="12"><el-form-item label="MM/YY"><el-input v-model="pay.exp" placeholder="MM/YY" /></el-form-item></el-col>
                    <el-col :span="12"><el-form-item label="CVC"><el-input v-model="pay.cvc" placeholder="3 digits" /></el-form-item></el-col>
                </el-row>
                </template>
            </el-form>
            </el-card>

            <el-card shadow="never" class="panel">
            <template #header>配送方式</template>
            <el-radio-group v-model="ship" class="ship">
                <div class="ship-row" v-for="opt in shipOpts" :key="opt.id">
                <el-radio :label="opt.id">{{ opt.label }}</el-radio>
                <el-button v-if="opt.needStore" size="small" @click="chooseStore(opt.id)">選擇門市</el-button>
                <span class="fee">運費：NT {{ opt.fee }}</span>
                </div>
            </el-radio-group>
            </el-card>

            <el-card shadow="never" class="panel">
            <div class="sum-line"><span>共 2 件商品</span><span>商品金額</span><strong>NT 3,200</strong></div>
            <div class="sum-line"><span></span><span>活動優惠</span><strong class="discount">- NT 200</strong></div>
            <div class="sum-line"><span></span><span>運費</span><strong>NT 0</strong></div>
            <el-divider />
            <div class="sum-line"><span></span><span>小計</span><strong>NT$ 3,200</strong></div>
            </el-card>

            <div class="actions">
            <el-button @click="goBack">上一步</el-button>
            <el-button type="primary" :disabled="!canSubmit" @click="submitNext">下一步</el-button>
            </div>
        </div>
    </main>

</template>

<style lang="scss" scoped>
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';

.page { max-width: 980px; margin: 0 auto; }
.panel { margin-bottom: 16px; }
.ship { display:block; }
.ship-row { display:grid; grid-template-columns: 1fr auto auto; align-items:center; gap:12px; padding:8px 0; }
.fee{ color:#555; }
.sum-line{ display:grid; grid-template-columns:1fr auto auto; align-items:center; padding:6px 0; }
.discount{ color:#a33; }
.actions{ display:flex; justify-content:flex-end; gap:12px; margin: 20px 0 60px; }

</style>