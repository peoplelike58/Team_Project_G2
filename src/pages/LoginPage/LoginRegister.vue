
<script setup>
import NavMenu from '@/components/An/navMenu.vue';

</script>


<template>
    <div class="wrapper">
        <NavMenu/>
        <main>
            <div class="modal-container" >
                <RouterView/>
            </div>
        </main>
    </div> 
</template>


<style lang="scss" scoped>
@import '@/assets/styles/main.scss';
@import '@/assets/styles/othermixins.scss';

.wrapper{
    background-color: $bg-gray ;
    padding-bottom: 80px;
}

/* 模態視窗容器 */
.modal-container{
    // background-color: white;
    // border-radius: 16px;
    // width: 100%;
    // max-width: 448px;
    // position: relative;
    // box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    background: white;
    border-radius: 12px;
    width: 90%;
    max-width: 450px;
    margin: auto ;
}

</style>