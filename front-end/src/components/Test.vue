<script setup>
// static dulu, nanti gampang diganti v-for dari API
</script>

<template>
  <div class="flex min-h-screen flex-col bg-[#0f0505] font-poppins">
    <!-- NAVBAR -->
    <div class="flex items-center justify-between bg-[#180808] p-[12px]">
      <h1 class="ml-[1em] text-[16px] font-bold text-[#9a203e] sm:ml-[3em] sm:text-[24px]">Resonate</h1>
      <img src="../assets/img/me.jpg" alt="me" class="h-[40px] w-[40px] rounded-full object-cover block sm:mr-[1em]" />
    </div>

    <!-- MENU -->
    <div class="my-[2em] flex justify-center">
      <a
        href=""
        class="rounded-l-[10px] bg-[#1c1516] p-[12px] text-[16px] font-semibold text-[#e5e5e5] hover:bg-[#130f0f] hover:text-[#cdcccc]">
        Jelajahi Pesan
      </a>
      <a
        href=""
        class="rounded-r-[10px] bg-[#9a203e] p-[12px] text-[16px] font-semibold text-[#e5e5e5] hover:bg-[#821c35] hover:text-[#cdcccc]">
        Pesan Saya
      </a>
    </div>

    <!-- CARDS -->
    <div class="grid grid-cols-1 gap-[2em] p-[2em] sm:grid-cols-2">
      <!-- CARD -->
      <div
        v-for="i in 2"
        :key="i"
        class="cursor-pointer rounded-[10px] bg-[#1c1516] p-[10px] transition-transform duration-200 hover:scale-[1.02]">
        <!-- MUSIC -->
        <div class="flex items-center justify-between rounded-[10px] bg-[#100c0d] p-[10px]">
          <div class="flex items-center">
            <img src="../assets/img/me.jpg" class="h-[60px] w-[60px] object-cover block" />
            <div class="ml-[1em] flex flex-col">
              <p class="m-0 -mb-[4px] text-[20px] font-extrabold text-[#e5e5e5]">.love</p>
              <p class="m-0 -mt-[4px] text-[20px] font-semibold text-[#8c8a8a]">wave to earth</p>
            </div>
          </div>

          <img src="../assets/img/play.svg" class="h-[55px] w-[55px] -mb-[4px]" />
        </div>

        <!-- KEPADA -->
        <p class="text-[20px] text-[#8c8a8a]">
          kepada:
          <span class="text-[#e5e5e5]">Anya</span>
        </p>

        <!-- PESAN -->
        <p class="text-[20px] text-[#8c8a8a]">
          pesan:
          <br />
          <span class="text-[#e5e5e5]">
            i love you, but i cant say to you. i hope you find my message. this is for you anya
          </span>
        </p>

        <!-- FROM -->
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-[10px]">
            <img src="../assets/img/me.jpg" class="h-[45px] w-[45px] rounded-full object-cover block" />
            <p class="font-semibold text-[#9a203e]">Hani</p>
          </div>
          <p class="text-[#8c8a8a]">21/01/2026</p>
        </div>

        <!-- ACTION -->
        <div class="mt-[1em] flex justify-end gap-[10px]">
          <button
            class="rounded-[8px] border border-[#8c8a8a] bg-[#1c1516] px-[16px] py-[10px] text-[16px] font-semibold text-[#8c8a8a] hover:bg-[#130f0f]">
            Edit
          </button>
          <button
            class="rounded-[8px] bg-[#9a203e] px-[16px] py-[10px] text-[16px] font-semibold text-[#e5e5e5] hover:bg-[#821c35]">
            Hapus
          </button>
        </div>
      </div>
    </div>

    <!-- LOAD MORE -->
    <div class="mb-[5em] flex justify-end pr-[2em] text-[#e5e5e5] gap-1.5">
      <button class="bg-[#0f0505] font-semibold uppercase hover:underline">Lihat lebih banyak cerita</button>
      <img src="../assets/img/arrow-down.svg" alt="arrow-down" class="w-[14px]" />
    </div>

    <!-- FOOTER -->
    <div class="mt-auto text-center text-[10px] text-[#e5e5e5]">
      <p>
        &copy; 2026
        <span class="text-[12px] text-[#9a203e]">Resonate</span>
      </p>
    </div>
  </div>
</template>
