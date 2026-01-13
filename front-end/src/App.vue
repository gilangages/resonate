<script setup>
import { onMounted } from "vue";
import { useCardTheme } from "./lib/useCardTheme";

const { initTheme } = useCardTheme();

onMounted(() => {
  // Cek apakah ada user yang sedang login di session storage
  const storedUser = sessionStorage.getItem("user");

  if (storedUser) {
    const user = JSON.parse(storedUser);
    // Restore tema milik user tersebut
    initTheme(user.id);
  } else {
    // Jika tidak ada user, pastikan tema default
    initTheme(null);
  }
});
</script>

<template>
  <RouterView />
</template>

<style scoped></style>
