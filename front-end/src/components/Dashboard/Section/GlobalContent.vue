<script setup>
import { useLocalStorage } from "@vueuse/core";
import { noteList } from "../../../lib/api/NoteApi";
import { onMounted, ref, nextTick } from "vue";
import { formatTime, isEdited } from "../../../lib/dateFormatter";

const token = useLocalStorage("token", "");
const notes = ref([]);

// --- AUDIO PLAYER STATE ---
const currentAudio = ref(new Audio());
const currentTime = ref(0);

// --- STATE LOADING (BARU) ---
const isLoading = ref(true);

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

const formatDateDetail = (dateString) => {
  if (!dateString) return "";
  const date = new Date(dateString);
  const optionsDate = { weekday: "long", day: "numeric", month: "short", year: "numeric" };
  const hours = String(date.getHours()).padStart(2, "0");
  const minutes = String(date.getMinutes()).padStart(2, "0");
  return `${date.toLocaleDateString("id-ID", optionsDate)} • ${hours}:${minutes} WIB`;
};

// FUNGSI FORMAT WAKTU (Ubah detik jadi 0:00)
const formatTimeMusic = (time) => {
  if (!time || isNaN(time)) return "0:00";
  const minutes = Math.floor(time / 60);
  const seconds = Math.floor(time % 60);
  return `${minutes}:${seconds < 10 ? "0" : ""}${seconds}`;
};

// --- FETCH DATA ---
async function fetchNoteList(reset = true) {
  if (reset) {
    currentPage.value = 1;
    isLoading.value = true; // Mulai Loading
  }

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
  } finally {
    isLoading.value = false; // Selesai Loading
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
  currentTime.value = 0; // Reset waktu UI

  // Tentukan URL Lagu
  let streamUrl = null;
  if (note.music_track_id) {
    streamUrl = `${import.meta.env.VITE_APP_PATH || "http://localhost:8000/api"}/stream/${note.music_track_id}`;
  } else if (note.music_preview_url) {
    streamUrl = note.music_preview_url;
  }

  if (streamUrl) {
    currentAudio.value.src = streamUrl;
    currentAudio.value.volume = 0.5;

    currentAudio.value.loop = true;

    // --- UPDATE LOGIC: Event Listener untuk Waktu ---
    currentAudio.value.ontimeupdate = () => {
      currentTime.value = currentAudio.value.currentTime;
    };

    currentAudio.value.play().catch((e) => {
      console.error("Gagal play audio:", e);
    });
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
  currentTime.value = 0; // Reset UI Timer

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
    <div v-if="isLoading" class="columns-1 md:columns-2 lg:columns-3 gap-6 mb-10 space-y-6">
      <div v-for="i in 6" :key="i" class="break-inside-avoid relative">
        <div class="bg-[#1c1516] rounded-[24px] p-6 border border-[#2c2021] animate-pulse h-full flex flex-col">
          <div class="mb-5">
            <div class="h-3 w-10 bg-[#2b2122] rounded mb-2"></div>
            <div class="h-8 w-3/4 bg-[#2b2122] rounded-[8px]"></div>
          </div>

          <div class="flex gap-4 items-center mb-5">
            <div class="w-14 h-14 bg-[#2b2122] rounded-[12px]"></div>
            <div class="flex-1 space-y-2">
              <div class="h-4 w-1/2 bg-[#2b2122] rounded"></div>
              <div class="h-3 w-1/3 bg-[#2b2122] rounded"></div>
            </div>
          </div>

          <div class="h-24 bg-[#2b2122] rounded-[16px] mb-4 w-full"></div>

          <div class="flex flex-col gap-3 pt-4 border-t border-[#2c2021] mt-auto">
            <div class="flex items-center gap-2">
              <div class="w-6 h-6 rounded-full bg-[#2b2122]"></div>
              <div class="flex flex-col gap-1">
                <div class="h-2 w-8 bg-[#2b2122] rounded"></div>
                <div class="h-3 w-20 bg-[#2b2122] rounded"></div>
              </div>
              <div class="ml-auto h-3 w-16 bg-[#2b2122] rounded"></div>
            </div>

            <div class="w-full mt-2 h-9 bg-[#2b2122] rounded-lg"></div>
          </div>
        </div>
      </div>
    </div>

    <div v-else-if="notes.length === 0" class="w-full text-center text-[#8c8a8a] py-20 text-lg">
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
      v-if="!isLoading && currentPage < lastPage"
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
            <p class="text-[#9a203e] text-xs font-medium uppercase tracking-wide mb-3 mt-1">
              {{ selectedNote?.music_artist_name }}
            </p>

            <div class="w-full max-w-[200px] mb-5 mt-2">
              <div class="h-1 bg-[#2b2122] rounded-full overflow-hidden w-full">
                <div
                  class="h-full bg-[#9a203e] transition-all duration-100 ease-linear"
                  :style="{ width: `${(currentTime / 30) * 100}%` }"></div>
              </div>
              <div class="flex justify-between text-[10px] text-[#8c8a8a] mt-1 font-mono">
                <span>Preview: {{ formatTimeMusic(currentTime) }}</span>
                <span>0:30</span>
              </div>
            </div>

            <a
              v-if="selectedNote?.music_track_id"
              :href="`https://www.deezer.com/track/${selectedNote?.music_track_id}`"
              target="_blank"
              class="flex items-center gap-2 bg-[#9a203e] hover:bg-[#7d1a33] text-white px-5 py-2.5 rounded-full text-xs font-bold transition-transform hover:scale-105 shadow-[0_0_20px_rgba(154,32,62,0.3)] no-underline decoration-0 group">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                <path d="M8 5v14l11-7z" />
              </svg>
              <span>Putar Lagu Penuh</span>
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
              <p class="font-hand text-xl text-[#d4d4d4] leading-loose tracking-wide break-words">
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
