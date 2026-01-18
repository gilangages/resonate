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
    answer:
      "Di sini musik adalah bahasa utamanya. Kamu bisa merespon pesan dengan mengirimkan <strong>Lagu Balasan</strong> via Deezer yang mewakili perasaanmu, opsional dengan pesan teks singkat.",
    open: false,
  },
  {
    question: "Apakah Admin bisa melihat siapa pengirim pesan?",
    answer:
      "<strong>Tidak.</strong> Kami menerapkan sistem <em>Blind Moderation</em>. Saat meninjau pesan, identitas pengirim disembunyikan dari moderator. Kami hanya menilai isi konten, bukan orangnya.",
    open: false,
  },
  {
    question: "Bagaimana jika saya menemukan pesan tidak pantas?",
    answer:
      "Kami menerapkan <strong>Filter Kata Otomatis</strong> saat pembuatan pesan. Namun jika masih ada konten negatif yang lolos, kamu bisa melaporkannya lewat formulir di menu <strong>About</strong> (Footer). Pilih kategori <strong>'Lapor Konten'</strong> dan tim kami akan segera menindaklanjutinya.",
    open: false,
  },
  {
    question: "Kenapa pesan saya tiba-tiba hilang?", // FAQ PENTING (Transparansi Admin)
    answer:
      "Demi kenyamanan bersama, Admin memoderasi konten secara aktif. Jika pesanmu hilang, silakan cek menu <strong>Notifikasi</strong>. Sistem kami pasti mengirimkan info alasan penghapusan pesan tersebut.",
    open: false,
  },
  {
    question: "Kenapa saya tidak bisa membalas 'reply' orang lain?",
    answer:
      "Kami menerapkan konsep <strong>'One-Way Tribute'</strong>. Tujuannya agar interaksi tetap sederhana, fokus pada apresiasi lagu, dan menghindari debat panjang yang tidak perlu.",
    open: false,
  },
  {
    question: "Apakah saya perlu akun Premium untuk memutar lagu?",
    answer:
      "Tidak perlu! Berkat integrasi Deezer, siapa saja bisa mendengarkan cuplikan (preview) lagu selama 30 detik secara gratis tanpa perlu login atau berlangganan aplikasi musik apapun.",
    open: false,
  },
  {
    question: "Kenapa lagu hanya berputar selama 30 detik?",
    answer:
      "Ini adalah standar internasional untuk <strong>Audio Preview</strong> yang diizinkan oleh label musik secara gratis. Karena Resonate bukan aplikasi streaming berbayar (seperti Spotify/Deezer Premium), kami hanya diperbolehkan memutar cuplikan 30 detik agar tetap menghargai hak cipta musisi.",
    open: false,
  },
  {
    question: "Kenapa lagu yang saya cari tidak ditemukan?",
    answer:
      "Koleksi lagu di Resonate terhubung langsung secara <em>real-time</em> dengan database <strong>Deezer</strong>. Jika lagu favoritmu tidak muncul, kemungkinan besar lagu tersebut memang belum tersedia di katalog mereka. Hal ini di luar kendali kami, mohon pengertiannya ya!",
    open: false,
  },
  {
    question: "Kenapa menggunakan Deezer, bukan Spotify?",
    answer:
      "Kami mengerti bahwa Spotify adalah platform favorit banyak orang. Namun saat ini, akses integrasi baru sedang <strong>ditangguhkan sementara</strong> oleh pihak Spotify untuk pemeliharaan sistem. <br><br>Agar fitur preview lagu tetap berjalan lancar, saat ini kami menggunakan Deezer. Tenang saja! Jika akses Spotify sudah dibuka kembali, kami berencana untuk <strong>mendukung kedua platform sekaligus</strong> agar pencarian musikmu makin maksimal dan tanpa batas.",
    open: false,
  },
  {
    question: "Akun saya dibatasi/banned, apa solusinya?", // FAQ PENTING (Fitur Appeal)
    answer:
      "Kami menindak tegas pelanggaran komunitas. Namun jika kamu merasa sanksi ini keliru, kamu bisa mengajukan <strong>Banding (Appeal)</strong> langsung lewat aplikasi. Tim kami akan meninjau ulang status akunmu.",
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
