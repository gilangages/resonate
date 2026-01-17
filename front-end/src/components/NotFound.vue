<script setup>
import { useRouter } from "vue-router";

const router = useRouter();

function goBack() {
  const token = localStorage.getItem("token");

  if (token) {
    router.push({
      path: "/dashboard",
    });
  } else {
    router.push({
      path: "/",
    });
  }
}
</script>

<template>
  <div
    class="flex flex-col items-center justify-center min-h-screen bg-[#0f0505] text-[#e5e5e5] font-jakarta p-4 text-center">
    <div class="mb-6 relative">
      <h1 class="text-[120px] font-bold text-[#9a203e] opacity-20 select-none">404</h1>
      <div class="absolute inset-0 flex items-center justify-center">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="80"
          height="80"
          viewBox="0 0 24 24"
          fill="none"
          stroke="#e5e5e5"
          stroke-width="1.5"
          stroke-linecap="round"
          stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="15" y1="9" x2="9" y2="15"></line>
          <line x1="9" y1="9" x2="15" y2="15"></line>
        </svg>
      </div>
    </div>

    <h2 class="text-2xl md:text-3xl font-bold mb-3">Halaman Tidak Ditemukan</h2>
    <p class="text-[#8c8a8a] max-w-md mb-8">
      Oops! Sepertinya nada yang kamu cari fals atau halaman ini sudah menghilang dari peredaran.
    </p>

    <button
      v-on:click="goBack"
      class="cursor-pointer bg-[#9a203e] text-[#e5e5e5] px-8 py-3 rounded-[12px] font-semibold tracking-wide transition-all duration-300 hover:bg-[#b92b4f] hover:scale-105 shadow-[0_0_20px_rgba(154,32,62,0.4)]">
      Kembali ke Beranda
    </button>
  </div>
</template>

<style scoped>
/* Pastikan font terload */
.font-jakarta {
  font-family: "Plus Jakarta Sans", sans-serif;
}
</style>
