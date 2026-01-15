<script setup>
import { useLocalStorage } from "@vueuse/core";
import { noteList } from "../../../lib/api/NoteApi";
import { onMounted, ref, nextTick } from "vue";
import { formatTime, isEdited } from "../../../lib/dateFormatter";

const token = useLocalStorage("token", "");
const notes = ref([]);
// 1. Tambah Audio Player State
const currentAudio = ref(new Audio());

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
const openModalDetail = (note) => {
  selectedNote.value = note;
  showModal.value = true;

  // LOGIKA PEMUTAR MUSIK
  if (note.music_preview_url) {
    currentAudio.value.src = note.music_preview_url;
    currentAudio.value.volume = 0.5;

    // TAMBAHKAN INI AGAR LOOPING (MENGULANG TERUS)
    currentAudio.value.loop = true;

    currentAudio.value.play().catch((e) => console.log("Gagal memutar audio:", e));
  }

  nextTick(() => {
    setTimeout(() => {
      isVinylSpinning.value = true;
    }, 300);
  });
};

const closeModalDetail = () => {
  isVinylSpinning.value = false;

  // STOP MUSIK SAAT DITUTUP
  currentAudio.value.pause();
  currentAudio.value.currentTime = 0;

  // RESET LOOP JADI FALSE (Optional, good practice)
  currentAudio.value.loop = false;

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
  <div class="p-4 md:p-8 relative min-h-screen font-jakarta bg-[#0f0505]">
    <div v-if="notes.length === 0" class="w-full text-center text-[#8c8a8a] py-20 text-lg">
      Belum ada pesan yang dibuat.
    </div>

    <div v-else class="columns-1 md:columns-2 lg:columns-3 gap-6 mb-10 space-y-6">
      <div
        v-for="(note, index) in notes"
        :key="note.id || index"
        class="break-inside-avoid relative group/card cursor-pointer"
        @click="openModalDetail(note)">
        <div
          class="bg-[#1c1516] rounded-[24px] p-6 border border-[#2c2021] shadow-lg transition-all duration-300 hover:-translate-y-2 hover:border-[#9a203e]/50 hover:shadow-[0_15px_40px_-10px_rgba(154,32,62,0.3)] relative overflow-hidden flex flex-col h-full">
          <div
            class="absolute inset-0 bg-gradient-to-b from-[#9a203e]/10 to-transparent opacity-0 group-hover/card:opacity-100 transition-opacity duration-500"></div>

          <div class="mb-5 relative z-10">
            <p class="text-[11px] text-[#666] font-bold uppercase tracking-wider mb-1">UNTUK</p>
            <h2
              class="text-2xl font-bold text-white group-hover/card:text-[#9a203e] transition-colors break-words leading-tight">
              {{ note.recipient }}
            </h2>
          </div>

          <div class="flex gap-4 items-center relative z-10 mb-5">
            <div
              class="w-14 h-14 rounded-[12px] overflow-hidden shrink-0 border border-[#333] shadow-md group-hover/card:scale-105 transition-transform bg-black">
              <img :src="note.music_album_image" class="w-full h-full object-cover" />
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-bold text-white truncate">{{ note.music_track_name }}</p>
              <p class="text-xs text-[#888] truncate">{{ note.music_artist_name }}</p>
            </div>
          </div>

          <div
            class="bg-[#121011] rounded-[16px] p-4 border border-[#2c2021] mb-4 group-hover/card:border-[#9a203e]/30 transition-colors relative z-10">
            <p
              v-text="'&quot;' + note.content + '&quot;'"
              class="text-[15px] text-[#ccc] italic font-hand leading-relaxed whitespace-pre-wrap break-words line-clamp-6"></p>
          </div>

          <div class="flex flex-col gap-3 pt-4 border-t border-[#2c2021] relative z-10 mt-auto">
            <div class="flex items-center gap-2">
              <img :src="note.author_avatar" class="w-6 h-6 rounded-full border border-[#333] object-cover" />
              <div class="flex flex-col">
                <span class="text-[10px] text-[#666] uppercase font-bold">Dari</span>
                <span class="text-xs text-[#999] font-medium leading-none">{{ note.author }}</span>
              </div>
              <span class="text-[10px] text-[#555] font-mono ml-auto text-right">
                {{ formatTime(note.created_at) }}
                <span
                  v-if="isEdited(note.created_at, note.updated_at)"
                  class="text-[#9a203e] italic ml-1 block sm:inline">
                  (diedit)
                </span>
              </span>
            </div>

            <div
              class="w-full mt-2 opacity-100 lg:opacity-0 lg:group-hover/card:opacity-100 transition-opacity duration-300">
              <button
                class="cursor-pointer w-full py-2 rounded-lg bg-[#9a203e]/10 border border-[#9a203e]/30 text-[#9a203e] text-xs font-bold uppercase tracking-widest hover:bg-[#9a203e] hover:text-white transition-all flex items-center justify-center gap-2">
                BUKA
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="14"
                  height="14"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2.5"
                  stroke-linecap="round"
                  stroke-linejoin="round">
                  <path d="M5 12h14M12 5l7 7-7 7" />
                </svg>
              </button>
            </div>
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
      <svg
        v-if="!isLoadingMore"
        @click="loadMore"
        xmlns="http://www.w3.org/2000/svg"
        width="14"
        height="14"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        class="cursor-pointer">
        <path d="M6 9l6 6 6-6" />
      </svg>
    </div>

    <Transition name="fade">
      <div
        v-if="showModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-md p-4"
        @click.self="closeModalDetail">
        <div
          class="bg-[#1c1516] w-full max-w-[420px] rounded-[24px] shadow-2xl border border-[#2c2021] flex flex-col overflow-hidden relative max-h-[90vh] transition-transform duration-300"
          :class="showModal ? 'scale-100' : 'scale-95'">
          <button
            @click="closeModalDetail"
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
                :src="selectedNote?.music_album_image"
                class="w-[65px] h-[65px] rounded-full object-cover border-2 border-[#111] relative z-10" />
            </div>
            <h2 class="text-xl font-bold text-white text-center leading-tight px-4">
              {{ selectedNote?.music_track_name }}
            </h2>
            <p class="text-[#9a203e] text-xs font-medium uppercase tracking-wide mb-4 mt-1">
              {{ selectedNote?.music_artist_name }}
            </p>

            <a
              v-if="selectedNote?.spotify_track_link || selectedNote?.music_track_id"
              :href="selectedNote?.spotify_track_link || `https://www.deezer.com/track/${selectedNote?.music_track_id}`"
              target="_blank"
              class="flex items-center gap-3 bg-[#a238ff] hover:bg-[#8b21e0] text-white px-6 py-2.5 rounded-full text-xs font-bold transition-transform hover:scale-105 shadow-[0_0_20px_rgba(162,56,255,0.3)] mt-2 no-underline decoration-0 group">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="18"
                height="18"
                viewBox="0 0 24 24"
                fill="currentColor"
                class="text-white">
                <path d="M10 20H6V4H10V20ZM16 20H12V8H16V20ZM22 20H18V12H22V20ZM4 20H0V12H4V20Z" />
              </svg>
              <span>Dengar di Deezer</span>

              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="12"
                height="12"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="3"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="opacity-70 group-hover:translate-x-0.5 transition-transform">
                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                <polyline points="15 3 21 3 21 9"></polyline>
                <line x1="10" y1="14" x2="21" y2="3"></line>
              </svg>
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
                @click="closeModalDetail"
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
