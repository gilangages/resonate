<script setup>
import Ajakan from "./Section/Ajakan.vue";
import Fitur from "./Section/Fitur.vue";
import Footer from "./Section/Footer.vue";
import Hero from "./Section/Hero.vue";
import Navbar from "./Section/Navbar.vue";
import PesanLain from "./Section/PesanLain.vue";
</script>
<template>
  <div class="flex flex-col min-h-screen bg-[#0f0505] font-poppins">
    <Navbar />
    <Hero />
    <PesanLain />
    <Fitur />
    <Ajakan />
    <Footer />
  </div>
</template>
<style scoped></style>
