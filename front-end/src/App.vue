<script setup>
import { onMounted } from "vue";
import { useCardTheme } from "./lib/useCardTheme";

const { initTheme } = useCardTheme();
onMounted(() => {
  // Gunakan localStorage agar sinkron dengan store.js
  const storedUser = localStorage.getItem("user");
  if (storedUser) {
    const user = JSON.parse(storedUser);
    initTheme(user.id);
  } else {
    initTheme(null);
  }
});
</script>

<template>
  <RouterView />
</template>

<style scoped></style>
