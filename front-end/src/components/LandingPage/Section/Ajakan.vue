<script setup></script>
<template>
  <!-- AJAKAN -->
  <div class="flex flex-col items-center justify-center p-[2em] mt-[2em] text-[#e5e5e5]">
    <h1 class="text-[26px] m-0 font-bold">Mulai Tulis Pesanmu</h1>
    <p class="text-[#8c8a8a] text-[16px] text-center m-0">
      Rekam setiap perasaan dan momen berhargamu yang dipadukan dengan musik.
    </p>

    <RouterLink
      to="/register"
      class="cursor-pointer mt-[2em] bg-[#9a203e] px-[1em] py-[1em] rounded-[10px] font-semibold tracking-[2px] text-[16px] transition-transform duration-200 ease-out hover:bg-[#0F0505] border border-transparent hover:border-[#9a203e] hover:text-[#9a203e] hover:scale-105">
      Buat Akun
    </RouterLink>
  </div>
</template>
