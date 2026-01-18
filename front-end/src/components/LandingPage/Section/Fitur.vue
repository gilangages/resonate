<script setup>
import { ref } from "vue";

// --- ICON ASSETS (SVG Strings) ---

// --- NEW ICONS ---

// Pengganti samaran.svg: Topeng Anonim (Mask) - Melambangkan anonimitas dan kerahasiaan
// Pengganti samaran.svg: Topeng Anonim (Mask)
// FIXED: Ditambahkan header 'data:image...' dan ubah warna #ffffff jadi %23ffffff
const anonimIconSvg = `data:image/svg+xml;utf8,<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M21 16.5C21 18.433 19.433 20 17.5 20C15.567 20 14 18.433 14 16.5C14 14.567 15.567 13 17.5 13C19.433 13 21 14.567 21 16.5Z" fill="%23ffffff"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M1.25 10C1.25 9.58579 1.58579 9.25 2 9.25H22C22.4142 9.25 22.75 9.58579 22.75 10C22.75 10.4142 22.4142 10.75 22 10.75H2C1.58579 10.75 1.25 10.4142 1.25 10Z" fill="%23ffffff"></path> <path opacity="0.5" d="M4.1875 9.25L4.6138 7.54479C5.15947 5.36211 5.43231 4.27077 6.24609 3.63538C7.05988 3 8.1848 3 10.4347 3H13.5653C15.8152 3 16.9401 3 17.7539 3.63538C18.5677 4.27077 18.8405 5.36211 19.3862 7.54479L19.8125 9.25H4.1875Z" fill="%23ffffff"></path> <path d="M10 16.5C10 18.433 8.433 20 6.5 20C4.567 20 3 18.433 3 16.5C3 14.567 4.567 13 6.5 13C8.433 13 10 14.567 10 16.5Z" fill="%23ffffff"></path> <path opacity="0.5" d="M9.88379 17.3966C9.9594 17.1104 9.99968 16.8099 9.99968 16.5C9.99968 16.2272 9.96845 15.9616 9.90939 15.7067L10.323 15.4999C11.3787 14.972 12.6214 14.972 13.6771 15.4999L14.09 15.7064C14.0309 15.9614 13.9997 16.227 13.9997 16.5C13.9997 16.8098 14.0399 17.1101 14.1155 17.3961L13.0063 16.8415C12.3728 16.5248 11.6273 16.5248 10.9938 16.8415L9.88379 17.3966Z" fill="%23ffffff"></path> </g></svg>`;

// Pengganti personal.svg: Folder Terkunci/Terlindungi - Melambangkan ruang kontrol pribadi yang aman
const personalSpaceIconSvg = `data:image/svg+xml;utf8,<svg viewBox="-3.5 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="%23ffffff"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g id="icomoon-ignore"> </g> <path d="M6.294 14.164h12.588v1.049h-12.588v-1.049z" fill="%23ffffff"> </path> <path d="M6.294 18.36h12.588v1.049h-12.588v-1.049z" fill="%23ffffff"> </path> <path d="M6.294 22.557h8.392v1.049h-8.392v-1.049z" fill="%23ffffff"> </path> <path d="M15.688 3.674c-0.25-1.488-1.541-2.623-3.1-2.623s-2.85 1.135-3.1 2.623h-9.489v27.275h25.176v-27.275h-9.488zM10.49 6.082v-1.884c0-1.157 0.941-2.098 2.098-2.098s2.098 0.941 2.098 2.098v1.884l0.531 0.302c1.030 0.586 1.82 1.477 2.273 2.535h-9.803c0.453-1.058 1.243-1.949 2.273-2.535l0.53-0.302zM24.128 29.9h-23.078v-25.177h8.392v0.749c-1.638 0.932-2.824 2.566-3.147 4.496h12.588c-0.322-1.93-1.509-3.563-3.147-4.496v-0.749h8.392v25.177z" fill="%23ffffff"> </path> </g></svg>`;

// --- EXISTING ICONS (TIDAK DIUBAH) ---
const deezerIconSvg = `https://cdn.brandfetch.io/idEUKgCNtu/theme/dark/symbol.svg?c=1bxid64Mup7aczewSAYMX&t=1758260798610`;

const shareIconSvg = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>`;

const themeIconSvg = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" /></svg>`;

const replyIconSvg = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>`;

const blindIconSvg = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>`;

const googleIconSvg = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>`;

const shieldIconSvg = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>`;

// --- DATA FEATURES (Total 9 Item) ---
const features = ref([
  {
    title: "Mode Anonim",
    desc: "Ingin menyampaikan perasaan kepada seseorang tapi tak ingin identitasmu diketahui? Gunakan mode anonim (inisial) agar kamu bisa bebas bercerita tanpa rasa khawatir.",
    img: anonimIconSvg, // MENGGUNAKAN ICON BARU
    isGeneric: true,
  },
  {
    title: "Pustaka Deezer",
    desc: "Temukan jutaan lagu dari Deezer. Sematkan lagu yang pas dan biarkan orang lain mendengarkan cuplikannya.",
    img: deezerIconSvg,
    isGeneric: false,
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
    img: personalSpaceIconSvg, // MENGGUNAKAN ICON BARU
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
    title: "Moderasi Objektif",
    desc: "Sistem moderasi kami bekerja secara adil. Peninjauan konten dilakukan tanpa melihat identitas pengirim, sehingga privasimu tetap terjaga 100%.",
    img: blindIconSvg,
    isGeneric: true,
  },
  {
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
