<template>
    <div class="pagination">
        <button 
          v-for="page in totalPages" 
          :key="page"
          :class="['page-btn', { active: page === currentPage }]"
          @click="setPage(page)"
        >
          {{ page }}
        </button>
    </div>
</template>

<script setup>

</script>

<style scoped lang="scss">
@import '@/assets/styles/main.scss';

.pagination {
    margin-top: 40px;
    display: flex;
    justify-content: center;
    gap: 8px;

    .page-btn {
      width: 40px;
      height: 40px;
      border: 1px solid #ddd;
      background: white;
      border-radius: 6px;
      cursor: pointer;
      transition: all 0.2s;

      &:hover {
        // background: $accent-color;
        color: white;
        // border-color: $accent-color;
      }

      &.active {
        // background: $primary-color;
        color: white;
        // border-color: $primary-color;
      }
    }
  }

</style>