<script setup>
import { ref, onMounted } from "vue";
import { useSessionStorage } from "@vueuse/core"; // Import dari VueUse

// 1. Gunakan useSessionStorage
// Parameter: ('nama_key_penyimpanan', nilai_default)
// Ini otomatis mengecek storage. Jika kosong, nilainya false. Jika ada, nilainya diambil dari storage.
const hasSeenNotice = useSessionStorage("has_seen_free_tier_notice", false);

const isVisible = ref(false);

const closeNotice = () => {
  isVisible.value = false;
  // 2. Enaknya VueUse: Cukup ubah nilainya, otomatis tersimpan ke Session Storage!
  // Gak perlu lagi manual localStorage.setItem(...)
  hasSeenNotice.value = true;
};

onMounted(() => {
  // Cek nilai dari variable reactive tadi
  // Jika bernilai false (belum pernah lihat di sesi ini), tampilkan.
  if (!hasSeenNotice.value) {
    setTimeout(() => {
      isVisible.value = true;
    }, 1000);
  }
});
</script>

<template>
  <Transition name="fade">
    <div
      v-if="isVisible"
      class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
      <div
        class="bg-[#1c1516] border border-[#2c2021] rounded-xl shadow-2xl max-w-md w-full overflow-hidden flex flex-col relative animate-bounce-in">
        <div class="p-6 pb-2">
          <div class="flex items-center gap-3 mb-2">
            <div class="bg-yellow-500/10 p-2 rounded-lg">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="#eab308"
                class="w-6 h-6">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
              </svg>
            </div>
            <h3 class="text-xl font-bold text-white font-jakarta">Info Server</h3>
          </div>
        </div>

        <div class="px-6 py-2">
          <p class="text-gray-300 text-sm leading-relaxed font-poppins">
            Halo! Website ini sedang di-hosting menggunakan layanan
            <span class="text-yellow-500 font-semibold">Gratis (Free Tier)</span>
            .
            <br />
            <br />
            Mohon dimaklumi jika akses terasa agak lambat atau web sempat "tertidur" dan butuh waktu untuk bangun
            kembali.
          </p>
        </div>

        <div class="p-6 pt-4 flex justify-end">
          <button
            @click="closeNotice"
            class="bg-[#9a203e] hover:bg-[#b92b4a] text-white px-6 py-2 rounded-lg text-sm font-semibold transition-all duration-300 font-jakarta shadow-lg hover:shadow-red-900/20 active:scale-95">
            Oke, Mengerti
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<style scoped>
/* Animasi Muncul Halus */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Animasi Boxnya agak membal sedikit pas muncul */
@keyframes bounceIn {
  0% {
    transform: scale(0.9);
    opacity: 0;
  }
  50% {
    transform: scale(1.02);
    opacity: 1;
  }
  100% {
    transform: scale(1);
  }
}
.animate-bounce-in {
  animation: bounceIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
</style>
