<template>
    <article class="activity-card">
        <!-- 日期 -->
        <time class="activity-card_date">{{ dateText }}</time>

        <!-- 圖片 -->
        <img
        class="activity-card_img"
        :src="image"
        :alt="imageAlt"
        loading="lazy"
        decoding="async"
        />

        <!-- 內容 -->
        <div class="activity-card_body">
            <h3 class="activity-card_title">
                {{ title }}
            </h3>

            <ul class="activity-card_tags" v-if="tags?.length">
                <li class="activity-card_tag" v-for="(tag, i) in tags" :key="i">
                    {{ tag }}
                </li>
            </ul>
        </div>

        <!-- CTA -->
        <a
        class="activity-card_cta"
        :href="href"
        aria-label="查看詳情"
        >
        查看詳情
        </a>
    </article>

</template>

<script setup>

</script>

<style scoped lang="scss">
    @import '../assets/styles/main.scss';
</style>