<script setup>
// Kita pakai SVG langsung untuk icon musik biar gak perlu cari file gambar lagi
// Ini icon "Music Note" universal (Generic)
const musicIconSvg = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" /></svg>`;

const features = [
  {
    title: "Mode Anonim",
    desc: "Ingin curhat atau mengungkapkan perasaan dengan rahasia? Gunakan nama inisial agar identitasmu tetap terjaga.",
    img: "../../src/assets/img/samaran.svg", // Pastikan file ini ada
    isGeneric: false,
  },
  {
    title: "Pustaka Musik", // Dulu "Integrasi Deezer", sekarang jadi umum
    desc: "Cari dan sematkan jutaan lagu favoritmu. Preview 30 detik otomatis tersedia untuk menghidupkan pesanmu.",
    img: musicIconSvg, // Pakai icon musik umum
    isGeneric: true,
  },
  {
    title: "Personal Space",
    desc: "Kelola pesanmu sendiri. Edit kesalahan ketik atau hapus pesan kapan saja kamu mau.",
    img: "../../src/assets/img/personal.svg", // Pastikan file ini ada
    isGeneric: false,
  },
];
</script>

<template>
  <div class="p-[2em]">
    <h2 class="text-[#e5e5e5] text-[20px] font-semibold sm:text-left">Fitur Utama</h2>

    <div class="flex flex-col sm:flex-row gap-6">
      <div
        v-for="(item, index) in features"
        :key="index"
        class="flex-1 flex flex-col items-center justify-start mt-[2em] text-center text-[#e5e5e5]">
        <div class="w-[100px] h-[100px] flex items-center justify-center transition-transform hover:scale-105">
          <img
            :src="item.img"
            class="w-full h-full object-contain"
            :class="{
              'filter brightness-0 invert opacity-80': !item.isGeneric,
              'opacity-90 drop-shadow-[0_0_10px_rgba(255,255,255,0.3)]': item.isGeneric,
            }" />
        </div>

        <h3 class="mt-4 mb-3 font-bold text-lg">{{ item.title }}</h3>
        <p class="text-[#8c8a8a] text-[15px] px-[1em] leading-relaxed">
          {{ item.desc }}
        </p>
      </div>
    </div>
  </div>
</template>
