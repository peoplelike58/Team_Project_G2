<template>
    <div id="qaList">
        <h1>Q & A</h1>
        <div v-for="(item, index) in qnaList" :key="index" class="textList"
             :class="{ active: activeIndex === index }" @click="toggle(index)">
            <div class="question-wrapper">
                <h2>{{ item.qtitle }}</h2>
                <!-- 新增：展開/收合指示器 -->
                <div class="toggle-icon" :class="{ rotated: activeIndex === index }">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
            <div class="answer-wrapper">
                <p>{{ item.atitle }}</p>
            </div>
            <div class="indexList"></div>
        </div>
    </div>
</template>

<script setup>

import { ref } from 'vue'

const qnaList = [
    {
        qtitle: '我要怎麼加入一個登山團？',
        atitle: '請先註冊帳號，進入「揪團」頁面，篩選符合你時間、地點與等級的登山活動，點擊「我要參加」即可提出參加申請'
    },
    {
        qtitle: '我可以匿名參加嗎？',
        atitle: '為確保安全與聯繫順利，參團需使用真實姓名與聯絡方式，發起人與參團人僅能查看彼此必要資訊，平台保障個資隱私。'
    },
    {
        qtitle: '有沒有推薦的新手路線？',
        atitle: '在「揪團」頁面可用「新手友善」篩選條件，常見推薦路線如陽明山、郊山步道、合歡山主峰等，適合首次登山體驗。'
    },
    {
        qtitle: '怎麼知道這個揪團是不是安全可靠？',
        atitle: '本站活動皆有嚴格篩選，平台也會審核爬山路線難度，提醒適合的參加者。'
    },
    {
        qtitle: '可以取消參加嗎？',
        atitle: '可在活動開始前「於會員中心取消」內退出團隊；若逾期取消或臨時爽約，可能會影響你的信用評價，並遭限制參加其他揪團。'
    },
    {
        qtitle: '揪團會有保險嗎？',
        atitle: '平台鼓勵發起人提供登山意外保險選項，但是否保險由該團隊自行決定，請務必自行確認。平台也會提供相關保險資訊連結供參考。'
    }
]

const activeIndex = ref(null)

function toggle (index) {
    activeIndex.value = activeIndex.value === index ? null : index
}
</script>

<style lang="scss" scoped>
@import '../../assets/styles/main.scss';
// 問與答區塊
#qaList {
    max-width: 1080px; 
    margin: 0 auto;    
    padding: 0 20px; 
    
    h1 {
        width: 100%;        
        max-width: 1030px;     
        font-size: 40px;       
        font-weight: bold;
        text-align: center;
        margin: 10px auto 0;  
        padding-bottom: 30px;
        border-bottom: 1px dashed $black-14;
    }
}

// Q&A 內容
.textList {
    width: 100%;              
    max-width: 1030px;       
    margin: 20px auto 0;      
    cursor: pointer;
    transition: background-color 0.3s ease; // 新增：hover 效果
    padding: 0 4px;          
    border-radius: 8px;    
    
    
    &:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    .question-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        
        h2 {
            width: auto;          
            max-width: 90%; 
            font-size: 24px;     
            font-weight: bold;
            margin: 0;
            line-height: 1.4;   
        }
        
        .toggle-icon {
            flex-shrink: 0;  
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s ease;
            color: $black-14;
            
            &.rotated {
                transform: rotate(180deg);
            }
            
            svg {
                width: 16px;
                height: 16px;
            }
        }
    }
    
    .answer-wrapper {
        overflow: hidden;    
        
        p {
            width: 95%;        
            max-width: 823px;    
            font-size: 20px;     
            font-weight: normal;
            margin: 0;
            margin-top: 10px;
            display: block;
            opacity: 0;
            transform: translateY(-10px);
            max-height: 0;
            overflow: hidden;
            transition:
                max-height 0.3s ease,
                opacity 0.3s ease,
                transform 0.3s ease,
                margin-top 0.3s ease; 
            line-height: 1.6;    
            color: $black-14;       
            padding-right: 40px;
        }
    }

    .indexList {
        margin-top: 20px;
        border-bottom: 1px dashed $black-14;
    }

    /* 展開狀態 */
    &.active {
        .answer-wrapper p {
            max-height: 500px; 
            opacity: 1;
            transform: translateY(0);
        }
    }
}

@media screen and ( max-width:430px ) {
  // RWD
  #qaList {
        padding: 0 5px; 
        
        h1 {
            font-size: 24px;  
            padding-bottom: 20px;
            margin-top: 15px;  
        }
    }
    
    .textList {
        margin-top: 15px;    
        padding: 0 2px;   
        
        .question-wrapper {
            padding: 8px 0;  
            
            h2 {
                font-size: 20px;
                max-width: 85%; 
                line-height: 1.3; 
                
                // 過長標題處理
                display: -webkit-box;
                -webkit-line-clamp: 2; // 最多顯示2行
                -webkit-box-orient: vertical;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            
            .toggle-icon {
                width: 20px; 
                height: 20px;
                
                svg {
                    width: 14px;
                    height: 14px;
                }
            }
        }
        
        .answer-wrapper p {
            font-size: 16px;     
            margin-top: 12px;    
            padding-right: 20px; 
            line-height: 1.5; 
        }
        
        .indexList {
            margin-top: 15px; 
        }
        
        
        &.active {
            .answer-wrapper p {
                max-height: 400px; // 稍微縮小最大高度
            }
        }
    }
}
</style>