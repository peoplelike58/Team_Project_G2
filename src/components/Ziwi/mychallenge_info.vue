<template>
    <section class="mychallenge-infomation">
        <article class="total-info">
            <div class="total">
                <p>[總累積高度]</p>
                <p><span>{{ heightTotal }}</span> m</p>
                <p>[總累積里程]</p>
                <p><span>{{ kiloTotal }}</span> km</p>
                <p>[總累積時間]</p>
                <p><span>{{ timeTotal }}</span> hr</p>
            </div>
            <div>
                <button>您的歷史紀錄</button>
            </div>
        </article>
    </section>
</template>

<script setup>
    let heightTotal = 1
    let kiloTotal = 2
    let timeTotal = 3
</script>

<style scoped lang="scss">
    @import '@/assets/styles/main.scss';

    .mychallenge-infomation{
        margin: 12.5px 0;
    
        .total-info{
            display: flex;
            justify-content: space-between;
        
            p{
                font-size: $pcFont-p-s;
                font-weight: $bold;
                line-height: $linHeight-p-150;
                display: block;
            
                span{
                    font-size: $pcFont-H1-m;
                    font-weight: $medium;
                    line-height: $lineHeight-title-120;
                }
            }
            button{
                font-size: $pcFont-H4;
                font-weight: $semiBold;
                line-height: $linHeight-p-150;
                background-color: #fff;
                margin-left: 204px;
                padding: 8px;
                border: 1px solid $black-14;
                border-radius: 8px;
                cursor: pointer;
            }
        }
    }

    // .mychallenge-infomation{
    //     margin: 12.5px 0;
    
    // }
    // .total_info{
    //     display: flex;
    //     justify-content: space-between;
    // }
    // p{
    //     font-size: $pcFont-p-s;
    //     font-weight: $bold;
    //     line-height: $linHeight-p-150;
    //     display: block;
    // }
    // span{
    //     font-size: $pcFont-H1-m;
    //     font-weight: $medium;
    //     line-height: $lineHeight-title-120;
    // }
    // button{
    //     font-size: $pcFont-H4;
    //     font-weight: $semiBold;
    //     line-height: $linHeight-p-150;
    //     background-color: #fff;
    //     margin-left: 204px;
    //     padding: 8px;
    //     border: 1px solid $black-14;
    //     border-radius: 8px;
    //     cursor: pointer;
    // }
</style>