<script setup>
import { useLocalStorage } from "@vueuse/core";
import { noteList } from "../../../lib/api/NoteApi"; // Ensure path is correct
import { onMounted, ref } from "vue";

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

async function fetchNoteList(reset = true) {
  if (reset) {
    currentPage.value = 1;
    // Optional: notes.value = []; // Don't clear to avoid flashing
  }

  try {
    const response = await noteList(token.value, currentPage.value);
    const responseBody = await response.json();

    // Debugging
    console.log("Global Notes Response:", responseBody);

    if (response.ok) {
      // 1. Update Pagination Meta
      if (responseBody.meta) {
        lastPage.value = responseBody.meta.last_page;
        currentPage.value = responseBody.meta.current_page;
      }

      // 2. Update Data
      if (reset) {
        notes.value = responseBody.data;
      } else {
        // Append for 'Load More'
        notes.value.push(...responseBody.data);
      }
    } else {
      console.error("Failed to load notes:", responseBody.message);
    }
  } catch (error) {
    console.error("Fetch error:", error);
  }
}

// --- LOAD MORE FUNCTION ---
const loadMore = async () => {
  if (currentPage.value < lastPage.value) {
    isLoadingMore.value = true;
    currentPage.value++; // Increment page
    await fetchNoteList(false); // false = append mode
    isLoadingMore.value = false;
  }
};

onMounted(async () => {
  await fetchNoteList(true);
});
</script>

<template>
  <div class="p-[2em] relative min-h-screen">
    <div v-if="notes.length === 0" class="w-full text-center text-[#8c8a8a] py-10">Belum ada pesan yang dibuat.</div>

    <div v-else class="columns-1 min-[600px]:columns-3 gap-[2em] space-y-[2em] mb-10">
      <div
        v-for="(note, index) in notes"
        :key="note.id"
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
                <p class="m-0 -mb-[2px] text-[16px] sm:text-[20px] font-extrabold text-[#e5e5e5] truncate">
                  {{ note.spotify_track_name }}
                </p>
                <p class="m-0 text-[14px] sm:text-[18px] font-semibold text-[#8c8a8a] truncate">
                  {{ note.spotify_artist }}
                </p>
              </div>
            </div>
            <img src="../../../assets/img/play.svg" class="h-[40px] w-[40px] sm:h-[55px] sm:w-[55px] shrink-0" />
          </div>

          <p class="text-[16px] sm:text-[20px] text-[#8c8a8a] mt-2">
            kepada:
            <span class="text-[#e5e5e5] font-medium">{{ note.recipient }}</span>
          </p>

          <div class="text-[16px] sm:text-[20px] text-[#8c8a8a] mt-2 leading-relaxed">
            pesan:
            <span class="text-[#e5e5e5] block mt-1 break-words">{{ note.content }}</span>
          </div>
        </div>

        <div class="mt-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-[10px]">
              <img
                :src="note.author_avatar"
                class="h-[35px] w-[35px] sm:h-[45px] sm:w-[45px] rounded-full object-cover border border-[#2c0f0f]" />
              <p class="font-semibold text-[#9a203e] text-[14px] sm:text-[16px]">{{ note.author }}</p>
            </div>
            <p class="text-[#8c8a8a] text-xs sm:text-sm">{{ formatDate(note.created_at) }}</p>
          </div>
        </div>
      </div>
    </div>

    <div
      v-if="currentPage < lastPage"
      class="mb-[5em] flex justify-end text-[#e5e5e5] gap-1.5 cursor-pointer hover:opacity-80">
      <button
        @click="loadMore"
        :disabled="isLoadingMore"
        class="bg-transparent font-semibold uppercase hover:underline cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
        {{ isLoadingMore ? "Memuat..." : "Lihat lebih banyak cerita" }}
      </button>
      <img v-if="!isLoadingMore" src="../../../assets/img/arrow-down.svg" class="w-[14px]" />
    </div>
  </div>
</template>
