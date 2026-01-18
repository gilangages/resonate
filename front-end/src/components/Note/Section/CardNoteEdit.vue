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
const name = ref("");

const note = reactive({
  content: "",
  recipient: "",
  initial_name: null,
  music_track_id: "",
  music_track_name: "",
  music_artist_name: "",
  music_album_image: "",
  music_preview_url: null,
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

  Object.assign(note, {
    content: d.content,
    recipient: d.recipient,
    initial_name: d.initial_name,
    music_track_id: d.music_track_id,
    music_track_name: d.music_track_name,
    music_artist_name: d.music_artist_name,
    music_album_image: d.music_album_image,
    music_preview_url: d.music_preview_url,
  });

  // Membentuk object selectedSong agar UI Card langsung muncul saat Edit dibuka
  selectedSong.value = {
    id: d.music_track_id,
    name: d.music_track_name,
    artists: [{ name: d.music_artist_name }],
    album: { images: [{ url: d.music_album_image }] },
    preview_url: d.music_preview_url,
  };

  // queryLagu tidak perlu diisi karena inputnya akan disembunyikan oleh selectedSong
  queryLagu.value = "";

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

  if (response.ok) {
    name.value = responseBody.data.name;
  } else {
    const pesanError = responseBody.errors ? Object.values(responseBody.errors)[0][0] : responseBody.message;
    await alertError(pesanError);
  }
}

const handleSearchInput = () => {
  if (debounceTimer) clearTimeout(debounceTimer);
  selectedSong.value = null; // Reset
  if (queryLagu.value.length < 2) {
    searchResults.value = [];
    return;
  }
  isSearching.value = true;
  debounceTimer = setTimeout(async () => {
    try {
      const response = await searchMusic(token.value, { query: queryLagu.value });
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
  // Bersihkan search
  queryLagu.value = "";
  searchResults.value = [];

  note.music_track_id = song.id;
  note.music_track_name = song.name;
  note.music_artist_name = song.artists[0].name;
  note.music_album_image = song.album.images[0]?.url || "";
  note.music_preview_url = song.preview_url;
};

// Fungsi Remove Lagu untuk Edit Mode
const removeSelectedSong = () => {
  selectedSong.value = null;
  // Reset field di object note juga jika perlu, tapi tidak wajib sampai user save
  queryLagu.value = "";
  searchResults.value = [];
};

// --- LOGIC UPDATE ---
async function handleUpdate() {
  if (!selectedSong.value) {
    await alertError("Kamu belum memilih lagu!");
    return;
  }

  if (kirimSebagai.value === "samaran") {
    if (!namaSamaran.value) {
      await alertError("Nama samaran wajib diisi!");
      return;
    }
    note.initial_name = namaSamaran.value;
  } else {
    note.initial_name = null;
  }

  // Pastikan data note terupdate dengan selectedSong terbaru sebelum kirim
  note.music_track_id = selectedSong.value.id;
  note.music_track_name = selectedSong.value.name;
  note.music_artist_name = selectedSong.value.artists[0].name;
  note.music_album_image = selectedSong.value.album.images[0]?.url || "";
  note.music_preview_url = selectedSong.value.preview_url || null;

  const response = await noteUpdate(token.value, props.noteData.id, note);
  const responseBody = await response.json();

  if (response.ok) {
    alertSuccess("Pesan berhasil diperbarui.");
    emit("update-success");
  } else {
    const pesanError = responseBody.errors ? Object.values(responseBody.errors)[0][0] : responseBody.message;
    await alertError(pesanError);
  }
}

const handleKembali = () => {
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

      <div class="text-[14px] relative mb-[20px]">
        <label class="block mb-[6px]">Pilih Lagu</label>

        <div
          v-if="selectedSong"
          class="flex items-center gap-3 bg-[#2b2122] p-3 rounded-[10px] border border-[#9a203e] shadow-md animate-fade-in">
          <img :src="selectedSong.album.images[0]?.url" class="w-12 h-12 rounded shadow-sm" alt="album art" />
          <div class="flex-1 min-w-0">
            <p class="font-bold text-sm text-[#e5e5e5] truncate">{{ selectedSong.name }}</p>
            <p class="text-xs text-[#8c8a8a] truncate">{{ selectedSong.artists[0].name }}</p>
          </div>
          <button
            type="button"
            @click="removeSelectedSong"
            class="p-2 text-[#8c8a8a] hover:text-[#e5e5e5] hover:bg-[#9a203e] rounded-full transition-colors cursor-pointer"
            title="Ganti Lagu">
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
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>

        <div v-else class="relative">
          <input
            type="text"
            v-model="queryLagu"
            @input="handleSearchInput"
            placeholder="Ketik judul lagu..."
            class="w-full rounded-[10px] bg-[#2b2122] p-4 text-[#e5e5e5] caret-[#e5e5e5] focus:outline focus:outline-2 focus:outline-[#9a203e]" />

          <div v-if="isSearching" class="absolute top-[14px] right-4 text-xs text-gray-400">Searching...</div>

          <div
            v-if="searchResults.length > 0"
            class="custom-scrollbar absolute z-20 top-[60px] left-0 w-full bg-[#2b2122] border border-[#9a203e] rounded-[10px] max-h-[240px] overflow-y-auto shadow-lg flex flex-col">
            <div
              v-for="song in searchResults"
              :key="song.id"
              @click="selectSong(song)"
              class="p-3 hover:bg-[#9a203e] cursor-pointer flex items-center gap-3 border-b border-[#1c1516] last:border-0 shrink-0">
              <img :src="song.album.images[0]?.url" class="w-10 h-10 rounded" alt="art" />
              <div>
                <p class="font-bold text-sm">{{ song.name }}</p>
                <p class="text-xs text-gray-400">{{ song.artists[0].name }}</p>
              </div>
            </div>

            <div class="sticky bottom-0 bg-[#2b2122] border-t border-[#9a203e]/30 p-2 flex justify-end">
              <a
                href="https://www.deezer.com"
                target="_blank"
                rel="noopener noreferrer"
                class="flex items-center gap-1.5 opacity-60 hover:opacity-100 transition-opacity cursor-pointer group"
                title="Search powered by Deezer">
                <span class="text-[9px] text-gray-400 group-hover:text-gray-200">Search results by</span>
                <img
                  src="https://cdn.brandfetch.io/idEUKgCNtu/theme/light/logo.svg?c=1dxbfHSJFAPEGdCLU4o5B"
                  class="h-3 w-auto grayscale group-hover:grayscale-0 transition-all"
                  alt="Deezer" />
              </a>
            </div>
          </div>
        </div>
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
          class="custom-scrollbar mt-[6px] mb-[20px] h-[120px] w-full resize-y rounded-[10px] bg-[#2b2122] p-4 text-[#e5e5e5] focus:outline focus:outline-2 focus:outline-[#9a203e]"></textarea>
      </div>

      <div class="text-[14px]">
        <label>Kirim Sebagai</label>

        <div class="mt-[6px] mb-[10px] flex items-center gap-2">
          <input type="radio" value="asli" v-model="kirimSebagai" class="cursor-pointer w-5 h-5 accent-[#9a203e]" />
          <span class="mr-[2em] cursor-pointer" @click="kirimSebagai = 'asli'">{{ name }} (Asli)</span>

          <input type="radio" value="samaran" v-model="kirimSebagai" class="cursor-pointer w-5 h-5 accent-[#9a203e]" />
          <span class="cursor-pointer" @click="kirimSebagai = 'samaran'">Nama Samaran</span>
        </div>

        <Transition name="expand">
          <div v-if="kirimSebagai === 'samaran'" class="overflow-hidden -mx-1 px-1">
            <div class="mt-2">
              <label class="text-[#9a203e] text-xs font-bold uppercase tracking-wider mb-1 block">Nama Samaran</label>
              <input
                type="text"
                v-model="namaSamaran"
                required
                placeholder="Contoh: Secret Admirer..."
                class="mb-[20px] w-full rounded-[10px] bg-[#2b2122] p-4 text-[#e5e5e5] caret-[#e5e5e5] focus:outline focus:outline-2 focus:outline-[#9a203e]" />
            </div>
          </div>
        </Transition>
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
.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: #3f3233 transparent;
}

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

/* --- ANIMASI EXPAND --- */
.expand-enter-active,
.expand-leave-active {
  transition: all 0.3s ease-in-out;
  max-height: 100px;
  opacity: 1;
}

.expand-enter-from,
.expand-leave-to {
  max-height: 0;
  opacity: 0;
  margin-top: 0;
  margin-bottom: 0;
  padding-top: 0;
  padding-bottom: 0;
}

.animate-fade-in {
  animation: fadeIn 0.3s ease-out;
}
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-5px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
