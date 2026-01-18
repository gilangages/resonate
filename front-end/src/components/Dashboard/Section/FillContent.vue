<script setup>
import { useLocalStorage } from "@vueuse/core";
import { myNoteList, noteDelete } from "../../../lib/api/NoteApi";
import { ref, nextTick, onMounted, Teleport, computed } from "vue";
import { alertConfirm, alertError, alertSuccess } from "../../../lib/alert";
import { formatTime, isEdited } from "../../../lib/dateFormatter";
import { useDebounceFn } from "@vueuse/core";
import { noteBulkDelete } from "../../../lib/api/NoteApi";
import DashboardToolbar from "./DashboardToolbar.vue";
import { useCardTheme } from "../../../lib/useCardTheme";
import { useNow, useWindowSize } from "@vueuse/core";

// Emit ke Parent
const emit = defineEmits(["open-modal", "is-empty", "edit-note"]);
const { getTheme, getSelectedTheme } = useCardTheme();
const token = useLocalStorage("token", "");
const notes = ref([]);
const currentAudio = ref(new Audio());
const currentTime = ref(0);
const now = useNow({ interval: 60000 });
const { width } = useWindowSize();

// --- STATE LOADING (BARU) ---
const isLoading = ref(true);

// --- STATE PAGINATION ---
const currentPage = ref(1);
const lastPage = ref(1);
const isLoadingMore = ref(false);

// --- STATE MODAL & PREVIEW ---
const showModal = ref(false);
const selectedNote = ref(null);
const isVinylSpinning = ref(false);
const showImagePreview = ref(false);
const previewImageUrl = ref("");

const searchQuery = ref("");
const sortBy = ref("newest"); // 'newest' | 'oldest'
const isSelectionMode = ref(false); // Mode pilih aktif/tidak
const selectedIds = ref([]); // ID note yang dipilih
const selectedTheme = computed(() => {
  return getSelectedTheme(selectedNote.value);
});

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

// --- FETCH DATA ---
async function fetchNoteList(reset = true) {
  if (reset) {
    currentPage.value = 1;
    isLoading.value = true; // Mulai Loading
    selectedIds.value = []; // Reset pilihan saat refresh
  }

  try {
    // Panggil API dengan parameter baru
    const response = await myNoteList(token.value, currentPage.value, searchQuery.value, sortBy.value);
    const responseBody = await response.json();
    console.log(responseBody);

    if (response.ok) {
      if (responseBody.meta) {
        lastPage.value = responseBody.meta.last_page;
        currentPage.value = responseBody.meta.current_page;
      }

      if (reset) {
        notes.value = responseBody.data;
      } else {
        notes.value.push(...responseBody.data);
      }
    } else {
      console.error("Gagal load data:", responseBody.message);
    }
  } catch (error) {
    console.error("Gagal fetch:", error);
  } finally {
    isLoading.value = false; // Selesai Loading
  }
}

// --- DEBOUNCE SEARCH (Supaya tidak spam API saat ngetik) ---
const handleSearch = useDebounceFn(() => {
  fetchNoteList(true);
}, 500);

// --- LOGIC BULK DELETE ---
const toggleSelectionMode = () => {
  isSelectionMode.value = !isSelectionMode.value;
  selectedIds.value = []; // Reset pilihan saat mode berubah
};

const cancelSelectionMode = () => {
  isSelectionMode.value = false;
  selectedIds.value = [];
};

const toggleSelectAll = () => {
  if (selectedIds.value.length === notes.value.length) {
    selectedIds.value = []; // Uncheck all
  } else {
    selectedIds.value = notes.value.map((n) => n.id); // Check all visible
  }
};

const handleBulkDelete = async () => {
  if (selectedIds.value.length === 0) return;

  if (!(await alertConfirm(`Hapus ${selectedIds.value.length} pesan terpilih?`))) return;

  try {
    const response = await noteBulkDelete(token.value, selectedIds.value);
    if (response.ok) {
      alertSuccess("Pesan terpilih berhasil dihapus.");
      isSelectionMode.value = false;
      await fetchNoteList(true); // Refresh data
    } else {
      await alertError("Gagal menghapus pesan.");
    }
  } catch (e) {
    console.error(e);
  }
};

// --- DELETE LOGIC ---
async function handleDelete(id) {
  if (!(await alertConfirm("Are you sure you want to delete this note?"))) return;

  try {
    const response = await noteDelete(token.value, id);
    const responseBody = await response.json();

    if (response.ok) {
      alertSuccess("Pesan berhasil dihapus.");
      const index = notes.value.findIndex((n) => n.id === id);
      if (index !== -1) notes.value.splice(index, 1);
      if (notes.value.length === 0) emit("is-empty");
    } else {
      const pesanError = responseBody.errors ? Object.values(responseBody.errors)[0][0] : responseBody.message;
      await alertError(pesanError);
    }
  } catch (error) {
    console.error("Gagal delete:", error);
    await alertError("Terjadi kesalahan saat menghapus note.");
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
  setTimeout(() => {
    previewImageUrl.value = "";
  }, 300);
};

// Fungsi untuk membagi notes menjadi 3 kolom agar urutannya menyamping
const columns = computed(() => {
  // JIKA MOBILE (< 768px alias 'md' di Tailwind):
  // Kembalikan 1 kolom berisi semua notes.
  // Ini akan membuat urutan render: Note 1, Note 2, Note 3... (Urut ke bawah)
  if (width.value < 768) {
    return [notes.value];
  }

  // JIKA DESKTOP:
  // Bagi menjadi 3 kolom (Masonry style)
  const cols = [[], [], []]; // Untuk 3 kolom (lg)
  notes.value.forEach((note, index) => {
    cols[index % 3].push(note); // Note 1 ke Kolom 1, Note 2 ke Kolom 2, dst
  });
  return cols;
});

onMounted(async () => {
  await fetchNoteList(true);
});
defineExpose({
  fetchNoteList,
});
</script>

<template>
  <div>
    <div class="p-4 md:pt-0 md:p-8 relative min-h-screen font-jakarta bg-[#0f0505]">
      <DashboardToolbar
        v-model:searchQuery="searchQuery"
        v-model:sortBy="sortBy"
        @search="handleSearch"
        placeholder="Cari pesan, lagu, atau artis...">
        <template #actions>
          <button
            @click="toggleSelectionMode"
            class="px-4 py-2 rounded-lg border text-xs font-bold uppercase tracking-wider transition-all"
            :class="
              isSelectionMode
                ? 'bg-[#2c2021] border-[#3f3233] text-white hover:border-[#9a203e]'
                : 'bg-transparent border-[#3f3233] text-[#8c8a8a] hover:border-[#9a203e]'
            ">
            {{ isSelectionMode ? "Batal" : "Pilih" }}
          </button>
        </template>
      </DashboardToolbar>

      <div v-if="isLoading" class="relative z-0 columns-1 md:columns-2 lg:columns-3 gap-6 mb-10 space-y-6">
        <div v-for="i in 6" :key="i" class="break-inside-avoid relative">
          <div class="bg-[#1c1516] rounded-[24px] p-6 border border-[#2c2021] animate-pulse h-full">
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
                <div class="h-3 w-20 bg-[#2b2122] rounded"></div>
                <div class="ml-auto h-3 w-16 bg-[#2b2122] rounded"></div>
              </div>
              <div class="flex gap-2 mt-2">
                <div class="flex-1 h-8 bg-[#2b2122] rounded-lg"></div>
                <div class="flex-1 h-8 bg-[#2b2122] rounded-lg"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-else-if="notes.length === 0" class="w-full flex flex-col items-center justify-center py-20 text-[#8c8a8a]">
        <div class="mb-4 text-lg">Belum ada pesan yang dibuat.</div>
        <button
          @click="$emit('open-modal')"
          class="hidden md:flex items-center gap-2 bg-[#9a203e] hover:bg-[#821c35] text-white px-6 py-3 rounded-full font-bold text-sm uppercase tracking-wider transition-all shadow-lg hover:shadow-[#9a203e]/20">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="18"
            height="18"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
            stroke-linecap="round"
            stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
          </svg>
          Buat Pesan Baru
        </button>
      </div>

      <div v-else class="flex flex-col md:flex-row gap-6 mb-10 items-start w-full">
        <div v-for="(col, colIdx) in columns" :key="colIdx" class="flex-1 min-w-0 flex flex-col gap-6 w-full md:w-1/3">
          <div v-for="note in col" :key="note.id" class="group/card flex flex-col h-auto relative w-full">
            <div v-if="isSelectionMode" class="absolute top-4 right-4 z-30">
              <input
                type="checkbox"
                :value="note.id"
                v-model="selectedIds"
                class="w-6 h-6 accent-[#9a203e] cursor-pointer rounded shadow-md border-2 border-white/20" />
            </div>

            <div
              @click="!isSelectionMode && openModalDetail(note)"
              :class="[
                getTheme(note.id).bg,
                getTheme(note.id).border,
                getTheme(note.id).hover,
                isSelectionMode ? 'cursor-default' : 'cursor-pointer',
              ]"
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
                :class="[getTheme(note.id).border, `group-hover/card:border-${getTheme(note.id).id}-500/50`]"
                class="bg-black/20 rounded-[16px] p-4 border mb-4 transition-colors relative z-10">
                <p
                  v-text="'&quot;' + note.content + '&quot;'"
                  class="text-[15px] text-[#ccc] italic font-hand leading-relaxed whitespace-pre-wrap break-words line-clamp-6"></p>
              </div>

              <div :class="getTheme(note.id).border" class="flex flex-col gap-3 pt-4 border-t relative z-10 mt-auto">
                <div class="flex items-center gap-2">
                  <img
                    :src="note.author_avatar || note.author_photo_url"
                    class="w-6 h-6 rounded-full border border-[#333] object-cover" />
                  <div class="flex flex-col">
                    <span class="text-[10px] text-[#666] uppercase font-bold">Dari</span>
                    <div class="flex items-center gap-1.5">
                      <span class="text-xs text-[#999] font-medium leading-none">
                        {{ note.author_name }}
                      </span>
                      <span
                        v-if="note.is_admin"
                        class="bg-[#9a203e] text-white text-[9px] px-1.5 py-0.5 rounded-[4px] font-bold uppercase tracking-wider border border-white/10 shadow-[0_0_10px_rgba(154,32,62,0.6)]">
                        Admin
                      </span>
                    </div>
                  </div>
                  <span class="text-[10px] text-[#555] font-mono ml-auto text-right">
                    {{ formatTime(note.created_at, now) }}
                    <span
                      v-if="isEdited(note.created_at, note.updated_at)"
                      :class="getTheme(note.id).text"
                      class="italic ml-1 block sm:inline">
                      (diedit)
                    </span>
                  </span>
                </div>

                <div
                  class="flex gap-2 w-full mt-2 transition-opacity duration-300"
                  :class="
                    isSelectionMode
                      ? 'opacity-0 pointer-events-none'
                      : 'opacity-100 lg:opacity-0 lg:group-hover/card:opacity-100'
                  ">
                  <button
                    @click.stop="$emit('edit-note', note)"
                    :class="[
                      getTheme(note.id).btn_border,
                      getTheme(note.id).btn_text,
                      'hover:bg-white/10 hover:text-white',
                    ]"
                    class="flex-1 py-2 rounded-lg border text-xs font-bold uppercase tracking-wider transition-colors">
                    Edit
                  </button>

                  <button
                    @click.stop="handleDelete(note.id)"
                    :class="[
                      getTheme(note.id).btn_bg,
                      getTheme(note.id).btn_border,
                      getTheme(note.id).btn_text,
                      getTheme(note.id).btn_hover,
                      'hover:text-white',
                    ]"
                    class="flex-1 py-2 rounded-lg border text-xs font-bold uppercase tracking-wider transition-colors">
                    Hapus
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
        <svg
          v-if="!isLoadingMore"
          xmlns="http://www.w3.org/2000/svg"
          width="14"
          height="14"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round">
          <path d="M6 9l6 6 6-6" />
        </svg>
      </div>

      <Transition name="fade">
        <button
          v-if="!isSelectionMode"
          @click="$emit('open-modal')"
          class="cursor-pointer fixed bottom-24 right-6 md:bottom-18 md:right-8 z-40 flex h-14 w-14 items-center justify-center rounded-full bg-[#9a203e] text-white shadow-[0_0_30px_rgba(154,32,62,0.4)] transition-all duration-300 hover:scale-110 hover:bg-[#821c35] active:scale-95 focus:outline-none sm:bottom-12 sm:right-12 sm:h-16 sm:w-16 group"
          :class="{ 'md:hidden': notes.length === 0 }"
          title="Buat Cerita Baru">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="2.5"
            stroke="currentColor"
            class="h-8 w-8 sm:h-9 sm:w-9 transition-transform group-hover:rotate-90">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
          </svg>
        </button>
      </Transition>

      <Teleport to="body">
        <Transition name="slide-up">
          <div
            v-if="isSelectionMode"
            class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[9999] bg-[#1c1516] border border-[#2c2021] text-white pl-6 pr-4 py-3 rounded-full shadow-2xl flex items-center gap-4 w-[90%] max-w-md justify-between sm:justify-center">
            <div class="flex items-center gap-3">
              <div class="flex items-center gap-2">
                <input
                  type="checkbox"
                  :checked="selectedIds.length === notes.length && notes.length > 0"
                  @change="toggleSelectAll"
                  class="w-4 h-4 accent-[#9a203e] cursor-pointer" />
                <span class="text-sm font-medium whitespace-nowrap">{{ selectedIds.length }} Terpilih</span>
              </div>
            </div>
            <div class="h-6 w-[1px] bg-[#2c2021] hidden sm:block"></div>
            <div class="flex items-center gap-2">
              <button
                @click="handleBulkDelete"
                :disabled="selectedIds.length === 0"
                class="text-[#9a203e] text-sm font-bold hover:text-[#b92b4a] transition-colors flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed px-2 py-1">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="18"
                  height="18"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round">
                  <path d="M3 6h18"></path>
                  <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                  <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                </svg>
                <span class="hidden sm:inline">Hapus</span>
              </button>
              <button
                @click="cancelSelectionMode"
                class="text-gray-400 hover:text-white text-sm font-medium px-2 py-1 ml-1">
                Batal
              </button>
            </div>
          </div>
        </Transition>
      </Teleport>

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
                    class="w-[160px] h-[160px] rounded-full bg-[#111] shadow-2xl border-4 border-[#1c1516] flex items-center justify-center relative mb-5 transition-transform duration-[8s] ease-linear"
                    :class="[isVinylSpinning ? 'animate-spin-slow' : '', selectedTheme.shadow]">
                    <div
                      class="absolute inset-0 rounded-full border-[2px] border-[#222] opacity-50 transform scale-90"></div>
                    <img
                      :src="selectedNote?.music_album_image"
                      class="w-[60px] h-[60px] rounded-full object-cover border-2 border-[#111] relative z-10" />
                  </div>

                  <h2 class="text-2xl font-bold text-white text-center leading-tight px-4">
                    {{ selectedNote?.music_track_name }}
                  </h2>
                  <p :class="selectedTheme.text" class="text-sm font-medium uppercase tracking-wide mb-3 mt-1">
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
                <div class="flex justify-between items-center mb-6 pb-6 border-b" :class="selectedTheme.border">
                  <div class="flex items-center gap-3">
                    <div
                      @click.stop="openPreview(selectedNote?.author_avatar || selectedNote?.author_photo_url)"
                      class="relative group/avatar cursor-zoom-in">
                      <img
                        :src="selectedNote?.author_avatar || selectedNote?.author_photo_url"
                        class="w-12 h-12 rounded-full border border-white/10 object-cover transition-transform group-hover/avatar:scale-110" />
                    </div>
                    <div>
                      <p class="text-[10px] text-white/50 uppercase tracking-wide font-bold">DARI</p>
                      <div class="flex items-center gap-2">
                        <p class="text-base font-bold text-white">{{ selectedNote?.author_name }}</p>
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
                    <p class="text-[10px] text-white/50 uppercase tracking-wide font-bold">UNTUK</p>
                    <p :class="selectedTheme.text" class="text-base font-bold">{{ selectedNote?.recipient }}</p>
                  </div>
                </div>

                <div class="mb-8">
                  <p class="font-hand text-xl text-[#e5e5e5] leading-loose tracking-wide break-words">
                    "{{ selectedNote?.content }}"
                  </p>
                </div>

                <div
                  class="flex items-center gap-2 text-[11px] text-white/60 font-mono bg-black/20 p-3 rounded-lg border mb-6"
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

                <div class="flex gap-3">
                  <button
                    @click="closeModalDetail"
                    :class="[
                      selectedTheme.btn_hover,
                      'border-white/20 text-white/50',
                      'hover:text-white hover:border-transparent',
                    ]"
                    class="flex-1 py-3 rounded-[14px] border font-bold text-xs uppercase tracking-widest transition-all cursor-pointer">
                    Tutup
                  </button>

                  <button
                    @click="
                      $emit('edit-note', selectedNote);
                      closeModalDetail();
                    "
                    :class="selectedTheme.modal_btn"
                    class="flex-1 py-3 rounded-[14px] text-white font-bold text-xs uppercase tracking-widest hover:brightness-110 transition-all cursor-pointer">
                    Edit Pesan
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
  </div>
</template>

<style scoped></style>
