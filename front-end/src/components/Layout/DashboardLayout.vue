<script setup>
import FooterDashboard from "../User/Section/FooterDashboard.vue";
import NavbarDashboard from "../User/Section/NavbarDashboard.vue";
</script>
<template>
  <div class="min-h-screen bg-[#0f0505] font-poppins flex flex-col">
    <NavbarDashboard />
    <RouterView />
    <FooterDashboard />
  </div>
</template>

<style scoped>
body {
  background-color: #0f0505 !important;
}
</style>
