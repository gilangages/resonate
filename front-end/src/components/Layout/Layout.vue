<script setup>
import Footer from "../User/Section/FooterUser.vue";
import NavbarUser from "../User/Section/NavbarUser.vue";
</script>

<template>
  <div class="min-h-screen bg-[#0f0505] font-poppins overflow-y-hidden">
    <NavbarUser />
    <RouterView />
    <Footer />
  </div>
</template>

<style scoped>
body {
  overflow-y: hidden !important;
}
</style>
