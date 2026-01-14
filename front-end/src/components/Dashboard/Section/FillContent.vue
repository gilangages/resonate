<script setup>
import { useLocalStorage } from "@vueuse/core";
import { myNoteList } from "../../../lib/api/NoteApi";
import { onBeforeMount, ref } from "vue";

const token = useLocalStorage("token", "");
const notes = ref([]);

// --- 1. FORMAT TANGGAL ---
const formatDate = (dateString) => {
  if (!dateString) return "";
  return new Date(dateString).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
};

// --- 2. LOGIC TINGGI RANDOM (BEST PRACTICE) ---
// Kita tambahkan prefix 'min-[600px]:'
// Artinya: Tinggi random ini CUMA jalan di layar >= 600px (3 kolom).
// Di layar < 600px (HP 1 kolom), tingginya otomatis ikut konten (rapi).
const getCardHeight = (index) => {
  const heights = [
    "min-[600px]:min-h-[220px]", // Pendek
    "min-[600px]:min-h-[350px]", // Panjang
    "min-[600px]:min-h-[280px]", // Sedang
    "min-[600px]:min-h-[400px]", // Sangat Panjang
  ];
  return heights[index % heights.length];
};

async function fetchNoteList() {
  try {
    const response = await myNoteList(token.value);
    const responseBody = await response.json();
    console.log("Response API:", responseBody);

    if (response.ok) {
      notes.value = responseBody.data;
    } else {
      const pesanError = responseBody.errors ? Object.values(responseBody.errors)[0][0] : responseBody.message;
      alert(pesanError);
    }
  } catch (error) {
    console.error("Gagal fetch:", error);
  }
}

onBeforeMount(async () => {
  await fetchNoteList();
});
</script>

<template>
  <div class="p-[2em]">
    <div v-if="notes.length === 0" class="w-full text-center text-[#8c8a8a] py-10">Belum ada cerita yang dibuat.</div>

    <div v-else class="columns-1 min-[600px]:columns-3 gap-[2em] space-y-[2em]">
      <div
        v-for="note in notes"
        :key="note.id || index"
        :class="[
          'break-inside-avoid relative flex flex-col justify-between rounded-[10px] bg-[#1c1516] p-[10px] transition-transform duration-200 hover:scale-[1.02]',
          getCardHeight(index),
        ]">
        <div>
          <div class="flex items-center justify-between rounded-[10px] bg-[#100c0d] p-[10px] mb-4">
            <div class="flex items-center overflow-hidden">
              <img
                :src="note.spotify_album_image"
                class="h-[50px] w-[50px] sm:h-[60px] sm:w-[60px] object-cover block rounded-[4px] shrink-0" />
              <div class="ml-[0.8em] flex flex-col min-w-0">
                <p class="m-0 -mb-[2px] text-[16px] sm:text-[20px] font-extrabold text-[#e5e5e5] truncate">
                  {{ note.spotify_track_name }}
                </p>
                <p class="m-0 text-[14px] sm:text-[18px] font-semibold text-[#8c8a8a] truncate">
                  {{ note.spotify_artist }}
                </p>
              </div>
            </div>
            <img
              src="../../../assets/img/play.svg"
              alt="play"
              class="h-[40px] w-[40px] sm:h-[55px] sm:w-[55px] shrink-0" />
          </div>

          <p class="text-[16px] sm:text-[20px] text-[#8c8a8a] mt-2">
            kepada:
            <span class="text-[#e5e5e5] font-medium">{{ note.recipient }}</span>
          </p>

          <div class="text-[16px] sm:text-[20px] text-[#8c8a8a] mt-2 leading-relaxed">
            pesan:
            <span class="text-[#e5e5e5] block mt-1 break-words">
              {{ note.content }}
            </span>
          </div>
        </div>

        <div class="mt-4">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-[10px]">
              <img
                :src="note.author_avatar"
                class="h-[35px] w-[35px] sm:h-[45px] sm:w-[45px] rounded-full object-cover block border border-[#2c0f0f]" />
              <p class="font-semibold text-[#9a203e] text-[14px] sm:text-[16px]">
                {{ note.author }}
              </p>
            </div>
            <p class="text-[#8c8a8a] text-xs sm:text-sm">
              {{ formatDate(note.created_at) }}
            </p>
          </div>

          <div class="flex justify-end gap-[8px] sm:gap-[10px]">
            <button
              class="cursor-pointer rounded-[8px] border border-[#8c8a8a] bg-[#1c1516] px-[12px] py-[8px] text-[14px] sm:text-[16px] font-semibold text-[#8c8a8a] hover:bg-[#130f0f] transition-colors">
              Edit
            </button>
            <button
              class="cursor-pointer rounded-[8px] bg-[#9a203e] px-[12px] py-[8px] text-[14px] sm:text-[16px] font-semibold text-[#e5e5e5] hover:bg-[#821c35] transition-colors">
              Hapus
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="mb-[5em] flex justify-end pr-[2em] text-[#e5e5e5] gap-1.5 cursor-pointer hover:opacity-80">
    <button class="bg-transparent font-semibold uppercase hover:underline cursor-pointer">
      Lihat lebih banyak cerita
    </button>
    <img src="../../../assets/img/arrow-down.svg" alt="arrow-down" class="w-[14px]" />
  </div>
</template>
