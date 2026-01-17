<script setup>
import { ref, onMounted } from "vue";
import { useLocalStorage } from "@vueuse/core";
import { getAdminUsers, deleteUserByAdmin } from "../../lib/api/UserApi";
import { useRouter } from "vue-router";

const token = useLocalStorage("token", "");
const users = ref([]);
const isLoading = ref(true);
const router = useRouter();

const getImageUrl = (url) => {
  if (!url) return "";

  // URL Eksternal (Google/Deezer/Dicebear) -> Langsung
  if (url.includes("http")) {
    return url;
  }

  // URL Lokal -> Lewat Proxy
  const apiUrl = import.meta.env.VITE_APP_PATH || "http://localhost:8000/api";
  const encodedImageUrl = encodeURIComponent(url);
  return `${apiUrl}/image-proxy?url=${encodedImageUrl}&t=${Date.now()}`; // Tambah timestamp biar fresh
};

// Fetch Data User
const loadUsers = async () => {
  isLoading.value = true;
  try {
    const response = await getAdminUsers(token.value);
    const data = await response.json();

    // --- TAMBAHKAN INI UNTUK CEK ISI DATA ---
    console.log("Status API:", response.status);
    console.log("Isi Data Mentah:", data);
    // ----------------------------------------

    if (response.ok) {
      // Pastikan backend mengirim format { data: [...] }
      // Kalau backend kirim langsung array [...], ubah jadi users.value = data;
      users.value = data.data;

      console.log("Data yang dimasukkan ke tabel:", users.value);
    } else {
      if (response.status === 403) {
        alert("Akses Ditolak!");
        router.push("/dashboard");
      }
    }
  } catch (error) {
    console.error("Gagal load users:", error);
  } finally {
    isLoading.value = false;
  }
};

// Hapus User
const handleDelete = async (id, name) => {
  if (!confirm(`Yakin ingin menghapus user "${name}"? Semua pesan mereka akan hilang.`)) return;

  try {
    const response = await deleteUserByAdmin(token.value, id);
    if (response.ok) {
      alert("User berhasil dihapus");
      loadUsers(); // Refresh tabel
    } else {
      const err = await response.json();
      alert(err.message || "Gagal menghapus user");
    }
  } catch (error) {
    alert("Terjadi kesalahan sistem");
  }
};

onMounted(() => {
  loadUsers();
});
</script>

<template>
  <div class="p-8 max-w-6xl mx-auto font-jakarta text-white">
    <div class="flex justify-between items-center mb-8">
      <div>
        <h1 class="text-3xl font-bold">Admin Dashboard</h1>

        <p class="text-white/50 mt-1">Kelola pengguna dan jaga komunitas tetap bersih.</p>
      </div>
      <button
        @click="loadUsers"
        class="px-4 py-2 bg-white/10 rounded-lg hover:bg-white/20 transition-colors text-sm font-bold">
        Refresh Data
      </button>
    </div>

    <div class="bg-[#1c1516] border border-[#2c2021] rounded-[24px] overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-white/5 border-b border-white/10 text-xs uppercase tracking-widest text-white/50">
              <th class="p-6 font-bold">User</th>
              <th class="p-6 font-bold">Role</th>
              <th class="p-6 font-bold">Bergabung</th>
              <th class="p-6 font-bold text-right">Aksi</th>
            </tr>
          </thead>
          <tbody v-if="!isLoading && users.length > 0">
            <tr
              v-for="user in users"
              :key="user.id"
              class="border-b border-white/5 hover:bg-white/[0.02] transition-colors group">
              <td class="p-6">
                <div class="flex items-center gap-3">
                  <div
                    class="relative w-10 h-10 rounded-full overflow-hidden bg-gray-800 border border-white/10 shrink-0">
                    <img
                      v-if="user.avatar"
                      :src="getImageUrl(user.avatar)"
                      class="w-full h-full object-cover"
                      alt="Avatar"
                      @error="user.avatar = null" />

                    <div v-else class="w-full h-full flex items-center justify-center font-bold text-white/30 text-sm">
                      {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                  </div>

                  <div>
                    <p class="font-bold text-white">{{ user.name }}</p>
                    <p class="text-xs text-white/40">{{ user.email }}</p>
                  </div>
                </div>
              </td>
              <td class="p-6">
                <span
                  :class="
                    user.role === 'admin'
                      ? 'bg-purple-500/20 text-purple-300 border-purple-500/30'
                      : 'bg-white/10 text-white/60 border-white/10'
                  "
                  class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border">
                  {{ user.role }}
                </span>
              </td>
              <td class="p-6 text-sm text-white/40 font-mono">
                {{ new Date(user.created_at).toLocaleDateString() }}
              </td>
              <td class="p-6 text-right">
                <button
                  v-if="user.role !== 'admin'"
                  @click="handleDelete(user.id, user.name)"
                  class="text-red-400 hover:text-red-300 hover:bg-red-500/10 px-3 py-2 rounded-lg text-xs font-bold uppercase transition-all opacity-50 group-hover:opacity-100">
                  Hapus
                </button>
              </td>
            </tr>
          </tbody>

          <tbody v-else-if="isLoading">
            <tr>
              <td colspan="4" class="p-10 text-center text-white/30 animate-pulse">Memuat data user...</td>
            </tr>
          </tbody>

          <tbody v-else>
            <tr>
              <td colspan="4" class="p-10 text-center text-white/30">Tidak ada user ditemukan.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
