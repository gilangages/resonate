<script setup>
import GlobalContent from "./Section/GlobalContent.vue";
// import MenuGlobal from "./Section/MenuGlobal.vue"; // HAPUS INI
import WelcomeOverlay from "./Section/WelcomeOverlay.vue"; // TAMBAH INI
</script>

<template>
  <div>
    <WelcomeOverlay />
    <div class="pt-6"><GlobalContent /></div>
  </div>
</template>
