<template>
    <!-- 上方輪播圖 -->
    <header>
        <CardLoopItem/>
    </header>

    <!-- 篩選選單 -->
    <main>
        <FilterBarItem/>
    </main>

    <!-- 活動卡片區塊 -->
    <div class="cardList"></div>

    <!-- 問與答區塊 -->
    <div class="qaList">
        <h1>Q & A</h1>
        <ul class="textList">
            <li class="indexList">
                <h2>我要怎麼加入一個登山團？</h2>
                <p>請先註冊帳號，進入「揪團」頁面，篩選符合你時間、地點與等級的登山活動，點擊「我要參加」即可提出參加申請</p>
            </li>
            <li class="indexList">
                <h2>我可以匿名參加嗎？</h2>
                <p>為確保安全與聯繫順利，參團需使用真實姓名與聯絡方式，發起人與參團人僅能查看彼此必要資訊，平台保障個資隱私。</p>
            </li>
            <li class="indexList">
                <h2>有沒有推薦的新手路線？</h2>
                <p>在「揪團」頁面可用「新手友善」篩選條件，常見推薦路線如陽明山、郊山步道、合歡山主峰等，適合首次登山體驗。</p>
            </li>
            <li class="indexList">
                <h2>怎麼知道這個揪團是不是安全可靠？</h2>
                <p>本站活動皆有嚴格篩選，平台也會審核爬山路線難度，提醒適合的參加者。</p>
            </li>
            <li class="indexList">
                <h2>可以取消參加嗎？</h2>
                <p>可在活動開始前「於會員中心取消」內退出團隊；若逾期取消或臨時爽約，可能會影響你的信用評價，並遭限制參加其他揪團。</p>
            </li>
            <li class="indexList">
                <h2>揪團會有保險嗎？</h2>
                <p>平台鼓勵發起人提供登山意外保險選項，但是否保險由該團隊自行決定，請務必自行確認。平台也會提供相關保險資訊連結供參考。</p>
            </li>
        </ul>

    </div>

</template>

<script setup="setup">

    import CardLoopItem from '@/components/togetherItem/cardLoopItem.vue'
    import FilterBarItem from '@/components/togetherItem/filterBarItem.vue';
</script>

<style lang="scss" scoped="scoped">

    @import '../assets/styles/main.scss';
    // 活動卡片區塊
    .cardList {
        border: 2px solid red;
        width: 1080px;
        height: 873px;
        margin: 0 auto 179px;
    }

    // 問與答區塊
    .qaList {
        // border: 2px solid red;
        width: 1080px;
        height: 800px;
        margin: 0 auto;
        h1 {
            width: 1030px;
            font-size: 40px;
            font-weight: bold;
            text-align: center;
            margin-left: 20px;
            margin-top: 10px;
            padding-bottom: 20px;
            border-width: 3px;
            border-bottom: dashed;
            border-color: gray;
        }
    }

    .textList {
        width: 1030px;
        margin-left: 24px;
        margin-top: 20px;
        h2 {
            width: 823px;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        p {
            width: 823px;
            font-size: 20px;
            font-weight: bold;
        }
        .indexList {
            margin-top: 20px;
            border-bottom: 3px dashed gray;
        }
        // 初始隱藏答案與圖片，並設置漸變
        p {
            display: none;
            opacity: 0;
            max-height: 0;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        p {
            width: 823px;
            font-size: 20px;
            font-weight: bold;
            margin: 0;
        }

        &.active {
            // 加上 .active 時顯示
            p {
                display: block;
                opacity: 1;
                max-height: 1000px; // 確保能夠完整展開
            }
        }
    }
</style>