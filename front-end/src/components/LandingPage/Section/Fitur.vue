<script setup>
import { ref } from "vue";

// --- ICON ASSETS (SVG Strings) ---
// Icon Deezer
const deezerIconSvg = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" /></svg>`;

const shareIconSvg = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>`;

const themeIconSvg = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" /></svg>`;

const infoIconSvg = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;

// Icon Reply
const replyIconSvg = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>`;

// Icon Google
const googleIconSvg = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>`;

// --- DATA FEATURES ---
const features = ref([
  {
    title: "Mode Anonim",
    desc: "Ingin curhat atau mengungkapkan perasaan dengan rahasia? Gunakan nama inisial agar identitasmu tetap terjaga.",
    img: "../../src/assets/img/samaran.svg",
    isGeneric: false,
  },
  {
    title: "Pustaka Deezer",
    desc: "Temukan jutaan lagu dari Deezer. Sematkan lagu yang pas dan biarkan orang lain mendengarkan preview-nya.",
    img: deezerIconSvg,
    isGeneric: true,
  },
  {
    // REVISI: Judul dan Deskripsi disesuaikan logic (Balas pakai Lagu)
    title: "Respon dengan Lagu",
    desc: "Ada lagu yang cocok buat curhatan ini? Kirim lagu balasanmu (opsional dengan pesan) sebagai respon.",
    img: replyIconSvg,
    isGeneric: true,
  },
  {
    title: "Personal Space",
    desc: "Kelola pesanmu sendiri. Edit kesalahan ketik atau hapus pesan kapan saja kamu mau.",
    img: "../../src/assets/img/personal.svg",
    isGeneric: false,
  },
  {
    title: "Share sebagai Gambar",
    desc: "Ingin membagikan pesanmu ke Instagram Story? Unduh pesan sebagai gambar estetik dalam satu klik.",
    img: shareIconSvg,
    isGeneric: true,
  },
  {
    title: "Tema & Kustomisasi",
    desc: "Bosan dengan tampilan standar? Ubah tema kartu pesanmu agar sesuai dengan suasana hati atau warna favoritmu.",
    img: themeIconSvg,
    isGeneric: true,
  },
  {
    title: "Akses Instan",
    desc: "Masuk dengan akun Google dalam hitungan detik. Tanpa ribet registrasi, langsung tulis dan jelajahi pesan.",
    img: googleIconSvg,
    isGeneric: true,
  },
  {
    title: "Info Moderasi",
    desc: "Sistem notifikasi transparan. Kamu akan diberitahu langsung jika ada pesanmu yang dihapus oleh admin.",
    img: infoIconSvg,
    isGeneric: true,
  },
]);
</script>

<template>
  <div class="p-[2em]">
    <h2 class="text-[#e5e5e5] text-[20px] font-semibold sm:text-left mb-8">Fitur Unggulan</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 justify-items-center">
      <div
        v-for="(item, index) in features"
        :key="index"
        class="group flex flex-col items-center justify-start p-6 rounded-2xl transition-all duration-300 hover:bg-white/5 hover:-translate-y-2 border border-transparent hover:border-white/10 text-center text-[#e5e5e5] cursor-default bg-white/[0.02] w-full">
        <div
          class="w-[80px] h-[80px] flex items-center justify-center transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3 mb-4 bg-white/5 rounded-full p-4">
          <img
            :src="item.img"
            class="w-full h-full object-contain"
            :class="{
              'filter brightness-0 invert opacity-80': !item.isGeneric,
              'opacity-90 drop-shadow-[0_0_10px_rgba(255,255,255,0.3)]': item.isGeneric,
            }"
            alt="Fitur Icon" />
        </div>

        <h3 class="mt-2 mb-3 font-bold text-lg transition-colors group-hover:text-[#9a203e]">
          {{ item.title }}
        </h3>

        <p class="text-[#8c8a8a] text-[14px] leading-relaxed transition-colors group-hover:text-gray-300">
          {{ item.desc }}
        </p>
      </div>
    </div>
  </div>
</template>
