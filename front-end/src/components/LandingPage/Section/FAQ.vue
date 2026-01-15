<script setup>
import { ref } from "vue";

const faqs = ref([
  {
    question: "Apakah saya harus menggunakan nama asli?",
    answer:
      "Tidak harus, senyamannya kamu saja. Kamu boleh pakai nama asli jika ingin temanmu tahu itu kamu, atau gunakan inisial/nama samaran jika ingin lebih privat dan misterius.",
    open: false,
  },
  {
    question: "Bagaimana cara membalas pesan yang saya suka?",
    // Ganti **...** dengan <strong>...</strong>
    answer:
      "Di sini musik adalah bahasa utamanya. Kamu bisa merespon curhatan dengan mengirimkan <strong>Lagu Balasan</strong> via Deezer yang mewakili perasaanmu, opsional dengan pesan teks singkat.",
    open: false,
  },
  {
    question: "Kenapa saya tidak bisa membalas 'reply' orang lain?",
    // Ganti **...** dengan <strong>...</strong>
    answer:
      "Music Note menerapkan konsep <strong>'One-Way Tribute'</strong>. Tujuannya agar interaksi tetap sederhana, fokus pada apresiasi lagu, dan menghindari debat panjang yang tidak perlu.",
    open: false,
  },
  {
    question: "Apakah saya perlu akun Premium untuk memutar lagu?",
    answer:
      "Tidak perlu! Berkat integrasi Deezer, siapa saja bisa mendengarkan cuplikan (preview) lagu selama 30 detik secara gratis tanpa perlu login atau berlangganan aplikasi musik apapun.",
    open: false,
  },
  {
    question: "Bagaimana jika saya menemukan pesan yang tidak pantas?",
    // Ganti **...** dengan <strong>...</strong>
    answer:
      "Sistem sudah memiliki filter kata otomatis. Namun jika masih ada yang lolos, kamu bisa melaporkannya melalui <strong>form Laporan Bug</strong> yang ada di menu <strong>'About'</strong>. Laporanmu akan segera ditinjau.",
    open: false,
  },
  {
    question: "Bisakah saya mengubah tampilan atau menghapus akun?",
    answer:
      "Tentu. Kamu bisa mengubah tema kartu dan foto profil (avatar) sesuka hati. Jika ingin berhenti, tersedia juga opsi untuk menghapus akun secara permanen di pengaturan profil.",
    open: false,
  },
]);

const toggleFaq = (index) => {
  // Logic Accordion: Jika diklik, tutup yang lain
  faqs.value.forEach((item, i) => {
    if (i === index) {
      item.open = !item.open;
    } else {
      item.open = false;
    }
  });
};
</script>

<template>
  <div class="p-[2em] max-w-4xl mx-auto">
    <h2 class="text-[#e5e5e5] text-[24px] font-semibold text-center mb-2">Pertanyaan Umum</h2>
    <p class="text-[#8c8a8a] text-center mb-10 text-sm">Masih bingung? Cari tahu jawabannya di sini.</p>

    <div class="space-y-4">
      <div
        v-for="(faq, index) in faqs"
        :key="index"
        class="border border-white/10 rounded-xl overflow-hidden bg-white/[0.02] transition-colors duration-300 hover:border-white/20">
        <button
          @click="toggleFaq(index)"
          class="w-full flex items-center justify-between p-5 text-left focus:outline-none group cursor-pointer">
          <span
            class="text-[#e5e5e5] font-medium transition-colors duration-300 group-hover:text-[#9a203e]"
            :class="{ 'text-[#9a203e]': faq.open }">
            {{ faq.question }}
          </span>

          <span
            class="text-white/50 transition-transform duration-300 transform"
            :class="{ 'rotate-180 text-[#9a203e]': faq.open }">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path
                fill-rule="evenodd"
                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                clip-rule="evenodd" />
            </svg>
          </span>
        </button>

        <div
          class="grid transition-[grid-template-rows] duration-300 ease-out"
          :class="faq.open ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'">
          <div class="overflow-hidden">
            <div class="px-5 pb-5 text-[#8c8a8a] text-sm leading-relaxed">
              <span v-html="faq.answer"></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
