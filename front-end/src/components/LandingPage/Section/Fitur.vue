<script setup></script>

<template>
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
        <h3 class="mt-2 font-bold mt-4 mb-4">
          {{ fitur === 1 ? "Integrasi Spotify" : fitur === 2 ? "Mode Anonim" : "Personal Space" }}
        </h3>
        <p class="text-[#8c8a8a] text-[16px] px-[1em]">
          {{
            fitur === 1
              ? "Cari dan sematkan jutaan lagu langsung dari Spotify. Preview 30 detik otomatis tersedia."
              : fitur === 2
              ? "Ingin curhat atau mengungkapkan perasaan dengan rahasia? Gunakan inisial nama agar identitasmu tetap terjaga."
              : "Kelola catatanmu sendiri. Edit atau hapus kapan saja kamu mau."
          }}
        </p>
      </div>
    </div>
  </div>
</template>
