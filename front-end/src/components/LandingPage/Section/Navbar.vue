<script></script>
<template>
  <!-- NAVBAR -->
  <div class="flex items-center justify-between bg-[#180808] p-[12px] text-[#e5e5e5] sticky top-0 z-50">
    <RouterLink to="/" data-title="Resonate" class="tooltip-container-mid no-underline">
      <h1
        class="text-[16px] sm:text-[24px] text-[#9a203e] m-0 sm:ml-[1em] font-bold hover:text-[#b92b4a] transition-colors cursor-pointer">
        Resonate
      </h1>
    </RouterLink>
    <div class="flex items-center gap-[10px] pr-[10px] justify-center">
      <RouterLink
        to="/register"
        class="cursor-pointer hidden lg:block bg-[#180808] text-[#e5e5e5] text-[14px] font-semibold hover:text-[#8c8a8a]">
        Daftar
      </RouterLink>
      <RouterLink
        to="/login"
        class="cursor-pointer bg-[#180808] transition-transform duration-200 ease-out hover:scale-105 border border-[#9a203e] hover:bg-[#9a203e] text-[#e5e5e5] text-[14px] font-semibold px-[16px] py-[8px] rounded-[20px]">
        Masuk
      </RouterLink>
    </div>
  </div>
</template>
<style scoped></style>
