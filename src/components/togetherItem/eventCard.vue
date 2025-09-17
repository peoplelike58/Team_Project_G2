<template>
    <!-- Card -->
    <article class="activity-card">
        <div class="top-content">
            <!-- 日期 -->
            <slot name="date" :item="item" :dateText="dateText" :index="index">
                <time class="ac-date">{{ dateText }}</time>
            </slot>

            <!-- 圖片 - 使用計算屬性的 imageUrl -->
            <slot name="image" :item="item" :index="index">
                <img 
                    class="ac-img" 
                    :src="imageUrl"
                    :alt="item.title"
                    loading="lazy"
                    @error="handleImageError"
                />
            </slot>

            <!-- 標題 -->
            <slot name="title" :item="item" :title="item.title" :index="index">
                <h3 class="ac-title">{{ item.title }}</h3>
            </slot>

            <!-- 標籤 -->
            <slot name="tags" :item="item" :tags="item.tags" :index="index">
                <ul class="ac-tags" v-if="item.tags?.length">
                    <li v-for="tag in item.tags" :key="tag" class="tag">{{ tag }}</li>
                </ul>
            </slot>
        </div>

        <router-link
            v-if="item.ctaUrl"
            class="ac-cta"
            :href="item.ctaUrl"
            @click.stop="handleCtaClick"
            :to="`together/activities/${item.id}`">
            查看詳情
        </router-link>
    </article>
</template>

<script setup>
import { computed, ref, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

/**
 * 預期的資料格式（從 PHP 回傳）
 * {
 *   id: string|number,           // 活動編號
 *   date: string,                // 活動日期
 *   title: string,               // 活動標題
 *   mountainId: number,          // 山岳編號（對應 MOUNTAIN_ID）
 *   tags?: string[],            // 標籤陣列
 *   ctaText?: string,           // 按鈕文字
 *   ctaUrl?: string,            // 按鈕連結
 *   joinQty?: number,           // 報名人數
 *   eventTime?: string,         // 活動時間
 *   meetingPlace?: string,      // 集合地點
 *   distance?: string,          // 路程
 *   content?: string,           // 活動簡介
 *   status?: number             // 活動狀態
 * }
 */

// 定義 props
const props = defineProps({
    item: {
        type: Object,
        required: true
    },
    index: {
        type: Number,
        default: 0
    }
})

// 定義事件
const emit = defineEmits(['cta-click'])

// 儲存圖片資料的響應式變數
// key: MOUNTAIN_ID, value: 圖片資訊物件
const imageDataMap = ref({})

// 載入狀態
const isLoading = ref(false)
const loadError = ref(null)

const fetchImageData = async () => {
    try {
        isLoading.value = true
        loadError.value = null
        
        // 呼叫 API 取得圖片資料
        const response = await axios.get(
            `${import.meta.env.VITE_AJAX_URL}/eventCardImg.php`
        )
        
        // 檢查回應是否成功
        if (response.data.success) {
            // 將圖片資料存入 map
            imageDataMap.value = response.data.data
            // console.log('圖片資料載入成功:', imageDataMap.value)
        } else {
            // console.error('取得圖片資料失敗:', response.data.message)
            loadError.value = response.data.message
        }
    } catch (error) {
        // console.error('API 呼叫錯誤:', error)
        loadError.value = error.message
    } finally {
        isLoading.value = false
    }
}

/**
 * 組件掛載時取得圖片資料
 */
onMounted(() => {
    // 檢查是否已有圖片資料，避免重複載入
    if (Object.keys(imageDataMap.value).length === 0) {
        fetchImageData()
    }
})


watch(() => props.item.mountainId, (newId) => {
    if (newId && !imageDataMap.value[newId] && !isLoading.value) {
        fetchImageData()
    }
})


const baseUrl = computed(() => {
    // 從環境變數取得 AJAX URL
    const ajaxUrl = import.meta.env.VITE_AJAX_URL || ''
    return ajaxUrl.replace('/PHP', '/')
})


const imageUrl = computed(() => {
    // 取得活動的 mountainId
    const mountainId = props.item.mountainId
    
    // 檢查是否有對應的圖片資料
    if (mountainId && imageDataMap.value[mountainId]) {
        // 取得圖片資料
        const imageData = imageDataMap.value[mountainId]
        
        // 檢查是否有圖片檔名
        if (imageData.imageName) {
            const fullUrl = `${baseUrl.value}images/Mountain/${mountainId}/${imageData.imageName}`
            // console.log(`活動 ${props.item.id} (Mountain ID: ${mountainId}) 使用圖片: ${fullUrl}`)
            return fullUrl
        }
    }
    
    // 沒有對應圖片時，使用預設圖片
    const defaultUrl = `${baseUrl.value}images/eventCard/cardimg1.jpg`
    // console.log(`活動 ${props.item.id} 使用預設圖片: ${defaultUrl}`)
    return defaultUrl
})

const defaultImage = computed(() => `${baseUrl.value}images/eventCard/cardimg1.jpg`)
const dateText = computed(() => props.item?.date ?? '')

function handleCtaClick() {
    emit('cta-click', props.item)
}

function handleImageError(event) {
    // 將圖片來源替換為預設圖片
    event.target.src = defaultImage.value

}

// 取得路由實例
const router = useRouter()

defineExpose({
    fetchImageData,
    imageDataMap
})
</script>

<style scoped lang="scss">
@import '@/assets/styles/main.scss';

/* ========== card ========== */
.activity-card {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    
    width: 100%;
    max-width: 330px;
    padding: 40px;
    border: 1px dashed $black-14;
    border-radius: 16px;
    background: #fff;
    
    transition: background-color 0.3s ease, color 0.3s ease;
}
.activity-card:hover {
    background-color: #EBEBDF;
    color: #292C4B;
}

.top-content {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.ac-date {
    font-size: $pcFont-p-m;
    font-weight: $medium;
}

/* image block */
.ac-img {
    display: block;
    width: 100%;
    height: auto;
    aspect-ratio: 16/10;
    object-fit: cover;
    border-radius: 8px;
    transition: border-radius 0.3s ease;
}
.activity-card:hover .ac-img {
    border-radius: 56px 100px 56px 100px;
}

/* title */
.ac-title {
    font-size: $pcFont-H4;
    font-weight: $bold;
    line-height: $lineHeight-title-120;
}

/* tags */
.ac-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    list-style: none;
}
.tag {
    display: inline-block;
    padding: 8px;
    color: #fff;
    font-size: $pcFont-label;
    font-weight: $medium;
    background-color: $tag;
    border-radius: 4px;
    white-space: nowrap;
    
    transition: background-color 0.3s ease;
}
.activity-card:hover .tag {
    background-color: #292C4B;
}

/* CTA */
.ac-cta {
    display: flex;
    justify-content: end;
    align-items: center;
    gap: 8px;
    
    margin-top: 40px;
    
    font-size: $pcFont-p-s;
    font-weight: $semiBold;
    color: $black-14;
    text-decoration: 1px underline;
    text-underline-offset: 6px;
}

// RWD
@media screen and (max-width:768px) {
    .activity-card {
        max-width: none;
        width: 100%;
        padding: 20px;
        border-radius: 16px;
        margin: 0;
    }
    .top-content {
        gap: 12px;
    }
    .ac-date {
        font-size: 16px;
        font-weight: $medium;
    }
    .ac-img {
        border-radius: 6px;
        aspect-ratio: 16/9;
    }
    .ac-title {
        font-size: 20px;
        font-weight: $medium;
        line-height: 1.2;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ac-tags {
        gap: 6px;
    }
    .tag {
        padding: 4px 6px;
        font-size: 12px;
        font-weight: $medium;
        border-radius: 3px;
        max-width: 80px;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ac-cta {
        margin-top: 20px;
        font-size: 16px;
        font-weight: $semiBold;
        text-underline-offset: 4px;
    }
    
    .activity-card:hover {
        background-color: #EBEBDF;
        color: #292C4B;
    }
    
    .activity-card:hover .tag {
        background-color: #292C4B;
    }
}
</style>