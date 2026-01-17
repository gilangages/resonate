<script setup>
import { onMounted, ref, nextTick } from "vue";
import { noteListGlobal } from "../../../lib/api/NoteApi";
import { alertError } from "../../../lib/alert";
import { formatTime, isEdited } from "../../../lib/dateFormatter";

// --- STATE ---
const notes = ref([]);
const scrollContainer = ref(null);
const currentAudio = ref(new Audio()); // State Audio Player

// Modal & Preview
const showModal = ref(false);
const selectedNote = ref(null);
const isVinylSpinning = ref(false);
const showImagePreview = ref(false);
const previewImageUrl = ref("");

// --- FETCH DATA ---
async function fetchNoteGlobal() {
  try {
    const response = await noteListGlobal();
    const responseBody = await response.json();
    console.log(responseBody);

    if (response.ok) {
      notes.value = responseBody.data;
    } else {
      const pesanError = responseBody.errors ? Object.values(responseBody.errors)[0][0] : responseBody.message;
      await alertError(pesanError);
    }
  } catch (error) {
    console.error(error);
  }
}

// --- SCROLL LOGIC ---
const scroll = (direction) => {
  if (scrollContainer.value) {
    const scrollAmount = 450;
    if (direction === "left") {
      scrollContainer.value.scrollBy({ left: -scrollAmount, behavior: "smooth" });
    } else {
      scrollContainer.value.scrollBy({ left: scrollAmount, behavior: "smooth" });
    }
  }
};

// --- MODAL & AUDIO LOGIC ---
const openModalDetail = (note) => {
  selectedNote.value = note;
  showModal.value = true;

  // LOGIKA BARU: Gunakan Proxy URL dari Backend kita sendiri
  // Pastikan note punya ID lagu
  if (note.music_track_id) {
    // URL Backend Laravel
    // Sesuaikan base URL API kamu, misal: http://localhost:8000/api/stream/
    const streamUrl = `${import.meta.env.VITE_APP_PATH || "http://localhost:8000/api"}/stream/${note.music_track_id}`;

    console.log("Memutar via Proxy:", streamUrl);

    currentAudio.value.src = streamUrl;
    currentAudio.value.volume = 0.5;
    currentAudio.value.loop = true;

    currentAudio.value.play().catch((e) => {
      console.error("Gagal play audio:", e);
    });
  } else if (note.music_preview_url) {
    // Fallback: Kalau data lama banget yg gapunya ID tapi punya URL (meski mungkin basi)
    currentAudio.value.src = note.music_preview_url;
    currentAudio.value.play().catch((e) => console.log(e));
  }

  nextTick(() => {
    setTimeout(() => {
      isVinylSpinning.value = true;
    }, 300);
  });
};

const closeModalDetail = () => {
  isVinylSpinning.value = false;

  // 4. STOP AUDIO & RESET
  currentAudio.value.pause();
  currentAudio.value.currentTime = 0;
  currentAudio.value.loop = false; // Matikan loop saat tutup

  setTimeout(() => {
    showModal.value = false;
    selectedNote.value = null;
  }, 100);
};

// --- IMAGE PREVIEW ---
const openPreview = (url) => {
  if (url) {
    previewImageUrl.value = url;
    showImagePreview.value = true;
  }
};
const closePreview = () => {
  showImagePreview.value = false;
  setTimeout(() => {
    previewImageUrl.value = "";
  }, 300);
};

// --- FORMATTER ---
const formatDateDetail = (dateString) => {
  if (!dateString) return "";
  const date = new Date(dateString);
  const optionsDate = { weekday: "long", day: "numeric", month: "short", year: "numeric" };
  const hours = String(date.getHours()).padStart(2, "0");
  const minutes = String(date.getMinutes()).padStart(2, "0");
  return `${date.toLocaleDateString("id-ID", optionsDate)} • ${hours}:${minutes} WIB`;
};

onMounted(async () => {
  await fetchNoteGlobal();
});
</script>

<template>
  <div class="mt-[4em] relative font-jakarta">
    <div class="flex justify-between items-center text-[#e5e5e5] mx-[2em] mb-[1em]">
      <h2 class="text-[18px] sm:text-[20px] font-semibold">Dengarkan Pesan Pengguna Lain</h2>
      <RouterLink
        to="/login"
        class="hidden sm:flex gap-1 items-center cursor-pointer hover:opacity-80 decoration-0 no-underline">
        <span class="uppercase text-[12px] font-semibold text-[#e5e5e5] hover:underline">Lihat semua pesan</span>
        <img src="../../../assets/img/arrow-right.svg" class="w-[14px]" />
      </RouterLink>
    </div>

    <div class="relative group px-4 sm:px-12">
      <button
        @click="scroll('left')"
        class="hidden sm:flex absolute left-2 top-1/2 -translate-y-1/2 z-20 bg-[#1c1516] p-2 rounded-full cursor-pointer hover:bg-[#9a203e] border border-[#3f3233] shadow-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="2"
          stroke="currentColor"
          class="w-6 h-6 text-white">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
        </svg>
      </button>

      <div
        ref="scrollContainer"
        class="flex gap-[1.5em] overflow-x-auto pb-8 pt-4 snap-x snap-mandatory scrollbar-hide px-2">
        <div
          v-for="(note, index) in notes"
          :key="note.id || index"
          @click="openModalDetail(note)"
          class="min-w-[85vw] sm:min-w-[450px] snap-center group/card cursor-pointer">
          <div
            class="bg-[#1c1516] rounded-[24px] p-6 border border-[#2c2021] shadow-lg transition-all duration-300 hover:-translate-y-2 hover:border-[#9a203e]/50 hover:shadow-[0_15px_40px_-10px_rgba(154,32,62,0.3)] relative overflow-hidden h-full flex flex-col">
            <div
              class="absolute inset-0 bg-gradient-to-b from-[#9a203e]/10 to-transparent opacity-0 group-hover/card:opacity-100 transition-opacity duration-500"></div>

            <div class="mb-5 relative z-10">
              <p class="text-[11px] text-[#666] font-bold uppercase tracking-wider mb-1">KEPADA</p>
              <h2 class="text-2xl font-bold text-white group-hover/card:text-[#9a203e] transition-colors truncate">
                {{ note.recipient }}
              </h2>
            </div>

            <div class="flex gap-4 items-center relative z-10 mb-5">
              <div
                class="w-14 h-14 rounded-[12px] overflow-hidden shrink-0 border border-[#333] shadow-md group-hover/card:scale-105 transition-transform bg-black">
                <img :src="note.music_album_image" class="w-full h-full object-cover" />
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-white truncate">
                  {{ note.music_track_name }}
                </p>
                <p class="text-xs text-[#888] truncate">{{ note.music_artist_name }}</p>
              </div>
            </div>

            <div
              class="bg-[#121011] rounded-[16px] p-4 border border-[#2c2021] mb-4 group-hover/card:border-[#9a203e]/30 transition-colors relative z-10">
              <p
                v-text="'&quot;' + note.content + '&quot;'"
                class="text-[15px] text-[#ccc] italic font-hand leading-relaxed whitespace-pre-wrap line-clamp-3 break-words"></p>
            </div>

            <div class="flex flex-col gap-3 pt-4 border-t border-[#2c2021] relative z-10 mt-auto">
              <div class="flex items-center gap-2">
                <img :src="note.author_avatar" class="w-6 h-6 rounded-full border border-[#333] object-cover" />
                <div class="flex flex-col">
                  <span class="text-[10px] text-[#666] uppercase font-bold">Dari</span>
                  <span class="text-xs text-[#999] font-medium leading-none truncate max-w-[150px]">
                    {{ note.author }}
                  </span>
                </div>

                <div class="ml-auto flex items-center gap-3">
                  <span class="text-[10px] text-[#555] font-mono">
                    {{ formatTime(note.created_at) }}
                    <span
                      v-if="isEdited(note.created_at, note.updated_at)"
                      class="text-[#9a203e] italic ml-1 block sm:inline">
                      (diedit)
                    </span>
                  </span>

                  <div
                    class="text-[#e5e5e5] group-hover/card:text-[#9a203e] transition-colors transform group-hover/card:translate-x-1 duration-300">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="16"
                      height="16"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2.5"
                      stroke-linecap="round"
                      stroke-linejoin="round">
                      <path d="M5 12h14M12 5l7 7-7 7" />
                    </svg>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <button
        @click="scroll('right')"
        class="hidden sm:flex absolute right-2 top-1/2 -translate-y-1/2 z-20 bg-[#1c1516] p-2 rounded-full cursor-pointer hover:bg-[#9a203e] border border-[#3f3233] shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="2"
          stroke="currentColor"
          class="w-6 h-6 text-white">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
      </button>
    </div>

    <div class="flex justify-end mr-[2em] sm:hidden mt-2 mb-10">
      <RouterLink to="/login" class="flex gap-1 items-center cursor-pointer hover:opacity-80 decoration-0 no-underline">
        <span class="uppercase text-[10px] font-semibold text-[#e5e5e5] hover:underline">Lihat semua pesan</span>
        <img src="../../../assets/img/arrow-right.svg" class="w-[12px]" />
      </RouterLink>
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

            <h2 class="text-xl font-bold text-white text-center leading-tight">
              {{ selectedNote?.music_track_name }}
            </h2>
            <p class="text-[#9a203e] text-xs font-medium uppercase tracking-wide mb-4 mt-1">
              {{ selectedNote?.music_artist_name }}
            </p>

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
                <div
                  @click.stop="openPreview(selectedNote?.author_avatar)"
                  class="relative group/avatar cursor-zoom-in">
                  <img
                    :src="selectedNote?.author_avatar"
                    class="w-10 h-10 rounded-full border border-[#3f3233] object-cover transition-transform group-hover/avatar:scale-110" />
                </div>
                <div>
                  <p class="text-[10px] text-[#666] uppercase tracking-wide">DARI</p>
                  <p class="text-sm font-bold text-white">{{ selectedNote?.author }}</p>
                </div>
              </div>

              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
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
                Tutup Pesan
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
        @click="closePreview">
        <div class="relative flex flex-col items-center w-full max-w-[90vw] max-h-[90vh]">
          <button
            @click.stop="closePreview"
            class="absolute -top-12 right-0 text-white/50 hover:text-white transition-colors p-2 z-50">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="32"
              height="32"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
          <img
            :src="previewImageUrl"
            class="w-auto h-auto max-w-full max-h-[80vh] object-contain rounded-lg shadow-2xl cursor-default"
            @click.stop />
          <p class="text-white/50 text-sm tracking-widest uppercase font-bold mt-4" @click.stop>Foto Profil</p>
        </div>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
/* CSS Sama Seperti Sebelumnya */
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");
@import url("https://fonts.googleapis.com/css2?family=Patrick+Hand&display=swap");

.font-jakarta {
  font-family: "Plus Jakarta Sans", sans-serif;
}
.font-hand {
  font-family: "Patrick Hand", cursive;
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
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
