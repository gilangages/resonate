<script setup>
import { ref, onBeforeMount, reactive } from "vue";
import { noteCreate, searchMusic } from "../../../lib/api/NoteApi";
import { useLocalStorage } from "@vueuse/core";
import { alertSuccess, alertError } from "../../../lib/alert";
import { userDetail } from "../../../lib/api/UserApi"; // Import userDetail

const emit = defineEmits(["close", "submit"]);

// --- STATE DATA ---
const token = useLocalStorage("token", "");
const kirimSebagai = ref("samaran");
const namaSamaran = ref("");
const name = ref("User"); // Default text kalau loading/gagal

// Objek Note
const note = reactive({
  content: "",
  recipient: "",
});

// --- STATE PENCARIAN LAGU ---
const queryLagu = ref("");
const searchResults = ref([]);
const selectedSong = ref(null);
const isSearching = ref(false);
let debounceTimer = null;

// --- 1. FETCH USER DATA ---
async function fetchUser() {
  try {
    const response = await userDetail(token.value);
    const responseBody = await response.json();

    if (response.ok) {
      // Simpan nama user ke state
      name.value = responseBody.data.name;
    }
  } catch (error) {
    console.error("Gagal ambil data user", error);
  }
}

// --- 2. LOGIC SEARCH LAGU ---
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
  queryLagu.value = song.name + " - " + song.artists[0].name;
  searchResults.value = [];
};

// --- 3. LOGIC SUBMIT (Payload Pindah Kesini!) ---
async function handleSubmit() {
  // Validasi Lagu
  if (!selectedSong.value) {
    await alertError("Kamu belum memilih lagu! Silakan cari dan klik lagunya.");
    return;
  }

  // Tentukan Initial Name
  let finalInitialName = null;
  if (kirimSebagai.value === "samaran") {
    if (!namaSamaran.value) {
      await alertError("Nama samaran wajib diisi!");
      return;
    }
    finalInitialName = namaSamaran.value;
  }

  // --- PERBAIKAN: Payload Dibuat Disini ---
  // Supaya datanya FRESH sesuai apa yang diketik user saat tombol ditekan
  const payload = {
    content: note.content,
    recipient: note.recipient,
    initial_name: finalInitialName,

    spotify_track_id: selectedSong.value.id,
    spotify_track_name: selectedSong.value.name,
    spotify_artist: selectedSong.value.artists[0].name,
    spotify_album_image: selectedSong.value.album.images[0]?.url || "",
    spotify_preview_url: selectedSong.value.preview_url || null,
  };

  // Kirim ke Backend
  const response = await noteCreate(token.value, payload);
  const responseBody = await response.json();

  if (response.ok) {
    alertSuccess("Pesan berhasil dibuat!");
    emit("submit");
    emit("close");
  } else {
    const pesanError = responseBody.errors ? Object.values(responseBody.errors)[0][0] : responseBody.message;
    await alertError(pesanError);
  }
}

const handleClose = () => {
  emit("close");
};

// --- LIFECYCLE ---
onBeforeMount(async () => {
  kirimSebagai.value = "samaran";
  await fetchUser(); // Panggil fetch user saat komponen dimuat
});
</script>

<template>
  <div
    class="w-full max-w-[420px] rounded-[20px] bg-[#1c1516] p-4 sm:max-w-[560px] max-h-[90vh] overflow-y-auto"
    @click.stop>
    <form @submit.prevent="handleSubmit" class="flex flex-col text-[#e5e5e5] font-poppins relative">
      <h1 class="text-center text-[#9a203e] text-3xl font-bold m-0">Buat Pesan Baru</h1>
      <p class="mt-0 mb-[3em] text-center text-[12px] text-[#8c8a8a]">Pilih lagu dan tulis pesan untuk seseorang.</p>

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
          class="absolute z-20 top-[80px] left-0 w-full bg-[#2b2122] border border-[#9a203e] rounded-[10px] max-h-[200px] overflow-y-auto shadow-lg">
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
          @click="handleClose"
          class="w-full rounded-[10px] border border-[#8c8a8a] bg-[#1c1516] px-3 py-3 text-xs font-medium text-[#8c8a8a] hover:border-[#666565] hover:bg-[#120e0e] cursor-pointer">
          Kembali
        </button>

        <button
          type="submit"
          class="w-full rounded-[10px] bg-[#9a203e] px-3 py-3 text-xs font-medium text-[#e5e5e5] hover:bg-[#7d1a33] cursor-pointer">
          Buat Pesan
        </button>
      </div>
    </form>
  </div>
</template>
