<script setup>
// App.vue sekarang hanya bertugas sebagai wadah utama (RouterView)
// Logika inisialisasi tema sudah ditangani di level modul (useCardTheme.js)
</script>

<template>
  <RouterView v-slot="{ Component }">
    <Transition name="slide-fade" mode="out-in">
      <component :is="Component" />
    </Transition>
  </RouterView>
</template>

<style scoped></style>
