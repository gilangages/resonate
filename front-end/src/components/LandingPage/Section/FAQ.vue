<script setup>
import { ref } from "vue";

const faqs = ref([
  {
    question: "Apakah identitas saya benar-benar aman?",
    answer:
      "Ya, 100%. Saat kamu mengirim pesan dalam Mode Anonim, kami tidak menampilkan nama atau foto profil aslimu. Kami hanya menampilkan inisial atau nama samaran yang kamu pilih.",
    isOpen: false,
  },
  {
    question: "Apakah layanan ini gratis?",
    answer:
      "Tentu saja! Kamu bisa mengirim pesan, menyematkan lagu dari Deezer, dan menggunakan fitur dasar lainnya secara gratis tanpa biaya langganan.",
    isOpen: false,
  },
  {
    question: "Bisakah saya menghapus pesan yang sudah terkirim?",
    answer:
      "Bisa. Kamu memiliki kontrol penuh di menu 'Pesan Saya'. Kamu bisa mengedit atau menghapus pesanmu kapan saja jika berubah pikiran.",
    isOpen: false,
  },
  {
    question: "Mengapa lagu yang saya cari tidak ada?",
    answer:
      "Kami menggunakan pustaka musik dari Deezer. Pastikan judul lagu atau nama artis yang kamu ketik sudah benar sesuai database Deezer.",
    isOpen: false,
  },
]);

const toggleFaq = (index) => {
  // Logic: Jika diklik, tutup yang lain, buka yang ini. Jika diklik lagi, tutup.
  faqs.value.forEach((faq, i) => {
    if (i === index) {
      faq.isOpen = !faq.isOpen;
    } else {
      faq.isOpen = false;
    }
  });
};
</script>

<template>
  <div class="p-[2em] max-w-4xl mx-auto font-jakarta">
    <div class="text-center mb-10">
      <h2 class="text-[#e5e5e5] text-[24px] font-bold mb-2">Pertanyaan Umum</h2>
      <p class="text-[#8c8a8a] text-sm">Hal yang sering ditanyakan pengguna baru</p>
    </div>

    <div class="flex flex-col gap-4">
      <div
        v-for="(item, index) in faqs"
        :key="index"
        @click="toggleFaq(index)"
        class="border border-white/10 rounded-xl bg-white/[0.02] overflow-hidden transition-all duration-300 cursor-pointer hover:bg-white/[0.05]"
        :class="{ 'border-[#9a203e]/50 bg-white/[0.05]': item.isOpen }">
        <div class="p-5 flex justify-between items-center">
          <h3 class="text-[#e5e5e5] font-semibold text-base sm:text-lg pr-4">{{ item.question }}</h3>
          <span
            class="text-[#9a203e] transition-transform duration-300 transform shrink-0"
            :class="item.isOpen ? 'rotate-180' : 'rotate-0'">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </span>
        </div>

        <div
          class="grid transition-all duration-300 ease-in-out"
          :class="item.isOpen ? 'grid-rows-[1fr] opacity-100 pb-5' : 'grid-rows-[0fr] opacity-0'">
          <div class="overflow-hidden px-5">
            <p class="text-[#8c8a8a] text-sm sm:text-base leading-relaxed border-t border-white/5 pt-3">
              {{ item.answer }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
