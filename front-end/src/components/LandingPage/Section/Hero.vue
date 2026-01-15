<script setup></script>
<template>
  <!-- HERO -->
  <div
    class="flex flex-col items-center justify-center gap-[20px] p-[2em] mt-[2em] text-[#e5e5e5] sm:flex-row sm:mt-[-1em] lg:mt-[-16px]">
    <div class="flex flex-col gap-[10px] justify-center">
      <h2 class="text-[20px] font-semibold text-center m-0">Ungkapkan Perasaanmu Lewat Nada!</h2>
      <h1 class="text-[28px] text-center m-0 sm:text-[26px] lg:text-[36px] font-semibold">
        Tulis pesanmu, sematkan lagu Spotify yang pas, dan biarkan dunia mendengarkannya.
      </h1>

      <div class="text-center">
        <RouterLink to="/login">
          <button
            class="cursor-pointer mt-[10px] bg-[#9a203e] text-[#e5e5e5] font-semibold tracking-[2px] px-[24px] py-[16px] rounded-[10px] border border-transparent transition-transform transition-colors duration-200 ease-out hover:text-[#9a203e] hover:bg-[#0F0505] hover:border-[#9a203e] hover:scale-105">
            Mulai Menulis
          </button>
        </RouterLink>
      </div>
    </div>

    <div class="mt-[3em] sm:mt-0 sm:mr-[1em]">
      <img src="../../../assets/img/note.svg" alt="" class="w-[320px] md:w-[700px]" />
    </div>
  </div>
</template>
