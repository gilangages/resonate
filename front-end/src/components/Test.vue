<template>
  <div class="flex flex-col min-h-screen bg-[#0f0505] font-[Poppins]">
    <!-- NAVBAR -->
    <div class="flex items-center justify-between bg-[#180808] p-[12px] text-[#e5e5e5]">
      <h1 class="text-[16px] text-[#9a203e] m-0 sm:ml-[1em]">Resonate</h1>

      <div class="flex items-center gap-[10px] pr-[10px] justify-center">
        <button class="hidden lg:block bg-[#180808] text-[#e5e5e5] text-[14px] font-semibold hover:text-[#8c8a8a]">
          Daftar
        </button>
        <button
          class="bg-[#9a203e] text-[#e5e5e5] text-[14px] font-semibold px-[16px] py-[8px] rounded-[20px] hover:bg-[#7d1a33]">
          Masuk
        </button>
      </div>
    </div>

    <!-- HERO -->
    <div
      class="flex flex-col items-center justify-center gap-[20px] p-[2em] mt-[2em] text-[#e5e5e5] sm:flex-row sm:mt-[-1em] lg:mt-[-16px]">
      <div class="flex flex-col gap-[10px] justify-center">
        <h2 class="text-[20px] font-semibold text-center m-0">Ungkapkan Perasaanmu Lewat Nada!</h2>
        <h1 class="text-[28px] text-center m-0 sm:text-[26px] lg:text-[36px]">
          Tulis pesanmu, sematkan lagu Spotify yang pas, dan biarkan dunia mendengarkannya.
        </h1>

        <div class="text-center">
          <button
            class="mt-[10px] bg-[#9a203e] text-[#e5e5e5] font-semibold tracking-[2px] px-[24px] py-[16px] rounded-[10px] hover:bg-[#7d1a33]">
            Mulai Menulis
          </button>
        </div>
      </div>

      <div class="mt-[3em] sm:mt-0 sm:mr-[1em]">
        <img src="../assets/img/note.svg" alt="" class="w-[320px]" />
      </div>
    </div>

    <!-- PESAN PENGGUNA LAIN -->
    <div class="mt-[4em]">
      <div class="flex justify-between items-center text-[#e5e5e5] mx-[2.5em] -mb-[1em]">
        <h2 class="text-[20px] font-semibold">Dengarkan Pesan Pengguna Lain</h2>

        <div class="hidden sm:flex items-center cursor-pointer">
          <button class="bg-transparent border-none uppercase text-[12px] font-semibold text-[#e5e5e5] hover:underline">
            Lihat semua pesan
          </button>
          <img src="../assets/img/arrow-right.svg" class="w-[14px]" />
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
              <img src="../assets/img/me.jpg" class="w-[60px] h-[60px] object-cover" />
              <div class="flex flex-col ml-[1em]">
                <p class="text-[20px] font-extrabold text-[#e5e5e5] m-0 -mb-[4px]">.love</p>
                <p class="text-[20px] font-semibold text-[#8c8a8a] m-0 -mt-[4px]">wave to earth</p>
              </div>
            </div>

            <img src="../assets/img/play.svg" class="w-[55px] h-[55px] -mb-[4px]" />
          </div>

          <p class="text-[20px] text-[#8c8a8a]">
            kepada:
            <span class="text-[#e5e5e5]">Anya</span>
          </p>

          <p class="text-[20px] text-[#8c8a8a]">
            pesan:
            <br />
            <span class="text-[#e5e5e5]">
              i love you, but i cant say to you. i hope you find my message. this is for you anya
            </span>
          </p>

          <div class="flex justify-between items-center">
            <div class="flex items-center gap-[10px]">
              <img src="../assets/img/me.jpg" class="w-[45px] h-[45px] rounded-full object-cover" />
              <p class="text-[#9a203e] font-semibold">Hani</p>
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
        <img src="../assets/img/arrow-right.svg" class="w-[16px]" />
      </div>
    </div>

    <!-- FITUR -->
    <div class="p-[2em]">
      <h2 class="text-[#e5e5e5] text-[20px] font-semibold">Fitur Utama</h2>

      <div class="flex flex-col sm:flex-row">
        <div
          v-for="fitur in 3"
          :key="fitur"
          class="flex flex-col items-center justify-center mt-[2em] text-center text-[#e5e5e5]">
          <img
            :src="
              fitur === 1
                ? '../../src/assets/img/spotify.svg'
                : fitur === 2
                ? '../../src/assets/img/samaran.svg'
                : '../../src/assets/img/personal.svg'
            "
            class="w-[120px]" />
          <h3 class="mt-2">
            {{ fitur === 1 ? "Integrasi Spotify" : fitur === 2 ? "Mode Anonim" : "Personal Space" }}
          </h3>
          <p class="text-[#8c8a8a] text-[16px] px-[1em]">
            {{
              fitur === 1
                ? "Cari dan sematkan jutaan lagu langsung dari Spotify. Preview 30 detik otomatis tersedia"
                : fitur === 2
                ? "Ingin curhat atau mengungkapkan perasaan dengan rahasia? Gunakan inisial nama agar identitasmu tetap terjaga."
                : "Kelola catatanmu sendiri. Edit atau hapus kapan saja kamu mau."
            }}
          </p>
        </div>
      </div>
    </div>

    <!-- AJAKAN -->
    <div class="flex flex-col items-center justify-center p-[2em] mt-[2em] text-[#e5e5e5]">
      <h1 class="text-[26px] m-0">Mulai Tulis Pesanmu</h1>
      <p class="text-[#8c8a8a] text-[16px] text-center m-0">
        Rekam setiap perasaan dan momen berhargamu yang dipadukan dengan musik
      </p>

      <button
        class="mt-[2em] bg-[#9a203e] px-[1em] py-[1em] rounded-[10px] font-semibold tracking-[2px] text-[16px] hover:bg-[#7d1a33]">
        Buat Akun
      </button>
    </div>

    <!-- FOOTER -->
    <div class="text-center text-[#e5e5e5] text-[10px] mt-[4em] mb-[1em]">
      © 2026
      <span class="text-[#9a203e] text-[12px]">Resonate</span>
    </div>
  </div>
</template>

<script setup>
// pure UI – belum ada logic
</script>
