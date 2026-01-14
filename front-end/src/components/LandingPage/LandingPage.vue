<script setup>
// Import komponen-komponen UI yang sudah ada
import Navbar from "./Section/Navbar.vue";
import Hero from "./Section/Hero.vue";
import Fitur from "./Section/Fitur.vue";
import PesanLain from "./Section/PesanLain.vue";
import Ajakan from "./Section/Ajakan.vue";
import Footer from "./Section/Footer.vue";

// --- LOGIKA BARU UNTUK SHARE LINK ---
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import Swal from "sweetalert2";

const route = useRoute();
const router = useRouter();

// State untuk Modal
const showSharedModal = ref(false);
const sharedNote = ref(null);
const isLoading = ref(false);

// Fungsi Fetch Data (Tanpa Token)
const checkSharedUrl = async () => {
  // Cek apakah ada parameter :id di URL (dari route /note/:id)
  if (!route.params.id) return;

  const noteId = route.params.id;
  isLoading.value = true;

  try {
    // Gunakan fetch native
    const response = await fetch(`${import.meta.env.VITE_APP_PATH}/notes/${noteId}`, {
      headers: { Accept: "application/json" },
    });

    const result = await response.json();

    if (response.ok) {
      sharedNote.value = result.data;
      showSharedModal.value = true;
    } else {
      // Jika data tidak ditemukan
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "Pesan ini tidak ditemukan atau mungkin sudah dihapus.",
        confirmButtonColor: "#9a203e",
      });
      router.replace("/"); // Bersihkan URL
    }
  } catch (error) {
    console.error("Error fetching shared note:", error);
  } finally {
    isLoading.value = false;
  }
};

const goToRegister = () => {
  showSharedModal.value = false; // Tutup modal dulu agar rapi
  sharedNote.value = null;
  router.push("/register"); // Arahkan ke halaman daftar
};

// Fungsi Tutup Modal
const closeSharedModal = () => {
  showSharedModal.value = false;
  sharedNote.value = null;
  // PENTING: Ubah URL kembali ke '/' agar bersih
  router.replace("/");
};

// Jalankan saat halaman dimuat
onMounted(() => {
  checkSharedUrl();
});
</script>

<template>
  <div class="relative min-h-screen bg-[#0f0505]">
    <Navbar />
    <Hero />
    <Fitur />
    <PesanLain />
    <Ajakan />
    <Footer />

    <div v-if="showSharedModal && sharedNote" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="closeSharedModal"></div>

      <div
        class="relative w-full max-w-md bg-[#1c1516] border border-[#9a203e] rounded-2xl p-6 shadow-2xl animate-fade-in">
        <button
          @click="closeSharedModal"
          class="absolute top-3 right-3 text-gray-400 hover:text-white transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>

        <div class="flex items-center gap-3 mb-5">
          <div class="w-12 h-12 rounded-full overflow-hidden border border-gray-700 bg-black">
            <img
              v-if="sharedNote.author_avatar || sharedNote.author_photo_url"
              :src="sharedNote.author_avatar || sharedNote.author_photo_url"
              class="w-full h-full object-cover"
              alt="User" />
            <div
              v-else
              class="w-full h-full bg-[#9a203e] flex items-center justify-center text-white font-bold text-lg">
              {{ sharedNote.initial_name || "?" }}
            </div>
          </div>
          <div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Pesan dari</p>
            <h3 class="text-white font-bold text-lg">{{ sharedNote.author_name || "Seseorang" }}</h3>
          </div>
        </div>

        <div class="bg-[#2b2122] p-4 rounded-xl border-l-4 border-[#9a203e] mb-5 shadow-inner">
          <p class="text-gray-200 italic font-medium font-hand text-lg leading-relaxed">"{{ sharedNote.content }}"</p>
          <p class="text-right text-xs text-[#9a203e] mt-3 font-bold uppercase tracking-wider">
            ~ Untuk: {{ sharedNote.recipient }}
          </p>
        </div>

        <div
          v-if="sharedNote.music_track_id"
          class="flex items-center gap-3 bg-black/40 p-3 rounded-lg mb-6 border border-[#333]">
          <img :src="sharedNote.music_album_image" class="w-14 h-14 rounded bg-gray-800 shadow-md object-cover" />
          <div class="overflow-hidden flex-1">
            <p class="text-white text-sm font-bold truncate">{{ sharedNote.music_track_name }}</p>
            <p class="text-gray-400 text-xs truncate">{{ sharedNote.music_artist_name }}</p>

            <a
              :href="`https://www.deezer.com/track/${sharedNote.music_track_id}`"
              target="_blank"
              class="text-[#9a203e] text-[10px] font-bold mt-1 inline-flex items-center gap-1 hover:underline decoration-0">
              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.6 0 12 0zm-2 14.5v-5l6 2.5-6 2.5z" />
              </svg>
              Putar Lagu Penuh
            </a>
          </div>
        </div>

        <div class="flex flex-col gap-3">
          <button
            @click="goToRegister"
            class="w-full py-3 bg-[#9a203e] hover:bg-[#7a1830] text-white font-bold rounded-xl transition-all shadow-lg shadow-[#9a203e]/20 cursor-pointer">
            Buat Pesan Sendiri ➜
          </button>
          <button
            @click="closeSharedModal"
            class="text-sm text-gray-500 hover:text-white transition-colors cursor-pointer">
            Tutup & Lihat Website
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Font khusus untuk teks pesan agar lebih estetik */
@import url("https://fonts.googleapis.com/css2?family=Patrick+Hand&display=swap");

.font-hand {
  font-family: "Patrick Hand", cursive;
}

/* Animasi Sederhana */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}
.animate-fade-in {
  animation: fadeIn 0.3s ease-out forwards;
}
</style>
