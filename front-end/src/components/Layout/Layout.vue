<script setup>
import Footer from "../User/Section/Footer.vue";
import Navbar from "../User/Section/Navbar.vue";
</script>

<template>
  <div class="min-h-screen bg-[#0f0505] font-poppins overflow-y-hidden">
    <Navbar />
    <RouterView />
    <Footer />
  </div>
</template>

<style scoped>
body {
  overflow-y: hidden !important;
}
</style>
