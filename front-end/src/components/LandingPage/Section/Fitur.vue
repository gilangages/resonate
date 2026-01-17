<script setup>
import { ref } from "vue";

// --- ICON ASSETS (SVG Strings) ---

const deezerIconSvg = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" /></svg>`;

const shareIconSvg = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>`;

const themeIconSvg = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" /></svg>`;

// Icon Notifikasi (Lonceng)
const notifIconSvg = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>`;

const replyIconSvg = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>`;

// Icon Mata Tertutup (Blind/Hidden) - BARU
const blindIconSvg = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>`;

const googleIconSvg = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>`;

// Icon Shield (Banding)
const shieldIconSvg = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>`;

// --- DATA FEATURES (Total 9 Item) ---
const features = ref([
  {
    title: "Mode Anonim",
    desc: "Ingin menyampaikan perasaan kepada seseorang tapi tak ingin identitasmu diketahui? Gunakan mode anonim (inisial) agar kamu bisa bebas bercerita tanpa rasa khawatir.",
    img: "../../../public/samaran.svg",
    isGeneric: false,
  },
  {
    title: "Pustaka Deezer",
    desc: "Temukan jutaan lagu dari Deezer. Sematkan lagu yang pas dan biarkan orang lain mendengarkan cuplikannya.",
    img: deezerIconSvg,
    isGeneric: true,
  },
  {
    title: "Respon dengan Lagu",
    desc: "Kata-kata saja tidak cukup? Balas curhatan orang lain dengan lagu yang paling mewakili perasaanmu.",
    img: replyIconSvg,
    isGeneric: true,
  },
  {
    title: "Ruang Personal",
    desc: "Kontrol penuh di tanganmu. Edit kesalahan ketik atau hapus pesan kapan saja kamu mau.",
    img: "../../../../public/personal.svg",
    isGeneric: false,
  },
  {
    title: "Share ke Story",
    desc: "Ingin membagikan pesanmu? Unduh pesan sebagai gambar yang estetik hanya dengan satu klik.",
    img: shareIconSvg,
    isGeneric: true,
  },
  {
    title: "Tema & Kustomisasi",
    desc: "Ekspresikan mood kamu. Ganti tema warna kartu pesan sesuka hati agar tampilannya tidak membosankan.",
    img: themeIconSvg,
    isGeneric: true,
  },
  {
    // UPDATED: Lebih fokus ke sistem & komunitas, bukan "Admin"
    title: "Moderasi Objektif",
    desc: "Sistem moderasi kami bekerja secara adil. Peninjauan konten dilakukan tanpa melihat identitas pengirim, sehingga privasimu tetap terjaga 100%.",
    img: blindIconSvg, // Icon bisa tetap sama atau ganti shield
    isGeneric: true,
  },
  {
    // UPDATED: Lebih 'reassuring' (menenangkan)
    title: "Pusat Bantuan",
    desc: "Terjadi kesalahan pemblokiran? Jangan khawatir, kamu bisa mengajukan banding dengan mudah melalui formulir yang tersedia otomatis.",
    img: shieldIconSvg,
    isGeneric: true,
  },
  {
    title: "Akses Kilat",
    desc: "Masuk dengan akun Google dalam hitungan detik. Tanpa ribet isi formulir, langsung tulis dan jelajahi pesan.",
    img: googleIconSvg,
    isGeneric: true,
  },
]);
</script>

<template>
  <div class="p-[2em]">
    <h2 class="text-[#e5e5e5] text-[20px] font-semibold sm:text-left mb-8">Fitur Unggulan</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 justify-items-center">
      <div
        v-for="(item, index) in features"
        :key="index"
        class="group flex flex-col items-center justify-start p-6 rounded-2xl transition-all duration-300 hover:bg-white/5 hover:-translate-y-2 border border-transparent hover:border-white/10 text-center text-[#e5e5e5] cursor-default bg-white/[0.02] w-full sm:last:col-span-2 sm:last:w-[calc(50%-12px)] lg:last:col-span-1 lg:last:w-full">
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
