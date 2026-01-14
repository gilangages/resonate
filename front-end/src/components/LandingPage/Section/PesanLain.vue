<script setup>
import { onMounted, ref } from "vue";
import { noteListGlobal } from "../../../lib/api/NoteApi";
import { alertError } from "../../../lib/alert";

const notes = ref([]);
const scrollContainer = ref(null);

const formatDate = (dateString) => {
  if (!dateString) return "";
  return new Date(dateString).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
};

async function fetchNoteGlobal() {
  try {
    const response = await noteListGlobal();
    const responseBody = await response.json();

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

const scroll = (direction) => {
  if (scrollContainer.value) {
    const scrollAmount = 320;
    if (direction === "left") {
      scrollContainer.value.scrollBy({ left: -scrollAmount, behavior: "smooth" });
    } else {
      scrollContainer.value.scrollBy({ left: scrollAmount, behavior: "smooth" });
    }
  }
};

onMounted(async () => {
  await fetchNoteGlobal();
});
</script>

<template>
  <div class="mt-[4em] relative">
    <div class="flex justify-between items-center text-[#e5e5e5] mx-[2em] mb-[1em]">
      <h2 class="text-[18px] sm:text-[20px] font-semibold">Dengarkan Pesan Pengguna Lain</h2>

      <RouterLink
        to="/login"
        class="hidden sm:flex gap-1 items-center cursor-pointer hover:opacity-80 decoration-0 no-underline">
        <span class="uppercase text-[12px] font-semibold text-[#e5e5e5] hover:underline">Lihat semua pesan</span>
        <img src="../../../assets/img/arrow-right.svg" class="w-[14px]" />
      </RouterLink>
    </div>

    <div class="relative group px-[1em] sm:px-[2em]">
      <button
        @click="scroll('left')"
        class="hidden sm:flex absolute -left-2 top-1/2 -translate-y-1/2 z-10 bg-[#1c1516] p-2 rounded-full cursor-pointer hover:bg-[#9a203e] border border-[#3f3233] shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300">
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
        class="flex gap-[1em] sm:gap-[2em] overflow-x-auto pb-6 snap-x snap-mandatory scrollbar-hide px-2">
        <div
          v-for="(note, index) in notes"
          :key="note.id || index"
          class="min-w-[85vw] sm:min-w-[350px] snap-center bg-[#1c1516] p-[10px] rounded-[10px] cursor-pointer transition-transform duration-200 hover:scale-[1.01] border border-transparent hover:border-[#3f3233]">
          <div class="flex items-center justify-between bg-[#100c0d] p-[10px] rounded-[10px]">
            <div class="flex items-center overflow-hidden">
              <img
                :src="note.spotify_album_image"
                class="w-[50px] h-[50px] sm:w-[60px] sm:h-[60px] object-cover rounded-[4px] shrink-0" />
              <div class="flex flex-col ml-[1em] min-w-0">
                <p class="text-[16px] sm:text-[18px] font-extrabold text-[#e5e5e5] m-0 -mb-[4px] truncate">
                  {{ note.spotify_track_name }}
                </p>
                <p class="text-[14px] sm:text-[16px] font-semibold text-[#8c8a8a] m-0 -mt-[4px] truncate">
                  {{ note.spotify_artist }}
                </p>
              </div>
            </div>
            <img src="../../../assets/img/play.svg" class="w-[40px] h-[40px] sm:w-[50px] sm:h-[50px] shrink-0" />
          </div>

          <p class="text-[16px] sm:text-[18px] text-[#8c8a8a] my-4">
            kepada:
            <span class="text-[#e5e5e5] font-medium">{{ note.recipient }}</span>
          </p>

          <p class="text-[16px] sm:text-[18px] text-[#8c8a8a] my-4 min-h-[4.5em] line-clamp-3 leading-relaxed">
            pesan:
            <br />
            <span class="text-[#e5e5e5] break-words">{{ note.content }}</span>
          </p>

          <div class="flex justify-between items-center mt-6 mb-2">
            <div class="flex items-center gap-[10px]">
              <img
                :src="note.author_avatar"
                class="w-[35px] h-[35px] sm:w-[40px] sm:h-[40px] rounded-full object-cover border border-[#2c0f0f]" />
              <p class="text-[#9a203e] font-bold text-sm">{{ note.author }}</p>
            </div>
            <p class="text-[#8c8a8a] text-xs">{{ formatDate(note.created_at) }}</p>
          </div>
        </div>
      </div>

      <button
        @click="scroll('right')"
        class="hidden sm:flex absolute -right-2 top-1/2 -translate-y-1/2 z-10 bg-[#1c1516] p-2 rounded-full cursor-pointer hover:bg-[#9a203e] border border-[#3f3233] shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300">
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
  </div>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
