<template>
  <footer class="footer">© {{ year }} MountainPeak</footer>
</template>

<script setup>
const year = new Date().getFullYear()
</script>

<style scoped lang="scss">
.footer {
  text-align: center;
  color: #2f3e37;
  padding: 8px 0;
  opacity: .7;
}
</style>
