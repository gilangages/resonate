<script setup>
import { useLocalStorage } from "@vueuse/core";
import { noteList } from "../../../lib/api/NoteApi";
import { onMounted, ref, nextTick } from "vue";

const token = useLocalStorage("token", "");
const notes = ref([]);

// --- STATE PAGINATION ---
const currentPage = ref(1);
const lastPage = ref(1);
const isLoadingMore = ref(false);

// --- STATE MODAL DETAIL ---
const showModal = ref(false);
const selectedNote = ref(null);
const isVinylSpinning = ref(false);

// --- STATE IMAGE PREVIEW (AVATAR) ---
const showImagePreview = ref(false);
const previewImageUrl = ref("");

// --- FORMATTER ---
const formatDate = (dateString) => {
  if (!dateString) return "";
  return new Date(dateString).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "short",
    year: "numeric",
  });
};

const formatDateDetail = (dateString) => {
  if (!dateString) return "";
  const date = new Date(dateString);
  const optionsDate = { weekday: "long", day: "numeric", month: "short", year: "numeric" };
  const hours = String(date.getHours()).padStart(2, "0");
  const minutes = String(date.getMinutes()).padStart(2, "0");
  return `${date.toLocaleDateString("id-ID", optionsDate)} • ${hours}:${minutes} WIB`;
};

// --- FETCH DATA ---
async function fetchNoteList(reset = true) {
  if (reset) currentPage.value = 1;
  try {
    const response = await noteList(token.value, currentPage.value);
    const responseBody = await response.json();
    if (response.ok) {
      if (responseBody.meta) {
        lastPage.value = responseBody.meta.last_page;
        currentPage.value = responseBody.meta.current_page;
      }
      if (reset) notes.value = responseBody.data;
      else notes.value.push(...responseBody.data);
    }
  } catch (error) {
    console.error("Fetch error:", error);
  }
}

// --- LOAD MORE ---
const loadMore = async () => {
  if (currentPage.value < lastPage.value) {
    isLoadingMore.value = true;
    currentPage.value++;
    await fetchNoteList(false);
    isLoadingMore.value = false;
  }
};

// --- MODAL LOGIC ---
const openModal = (note) => {
  selectedNote.value = note;
  showModal.value = true;
  nextTick(() => {
    setTimeout(() => {
      isVinylSpinning.value = true;
    }, 300);
  });
};

const closeModal = () => {
  isVinylSpinning.value = false;
  setTimeout(() => {
    showModal.value = false;
    selectedNote.value = null;
  }, 100);
};

// --- IMAGE PREVIEW LOGIC ---
const openImagePreview = (url) => {
  if (!url) return;
  previewImageUrl.value = url;
  showImagePreview.value = true;
};

const closeImagePreview = () => {
  showImagePreview.value = false;
};

onMounted(async () => {
  await fetchNoteList(true);
});
</script>

<template>
  <div class="p-6 md:p-8 relative min-h-screen font-jakarta bg-[#0a0808]">
    <div v-if="notes.length === 0" class="w-full text-center text-[#8c8a8a] py-20 text-lg">
      Belum ada pesan yang dibuat.
    </div>

    <div v-else class="columns-1 md:columns-2 lg:columns-3 gap-6 mb-10 space-y-6">
      <div
        v-for="(note, index) in notes"
        :key="note.id || index"
        class="break-inside-avoid relative group/card cursor-pointer"
        @click="openModal(note)">
        <div
          class="bg-[#1c1516] rounded-[24px] p-5 border border-[#2c2021] shadow-lg transition-all duration-300 hover:-translate-y-2 hover:border-[#9a203e]/50 hover:shadow-[0_15px_40px_-10px_rgba(154,32,62,0.3)] relative overflow-hidden flex flex-col">
          <div
            class="absolute inset-0 bg-gradient-to-b from-[#9a203e]/10 to-transparent opacity-0 group-hover/card:opacity-100 transition-opacity duration-500"></div>

          <div class="flex justify-between items-start mb-4 relative z-10">
            <div>
              <p class="text-[11px] text-[#666] font-bold uppercase tracking-wider mb-0.5">UNTUK</p>
              <h2
                class="text-[18px] font-bold text-white group-hover/card:text-[#9a203e] transition-colors leading-tight break-words">
                {{ note.recipient }}
              </h2>
            </div>
            <div class="text-right">
              <p class="text-[11px] text-[#666] font-bold uppercase tracking-wider mb-0.5">DARI</p>
              <div class="flex items-center justify-end gap-2">
                <span class="text-[13px] font-bold text-[#ccc] truncate max-w-[100px]">{{ note.author }}</span>
                <img :src="note.author_avatar" class="w-7 h-7 rounded-full border border-[#333] object-cover" />
              </div>
            </div>
          </div>

          <div class="flex gap-4 items-start relative z-10">
            <div
              class="w-[70px] h-[70px] rounded-[14px] overflow-hidden shrink-0 border border-[#333] shadow-md group-hover/card:scale-105 transition-transform bg-black">
              <img :src="note.spotify_album_image" class="w-full h-full object-cover" />
            </div>
            <div class="flex-1 min-w-0">
              <div class="mb-2">
                <p class="text-sm font-bold text-white truncate">{{ note.spotify_track_name }}</p>
                <p class="text-xs text-[#888] truncate">{{ note.spotify_artist }}</p>
              </div>
              <div
                class="bg-[#121011] rounded-[12px] p-3 border border-[#2c2021] group-hover/card:border-[#9a203e]/30 transition-colors">
                <p class="text-[15px] text-[#ccc] italic font-hand leading-relaxed whitespace-pre-wrap break-words">
                  "{{ note.content }}"
                </p>
              </div>
            </div>
          </div>

          <div class="mt-4 flex justify-between items-center border-t border-[#2c2021] pt-3 relative z-10">
            <span class="text-[11px] text-[#555]">{{ formatDate(note.created_at) }}</span>
            <span
              class="text-[11px] font-bold text-[#e5e5e5] group-hover/card:text-[#9a203e] transition-colors flex items-center gap-1">
              Lihat Detail
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="12"
                height="12"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7" />
              </svg>
            </span>
          </div>
        </div>
      </div>
    </div>

    <div
      v-if="currentPage < lastPage"
      class="mb-[5em] flex justify-center py-6 text-[#e5e5e5] gap-1.5 hover:opacity-80">
      <button
        @click="loadMore"
        :disabled="isLoadingMore"
        class="bg-transparent font-semibold uppercase hover:underline cursor-pointer disabled:opacity-50 tracking-widest text-sm">
        {{ isLoadingMore ? "Memuat..." : "Lihat Lebih Banyak" }}
      </button>
      <img
        v-if="!isLoadingMore"
        @click="loadMore"
        src="../../../assets/img/arrow-down.svg"
        class="cursor-pointer w-[14px]" />
    </div>

    <Transition name="fade">
      <div
        v-if="showModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-md p-4"
        @click.self="closeModal">
        <div
          class="bg-[#1c1516] w-full max-w-[420px] rounded-[24px] shadow-2xl border border-[#2c2021] flex flex-col overflow-hidden relative max-h-[90vh] transition-transform duration-300"
          :class="showModal ? 'scale-100' : 'scale-95'">
          <button
            @click="closeModal"
            class="absolute top-4 right-4 z-50 bg-black/40 hover:bg-[#9a203e] text-white p-2 rounded-full transition-colors backdrop-blur-md border border-white/10 cursor-pointer">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="20"
              height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>

          <div
            class="bg-gradient-to-b from-[#251a1c] to-[#1c1516] p-6 pt-10 border-b border-[#2c2021] flex flex-col items-center shrink-0">
            <div
              class="w-[160px] h-[160px] rounded-full bg-[#111] shadow-2xl border-4 border-[#1c1516] flex items-center justify-center relative mb-5 transition-transform duration-[8s] ease-linear"
              :class="{ 'animate-spin-slow': isVinylSpinning }">
              <div class="absolute inset-0 rounded-full border-[2px] border-[#222] opacity-50 transform scale-90"></div>
              <img
                :src="selectedNote?.spotify_album_image"
                class="w-[65px] h-[65px] rounded-full object-cover border-2 border-[#111] relative z-10" />
            </div>
            <h2 class="text-xl font-bold text-white text-center leading-tight px-4">
              {{ selectedNote?.spotify_track_name }}
            </h2>
            <p class="text-[#9a203e] text-xs font-medium uppercase tracking-wide mb-4 mt-1">
              {{ selectedNote?.spotify_artist }}
            </p>
            <a
              v-if="selectedNote?.spotify_track_link"
              :href="selectedNote?.spotify_track_link"
              target="_blank"
              class="flex items-center gap-2 bg-[#1ed760] hover:bg-[#1db954] text-black px-5 py-2.5 rounded-full text-xs font-bold transition-transform hover:scale-105 shadow-[0_0_20px_rgba(30,215,96,0.2)] mt-2 no-underline decoration-0">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="currentColor"
                class="text-black">
                <path
                  d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.4-1.02 15.96 1.74.539.3.66 1.022.359 1.561-.3.479-1.02.6-1.56.3z" />
              </svg>
              <span>Dengar di Spotify</span>
            </a>
          </div>

          <div class="flex-1 bg-[#161213] p-6 overflow-y-auto custom-scrollbar">
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-[#2c2021]">
              <div class="flex items-center gap-3">
                <img
                  @click="openImagePreview(selectedNote?.author_avatar)"
                  :src="selectedNote?.author_avatar"
                  class="w-10 h-10 rounded-full border border-[#3f3233] object-cover cursor-zoom-in hover:scale-110 transition-transform" />
                <div>
                  <p class="text-[10px] text-[#666] uppercase tracking-wide">DARI</p>
                  <p class="text-sm font-bold text-white">{{ selectedNote?.author }}</p>
                </div>
              </div>
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="#555"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7" />
              </svg>
              <div class="text-right">
                <p class="text-[10px] text-[#666] uppercase tracking-wide">UNTUK</p>
                <p class="text-sm font-bold text-[#9a203e]">{{ selectedNote?.recipient }}</p>
              </div>
            </div>
            <div class="mb-6">
              <p class="font-hand text-xl text-[#d4d4d4] leading-loose tracking-wide whitespace-pre-wrap break-words">
                "{{ selectedNote?.content }}"
              </p>
            </div>
            <div
              class="flex items-center gap-2 text-[11px] text-[#555] font-mono bg-[#1c1a1b] p-3 rounded-lg border border-[#2c2021]">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="14"
                height="14"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
              <span>Dikirim: {{ formatDateDetail(selectedNote?.created_at) }}</span>
            </div>
            <div class="mt-6">
              <button
                @click="closeModal"
                class="w-full py-3 rounded-[12px] border border-[#3f3233] text-[#888] font-bold text-xs uppercase tracking-widest hover:bg-[#2c2021] hover:text-white transition-all cursor-pointer">
                Tutup Catatan
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>

    <Transition name="fade">
      <div
        v-if="showImagePreview"
        class="fixed inset-0 z-[60] flex flex-col items-center justify-center bg-black/95 backdrop-blur-xl p-4 cursor-pointer"
        @click="closeImagePreview">
        <div class="relative flex flex-col items-center w-full max-w-[90vw] max-h-[90vh] cursor-default">
          <img
            :src="previewImageUrl"
            class="w-auto h-auto max-w-full max-h-[80vh] object-contain rounded-lg shadow-2xl"
            @click.stop />
          <p class="text-white/50 text-sm tracking-widest uppercase font-bold mt-4" @click.stop>Foto Profil</p>
        </div>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");
@import url("https://fonts.googleapis.com/css2?family=Patrick+Hand&display=swap");

.font-jakarta {
  font-family: "Plus Jakarta Sans", sans-serif;
}
.font-hand {
  font-family: "Patrick Hand", cursive;
}

.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: #3f3233 #1c1516;
}
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #1c1516;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #3f3233;
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: #9a203e;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
.animate-spin-slow {
  animation: spin 8s linear infinite;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
