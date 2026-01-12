<script setup>
import { ref, onMounted } from "vue";
import { useLocalStorage } from "@vueuse/core";
import { getAdminUsers, deleteUserByAdmin } from "../../lib/api/UserApi";
import { getAdminNotes, deleteNoteByAdmin } from "../../lib/api/NoteApi"; // Pastikan sudah dibuat di NoteApi.js
import { alertConfirm, alertSuccess } from "../../lib/alert";
import { getAvatarUrl, userState } from "../../lib/store";

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

const deleteUser = async (id) => {
  if (!(await alertConfirm("Apakah kamu yakin ingin membatasi user ini?"))) {
    return;
  }

  await deleteUserByAdmin(token.value, id);
  alertSuccess("User berhasil dihapus.");
  fetchData();
};

const deleteNote = async (id) => {
  if (!(await alertConfirm("Apakah kamu yakin ingin menghapus pesan ini?"))) {
    return;
  }

  await deleteNoteByAdmin(token.value, id);
  alertSuccess("Pesan berhasil dihapus.");
  fetchData();
};

onMounted(fetchData);
</script>

<template>
  <div class="p-8 text-white relative min-h-screen font-jakarta bg-[#0f0505]">
    <h1 class="text-3xl font-bold mb-6">Panel Admin</h1>

    <div class="flex gap-4 mb-6 border-b border-white/10">
      <button
        @click="
          activeTab = 'users';
          fetchData();
        "
        :class="activeTab === 'users' ? 'border-b-2 border-red-500 text-red-500' : 'text-gray-400'"
        class="pb-2 px-4 transition">
        Manage Users
      </button>
      <button
        @click="
          activeTab = 'notes';
          fetchData();
        "
        :class="activeTab === 'notes' ? 'border-b-2 border-red-500 text-red-500' : 'text-gray-400'"
        class="pb-2 px-4 transition">
        Moderation Notes
      </button>
    </div>

    <div v-if="loading" class="text-center py-10">Loading data...</div>

    <div v-else>
      <div v-if="activeTab === 'users'" class="overflow-x-auto">
        <table class="w-full bg-white/5 rounded-lg overflow-hidden">
          <thead class="bg-white/10">
            <tr>
              <th class="p-4 text-left">User</th>
              <th class="p-4 text-left">Email</th>
              <th class="p-4 text-left">Role</th>
              <th class="p-4 text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users" :key="user.id" class="border-b border-white/5 hover:bg-white/5">
              <td class="p-4 flex items-center gap-3">
                <img
                  :src="user.avatar ? getAvatarUrl(user.avatar) : user.photo_url || '/default-avatar.png'"
                  class="w-8 h-8 rounded-full border border-white/20 object-cover"
                  alt="Avatar" />
                {{ user.name }}
              </td>
              <td class="p-4">{{ user.email }}</td>
              <td class="p-4 text-sm">{{ user.role }}</td>
              <td class="p-4 text-center">
                <button v-if="user.role !== 'admin'" @click="deleteUser(user.id)" class="text-red-500 hover:underline">
                  Ban User
                </button>
                <span v-else class="text-gray-500">N/A</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="activeTab === 'notes'" class="overflow-x-auto">
        <table class="w-full bg-white/5 rounded-lg overflow-hidden">
          <thead class="bg-white/10">
            <tr>
              <th class="p-4 text-left">Author</th>
              <th class="p-4 text-left">Note Content</th>
              <th class="p-4 text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="note in notes" :key="note.id" class="border-b border-white/5 hover:bg-white/5">
              <td class="p-4 font-bold">{{ note.user?.name }}</td>
              <td class="p-4 italic text-gray-300">"{{ note.content }}"</td>
              <td class="p-4 text-center">
                <button
                  @click="deleteNote(note.id)"
                  class="bg-red-500/20 text-red-500 px-3 py-1 rounded hover:bg-red-500 hover:text-white transition">
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
