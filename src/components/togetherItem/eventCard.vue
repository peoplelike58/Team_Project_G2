<template>
    <!-- Card -->
    <article class="activity-card">
        <div class="top-content">
            <!-- 日期 -->
            <slot name="date" :item="item" :dateText="dateText" :index="index">
                <time class="ac-date">{{ dateText }}</time>
            </slot>

            <!-- 圖片 -->
            <slot name="image" :item="item" :index="index">
                <!-- <img class="ac-img" src="@/assets/images/eventCard/cardimg1.jpg"
                loading="lazy"/> -->
                <img class="ac-img" :src="item.imageUrl" loading="lazy"/>
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

<script setup="setup">
    import {computed} from 'vue'
    import {useRouter} from 'vue-router'

    /**
 * 預期的資料格式
 * {
 *   id: string|number,
 *   date: string,
 *   title: string,
 *   imageUrl: string,
 *   tags?: string[],
 *   ctaText?: string,
 *   ctaUrl?: string
 * }
 */

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

    const emit = defineEmits(['cta-click'])

    const dateText = computed(
        () => props.item
            ?.date ?? ''
    )

    function handleCtaClick() {
        emit('cta-click', props.item)
    }

    const route = useRouter()
</script>

<style scoped="scoped" lang="scss">
    @import '@/assets/styles/main.scss';

    /* ========== card ========== */
    .activity-card {
        display: flex;
        flex-direction: column;
        justify-content: space-between;

        width: 100%;
        max-width: 270px;
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
    @media screen and (max-width:430px) {
        .activity-card {
            max-width: none;
            width: 75%;
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