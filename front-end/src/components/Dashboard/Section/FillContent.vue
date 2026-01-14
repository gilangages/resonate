<script setup>
import { useLocalStorage } from "@vueuse/core";
import { noteList } from "../../../lib/api/NoteApi";
import { onBeforeMount, ref } from "vue"; // Gunakan ref untuk array

const token = useLocalStorage("token", "");
// Ubah dari reactive object tunggal menjadi ref array kosong
const notes = ref([]);

const formatDate = (dateString) => {
  if (!dateString) return "";
  const date = new Date(dateString);
  return date.toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
};

async function fetchNoteList() {
  try {
    const response = await noteList(token.value);
    const responseBody = await response.json();
    console.log("Response API:", responseBody); // Debugging

    if (response.ok) {
      // Asumsi: responseBody.data adalah Array of Objects
      notes.value = responseBody.data;
    } else {
      const pesanError = responseBody.errors ? Object.values(responseBody.errors)[0][0] : responseBody.message;
      alert(pesanError); // Ganti dengan alertError function kamu jika ada
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
  <div class="grid grid-cols-1 gap-[2em] p-[2em] sm:grid-cols-2">
    <div v-if="notes.length === 0" class="col-span-full text-center text-[#8c8a8a]">Belum ada cerita yang dibuat.</div>

    <div
      v-for="(note, index) in notes"
      :key="note.id || index"
      class="cursor-pointer rounded-[10px] bg-[#1c1516] p-[10px] transition-transform duration-200 hover:scale-[1.02]">
      <div class="flex items-center justify-between rounded-[10px] bg-[#100c0d] p-[10px]">
        <div class="flex items-center">
          <img :src="note.spotify_album_image" class="h-[60px] w-[60px] object-cover block rounded-[4px]" />
          <div class="ml-[1em] flex flex-col">
            <p class="m-0 -mb-[4px] text-[20px] font-extrabold text-[#e5e5e5] line-clamp-1">
              {{ note.spotify_track_name }}
            </p>
            <p class="m-0 -mt-[4px] text-[20px] font-semibold text-[#8c8a8a] line-clamp-1">{{ note.spotify_artist }}</p>
          </div>
        </div>

        <img src="../../../assets/img/play.svg" alt="play" class="h-[55px] w-[55px] -mb-[4px]" />
      </div>

      <p class="text-[20px] text-[#8c8a8a] mt-4">
        kepada:
        <span class="text-[#e5e5e5]">{{ note.recipient }}</span>
      </p>

      <p class="text-[20px] text-[#8c8a8a]">
        pesan:
        <br />
        <span class="text-[#e5e5e5] block mt-1">
          {{ note.content }}
        </span>
      </p>

      <div class="flex items-center justify-between mt-4">
        <div class="flex items-center gap-[10px]">
          <img src="../../../assets/img/me.jpg" class="h-[45px] w-[45px] rounded-full object-cover block" />
          <p class="font-semibold text-[#9a203e]">{{ note.author ? note.author : note.initial_name }}</p>
        </div>
        <p class="text-[#8c8a8a] text-sm">{{ formatDate(note.created_at) }}</p>
      </div>

      <div class="mt-[1em] flex justify-end gap-[10px]">
        <button
          class="cursor-pointer rounded-[8px] border border-[#8c8a8a] bg-[#1c1516] px-[16px] py-[10px] text-[16px] font-semibold text-[#8c8a8a] hover:bg-[#130f0f]">
          Edit
        </button>
        <button
          class="cursor-pointer rounded-[8px] bg-[#9a203e] px-[16px] py-[10px] text-[16px] font-semibold text-[#e5e5e5] hover:bg-[#821c35]">
          Hapus
        </button>
      </div>
    </div>
  </div>

  <div class="mb-[5em] flex justify-end pr-[2em] text-[#e5e5e5] gap-1.5 cursor-pointer">
    <button class="bg-[#0f0505] font-semibold uppercase hover:underline cursor-pointer">
      Lihat lebih banyak cerita
    </button>
    <img src="../../../assets/img/arrow-down.svg" alt="arrow-down" class="w-[14px]" />
  </div>
</template>
