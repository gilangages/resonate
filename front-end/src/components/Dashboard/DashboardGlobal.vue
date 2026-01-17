<script setup>
import { ref } from "vue";
import GlobalContent from "./Section/GlobalContent.vue";
import WelcomeOverlay from "./Section/WelcomeOverlay.vue";

// Default FALSE agar GlobalContent tidak langsung muncul/nembak API
const canShowDashboard = ref(false);

const handleAnimationComplete = () => {
  canShowDashboard.value = true;
};
</script>

<template>
  <div class="min-h-screen bg-[#0f0505] relative">
    <WelcomeOverlay @animation-complete="handleAnimationComplete" />

    <template v-if="canShowDashboard">
      <GlobalContent />
    </template>

    <div v-else class="fixed inset-0 bg-[#0f0505] z-10"></div>
  </div>
</template>
