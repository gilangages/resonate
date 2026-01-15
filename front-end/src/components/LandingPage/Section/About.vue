<script setup>
// import Navbar from "./Section/Navbar.vue";
import Navbar from "./Navbar.vue";
import Footer from "./Footer.vue";

// Kita pakai style text yang sama dengan LandingPage agar konsisten
</script>

<template>
  <div class="min-h-screen flex flex-col bg-[#0F0505] font-['Poppins']">
    <Navbar />

    <div class="flex-grow flex flex-col items-center justify-center p-[2em] text-[#e5e5e5] mt-[4em]">
      <h1 class="text-[32px] md:text-[48px] font-bold text-[#9a203e] mb-6 text-center">Tentang Resonate</h1>

      <div class="max-w-3xl text-center space-y-6 text-[16px] md:text-[18px] text-[#cccccc] leading-relaxed">
        <p>
          Resonate lahir dari sebuah kesadaran sederhana: terkadang, kata-kata saja tidak cukup untuk mewakili apa yang
          kita rasakan.
        </p>

        <p>
          Kami percaya bahwa setiap perasaan layak untuk didengar. Entah itu surat cinta yang tak terkirim, curahan hati
          yang berat, atau sekadar lelucon untuk menghibur diri sendiri. Namun, teks seringkali terasa datar. Itulah
          mengapa kami menggabungkannya dengan musik.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12 max-w-5xl">
        <div
          class="bg-white/5 p-6 rounded-2xl border border-white/10 hover:border-[#9a203e] transition-colors duration-300">
          <h3 class="text-xl font-bold text-[#9a203e] mb-2">Amplifikasi Rasa</h3>
          <p class="text-sm text-gray-400">
            Musik adalah bahasa universal. Dengan menyematkan lagu dari Deezer, pesanmu tidak hanya dibaca, tapi juga
            "dirasakan".
          </p>
        </div>

        <div
          class="bg-white/5 p-6 rounded-2xl border border-white/10 hover:border-[#9a203e] transition-colors duration-300">
          <h3 class="text-xl font-bold text-[#9a203e] mb-2">Tanpa Penghakiman</h3>
          <p class="text-sm text-gray-400">
            Lewat fitur Anonim, kami menciptakan ruang aman. Kamu bebas menjadi dirimu sendiri tanpa perlu khawatir
            tentang siapa kamu.
          </p>
        </div>

        <div
          class="bg-white/5 p-6 rounded-2xl border border-white/10 hover:border-[#9a203e] transition-colors duration-300">
          <h3 class="text-xl font-bold text-[#9a203e] mb-2">Jejak Digital</h3>
          <p class="text-sm text-gray-400">
            Anggap ini sebagai jurnal emosionalmu. Tempat di mana kenangan visual dan audio tersimpan rapi untuk masa
            depan.
          </p>
        </div>
      </div>

      <div class="mt-16 text-center">
        <p class="text-sm text-gray-500 uppercase tracking-widest mb-2">Dibuat Oleh</p>
        <div class="flex items-center justify-center gap-4">
          <span class="font-semibold text-lg">Abdian</span>
        </div>
      </div>
    </div>

    <Footer />
  </div>
</template>
