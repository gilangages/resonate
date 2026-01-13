<script setup>
import { ref, onMounted } from "vue";
import { useLocalStorage } from "@vueuse/core";
import { getAdminUsers, deleteUserByAdmin, restoreUserByAdmin } from "../../lib/api/UserApi";
import { getAdminNotes, deleteNoteByAdmin } from "../../lib/api/NoteApi"; // Pastikan sudah dibuat di NoteApi.js
import { alertConfirm, alertSuccess } from "../../lib/alert";
import { getAvatarUrl } from "../../lib/store";
import Swal from "sweetalert2";

const token = useLocalStorage("token", "");
const activeTab = ref("users"); // 'users' atau 'notes'
const users = ref([]);
const notes = ref([]);
const loading = ref(false);

const fetchData = async () => {
  loading.value = true;
  if (activeTab.value === "users") {
    const res = await getAdminUsers(token.value);
    const json = await res.json();
    console.log(json);
    users.value = json.data;
  } else {
    const res = await getAdminNotes(token.value);
    const json = await res.json();
    notes.value = json.data;
  }
  loading.value = false;
};

const restoreUser = async (id) => {
  if (!(await alertConfirm("Pulihkan akses akun user ini?"))) {
    return;
  }

  try {
    const res = await restoreUserByAdmin(token.value, id);
    if (res.ok) {
      // Update state lokal biar UI langsung berubah tanpa refresh
      const userIndex = users.value.findIndex((u) => u.id === id);
      if (userIndex !== -1) {
        users.value[userIndex].is_banned = 0; // atau false
      }
      alertSuccess("Akun user berhasil dipulihkan.");
    }
  } catch (error) {
    console.error(error);
  }
};

const deleteUser = async (id) => {
  if (!(await alertConfirm("Apakah kamu yakin ingin membatasi user ini?"))) {
    return;
  }

  try {
    await deleteUserByAdmin(token.value, id);

    // --- PERBAIKAN DI SINI ---
    // Jangan dihapus (filter), tapi update statusnya di local state
    const userIndex = users.value.findIndex((u) => u.id === id);
    if (userIndex !== -1) {
      users.value[userIndex].is_banned = 1; // Atau true, sesuaikan return DB
    }

    alertSuccess("User berhasil diblokir.");
  } catch (error) {
    console.error(error);
  }
};

const deleteNote = async (id) => {
  const { value: reason, isConfirmed } = await Swal.fire({
    title: "Hapus Note?",
    text: "Masukkan alasan penghapusan:",
    input: "text", // Input text muncul
    inputPlaceholder: "Contoh: Kasar, SARA, Spam...",
    showCancelButton: true,
    confirmButtonText: "Hapus",
    confirmButtonColor: "#d33",
  });
  if (!isConfirmed) return;
  // if (!(await alertConfirm("Apakah kamu yakin ingin menghapus pesan ini?"))) {
  //   return;

  try {
    // 1. Panggil API Hapus Note
    await deleteNoteByAdmin(token.value, id, reason);

    // 2. Hapus manual dari list lokal agar instan hilang
    notes.value = notes.value.filter((note) => note.id !== id);

    alertSuccess("Pesan berhasil dihapus.");
  } catch (error) {
    console.error(error);
  }
};

onMounted(fetchData);
</script>

<template>
  <div class="p-4 md:p-8 text-white relative min-h-screen font-jakarta bg-[#0f0505]">
    <h1 class="text-2xl md:text-3xl font-bold mb-6">Panel Admin</h1>

    <div class="flex gap-4 mb-6 border-b border-white/10 overflow-x-auto">
      <button
        @click="
          activeTab = 'users';
          fetchData();
        "
        :class="activeTab === 'users' ? 'border-b-2 border-red-500 text-red-500' : 'text-gray-400'"
        class="pb-2 px-4 transition whitespace-nowrap">
        Manage Users
      </button>
      <button
        @click="
          activeTab = 'notes';
          fetchData();
        "
        :class="activeTab === 'notes' ? 'border-b-2 border-red-500 text-red-500' : 'text-gray-400'"
        class="pb-2 px-4 transition whitespace-nowrap">
        Moderation Notes
      </button>
    </div>

    <div v-if="loading" class="text-center py-10">Loading data...</div>

    <div v-else>
      <div v-if="activeTab === 'users'" class="overflow-hidden bg-white/5 rounded-lg">
        <table class="w-full text-left border-collapse">
          <thead class="bg-white/10 text-xs md:text-sm uppercase text-gray-400">
            <tr>
              <th class="p-3 md:p-4">User Info</th>
              <th class="hidden md:table-cell p-4">Email</th>
              <th class="hidden md:table-cell p-4">Role</th>
              <th class="p-3 md:p-4 text-center">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/10 text-sm md:text-base">
            <tr v-for="user in users" :key="user.id" class="hover:bg-white/5 transition">
              <td class="p-3 md:p-4">
                <div class="flex items-center gap-3">
                  <img
                    :src="user.avatar ? getAvatarUrl(user.avatar) : user.photo_url || '/default-avatar.png'"
                    class="w-10 h-10 rounded-full border border-white/20 object-cover flex-shrink-0"
                    alt="Avatar" />
                  <div class="flex flex-col">
                    <span class="font-semibold text-white">{{ user.name }}</span>
                    <span class="text-xs text-gray-400 md:hidden">{{ user.email }}</span>
                    <span class="text-[10px] bg-white/10 px-1 rounded w-fit md:hidden mt-1">{{ user.role }}</span>
                  </div>
                </div>
              </td>

              <td class="hidden md:table-cell p-4 text-gray-300">{{ user.email }}</td>
              <td class="hidden md:table-cell p-4 text-gray-300">{{ user.role }}</td>

              <td class="p-3 md:p-4 text-center align-middle">
                <span v-if="user.role === 'admin'" class="text-gray-500 text-xs md:text-sm">Admin</span>

                <button
                  v-else-if="!user.is_banned"
                  @click="deleteUser(user.id)"
                  class="text-red-400 hover:text-red-300 text-xs md:text-sm border border-red-500/30 px-2 py-1 rounded hover:bg-red-500/10 transition">
                  Ban
                </button>

                <div v-else class="flex flex-col items-center gap-1">
                  <span class="text-red-500 font-bold italic text-[10px]">Banned</span>
                  <button
                    @click="restoreUser(user.id)"
                    class="bg-green-600/20 text-green-400 text-[10px] md:text-xs px-2 py-1 rounded hover:bg-green-600 hover:text-white transition">
                    Pulihkan
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="activeTab === 'notes'" class="overflow-x-auto bg-white/5 rounded-lg">
        <table class="w-full text-left border-collapse">
          <thead class="bg-white/10 text-xs md:text-sm uppercase text-gray-400">
            <tr>
              <th class="p-3 md:p-4 w-1/4">Author</th>
              <th class="p-3 md:p-4 w-1/2">Note Content</th>
              <th class="p-3 md:p-4 text-center w-1/4">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/10 text-sm md:text-base">
            <tr v-for="note in notes" :key="note.id" class="hover:bg-white/5 transition">
              <td class="p-3 md:p-4 align-top">
                <div class="font-bold text-white">{{ note.user?.name }}</div>
                <div class="text-xs text-gray-500 md:hidden mt-1">ID: {{ note.id }}</div>
              </td>

              <td class="p-3 md:p-4 align-top">
                <div
                  class="whitespace-pre-wrap break-words text-gray-300 italic min-w-[200px] max-w-[200px] md:max-w-[400px] lg:max-w-[600px]">
                  "{{ note.content }}"
                </div>
              </td>

              <td class="p-3 md:p-4 text-center align-top">
                <button
                  @click="deleteNote(note.id)"
                  class="bg-red-500/20 text-red-500 px-3 py-1 rounded text-xs md:text-sm hover:bg-red-500 hover:text-white transition">
                  Hapus
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
