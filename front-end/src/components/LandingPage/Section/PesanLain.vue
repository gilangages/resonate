<script setup>
import { onMounted, ref, nextTick } from "vue";
import { noteListGlobal } from "../../../lib/api/NoteApi";
import { alertError } from "../../../lib/alert";
import { formatTime, isEdited } from "../../../lib/dateFormatter";
import { useCardTheme } from "../../../lib/useCardTheme";
import { useNow } from "@vueuse/core";

//useTheme
const { getTheme, getSelectedTheme } = useCardTheme();
// --- STATE ---
const notes = ref([]);
const scrollContainer = ref(null);
const currentAudio = ref(new Audio()); // State Audio Player
const currentTime = ref(0); // <--- 1. TAMBAHKAN INI

// Modal & Preview
const showModal = ref(false);
const selectedNote = ref(null);
const isVinylSpinning = ref(false);
const showImagePreview = ref(false);
const previewImageUrl = ref("");
// const selectedTheme = getSelectedTheme(selectedNote); // Hapus baris ini karena selectedNote masih null saat init, logic theme di handle di template via getTheme(note.id) atau computed
const now = useNow({ interval: 60000 });

// Helper computed untuk theme modal (opsional, tapi biar konsisten dengan FillContent)
import { computed } from "vue";
const selectedTheme = computed(() => {
  return getSelectedTheme(selectedNote.value);
});

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
  currentTime.value = 0; // <--- 2. RESET WAKTU SAAT BUKA

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

    // <--- 3. TAMBAHKAN EVENT LISTENER INI (PENTING AGAR ANIMASI JALAN)
    currentAudio.value.ontimeupdate = () => {
      currentTime.value = currentAudio.value.currentTime;
    };
    // ------------------------------------------------------------------

    currentAudio.value.play().catch((e) => {
      console.error("Gagal play audio:", e);
    });
  } else if (note.music_preview_url) {
    // Fallback: Kalau data lama banget yg gapunya ID tapi punya URL (meski mungkin basi)
    currentAudio.value.src = note.music_preview_url;

    // <--- TAMBAHKAN JUGA DI SINI UTK FALLBACK
    currentAudio.value.ontimeupdate = () => {
      currentTime.value = currentAudio.value.currentTime;
    };

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
  currentAudio.value.currentTime = 0; // Reset player audio
  currentTime.value = 0; // <--- 4. RESET VARIABLE STATE
  currentAudio.value.loop = false; // Matikan loop saat tutup

  // Hapus listener biar ga memory leak (opsional tapi good practice)
  currentAudio.value.ontimeupdate = null;

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

const formatTimeMusic = (time) => {
  if (!time || isNaN(time)) return "0:00";
  const minutes = Math.floor(time / 60);
  const seconds = Math.floor(time % 60);
  return `${minutes}:${seconds < 10 ? "0" : ""}${seconds}`;
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
          class="min-w-[85vw] sm:min-w-[450px] snap-center group/card cursor-pointer flex flex-col h-full">
          <div
            :class="[getTheme(note.id).bg, getTheme(note.id).border, getTheme(note.id).hover]"
            class="rounded-[24px] p-6 border shadow-lg transition-all duration-300 hover:-translate-y-2 relative overflow-hidden h-full flex flex-col">
            <div
              :class="`bg-gradient-to-b ${getTheme(note.id).gradient} to-transparent`"
              class="absolute inset-0 opacity-0 group-hover/card:opacity-100 transition-opacity duration-500"></div>

            <div class="mb-5 relative z-10">
              <p class="text-[11px] text-[#666] font-bold uppercase tracking-wider mb-1">KEPADA</p>
              <h2
                :class="getTheme(note.id).text_hover"
                class="text-2xl font-bold text-white transition-colors truncate">
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
              :class="[getTheme(note.id).border, `group-hover/card:border-${getTheme(note.id).id}-500/50`]"
              class="bg-black/20 rounded-[16px] p-4 border mb-4 transition-colors relative z-10">
              <p
                v-text="'&quot;' + note.content + '&quot;'"
                class="text-[15px] text-[#ccc] italic font-hand leading-relaxed whitespace-pre-wrap line-clamp-3 break-words"></p>
            </div>

            <div :class="getTheme(note.id).border" class="flex flex-col gap-3 pt-4 border-t relative z-10 mt-auto">
              <div class="flex items-center gap-2">
                <img
                  :src="note.author_avatar || note.author_photo_url"
                  class="w-6 h-6 rounded-full border border-[#333] object-cover" />
                <div class="flex flex-col">
                  <span class="text-[10px] text-[#666] uppercase font-bold">Dari</span>
                  <div class="flex items-center gap-1.5">
                    <span class="text-xs text-[#999] font-medium leading-none">{{ note.author_name }}</span>
                    <span
                      v-if="note.is_admin"
                      class="bg-[#9a203e] text-white text-[9px] px-1.5 py-0.5 rounded-[4px] font-bold uppercase tracking-wider border border-white/10 shadow-[0_0_10px_rgba(154,32,62,0.6)]">
                      Admin
                    </span>
                  </div>
                </div>

                <div class="ml-auto flex items-center gap-3">
                  <span class="text-[10px] text-[#555] font-mono">
                    {{ formatTime(note.created_at, now) }}
                    <span
                      v-if="isEdited(note.created_at, note.updated_at)"
                      :class="getTheme(note.id).text"
                      class="italic ml-1 block sm:inline">
                      (diedit)
                    </span>
                  </span>

                  <div
                    :class="getTheme(note.id).text_hover"
                    class="text-[#e5e5e5] transition-colors transform group-hover/card:translate-x-1 duration-300">
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

    <Teleport to="body">
      <Transition name="fade">
        <div
          v-if="showModal"
          class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/90 backdrop-blur-md p-4"
          @click.self="closeModalDetail">
          <div
            class="w-full max-w-[420px] md:max-w-[600px] rounded-[24px] shadow-2xl border flex flex-col overflow-hidden relative max-h-[90vh] transition-transform duration-300"
            :class="[showModal ? 'scale-100' : 'scale-95', selectedTheme.bg, selectedTheme.border]">
            <button
              @click="closeModalDetail"
              :class="selectedTheme.btn_hover"
              class="absolute top-4 right-4 z-50 bg-black/40 text-white p-2 rounded-full transition-colors backdrop-blur-md border border-white/10 cursor-pointer">
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
              class="relative p-6 pt-10 border-b flex flex-col items-center shrink-0 overflow-hidden"
              :class="selectedTheme.border">
              <div
                class="absolute inset-0 opacity-40 pointer-events-none bg-gradient-to-b to-transparent"
                :class="selectedTheme.gradient"></div>

              <div class="relative z-10 w-full flex flex-col items-center">
                <div
                  class="w-[160px] h-[160px] rounded-full bg-[#111] border-4 border-[#1c1516] flex items-center justify-center relative mb-5 transition-transform duration-[8s] ease-linear"
                  :class="[isVinylSpinning ? 'animate-spin-slow' : '', selectedTheme.shadow]">
                  <div
                    class="absolute inset-0 rounded-full border-[2px] border-[#222] opacity-50 transform scale-90"></div>
                  <img
                    :src="selectedNote?.music_album_image"
                    class="w-[80px] h-[80px] rounded-full object-cover border-2 border-[#111] relative z-10" />
                </div>

                <h2 class="text-xl font-bold text-white text-center leading-tight">
                  {{ selectedNote?.music_track_name }}
                </h2>
                <p :class="selectedTheme.text" class="text-xs font-medium uppercase tracking-wide mb-4 mt-1">
                  {{ selectedNote?.music_artist_name }}
                </p>

                <div class="w-full max-w-[200px] mb-5 mt-2">
                  <div class="h-1 bg-black/40 rounded-full overflow-hidden w-full">
                    <div
                      :class="selectedTheme.bg_color"
                      class="h-full transition-all duration-100 ease-linear"
                      :style="{ width: `${(currentTime / 30) * 100}%` }"></div>
                  </div>
                  <div class="flex justify-between text-[10px] text-white/50 mt-1 font-mono">
                    <span>Preview: {{ formatTimeMusic(currentTime) }}</span>
                    <span>0:30</span>
                  </div>
                </div>

                <a
                  v-if="selectedNote?.music_track_id"
                  :href="`https://www.deezer.com/track/${selectedNote?.music_track_id}`"
                  target="_blank"
                  :class="selectedTheme.modal_btn"
                  class="flex items-center gap-2 text-white px-5 py-2.5 rounded-full text-xs font-bold transition-transform hover:scale-105 no-underline decoration-0 group">
                  <img
                    src="https://cdn.brandfetch.io/idEUKgCNtu/theme/dark/symbol.svg?c=1dxbfHSJFAPEGdCLU4o5B"
                    alt="Deezer"
                    class="w-4 h-4 object-contain filter brightness-0 invert" />
                  <span>Putar Lagu Penuh</span>
                </a>
              </div>
            </div>

            <div class="flex-1 bg-black/20 p-6 overflow-y-auto custom-scrollbar">
              <div class="flex justify-between items-center mb-6 pb-4 border-b" :class="selectedTheme.border">
                <div class="flex items-center gap-3">
                  <div
                    @click.stop="openPreview(selectedNote?.author_avatar || selectedNote?.author_photo_url)"
                    class="relative group/avatar cursor-zoom-in">
                    <img
                      :src="selectedNote?.author_avatar || selectedNote?.author_photo_url"
                      class="w-10 h-10 rounded-full border border-white/10 object-cover transition-transform group-hover/avatar:scale-110" />
                  </div>
                  <div>
                    <p class="text-[10px] text-white/50 uppercase tracking-wide">DARI</p>
                    <div class="flex items-center gap-2">
                      <p class="text-sm font-bold text-white">{{ selectedNote?.author_name }}</p>
                      <span
                        v-if="selectedNote?.is_admin"
                        class="bg-[#9a203e] text-white text-[9px] px-1.5 py-0.5 rounded-[4px] font-bold uppercase tracking-wider border border-white/10">
                        Admin
                      </span>
                    </div>
                  </div>
                </div>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  class="text-white/30"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round">
                  <path d="M5 12h14M12 5l7 7-7 7" />
                </svg>
                <div class="text-right">
                  <p class="text-[10px] text-white/50 uppercase tracking-wide">UNTUK</p>
                  <p :class="selectedTheme.text" class="text-sm font-bold">{{ selectedNote?.recipient }}</p>
                </div>
              </div>

              <div class="mb-6">
                <p class="font-hand text-xl text-[#e5e5e5] leading-loose tracking-wide break-words">
                  "{{ selectedNote?.content }}"
                </p>
              </div>

              <div
                class="flex items-center gap-2 text-[11px] text-white/60 font-mono bg-black/20 p-3 rounded-lg border"
                :class="selectedTheme.border">
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
                <span>
                  Dikirim: {{ formatDateDetail(selectedNote?.created_at) }}
                  <span
                    v-if="isEdited(selectedNote?.created_at, selectedNote?.updated_at)"
                    :class="selectedTheme.text"
                    class="italic ml-1 font-bold">
                    (diedit)
                  </span>
                </span>
              </div>

              <div class="mt-6">
                <button
                  @click="closeModalDetail"
                  :class="[
                    selectedTheme.btn_hover, // 1. Saat hover, background berubah jadi warna tema
                    'border-white/10 text-white/50', // 2. State normal (Netral/Abu-abu)
                    'hover:text-white hover:border-transparent', // 3. Saat hover, text jadi putih & border hilang
                  ]"
                  class="w-full py-3 rounded-[12px] border font-bold text-xs uppercase tracking-widest transition-all cursor-pointer">
                  Tutup Pesan
                </button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <Teleport to="body">
      <Transition name="fade">
        <div
          v-if="showImagePreview"
          class="fixed inset-0 z-[10000] flex flex-col items-center justify-center bg-black/95 backdrop-blur-xl p-4 cursor-pointer"
          @click="closePreview">
          <div class="relative flex flex-col items-center w-full max-w-[90vw] max-h-[90vh] cursor-default">
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
              class="w-auto h-auto max-w-full max-h-[80vh] object-contain rounded-lg shadow-2xl"
              @click.stop />
            <p class="text-white/50 text-sm tracking-widest uppercase font-bold mt-4" @click.stop>Foto Profil</p>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<style scoped></style>
