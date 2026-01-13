<script setup></script>
<template>
  <!-- PESAN PENGGUNA LAIN -->
  <div class="mt-[4em]">
    <div class="flex justify-between items-center text-[#e5e5e5] mx-[2.5em] -mb-[1em]">
      <h2 class="text-[20px] font-semibold">Dengarkan Pesan Pengguna Lain</h2>

      <div class="hidden sm:flex gap-1 items-center cursor-pointer">
        <button
          class="cursor-pointer bg-transparent border-none uppercase text-[12px] font-semibold text-[#e5e5e5] hover:underline">
          Lihat semua pesan
        </button>
        <img src="../../../assets/img/arrow-right.svg" class="w-[14px]" />
      </div>
    </div>

    <!-- CARDS -->
    <div class="grid grid-cols-1 gap-[2em] p-[2em] sm:grid-cols-2">
      <div
        v-for="i in 2"
        :key="i"
        class="bg-[#1c1516] p-[10px] rounded-[10px] cursor-pointer transition-transform duration-200 hover:scale-[1.02]">
        <!-- MUSIC -->
        <div class="flex items-center justify-between bg-[#100c0d] p-[10px] rounded-[10px]">
          <div class="flex items-center">
            <img src="../../../assets/img/me.jpg" class="w-[60px] h-[60px] object-cover" />
            <div class="flex flex-col ml-[1em]">
              <p class="text-[20px] font-extrabold text-[#e5e5e5] m-0 -mb-[4px]">.love</p>
              <p class="text-[20px] font-semibold text-[#8c8a8a] m-0 -mt-[4px]">wave to earth</p>
            </div>
          </div>

          <img src="../../../assets/img/play.svg" class="w-[55px] h-[55px] -mb-[4px]" />
        </div>

        <p class="text-[20px] text-[#8c8a8a] my-4">
          kepada:
          <span class="text-[#e5e5e5]">Anya</span>
        </p>

        <p class="text-[20px] text-[#8c8a8a] my-4">
          pesan:
          <br />
          <span class="text-[#e5e5e5]">
            i love you, but i cant say to you. i hope you find my message. this is for you anya
          </span>
        </p>

        <div class="flex justify-between items-center mt-8 mb-4">
          <div class="flex items-center gap-[10px]">
            <img src="../../../assets/img/me.jpg" class="w-[45px] h-[45px] rounded-full object-cover" />
            <p class="text-[#9a203e] font-bold">Hani</p>
          </div>
          <p class="text-[#8c8a8a]">21/01/2026</p>
        </div>
      </div>
    </div>

    <!-- LIHAT LEBIH (HP) -->
    <div class="flex justify-end mr-[2em] sm:hidden">
      <button class="uppercase text-[12px] font-semibold text-[#e5e5e5] bg-transparent border-none">
        Lihat semua pesan
      </button>
      <img src="../../../assets/img/arrow-right.svg" class="w-[16px]" />
    </div>
  </div>
</template>
