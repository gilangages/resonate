<script setup>
import { useLocalStorage } from "@vueuse/core";
import { myNoteList, noteDelete } from "../../../lib/api/NoteApi";
import { onBeforeMount, ref } from "vue";
import { alertConfirm, alertSuccess } from "../../../lib/alert";

// Definisi Emit ke Parent (DashboardUser)
const emit = defineEmits(["open-modal", "is-empty"]);

const token = useLocalStorage("token", "");
const notes = ref([]);

// --- STATE PAGINATION ---
const currentPage = ref(1);
const lastPage = ref(1);
const isLoadingMore = ref(false);

const formatDate = (dateString) => {
  if (!dateString) return "";
  return new Date(dateString).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
};

const getCardHeight = (index) => {
  const heights = [
    "min-[600px]:min-h-[220px]",
    "min-[600px]:min-h-[350px]",
    "min-[600px]:min-h-[280px]",
    "min-[600px]:min-h-[400px]",
  ];
  return heights[index % heights.length];
};

// --- PERBAIKAN DI SINI: Tambahkan parameter reset = true ---
async function fetchNoteList(reset = true) {
  if (reset) {
    currentPage.value = 1;
    // Jangan kosongkan notes dulu biar ga kedip kalo cuma refresh
    // notes.value = [];
  }

  try {
    const response = await myNoteList(token.value, currentPage.value);
    const responseBody = await response.json();
    console.log("API Response:", responseBody); // Debugging

    if (response.ok) {
      // Update Info Pagination (PENTING!)
      if (responseBody.meta) {
        lastPage.value = responseBody.meta.last_page;
        currentPage.value = responseBody.meta.current_page;
      }

      if (reset) {
        notes.value = responseBody.data;
      } else {
        // Append data baru ke array lama
        notes.value.push(...responseBody.data);
      }
    } else {
      // Handle error jika token expired dll
      console.error("Gagal load data:", responseBody.message);
    }
  } catch (error) {
    console.error("Gagal fetch:", error);
  }
}

async function handleDelete(id) {
  // 1. Konfirmasi Hapus
  if (!(await alertConfirm("Are you sure you want to delete this note?"))) {
    return;
  }

  try {
    // 2. Panggil API Hapus
    const response = await noteDelete(token.value, id);
    const responseBody = await response.json();

    if (response.ok) {
      alertSuccess("Note deleted successfully");

      // 3. Hapus item dari Array Lokal (Tanpa fetch ulang biar cepat)
      // Cari index note yang dihapus
      const index = notes.value.findIndex((n) => n.id === id);
      if (index !== -1) {
        notes.value.splice(index, 1);
      }

      // 4. Cek apakah Array jadi kosong?
      if (notes.value.length === 0) {
        // Emit ke parent supaya ganti ke EmptyContent
        emit("is-empty");
      } else {
        // Opsional: Jika masih ada data tapi kurang dari 15,
        // kita bisa fetch ulang untuk memastikan pagination rapi,
        // tapi untuk sekarang biarkan saja agar performa cepat.
      }
    } else {
      // Handle Error dari Backend
      const pesanError = responseBody.errors ? Object.values(responseBody.errors)[0][0] : responseBody.message;
      await alertError(pesanError);
    }
  } catch (error) {
    console.error("Gagal delete:", error);
    await alertError("Terjadi kesalahan saat menghapus note.");
  }
}

// --- FUNGSI TOMBOL LOAD MORE ---
const loadMore = async () => {
  // Cek apakah masih ada halaman selanjutnya
  if (currentPage.value < lastPage.value) {
    isLoadingMore.value = true;
    currentPage.value++; // Naikkan counter halaman
    await fetchNoteList(false); // false = append mode
    isLoadingMore.value = false;
  }
};

onBeforeMount(async () => {
  await fetchNoteList(true); // Load awal (reset)
});
</script>

<template>
  <div class="p-[2em] relative min-h-screen">
    <div v-if="notes.length === 0" class="w-full text-center text-[#8c8a8a] py-10">Belum ada pesan yang dibuat.</div>

    <div v-else class="columns-1 min-[600px]:columns-3 gap-[2em] space-y-[2em] mb-10">
      <div
        v-for="(note, index) in notes"
        :key="note.id || index"
        :class="[
          'cursor-pointer break-inside-avoid relative flex flex-col justify-between rounded-[10px] bg-[#1c1516] p-[10px] transition-transform duration-200 hover:scale-[1.02]',
          getCardHeight(index),
        ]">
        <div>
          <div class="flex items-center justify-between rounded-[10px] bg-[#100c0d] p-[10px] mb-4">
            <div class="flex items-center overflow-hidden">
              <img
                :src="note.spotify_album_image"
                class="h-[50px] w-[50px] sm:h-[60px] sm:w-[60px] object-cover block rounded-[4px] shrink-0" />
              <div class="ml-[0.8em] flex flex-col min-w-0">
                <p class="text-[#e5e5e5] font-extrabold truncate">{{ note.spotify_track_name }}</p>
                <p class="text-[#8c8a8a] font-semibold truncate">{{ note.spotify_artist }}</p>
              </div>
            </div>
            <img src="../../../assets/img/play.svg" class="h-[40px] w-[40px] sm:h-[55px] sm:w-[55px] shrink-0" />
          </div>
          <p class="text-[#8c8a8a] mt-2">
            kepada:
            <span class="text-[#e5e5e5] font-medium">{{ note.recipient }}</span>
          </p>
          <div class="text-[#8c8a8a] mt-2">
            pesan:
            <span class="text-[#e5e5e5] block mt-1 break-words">{{ note.content }}</span>
          </div>
        </div>

        <div class="mt-4">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-[10px]">
              <img
                :src="note.author_avatar"
                class="h-[35px] w-[35px] rounded-full object-cover border border-[#2c0f0f]" />
              <p class="text-[#9a203e] font-semibold text-[14px]">{{ note.author }}</p>
            </div>
            <p class="text-[#8c8a8a] text-xs">{{ formatDate(note.created_at) }}</p>
          </div>
          <div class="flex justify-end gap-[8px]">
            <button
              class="cursor-pointer hover:bg-[#130f0f] border border-[#8c8a8a] text-[#8c8a8a] rounded-[8px] px-[12px] py-[8px]">
              Edit
            </button>
            <button
              v-on:click="() => handleDelete(note.id)"
              class="cursor-pointer hover:bg-[#821c35] bg-[#9a203e] text-[#e5e5e5] rounded-[8px] px-[12px] py-[8px]">
              Hapus
            </button>
          </div>
        </div>
      </div>
    </div>

    <div
      v-if="currentPage < lastPage"
      class="mb-[5em] flex justify-end pr-[2em] text-[#e5e5e5] gap-1.5 cursor-pointer hover:opacity-80">
      <button
        @click="loadMore"
        :disabled="isLoadingMore"
        class="bg-transparent font-semibold uppercase hover:underline cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
        {{ isLoadingMore ? "Memuat..." : "Lihat lebih banyak cerita" }}
      </button>
      <img v-if="!isLoadingMore" src="../../../assets/img/arrow-down.svg" class="w-[14px]" />
    </div>
  </div>

  <button
    @click="$emit('open-modal')"
    class="cursor-pointer fixed bottom-8 right-8 z-40 flex h-14 w-14 items-center justify-center rounded-full bg-[#9a203e] text-white shadow-2xl transition-all duration-300 hover:scale-110 hover:bg-[#821c35] active:scale-95 focus:outline-none sm:bottom-12 sm:right-12 sm:h-16 sm:w-16"
    title="Buat Cerita Baru">
    <svg
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
      stroke-width="2.5"
      stroke="currentColor"
      class="h-8 w-8 sm:h-9 sm:w-9">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
    </svg>
  </button>
</template>
