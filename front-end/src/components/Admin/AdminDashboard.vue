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

// Debounce Search
const debouncedSearch = useDebounceFn(() => {
  currentPage.value = 1;
  fetchData();
}, 500);

watch(searchQuery, () => {
  debouncedSearch();
});

const changePage = (page) => {
  if (page >= 1 && page <= lastPage.value) {
    currentPage.value = page;
    fetchData();
  }
};

const switchTab = (tab) => {
  activeTab.value = tab;
  searchQuery.value = "";
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
    items.value = items.value.filter((n) => n.id !== id);
    alertSuccess("Note dihapus.");
  } catch (err) {
    console.error(err);
  }
};

onMounted(fetchData);
</script>

<template>
  <div
    class="min-h-screen bg-[#0f0505] text-white font-jakarta p-4 md:p-8 relative selection:bg-red-500/30 selection:text-red-200">
    <div
      class="absolute top-0 left-0 w-full h-96 bg-gradient-to-b from-red-900/10 to-transparent pointer-events-none z-0"></div>

    <div class="relative z-10 max-w-7xl mx-auto">
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-6">
        <div>
          <h1
            class="text-3xl md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white via-gray-200 to-gray-500 tracking-tight">
            Admin Dashboard
          </h1>
          <p class="text-gray-400 text-sm mt-2 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-red-500 inline-block animate-pulse"></span>
            Manage users & content moderation
          </p>
        </div>

        <div class="relative w-full md:w-72 group">
          <input
            v-model="searchQuery"
            type="text"
            :placeholder="activeTab === 'users' ? 'Cari nama/email...' : 'Cari isi konten...'"
            class="w-full bg-[#1c1516]/80 backdrop-blur-sm border border-[#2c2021] rounded-xl py-2.5 pl-10 pr-4 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500/50 transition-all text-white placeholder-gray-600 shadow-lg group-hover:border-white/10" />
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="absolute left-3 top-3 w-4 h-4 text-gray-500 group-focus-within:text-red-400 transition-colors"
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

      <div class="flex gap-4 md:gap-8 mb-6 border-b border-white/10 relative overflow-x-auto">
        <button
          @click="switchTab('users')"
          :class="
            activeTab === 'users'
              ? 'text-red-400 border-b-2 border-red-500 bg-white/5'
              : 'text-gray-500 hover:text-gray-200 hover:bg-white/5'
          "
          class="pb-3 px-4 font-medium transition-all rounded-t-lg text-sm tracking-wide whitespace-nowrap">
          Users Management
        </button>
        <button
          @click="switchTab('notes')"
          :class="
            activeTab === 'notes'
              ? 'text-red-400 border-b-2 border-red-500 bg-white/5'
              : 'text-gray-500 hover:text-gray-200 hover:bg-white/5'
          "
          class="pb-3 px-4 font-medium transition-all rounded-t-lg text-sm tracking-wide whitespace-nowrap">
          Notes Moderation
        </button>
      </div>

      <div v-if="loading" class="py-20 text-center">
        <div
          class="animate-spin w-10 h-10 border-2 border-red-500 border-t-transparent rounded-full mx-auto mb-4 shadow-[0_0_15px_rgba(239,68,68,0.5)]"></div>
        <p class="text-gray-500 text-sm animate-pulse">Sinkronisasi data...</p>
      </div>

      <div
        v-else-if="items.length === 0"
        class="py-20 text-center bg-[#160f10]/50 rounded-2xl border border-dashed border-white/10 backdrop-blur-sm">
        <div class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="w-8 h-8 text-gray-600"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <p class="text-gray-400 font-medium">Tidak ada data ditemukan.</p>
        <p class="text-gray-600 text-xs mt-1">Coba kata kunci lain atau ubah filter.</p>
      </div>

      <div
        v-else
        class="bg-[#160f10]/90 backdrop-blur border border-[#2c2021] rounded-2xl overflow-hidden shadow-2xl ring-1 ring-white/5">
        <div class="overflow-x-auto custom-scrollbar">
          <table class="w-full text-left border-collapse">
            <thead class="bg-[#1c1516] text-gray-400 text-xs uppercase tracking-wider sticky top-0 z-20 shadow-sm">
              <tr>
                <th v-if="activeTab === 'users'" class="p-4 md:p-5 font-semibold text-gray-300">User Profile</th>
                <th v-if="activeTab === 'users'" class="p-4 md:p-5 font-semibold text-gray-300">Account Status</th>
                <th v-if="activeTab === 'notes'" class="p-4 md:p-5 font-semibold text-gray-300 w-64 min-w-[250px]">
                  Author Info
                </th>
                <th v-if="activeTab === 'notes'" class="p-4 md:p-5 font-semibold text-gray-300 min-w-[300px]">
                  Note Content
                </th>
                <th class="p-4 md:p-5 text-center font-semibold text-gray-300 w-32">Actions</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-white/5 text-sm">
              <tr v-for="item in items" :key="item.id" class="hover:bg-white/[0.03] transition-colors group">
                <template v-if="activeTab === 'users'">
                  <td class="p-4 md:p-5">
                    <div class="flex items-center gap-3 md:gap-4">
                      <div class="relative shrink-0">
                        <img
                          :src="getAvatarUrl(item.avatar || item.photo_url)"
                          class="w-10 h-10 rounded-full object-cover bg-gray-800 ring-2 ring-white/10" />
                        <div
                          v-if="item.role === 'admin'"
                          class="absolute -top-1 -right-1 bg-blue-500 w-3 h-3 rounded-full border-2 border-[#160f10]"></div>
                      </div>

                      <div class="min-w-0">
                        <div class="font-medium text-white text-base break-words">{{ item.name }}</div>
                        <div class="text-xs text-gray-500 font-mono break-all">{{ item.email }}</div>
                      </div>
                    </div>
                  </td>

                  <td class="p-4 md:p-5">
                    <span
                      v-if="item.role === 'admin'"
                      class="inline-flex items-center px-2.5 py-1 rounded-md bg-blue-500/10 text-blue-400 text-xs font-medium border border-blue-500/20 shadow-[0_0_10px_rgba(59,130,246,0.1)]">
                      <span class="w-1.5 h-1.5 rounded-full bg-blue-400 mr-2"></span>
                      Admin
                    </span>
                    <span
                      v-else-if="item.is_banned"
                      class="inline-flex items-center px-2.5 py-1 rounded-md bg-red-500/10 text-red-400 text-xs font-medium border border-red-500/20 shadow-[0_0_10px_rgba(239,68,68,0.1)]">
                      <span class="w-1.5 h-1.5 rounded-full bg-red-400 mr-2"></span>
                      Banned
                    </span>
                    <span
                      v-else
                      class="inline-flex items-center px-2.5 py-1 rounded-md bg-green-500/10 text-green-400 text-xs font-medium border border-green-500/20 shadow-[0_0_10px_rgba(34,197,94,0.1)]">
                      <span class="w-1.5 h-1.5 rounded-full bg-green-400 mr-2"></span>
                      Active
                    </span>
                  </td>

                  <td class="p-4 md:p-5 text-center">
                    <button
                      v-if="item.role !== 'admin' && !item.is_banned"
                      @click="deleteUser(item.id)"
                      class="group/btn relative text-gray-400 hover:text-red-400 transition-all px-4 py-1.5 text-xs border border-gray-700 rounded-lg hover:border-red-500/50 hover:bg-red-500/10 active:scale-95 whitespace-nowrap">
                      Ban User
                    </button>
                    <button
                      v-if="item.is_banned"
                      @click="restoreUser(item.id)"
                      class="text-green-400 hover:text-white bg-green-500/10 hover:bg-green-500 transition-all px-4 py-1.5 text-xs border border-green-500/30 rounded-lg active:scale-95 shadow-[0_0_10px_rgba(34,197,94,0.1)] hover:shadow-[0_0_15px_rgba(34,197,94,0.3)]">
                      Restore
                    </button>
                  </td>
                </template>

                <template v-if="activeTab === 'notes'">
                  <td class="p-4 md:p-5 align-top">
                    <div class="flex items-start gap-3 md:gap-4">
                      <div class="relative shrink-0">
                        <img
                          :src="getAvatarUrl(item.user.avatar)"
                          class="w-10 h-10 rounded-full object-cover bg-gray-800 ring-2 ring-white/10" />
                      </div>

                      <div class="min-w-0 flex-1">
                        <div
                          tabindex="0"
                          class="font-medium text-white text-sm md:text-base truncate block max-w-[140px] md:max-w-[200px] cursor-pointer focus:whitespace-normal focus:max-w-full focus:overflow-visible outline-none transition-all duration-200"
                          :title="item.user?.name || 'Unknown'">
                          {{ item.user?.name || "Unknown" }}
                        </div>

                        <div class="text-[11px] text-gray-500 mt-0.5 flex items-center gap-1">
                          <span class="w-1.5 h-1.5 bg-gray-600 rounded-full shrink-0"></span>
                          <span class="truncate max-w-[120px]">
                            {{
                              new Date(item.created_at).toLocaleDateString("id-ID", {
                                day: "numeric",
                                month: "long",
                                year: "numeric",
                              })
                            }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </td>

                  <td class="p-4 md:p-5 align-top">
                    <div
                      class="text-gray-300 text-sm leading-relaxed break-words min-w-[250px] md:min-w-[350px] max-w-[600px] p-3 md:p-4 rounded-xl border border-white/5 bg-black/20 shadow-inner">
                      "{{ item.content }}"
                    </div>
                  </td>

                  <td class="p-4 md:p-5 text-center align-top">
                    <button
                      @click="deleteNote(item.id)"
                      title="Hapus Konten Permanen"
                      class="text-red-400 hover:text-white bg-red-500/10 hover:bg-red-600 border border-red-500/20 px-4 py-2 rounded-lg text-xs transition-all flex items-center gap-2 mx-auto active:scale-95 hover:shadow-[0_0_15px_rgba(239,68,68,0.4)] whitespace-nowrap">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-3.5 h-3.5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                      Hapus
                    </button>
                  </td>
                </template>
              </tr>
            </tbody>
          </table>
        </div>

        <div
          class="p-4 border-t border-white/5 flex flex-col sm:flex-row items-center justify-between bg-[#130b0c] gap-4">
          <div class="text-xs text-gray-500">
            Total Data:
            <span class="text-white font-bold ml-1">{{ totalData }}</span>
          </div>
          <div class="flex items-center gap-3">
            <button
              @click="changePage(currentPage - 1)"
              :disabled="currentPage === 1"
              class="px-4 py-1.5 text-xs rounded-lg border border-gray-700 text-gray-400 hover:bg-white/5 hover:text-white hover:border-gray-500 disabled:opacity-30 disabled:cursor-not-allowed transition-all">
              Previous
            </button>
            <span class="text-xs font-mono text-gray-400 bg-black/30 px-3 py-1 rounded border border-white/5">
              {{ currentPage }} / {{ lastPage }}
            </span>
            <button
              @click="changePage(currentPage + 1)"
              :disabled="currentPage === lastPage"
              class="px-4 py-1.5 text-xs rounded-lg border border-gray-700 text-gray-400 hover:bg-white/5 hover:text-white hover:border-gray-500 disabled:opacity-30 disabled:cursor-not-allowed transition-all">
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
