<script setup>
import FooterDashboard from "../Dashboard/Section/FooterDashboard.vue";
import NavbarDashboard from "../Dashboard/Section/NavbarDashboard.vue";
</script>

<template>
  <div class="min-h-screen bg-[#0f0505] font-poppins flex flex-col">
    <NavbarDashboard />

    <RouterView v-slot="{ Component }">
      <Transition name="fade">
        <component :is="Component" />
      </Transition>
    </RouterView>

    <FooterDashboard />
  </div>
</template>

<style scoped></style>
