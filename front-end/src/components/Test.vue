<script setup>
// data masih static, nanti gampang diubah ke v-for dari API
</script>

<template>
  <div class="flex min-h-screen flex-col bg-[#0f0505] font-poppins">
    <!-- NAVBAR -->
    <div class="flex items-center justify-between bg-[#180808] p-[12px]">
      <h1 class="ml-[1em] text-[16px] font-bold text-[#9a203e] sm:ml-[3em] sm:text-[24px]">Resonate</h1>
      <img src="../assets/img/me.jpg" alt="me" class="block h-[40px] w-[40px] rounded-full object-cover sm:mr-[1em]" />
    </div>

    <!-- SELAMAT DATANG -->
    <div class="pt-[1em] text-center text-[12px] text-[#e5e5e5]">
      <p>
        Selamat Datang
        <span class="font-bold text-[#9a203e]">Hani</span>
      </p>
      <p>Apa kabarmu hari ini?</p>
      <p>Sampaikan pesanmu jangan kamu pendam terus!</p>
    </div>

    <!-- MENU -->
    <div class="my-[2em] flex justify-center">
      <a
        href=""
        class="rounded-l-[10px] bg-[#9a203e] p-[12px] text-[16px] font-semibold text-[#e5e5e5] hover:bg-[#821c35] hover:text-[#cdcccc]">
        Jelajahi Pesan
      </a>
      <a
        href=""
        class="rounded-r-[10px] bg-[#1c1516] p-[12px] text-[16px] font-semibold text-[#e5e5e5] hover:bg-[#130f0f] hover:text-[#cdcccc]">
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
            <img src="../assets/img/me.jpg" class="block h-[60px] w-[60px] object-cover" />
            <div class="ml-[1em] flex flex-col">
              <p class="-mb-[4px] text-[20px] font-extrabold text-[#e5e5e5]">.love</p>
              <p class="-mt-[4px] text-[20px] font-semibold text-[#8c8a8a]">wave to earth</p>
            </div>
          </div>

          <img src="../assets/img/play.svg" class="-mb-[4px] h-[55px] w-[55px]" />
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
            <img src="../assets/img/me.jpg" class="block h-[45px] w-[45px] rounded-full object-cover" />
            <p class="font-semibold text-[#9a203e]">Hani</p>
          </div>
          <p class="text-[#8c8a8a]">21/01/2026</p>
        </div>
      </div>
    </div>

    <!-- LOAD MORE -->
    <div class="mb-[5em] flex justify-end pr-[2em] text-[#e5e5e5]">
      <button class="bg-[#0f0505] font-semibold uppercase hover:underline">Lihat lebih banyak cerita</button>
      <img src="../assets/img/arrow-down.svg" class="w-[14px]" />
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
