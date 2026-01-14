<script setup>
import { ref, onMounted, reactive } from "vue";
import { noteUpdate, searchMusic } from "../../../lib/api/NoteApi";
import { useLocalStorage } from "@vueuse/core";
import { alertSuccess, alertError } from "../../../lib/alert";
import { userDetail } from "../../../lib/api/UserApi";

const props = defineProps({
  noteData: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(["update-success", "go-back"]);

const token = useLocalStorage("token", "");
const kirimSebagai = ref("samaran");
const namaSamaran = ref("");
const name = ref("User");

// INI KUNCINYA: Semua data update akan masuk ke sini
const note = reactive({
  content: "",
  recipient: "",
  initial_name: null,
  spotify_track_id: "",
  spotify_track_name: "",
  spotify_artist: "",
  spotify_album_image: "",
  spotify_preview_url: null,
});

const queryLagu = ref("");
const searchResults = ref([]);
const selectedSong = ref(null);
const isSearching = ref(false);
let debounceTimer = null;

// --- POPULATE FORM ---
const populateForm = () => {
  const d = props.noteData;
  if (!d) return;

  // 1. Isi object 'note' langsung
  Object.assign(note, {
    content: d.content,
    recipient: d.recipient,
    initial_name: d.initial_name,
    spotify_track_id: d.spotify_track_id,
    spotify_track_name: d.spotify_track_name,
    spotify_artist: d.spotify_artist,
    spotify_album_image: d.spotify_album_image,
    spotify_preview_url: d.spotify_preview_url,
  });

  // 2. Isi UI Search
  selectedSong.value = {
    id: d.spotify_track_id,
    name: d.spotify_track_name,
    artists: [{ name: d.spotify_artist }],
    album: { images: [{ url: d.spotify_album_image }] },
  };
  queryLagu.value = `${d.spotify_track_name} - ${d.spotify_artist}`;

  if (d.initial_name) {
    kirimSebagai.value = "samaran";
    namaSamaran.value = d.initial_name;
  } else {
    kirimSebagai.value = "asli";
  }
};

async function fetchUser() {
  const response = await userDetail(token.value);
  const responseBody = await response.json();
  console.log(responseBody);

  if (response.ok) {
    name.value = responseBody.data.name;
  } else {
    const pesanError = responseBody.errors ? Object.values(responseBody.errors)[0][0] : responseBody.message;
    await alertError(pesanError);
  }
}

const handleSearchInput = () => {
  if (debounceTimer) clearTimeout(debounceTimer);
  selectedSong.value = null;
  if (queryLagu.value.length < 2) {
    searchResults.value = [];
    return;
  }
  isSearching.value = true;
  debounceTimer = setTimeout(async () => {
    try {
      const response = await searchMusic(token.value, queryLagu.value);
      const data = await response.json();
      if (data && data.tracks && data.tracks.items) {
        searchResults.value = data.tracks.items;
      } else {
        searchResults.value = [];
      }
    } catch (error) {
      console.error("Error search:", error);
    } finally {
      isSearching.value = false;
    }
  }, 500);
};

const selectSong = (song) => {
  selectedSong.value = song;
  queryLagu.value = `${song.name} - ${song.artists[0].name}`;
  searchResults.value = [];

  // PENTING: Update object 'note' saat lagu diganti
  note.spotify_track_id = song.id;
  note.spotify_track_name = song.name;
  note.spotify_artist = song.artists[0].name;
  note.spotify_album_image = song.album.images[0]?.url || "";
  note.spotify_preview_url = song.preview_url;
};

// --- BAGIAN YANG DIPERBAIKI ---
async function handleUpdate() {
  // Validasi: Pastikan ada lagu yang terpilih
  // (Entah dari populateForm atau selectSong, selectedSong.value harus ada)
  if (!selectedSong.value) {
    await alertError("Kamu belum memilih lagu!");
    return;
  }

  // Update logic nama samaran ke dalam 'note'
  if (kirimSebagai.value === "samaran") {
    if (!namaSamaran.value) {
      await alertError("Nama samaran wajib diisi!");
      return;
    }
    note.initial_name = namaSamaran.value;
  } else {
    note.initial_name = null; // Reset
  }

  // HAPUS kode "const payload = {...}" yang lama!
  // GANTIKAN dengan mengirim object 'note' langsung.
  // Karena 'note' sudah kita update di 'selectSong' & 'populateForm'.

  const response = await noteUpdate(token.value, props.noteData.id, note);
  const responseBody = await response.json();

  if (response.ok) {
    alertSuccess("Note updated successfully!");
    emit("update-success");
  } else {
    const pesanError = responseBody.errors ? Object.values(responseBody.errors)[0][0] : responseBody.message;
    await alertError(pesanError);
  }
}

const handleKembali = () => {
  console.log("Tombol Kembali Diklik");
  emit("go-back");
};

onMounted(async () => {
  await fetchUser();
  populateForm();
});
</script>

<template>
  <div
    class="custom-scrollbar w-full max-w-[420px] rounded-[20px] bg-[#1c1516] p-4 sm:max-w-[560px] max-h-[90vh] overflow-y-auto"
    @click.stop>
    <form @submit.prevent="handleUpdate" class="flex flex-col text-[#e5e5e5] font-poppins relative">
      <h1 class="text-center text-[#9a203e] text-3xl font-bold m-0">Edit Pesan</h1>
      <p class="mt-0 mb-[3em] text-center text-[12px] text-[#8c8a8a]">Edit lagu dan pesanmu.</p>

      <div class="text-[14px] relative">
        <label>Pilih Lagu (Cari)</label>
        <input
          type="text"
          v-model="queryLagu"
          @input="handleSearchInput"
          required
          placeholder="Ketik judul lagu..."
          class="mt-[6px] mb-[20px] w-full rounded-[10px] bg-[#2b2122] p-4 text-[#e5e5e5] caret-[#e5e5e5] focus:outline focus:outline-2 focus:outline-[#9a203e]" />

        <div
          v-if="searchResults.length > 0"
          class="custom-scrollbar absolute z-20 top-[80px] left-0 w-full bg-[#2b2122] border border-[#9a203e] rounded-[10px] max-h-[200px] overflow-y-auto shadow-lg">
          <div
            v-for="song in searchResults"
            :key="song.id"
            @click="selectSong(song)"
            class="p-3 hover:bg-[#9a203e] cursor-pointer flex items-center gap-3 border-b border-[#1c1516] last:border-0">
            <img :src="song.album.images[2]?.url" class="w-10 h-10 rounded" alt="art" />
            <div>
              <p class="font-bold text-sm">{{ song.name }}</p>
              <p class="text-xs text-gray-400">{{ song.artists[0].name }}</p>
            </div>
          </div>
        </div>
        <div v-if="isSearching" class="absolute top-[45px] right-4 text-xs text-gray-400">Searching...</div>
      </div>

      <div class="text-[14px]">
        <label>Kepada</label>
        <input
          type="text"
          required
          v-model="note.recipient"
          placeholder="Kepada siapa pesanmu"
          class="mt-[6px] mb-[20px] w-full rounded-[10px] bg-[#2b2122] p-4 text-[#e5e5e5] caret-[#e5e5e5] focus:outline focus:outline-2 focus:outline-[#9a203e]" />
      </div>

      <div class="text-[14px]">
        <label>Pesan</label>
        <textarea
          required
          v-model="note.content"
          placeholder="Tulis pesanmu"
          class="mt-[6px] mb-[20px] h-[120px] w-full resize-y rounded-[10px] bg-[#2b2122] p-4 text-[#e5e5e5] focus:outline focus:outline-2 focus:outline-[#9a203e]"></textarea>
      </div>

      <div class="text-[14px]">
        <label>Kirim Sebagai</label>

        <div class="mt-[6px] mb-[20px] flex items-center gap-2">
          <input type="radio" value="asli" v-model="kirimSebagai" class="cursor-pointer" />
          <span class="mr-[2em]">{{ name }} (Asli)</span>

          <input type="radio" value="samaran" v-model="kirimSebagai" class="cursor-pointer" />
          <span>Nama Samaran</span>
        </div>

        <div v-if="kirimSebagai === 'samaran'">
          <label class="text-[#9a203e]">Nama Samaran</label>
          <input
            type="text"
            v-model="namaSamaran"
            required
            placeholder="Contoh: Secret Admirer..."
            class="mt-[6px] mb-[20px] w-full rounded-[10px] bg-[#2b2122] p-4 text-[#e5e5e5] caret-[#e5e5e5] focus:outline focus:outline-2 focus:outline-[#9a203e]" />
        </div>
      </div>

      <div class="mt-[26px] flex gap-[10px]">
        <button
          type="button"
          @click="handleKembali"
          class="w-full rounded-[10px] border border-[#8c8a8a] bg-[#1c1516] px-3 py-3 text-xs font-medium text-[#8c8a8a] hover:border-[#666565] hover:bg-[#120e0e] cursor-pointer">
          Kembali
        </button>

        <button
          type="submit"
          class="w-full rounded-[10px] bg-[#9a203e] px-3 py-3 text-xs font-medium text-[#e5e5e5] hover:bg-[#7d1a33] cursor-pointer">
          Simpan Perubahan
        </button>
      </div>
    </form>
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #3f3233;
  border-radius: 20px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: #9a203e;
}
</style>
