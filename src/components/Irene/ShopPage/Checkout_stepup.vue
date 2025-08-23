
<script setup>
    const props = defineProps({
    current: { type: Number, required: true, default: 1, validator: v => [1, 2, 3].includes(v) }
})
</script>


<template>
  <el-steps style="max-width: 600px" :active="props.current" align-center>
    <el-step title="Step 1" description="購物車" />
    <el-step title="Step 2" description="填寫資料" />
    <el-step title="Step 3" description="完成訂單" />
  </el-steps>


</template>


<style lang="scss" scoped>
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';


.el-steps.el-steps--horizontal{
    margin: auto;

}
.checkout-steps {
  margin: 24px 0 28px;  
  :deep(.el-step__title) { font-weight: 600; letter-spacing: .5px; }
  :deep(.el-step__description) { color:#666; }
}
</style>
