<script setup>
import { useLocalStorage } from "@vueuse/core";
import { noteList } from "../../../lib/api/NoteApi";
import { onMounted, ref, nextTick, Teleport, computed } from "vue";
import { formatTime, isEdited } from "../../../lib/dateFormatter";
import { useDebounceFn } from "@vueuse/core";
import DashboardToolbar from "./DashboardToolbar.vue";
import { useCardTheme } from "../../../lib/useCardTheme";

const { getTheme, getSelectedTheme } = useCardTheme();
const token = useLocalStorage("token", "");
const notes = ref([]);

// --- AUDIO PLAYER STATE ---
const currentAudio = ref(new Audio());
const currentTime = ref(0);

// --- STATE LOADING ---
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
const searchQuery = ref("");
const sortBy = ref("newest");
const selectedTheme = getSelectedTheme(selectedNote);

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

// --- FETCH DATA ---
async function fetchNoteList(reset = true) {
  if (reset) {
    currentPage.value = 1;
    isLoading.value = true;
  }

  try {
    const response = await noteList(token.value, currentPage.value, searchQuery.value, sortBy.value);
    const responseBody = await response.json();
    if (response.ok) {
      if (reset) notes.value = responseBody.data;
      else notes.value.push(...responseBody.data);

      if (responseBody.meta) {
        lastPage.value = responseBody.meta.last_page;
        currentPage.value = responseBody.meta.current_page;
      }
    }
  } catch (error) {
    console.error("Fetch error:", error);
  } finally {
    isLoading.value = false;
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
  if (!localStorage.getItem("token")) {
    window.location.href = "/login";
    return;
  }

  selectedNote.value = note;
  showModal.value = true;
  currentTime.value = 0;

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
    currentAudio.value.ontimeupdate = () => {
      currentTime.value = currentAudio.value.currentTime;
    };
    currentAudio.value.play().catch((e) => console.error("Gagal play audio:", e));
  }

  nextTick(() => {
    setTimeout(() => {
      isVinylSpinning.value = true;
    }, 300);
  });
};

const closeModalDetail = () => {
  isVinylSpinning.value = false;
  currentAudio.value.pause();
  currentAudio.value.currentTime = 0;
  currentTime.value = 0;
  setTimeout(() => {
    showModal.value = false;
    selectedNote.value = null;
  }, 100);
};

const openPreview = (url) => {
  if (!url) return;
  previewImageUrl.value = url;
  showImagePreview.value = true;
};

const closePreview = () => {
  showImagePreview.value = false;
};

const handleSearch = useDebounceFn(() => fetchNoteList(true), 500);

// Fungsi pembagi kolom untuk Masonry (Sama dengan FillContent)
const columns = computed(() => {
  const cols = [[], [], []];
  notes.value.forEach((note, index) => {
    cols[index % 3].push(note);
  });
  return cols;
});

onMounted(async () => {
  await fetchNoteList(true);
});
</script>

<template>
  <div class="p-4 md:pt-0 md:p-8 relative min-h-screen font-jakarta bg-[#0f0505]">
    <DashboardToolbar
      v-model:searchQuery="searchQuery"
      v-model:sortBy="sortBy"
      @search="handleSearch"
      placeholder="Jelajahi pesan dunia..." />

    <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
      <div v-for="i in 6" :key="i" class="relative">
        <div class="bg-[#1c1516] rounded-[24px] p-6 border border-[#2c2021] animate-pulse h-80 flex flex-col"></div>
      </div>
    </div>

    <div v-else-if="notes.length === 0" class="w-full text-center text-[#8c8a8a] py-20 text-lg">
      Belum ada pesan yang dibuat.
    </div>

    <div v-else class="flex flex-col md:flex-row gap-6 mb-10 items-start w-full">
      <div v-for="(col, colIdx) in columns" :key="colIdx" class="flex-1 min-w-0 flex flex-col gap-6 w-full md:w-1/3">
        <div
          v-for="note in col"
          :key="note.id"
          class="group/card flex flex-col h-auto relative w-full cursor-pointer"
          @click="openModalDetail(note)">
          <div
            :class="[getTheme(note.id).bg, getTheme(note.id).border, getTheme(note.id).hover]"
            class="rounded-[24px] p-6 border shadow-lg transition-all duration-300 hover:-translate-y-2 relative overflow-hidden flex flex-col w-full">
            <div
              :class="`bg-gradient-to-b ${getTheme(note.id).gradient} to-transparent`"
              class="absolute inset-0 opacity-0 group-hover/card:opacity-100 transition-opacity duration-500"></div>

            <div class="mb-5 relative z-10">
              <p class="text-[11px] text-[#666] font-bold uppercase tracking-wider mb-1">UNTUK</p>
              <h2
                :class="getTheme(note.id).text_hover"
                class="text-2xl font-bold text-white transition-colors break-words leading-tight">
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
              :class="[
                getTheme(note.id).border, // 1. Border mengikuti tema (misal: Biru Tua)
                `group-hover/card:border-${getTheme(note.id).id}-500/50`, // 2. Efek hover menyala sesuai warna
              ]"
              class="bg-black/20 rounded-[16px] p-4 border mb-4 transition-colors relative z-10">
              <p
                v-text="'&quot;' + note.content + '&quot;'"
                class="text-[15px] text-[#ccc] italic font-hand leading-relaxed whitespace-pre-wrap break-words line-clamp-6"></p>
            </div>

            <div :class="getTheme(note.id).border" class="flex flex-col gap-3 pt-4 border-t relative z-10 mt-auto">
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
                    :class="getTheme(note.id).text"
                    class="italic ml-1 block sm:inline">
                    (diedit)
                  </span>
                </span>
              </div>

              <div
                class="w-full mt-2 opacity-100 lg:opacity-0 lg:group-hover/card:opacity-100 transition-opacity duration-300">
                <button
                  :class="[
                    getTheme(note.id).btn_bg,
                    getTheme(note.id).btn_border,
                    getTheme(note.id).btn_text,
                    getTheme(note.id).btn_hover,
                  ]"
                  class="w-full py-2 rounded-lg border text-xs font-bold uppercase tracking-widest hover:text-white transition-all flex items-center justify-center gap-2">
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
    </div>

    <Teleport to="body">
      <Transition name="fade">
        <div
          v-if="showModal"
          class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/90 backdrop-blur-md p-4"
          @click.self="closeModalDetail">
          <div
            class="w-full max-w-[420px] rounded-[24px] shadow-2xl border flex flex-col overflow-hidden relative max-h-[90vh] transition-transform duration-300"
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
                  class="w-[160px] h-[160px] rounded-full bg-[#111] border-4 border-[#1c1c1c] flex items-center justify-center relative mb-5 transition-transform duration-[8s] ease-linear"
                  :class="[
                    isVinylSpinning ? 'animate-spin-slow' : '',
                    selectedTheme.shadow /* INI YANG BARU: Efek cahaya di belakang vinyl */,
                  ]">
                  <div
                    class="absolute inset-0 rounded-full border-[2px] border-[#222] opacity-50 transform scale-90"></div>
                  <div class="absolute inset-0 rounded-full border border-[#333] opacity-30 transform scale-75"></div>

                  <img
                    :src="selectedNote?.music_album_image"
                    class="w-[65px] h-[65px] rounded-full object-cover border-2 border-[#111] relative z-10" />
                </div>

                <h2 class="text-xl font-bold text-white text-center leading-tight px-4">
                  {{ selectedNote?.music_track_name }}
                </h2>

                <p :class="selectedTheme.text" class="text-xs font-medium uppercase tracking-wide mb-3 mt-1">
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
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="currentColor">
                    <path d="M8 5v14l11-7z" />
                  </svg>
                  <span>Putar Lagu Penuh</span>
                </a>
              </div>
            </div>

            <div class="flex-1 bg-black/20 p-6 overflow-y-auto custom-scrollbar">
              <div class="flex justify-between items-center mb-6 pb-4 border-b" :class="selectedTheme.border">
                <div class="flex items-center gap-3">
                  <img
                    @click="openPreview(selectedNote?.author_avatar)"
                    :src="selectedNote?.author_avatar"
                    class="w-10 h-10 rounded-full border border-white/10 object-cover cursor-zoom-in hover:scale-110 transition-transform" />
                  <div>
                    <p class="text-[10px] text-white/50 uppercase tracking-wide">DARI</p>
                    <p class="text-sm font-bold text-white">{{ selectedNote?.author }}</p>
                  </div>
                </div>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="16"
                  height="16"
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
