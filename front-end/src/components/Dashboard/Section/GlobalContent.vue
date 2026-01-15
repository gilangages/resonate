<script setup>
import { useLocalStorage, useWindowSize } from "@vueuse/core";
// UPDATE 1: Tambahkan 'noteDelete' ke dalam import agar fungsi deleteReply berjalan
import { noteList, searchMusic, noteCreate, noteDetail, noteDelete } from "../../../lib/api/NoteApi";
import { onMounted, ref, nextTick, Teleport, computed, reactive } from "vue";
import { formatTime, isEdited } from "../../../lib/dateFormatter";
import { useDebounceFn } from "@vueuse/core";
import DashboardToolbar from "./DashboardToolbar.vue";
import { useCardTheme } from "../../../lib/useCardTheme";
import { useShareImage } from "../../../lib/useShareImage";
import { useNow } from "@vueuse/core";
import { alertSuccess, alertError, alertConfirm } from "../../../lib/alert";
import { userDetail } from "../../../lib/api/UserApi";

// --- DECLARATIONS LAMA (TIDAK DIUBAH) ---
const showShareOptions = ref(false);
const generatedFileUrl = ref(null);
const shareTextData = ref({ title: "", text: "" });
const tempFile = ref(null);
const notes = ref([]);
const isLoading = ref(true);
const currentPage = ref(1);
const lastPage = ref(1);
const isLoadingMore = ref(false);
const showModal = ref(false);
const selectedNote = ref(null);
const isVinylSpinning = ref(false);
const showImagePreview = ref(false);
const previewImageUrl = ref("");
const searchQuery = ref("");
const sortBy = ref("newest");

const { getTheme, getSelectedTheme } = useCardTheme();
const { captureRef, generateImageFile, triggerNativeShare, isDownloading } = useShareImage();
const token = useLocalStorage("token", "");
const cacheBuster = ref(Date.now());
const now = useNow({ interval: 60000 });
const { width } = useWindowSize();

const canNativeShare = computed(() => !!navigator.share);
const currentAudio = ref(new Audio());
const currentTime = ref(0);

// --- DECLARATIONS BARU (UNTUK FITUR REPLY) ---
const isReplying = ref(false);
const replyQuery = ref("");
const replySearchResults = ref([]);
const isSearchingReply = ref(false);
const selectedReplySong = ref(null);
const replyNote = reactive({ content: "", initial_name: "" });
let replyDebounceTimer = null;
const currentUser = ref(null);

// Fetch User Profile
const fetchCurrentUser = async () => {
  if (!token.value) return;
  try {
    const res = await userDetail(token.value);
    const data = await res.json();
    if (res.ok) currentUser.value = data.data;
  } catch (e) {
    console.error(e);
  }
};

// Fungsi Delete Reply (Pastikan noteDelete sudah diimport di atas)
const deleteReply = async (replyId) => {
  if (!(await alertConfirm("Hapus balasan lagu ini?"))) return;

  try {
    const response = await noteDelete(token.value, replyId);

    if (response.ok) {
      alertSuccess("Balasan dihapus.");
      // Refresh modal data
      const res = await noteDetail(token.value, selectedNote.value.id);
      if (res.ok) {
        const resData = await res.json();
        selectedNote.value = resData.data;
      }
    } else {
      await alertError("Gagal menghapus.");
    }
  } catch (error) {
    console.error(error);
  }
};

const selectedTheme = computed(() => {
  return getSelectedTheme(selectedNote.value);
});

// --- FUNCTIONS LAMA ---
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

const loadMore = async () => {
  if (currentPage.value < lastPage.value) {
    isLoadingMore.value = true;
    currentPage.value++;
    await fetchNoteList(false);
    isLoadingMore.value = false;
  }
};

const playAudio = (item) => {
  let streamUrl = null;
  if (item.music_track_id) {
    streamUrl = `${import.meta.env.VITE_APP_PATH || "http://localhost:8000/api"}/stream/${item.music_track_id}`;
  } else if (item.music_preview_url) {
    streamUrl = item.music_preview_url;
  }

  if (streamUrl) {
    currentAudio.value.pause();
    currentAudio.value.currentTime = 0;
    currentAudio.value.src = streamUrl;
    currentAudio.value.volume = 0.5;
    currentAudio.value.loop = true;
    currentAudio.value.ontimeupdate = () => {
      currentTime.value = currentAudio.value.currentTime;
    };
    currentAudio.value.play().catch((e) => console.error("Gagal play audio:", e));
  }
};

const openModalDetail = async (note) => {
  if (!localStorage.getItem("token")) {
    window.location.href = "/login";
    return;
  }
  cacheBuster.value = Date.now();
  selectedNote.value = note;
  showModal.value = true;
  currentTime.value = 0;

  isReplying.value = false;
  selectedReplySong.value = null;
  replyNote.content = "";
  replySearchResults.value = [];
  replyQuery.value = "";

  playAudio(note);

  try {
    const res = await noteDetail(token.value, note.id);
    if (res.ok) {
      const resData = await res.json();
      selectedNote.value = resData.data;
    }
  } catch (e) {
    console.error("Gagal refresh detail note", e);
  }

  nextTick(() => {
    const modalElement = document.querySelector(".modal-capture-target");
    if (modalElement) captureRef.value = modalElement;
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

const handleReplySearchInput = () => {
  if (replyDebounceTimer) clearTimeout(replyDebounceTimer);
  if (replyQuery.value.length < 2) {
    replySearchResults.value = [];
    return;
  }
  isSearchingReply.value = true;
  replyDebounceTimer = setTimeout(async () => {
    try {
      const response = await searchMusic(token.value, { query: replyQuery.value });
      const data = await response.json();
      if (data && data.tracks && data.tracks.items) {
        replySearchResults.value = data.tracks.items;
      } else {
        replySearchResults.value = [];
      }
    } catch (error) {
      console.error("Error search reply:", error);
    } finally {
      isSearchingReply.value = false;
    }
  }, 300);
};

const selectSongForReply = (song) => {
  selectedReplySong.value = song;
  replyQuery.value = "";
  replySearchResults.value = [];
};

const cancelReply = () => {
  isReplying.value = false;
  selectedReplySong.value = null;
  replyNote.content = "";
};

const submitReply = async () => {
  if (!selectedReplySong.value) {
    await alertError("Pilih lagu dulu dong!");
    return;
  }
  const senderName = replyNote.initial_name.trim() || "Teman Rahasia";

  const payload = {
    parent_id: selectedNote.value.id,
    content: replyNote.content || "Membalas dengan lagu...",
    recipient: selectedNote.value.author_name,
    initial_name: senderName,
    music_track_id: selectedReplySong.value.id,
    music_track_name: selectedReplySong.value.name,
    music_artist_name: selectedReplySong.value.artists[0].name,
    music_album_image: selectedReplySong.value.album.images[0]?.url || "",
    music_preview_url: selectedReplySong.value.preview_url || null,
    music_track_link: selectedReplySong.value.external_urls?.spotify || null,
  };

  try {
    const response = await noteCreate(token.value, payload);
    const responseBody = await response.json();

    if (response.ok) {
      replyNote.initial_name = "";
      alertSuccess("Balasan terkirim!");
      cancelReply();

      const res = await noteDetail(token.value, selectedNote.value.id);
      if (res.ok) {
        const resData = await res.json();
        selectedNote.value = resData.data;
      }
    } else {
      const pesanError = responseBody.errors ? Object.values(responseBody.errors)[0][0] : responseBody.message;
      await alertError(pesanError);
    }
  } catch (error) {
    console.error(error);
    await alertError("Terjadi kesalahan koneksi.");
  }
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

const handleShare = async () => {
  if (!selectedNote.value) return;
  const fileName = `pesan-dari-${selectedNote.value.author_name || "user"}`;
  const title = "Music Note Card";
  const text = `Dengerin pesan lagu dari ${selectedNote.value.author_name} buat ${selectedNote.recipient} 🎵`;

  try {
    const file = await generateImageFile(fileName);
    if (!file) return;
    tempFile.value = file;
    generatedFileUrl.value = URL.createObjectURL(file);
    shareTextData.value = { title, text };
    showShareOptions.value = true;
  } catch (error) {
    console.error("Error saat handleShare:", error);
  }
};

const downloadManual = () => {
  if (!generatedFileUrl.value) return;
  const link = document.createElement("a");

  // UPDATE: Gunakan nama file dari tempFile jika ada, fallback ke timestamp jika null
  link.download = tempFile.value?.name || `music-note-${Date.now()}.png`;

  link.href = generatedFileUrl.value;
  link.click();
  showShareOptions.value = false;
  setTimeout(() => {
    alertSuccess("Gambar berhasil disimpan ke galeri!");
  }, 300);
};

const onCopyLink = async () => {
  if (!selectedNote.value) return;
  const shareUrl = `${window.location.origin}/note/${selectedNote.value.id}`;
  try {
    await navigator.clipboard.writeText(shareUrl);
    showShareOptions.value = false;
    setTimeout(() => {
      alertSuccess("Link berhasil disalin!");
    }, 300);
  } catch (err) {
    console.error("Gagal menyalin link:", err);
  }
};

const onNativeShare = async () => {
  if (tempFile.value) {
    const success = await triggerNativeShare(tempFile.value, shareTextData.value.text);
    if (success) showShareOptions.value = false;
  }
};

const closeShareOptions = () => {
  showShareOptions.value = false;
  if (generatedFileUrl.value) {
    URL.revokeObjectURL(generatedFileUrl.value);
    generatedFileUrl.value = null;
  }
};

const columns = computed(() => {
  if (width.value < 768) return [notes.value];
  const cols = [[], [], []];
  notes.value.forEach((note, index) => {
    cols[index % 3].push(note);
  });
  return cols;
});

const getImageUrl = (url, uniqueId = "global") => {
  if (!url) return "";
  if (url.includes("api.dicebear.com") || url.startsWith("data:")) return url;
  if (!url.startsWith("http")) return url;
  if (url.includes("/image-proxy?url=")) return url;
  const apiUrl = import.meta.env.VITE_APP_PATH || "http://localhost:8000/api";
  const encodedImageUrl = encodeURIComponent(url);
  return `${apiUrl}/image-proxy?url=${encodedImageUrl}`;
};

const handleQuickShare = async (note, event) => {
  if (isDownloading.value) return;
  event.stopPropagation();
  event.preventDefault();
  selectedNote.value = note;
  const parentCard = event.currentTarget.closest(".group\\/card");
  const cardElement = parentCard ? parentCard.querySelector(".rounded-\\[24px\\]") : null;
  if (cardElement) {
    captureRef.value = cardElement;
    await nextTick();
    await new Promise((resolve) => setTimeout(resolve, 80));
  }
  await handleShare();
};

onMounted(async () => {
  await fetchCurrentUser();
  await fetchNoteList(true);
});
</script>

<template>
  <div>
    <div class="p-4 md:pt-0 md:p-8 relative min-h-screen font-jakarta bg-[#0f0505]">
      <DashboardToolbar
        v-model:searchQuery="searchQuery"
        v-model:sortBy="sortBy"
        @search="handleSearch"
        placeholder="Jelajahi pesan dunia..." />

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
          </div>
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
            <button
              @click.stop="handleQuickShare(note, $event)"
              class="absolute top-4 right-4 z-30 p-2.5 bg-black/60 backdrop-blur-md rounded-full border transition-all duration-300 flex items-center justify-center group/btn opacity-100 md:opacity-0 md:group-hover/card:opacity-100 hover:scale-110 active:scale-95"
              :class="getTheme(note.id).border">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                :class="getTheme(note.id).text">
                <circle cx="18" cy="5" r="3"></circle>
                <circle cx="6" cy="12" r="3"></circle>
                <circle cx="18" cy="19" r="3"></circle>
                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
              </svg>
            </button>
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
                  <img
                    :src="getImageUrl(note.music_album_image, note.id + '-card-album')"
                    :crossorigin="note.music_album_image?.includes('http') ? 'anonymous' : null"
                    class="w-full h-full object-cover" />
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
                    :src="getImageUrl(note.author_avatar || note.author_photo_url, note.id + '-card-avatar')"
                    :crossorigin="(note.author_avatar || note.author_photo_url)?.includes('http') ? 'anonymous' : null"
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
            class="fixed inset-0 z-[50] flex items-center justify-center bg-black/90 backdrop-blur-md p-4"
            @click.self="closeModalDetail">
            <div
              class="modal-capture-target w-full max-w-[420px] md:max-w-[600px] rounded-[24px] shadow-2xl border flex flex-col overflow-hidden relative max-h-[90vh] transition-transform duration-300"
              :class="[showModal ? 'scale-100' : 'scale-95', selectedTheme.bg, selectedTheme.border]">
              <button
                @click="closeModalDetail"
                :class="selectedTheme.btn_hover"
                class="exclude-from-capture absolute top-4 right-4 z-50 bg-black/40 text-white p-2 rounded-full transition-colors backdrop-blur-md border border-white/10 cursor-pointer">
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
                    :class="[isVinylSpinning ? 'animate-spin-slow' : '', selectedTheme.shadow]">
                    <img
                      v-if="selectedNote?.music_album_image"
                      :src="getImageUrl(selectedNote?.music_album_image, selectedNote?.id + '-album')"
                      crossorigin="anonymous"
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
                    <div
                      @click.stop="openPreview(selectedNote?.author_avatar || selectedNote?.author_photo_url)"
                      class="relative group/avatar cursor-zoom-in">
                      <img
                        v-if="selectedNote?.author_avatar || selectedNote?.author_photo_url"
                        :src="
                          getImageUrl(
                            selectedNote?.author_avatar || selectedNote?.author_photo_url,
                            'avatar-' + selectedNote?.id
                          )
                        "
                        crossorigin="anonymous"
                        class="w-10 h-10 rounded-full border border-white/10 object-cover" />
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

                <div class="border-t border-white/10 pt-6 exclude-from-capture">
                  <div class="flex items-center justify-between mb-4">
                    <h3 class="text-[#e5e5e5] text-sm font-bold uppercase tracking-widest flex items-center gap-2">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2">
                        <path d="M9 18l6-6-6-6" />
                      </svg>
                      Resonansi Balasan ({{ selectedNote?.replies?.length || 0 }})
                    </h3>
                    <button
                      v-if="!isReplying"
                      @click="isReplying = true"
                      class="text-xs bg-white/10 hover:bg-white/20 text-white px-3 py-1.5 rounded-full transition-colors font-semibold">
                      + Balas Lagu
                    </button>
                  </div>

                  <div
                    v-if="isReplying"
                    class="bg-black/30 p-4 rounded-xl border border-white/10 mb-6 animate-slide-up">
                    <div class="mb-4 relative">
                      <label class="text-[10px] text-white/50 font-bold uppercase mb-1 block">Pilih Lagu Balasan</label>
                      <div
                        v-if="selectedReplySong"
                        class="flex items-center gap-3 bg-white/5 p-2 rounded-lg border border-white/10">
                        <img :src="selectedReplySong.album.images[0]?.url" class="w-10 h-10 rounded shadow" />
                        <div class="flex-1 min-w-0">
                          <p class="text-xs font-bold text-white truncate">{{ selectedReplySong.name }}</p>
                          <p class="text-[10px] text-white/50 truncate">{{ selectedReplySong.artists[0].name }}</p>
                        </div>
                        <button @click="selectedReplySong = null" class="text-red-400 hover:text-red-300 p-1">
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="16"
                            height="16"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                          </svg>
                        </button>
                      </div>

                      <input
                        v-else
                        v-model="replyQuery"
                        @input="handleReplySearchInput"
                        type="text"
                        placeholder="Ketik judul lagu..."
                        class="w-full bg-black/40 border rounded-lg px-3 py-2 text-xs text-white focus:outline-none transition-colors"
                        :class="selectedTheme.border" />

                      <div
                        v-if="replySearchResults.length > 0"
                        class="absolute z-50 left-0 right-0 top-full mt-1 bg-[#1c1516] border border-white/10 rounded-lg max-h-40 overflow-y-auto shadow-xl custom-scrollbar">
                        <div
                          v-for="song in replySearchResults"
                          :key="song.id"
                          @click="selectSongForReply(song)"
                          class="flex items-center gap-3 p-2 hover:bg-white/10 cursor-pointer border-b border-white/5 last:border-0">
                          <img :src="song.album.images[0]?.url" class="w-8 h-8 rounded" />
                          <div class="min-w-0">
                            <p class="text-xs font-bold text-white truncate">{{ song.name }}</p>
                            <p class="text-[10px] text-white/50 truncate">{{ song.artists[0].name }}</p>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="mb-2">
                      <label class="text-[10px] text-white/50 font-bold uppercase mb-1 block">Pesan (Opsional)</label>
                      <textarea
                        v-model="replyNote.content"
                        rows="2"
                        placeholder="Tulis pesan singkat..."
                        class="w-full bg-black/40 border rounded-lg px-3 py-2 text-xs text-white focus:outline-none transition-colors resize-none"
                        :class="selectedTheme.border"></textarea>
                    </div>

                    <div class="mb-4">
                      <label class="text-[10px] text-white/50 font-bold uppercase mb-1 block">Dari Siapa?</label>
                      <div class="flex gap-2">
                        <input
                          v-model="replyNote.initial_name"
                          type="text"
                          placeholder="Ketik nama (atau kosongkan utk Anonim)"
                          class="flex-1 bg-black/40 border rounded-lg px-3 py-2 text-xs text-white focus:outline-none transition-colors"
                          :class="selectedTheme.border" />
                      </div>
                    </div>

                    <div class="flex gap-2">
                      <button
                        @click="cancelReply"
                        class="flex-1 py-2 text-[10px] uppercase font-bold text-white/40 hover:text-white border border-white/5 rounded-lg">
                        Batal
                      </button>
                      <button
                        @click="submitReply"
                        class="flex-1 py-2 text-[10px] uppercase font-bold text-white rounded-lg shadow-lg"
                        :class="selectedTheme.modal_btn">
                        Kirim Balasan
                      </button>
                    </div>
                  </div>

                  <div class="flex flex-col gap-3">
                    <div
                      v-if="!selectedNote?.replies || selectedNote.replies.length === 0"
                      class="text-center py-4 text-white/20 text-xs italic">
                      Belum ada yang membalas dengan lagu.
                    </div>

                    <div
                      v-for="reply in selectedNote.replies"
                      :key="reply.id"
                      class="flex items-center gap-3 bg-black/20 p-3 rounded-xl border hover:border-white/10 transition-colors group/reply"
                      :class="selectedTheme.border">
                      <div class="relative w-10 h-10 shrink-0">
                        <img
                          :src="reply.music_album_image"
                          class="w-full h-full rounded-md object-cover brightness-75 group-hover/reply:brightness-100 transition-all" />
                        <button
                          @click="playAudio(reply)"
                          class="absolute inset-0 flex items-center justify-center text-white opacity-0 group-hover/reply:opacity-100 transition-opacity">
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="currentColor">
                            <path d="M8 5v14l11-7z" />
                          </svg>
                        </button>
                      </div>

                      <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start">
                          <p class="text-xs font-bold text-white truncate pr-2">{{ reply.music_track_name }}</p>
                          <span class="text-[9px] text-white/30 whitespace-nowrap">
                            {{ formatTime(reply.created_at, now) }}
                          </span>
                        </div>
                        <p class="text-[10px] font-medium truncate" :class="selectedTheme.text">
                          {{ reply.music_artist_name }}
                        </p>
                        <p v-if="reply.content" class="text-[10px] text-white/60 italic truncate mt-0.5">
                          "{{ reply.content }}"
                        </p>
                        <p class="text-[9px] text-white/30 mt-1">Dari: {{ reply.author_name || "Anonim" }}</p>
                      </div>

                      <button
                        v-if="currentUser && (reply.user_id === currentUser.id || currentUser.role === 'admin')"
                        @click.stop="deleteReply(reply.id)"
                        class="text-white/20 hover:text-red-500 transition-colors p-1"
                        title="Hapus Balasan">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          width="14"
                          height="14"
                          viewBox="0 0 24 24"
                          fill="none"
                          stroke="currentColor"
                          stroke-width="2"
                          stroke-linecap="round"
                          stroke-linejoin="round">
                          <polyline points="3 6 5 6 21 6"></polyline>
                          <path
                            d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                      </button>
                    </div>
                    <div v-if="selectedNote?.replies?.length >= 10" class="text-center py-4">
                      <p class="text-[10px] text-white/30 italic">
                        Menampilkan 10 balasan terbaru.
                        <span class="block">Pesan ini sangat populer! 🔥</span>
                      </p>
                    </div>
                  </div>
                </div>

                <div class="mt-6 flex gap-3 exclude-from-capture">
                  <button
                    @click="closeModalDetail"
                    :class="[
                      selectedTheme.btn_hover,
                      'border-white/10 text-white/50',
                      'hover:text-white hover:border-transparent',
                    ]"
                    class="flex-1 py-3 rounded-[12px] border font-bold text-xs uppercase tracking-widest transition-all cursor-pointer">
                    Tutup
                  </button>
                  <button
                    @click="handleShare"
                    :disabled="isDownloading"
                    :class="[
                      selectedTheme.modal_btn,
                      isDownloading ? 'opacity-70 cursor-wait' : 'hover:brightness-110 cursor-pointer',
                    ]"
                    class="flex-1 py-3 rounded-[12px] text-white font-bold text-xs uppercase tracking-widest transition-all flex items-center justify-center gap-2 shadow-lg">
                    <span v-if="isDownloading">Memproses...</span>
                    <span v-else class="flex items-center gap-2">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="18" cy="5" r="3"></circle>
                        <circle cx="6" cy="12" r="3"></circle>
                        <circle cx="18" cy="19" r="3"></circle>
                        <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                        <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                      </svg>
                      Bagikan
                    </span>
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
            </div>
          </div>
        </Transition>
      </Teleport>
      <Teleport to="body">
        <Transition name="fade">
          <div
            v-if="showShareOptions"
            class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center bg-black/80 backdrop-blur-sm p-4"
            @click.self="closeShareOptions">
            <div
              class="bg-[#1c1516] border border-[#333] rounded-t-[32px] sm:rounded-3xl p-6 w-full max-w-sm shadow-2xl relative animate-slide-up">
              <h3 class="text-white text-lg font-bold mb-2 text-center">Bagikan Pesan</h3>
              <div class="grid grid-cols-1 gap-3 mt-6">
                <button
                  v-if="canNativeShare"
                  @click="onNativeShare"
                  class="flex items-center gap-4 p-4 rounded-2xl bg-[#9a203e]/10 border border-[#9a203e]/20 hover:bg-[#9a203e]/20 transition-all text-left">
                  <span class="text-2xl">📱</span>
                  <div><p class="text-sm font-bold text-[#f87171]">Bagikan ke Aplikasi</p></div>
                </button>
                <button
                  @click="downloadManual"
                  class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 transition-all text-left">
                  <span class="text-2xl">💾</span>
                  <div><p class="text-sm font-bold text-white">Simpan ke Galeri</p></div>
                </button>
                <button
                  @click="onCopyLink"
                  class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 transition-all text-left">
                  <span class="text-2xl">🔗</span>
                  <div><p class="text-sm font-bold text-white">Salin Link Pesan</p></div>
                </button>
              </div>
              <button
                @click="closeShareOptions"
                class="w-full mt-6 py-3 text-xs font-bold text-white/30 hover:text-white transition-colors uppercase tracking-widest">
                Batal
              </button>
            </div>
          </div>
        </Transition>
      </Teleport>
    </div>
  </div>
</template>

<style scoped>
.animate-spin-slow {
  animation: spin 8s linear infinite;
}
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
.animate-slide-up {
  animation: slideUp 0.3s ease-out forwards;
}
@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  bg: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.2);
}
</style>
