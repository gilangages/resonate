<script setup>
import { ref, onMounted, watch } from "vue";
import { useLocalStorage, useDebounceFn } from "@vueuse/core";
import { getAdminUsers, deleteUserByAdmin, restoreUserByAdmin } from "../../lib/api/UserApi";
import { getAdminNotes, deleteNoteByAdmin } from "../../lib/api/NoteApi";
import { alertConfirm, alertSuccess } from "../../lib/alert";
import { getAvatarUrl } from "../../lib/store";
import Swal from "sweetalert2";

const token = useLocalStorage("token", "");
const activeTab = ref("users"); // 'users' atau 'notes'

// Data & Meta Pagination
const items = ref([]); // Bisa user, bisa note
const currentPage = ref(1);
const lastPage = ref(1);
const totalData = ref(0);
const loading = ref(false);

// Search State
const searchQuery = ref("");

// Fetch Data Dinamis
const fetchData = async () => {
  loading.value = true;
  try {
    let res;
    if (activeTab.value === "users") {
      res = await getAdminUsers(token.value, currentPage.value, searchQuery.value);
    } else {
      res = await getAdminNotes(token.value, currentPage.value, searchQuery.value);
    }

    const json = await res.json();
    if (res.ok) {
      items.value = json.data; // Data array
      currentPage.value = json.current_page;
      lastPage.value = json.last_page;
      totalData.value = json.total;
    }
  } catch (error) {
    console.error("Error fetching admin data:", error);
  } finally {
    loading.value = false;
  }
};

// Debounce Search (Tunggu 500ms setelah ketik baru search) agar server tidak berat
const debouncedSearch = useDebounceFn(() => {
  currentPage.value = 1; // Reset ke halaman 1 saat search
  fetchData();
}, 500);

// Watcher untuk Search Input
watch(searchQuery, () => {
  debouncedSearch();
});

// Ganti Halaman
const changePage = (page) => {
  if (page >= 1 && page <= lastPage.value) {
    currentPage.value = page;
    fetchData();
  }
};

// Ganti Tab
const switchTab = (tab) => {
  activeTab.value = tab;
  searchQuery.value = ""; // Reset search saat ganti tab
  currentPage.value = 1;
  items.value = [];
  fetchData();
};

// --- ACTION HANDLERS ---

const restoreUser = async (id) => {
  if (!(await alertConfirm("Pulihkan akses akun user ini?"))) return;
  try {
    const res = await restoreUserByAdmin(token.value, id);
    if (res.ok) {
      // Update lokal
      const user = items.value.find((u) => u.id === id);
      if (user) user.is_banned = 0;
      alertSuccess("Akun dipulihkan.");
    }
  } catch (err) {
    console.error(err);
  }
};

const deleteUser = async (id) => {
  if (!(await alertConfirm("Yakin ingin memblokir user ini?"))) return;
  try {
    await deleteUserByAdmin(token.value, id);
    const user = items.value.find((u) => u.id === id);
    if (user) user.is_banned = 1;
    alertSuccess("User diblokir.");
  } catch (err) {
    console.error(err);
  }
};

const deleteNote = async (id) => {
  const { value: reason, isConfirmed } = await Swal.fire({
    title: "Hapus Note?",
    text: "Masukkan alasan (wajib):",
    input: "text",
    inputPlaceholder: "Contoh: Spam / Kata kasar",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    confirmButtonText: "Hapus",
  });

  if (!isConfirmed) return;

  try {
    await deleteNoteByAdmin(token.value, id, reason || "Konten tidak pantas");
    // Hapus dari list
    items.value = items.value.filter((n) => n.id !== id);
    alertSuccess("Note dihapus.");
  } catch (err) {
    console.error(err);
  }
};

onMounted(fetchData);
</script>

<template>
  <div class="min-h-screen bg-[#0f0505] text-white font-jakarta p-4 md:p-8 relative">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
      <div>
        <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400">
          Admin Dashboard
        </h1>
        <p class="text-gray-400 text-sm mt-1">Manage users & content moderation</p>
      </div>

      <div class="relative w-full md:w-64">
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="activeTab === 'users' ? 'Cari nama/email...' : 'Cari isi konten...'"
          class="w-full bg-[#1c1516] border border-[#2c2021] rounded-lg py-2 pl-10 pr-4 text-sm focus:outline-none focus:border-red-500 transition-colors text-white placeholder-gray-600" />
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="absolute left-3 top-2.5 w-4 h-4 text-gray-500"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
      </div>
    </div>

    <div class="flex gap-6 mb-6 border-b border-white/10">
      <button
        @click="switchTab('users')"
        :class="activeTab === 'users' ? 'text-red-500 border-b-2 border-red-500' : 'text-gray-500 hover:text-gray-300'"
        class="pb-3 px-2 font-medium transition-all">
        Users Management
      </button>
      <button
        @click="switchTab('notes')"
        :class="activeTab === 'notes' ? 'text-red-500 border-b-2 border-red-500' : 'text-gray-500 hover:text-gray-300'"
        class="pb-3 px-2 font-medium transition-all">
        Notes Moderation
      </button>
    </div>

    <div v-if="loading" class="py-12 text-center">
      <div class="animate-spin w-8 h-8 border-2 border-red-500 border-t-transparent rounded-full mx-auto mb-2"></div>
      <p class="text-gray-500 text-sm">Memuat data...</p>
    </div>

    <div v-else-if="items.length === 0" class="py-12 text-center bg-white/5 rounded-lg border border-white/5">
      <p class="text-gray-400">Tidak ada data ditemukan.</p>
    </div>

    <div v-else class="bg-[#160f10] border border-[#2c2021] rounded-xl overflow-hidden shadow-xl">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead class="bg-white/5 text-gray-400 text-xs uppercase tracking-wider">
            <tr>
              <th v-if="activeTab === 'users'" class="p-4">User</th>
              <th v-if="activeTab === 'users'" class="p-4">Status</th>
              <th v-if="activeTab === 'notes'" class="p-4">Author</th>
              <th v-if="activeTab === 'notes'" class="p-4">Content</th>
              <th class="p-4 text-center">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/5 text-sm">
            <tr v-for="item in items" :key="item.id" class="hover:bg-white/5 transition-colors">
              <template v-if="activeTab === 'users'">
                <td class="p-4">
                  <div class="flex items-center gap-3">
                    <img :src="getAvatarUrl(item.avatar)" class="w-9 h-9 rounded-full object-cover bg-gray-800" />
                    <div>
                      <div class="font-medium text-white">{{ item.name }}</div>
                      <div class="text-xs text-gray-500">{{ item.email }}</div>
                    </div>
                  </div>
                </td>
                <td class="p-4">
                  <span
                    v-if="item.role === 'admin'"
                    class="px-2 py-1 rounded bg-blue-500/10 text-blue-400 text-xs border border-blue-500/20">
                    Admin
                  </span>
                  <span
                    v-else-if="item.is_banned"
                    class="px-2 py-1 rounded bg-red-500/10 text-red-400 text-xs border border-red-500/20">
                    Banned
                  </span>
                  <span
                    v-else
                    class="px-2 py-1 rounded bg-green-500/10 text-green-400 text-xs border border-green-500/20">
                    Active
                  </span>
                </td>
                <td class="p-4 text-center">
                  <button
                    v-if="item.role !== 'admin' && !item.is_banned"
                    @click="deleteUser(item.id)"
                    class="text-gray-400 hover:text-red-500 transition px-3 py-1 text-xs border border-gray-700 rounded hover:border-red-500">
                    Ban
                  </button>
                  <button
                    v-if="item.is_banned"
                    @click="restoreUser(item.id)"
                    class="text-green-500 hover:text-green-400 transition px-3 py-1 text-xs border border-green-500/30 rounded">
                    Restore
                  </button>
                </td>
              </template>

              <template v-if="activeTab === 'notes'">
                <td class="p-4 align-top w-48">
                  <div class="font-bold text-white mb-1">{{ item.user?.name || "Unknown" }}</div>
                  <div class="text-[10px] text-gray-500">{{ new Date(item.created_at).toLocaleDateString() }}</div>
                </td>
                <td class="p-4 align-top">
                  <p class="text-gray-300 italic text-sm leading-relaxed line-clamp-3">"{{ item.content }}"</p>
                </td>
                <td class="p-4 text-center align-top w-32">
                  <button
                    @click="deleteNote(item.id)"
                    class="text-red-400 hover:text-white bg-red-500/10 hover:bg-red-600 border border-red-500/20 px-3 py-1.5 rounded text-xs transition">
                    Hapus
                  </button>
                </td>
              </template>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="p-4 border-t border-white/5 flex items-center justify-between bg-[#130b0c]">
        <div class="text-xs text-gray-500">
          Total:
          <span class="text-white font-bold">{{ totalData }}</span>
          data
        </div>
        <div class="flex items-center gap-2">
          <button
            @click="changePage(currentPage - 1)"
            :disabled="currentPage === 1"
            class="px-3 py-1 text-xs rounded border border-gray-700 text-gray-400 hover:bg-white/5 disabled:opacity-50 disabled:cursor-not-allowed">
            Prev
          </button>

          <span class="text-xs text-gray-300 px-2">Page {{ currentPage }} of {{ lastPage }}</span>

          <button
            @click="changePage(currentPage + 1)"
            :disabled="currentPage === lastPage"
            class="px-3 py-1 text-xs rounded border border-gray-700 text-gray-400 hover:bg-white/5 disabled:opacity-50 disabled:cursor-not-allowed">
            Next
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
