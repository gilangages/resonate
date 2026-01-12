<script setup>
import { ref, onMounted } from "vue";
// PERBAIKAN: Gunakan 'markAsRead' sesuai nama di API
import { getNotifications, markNotificationsRead } from "../../../lib/api/NotificationApi";
import { useLocalStorage } from "@vueuse/core";
import { formatTime } from "../../../lib/dateFormatter";

const token = useLocalStorage("token", "");
const notifications = ref([]);
const loading = ref(false);

const emit = defineEmits(["read"]);

const fetchNotifs = async () => {
  loading.value = true;
  try {
    const res = await getNotifications(token.value);
    const json = await res.json();
    console.log(json);
    if (res.ok) {
      notifications.value = json.data;
    }
  } catch (err) {
    console.error("Gagal ambil notif", err);
  } finally {
    loading.value = false;
  }
};

const handleMarkRead = async (notifId) => {
  // Cari index notif
  const index = notifications.value.findIndex((n) => n.id === notifId);

  // Jika ditemukan dan belum dibaca
  if (index !== -1 && !notifications.value[index].read_at) {
    try {
      // 1. Update UI Langsung (Optimistic UI) - Ubah status jadi ada tanggalnya
      notifications.value[index].read_at = new Date().toISOString();

      // 2. Emit ke Navbar untuk kurangi angka badge
      emit("read");

      // 3. Kirim request ke backend (Background process)
      await markNotificationsRead(token.value, notifId);
    } catch (err) {
      console.error("Gagal update status read", err);
    }
  }
};

onMounted(() => {
  fetchNotifs();
});
</script>

<template>
  <div
    class="absolute right-0 mt-[12px] w-[320px] bg-[#1e1e1e] border border-[#4b1a1a] rounded-[10px] shadow-xl overflow-hidden z-50">
    <div class="p-3 border-b border-[#4b1a1a] bg-[#2c0f0f]">
      <h3 class="font-bold text-[#e5e5e5] text-sm">Notifikasi</h3>
    </div>

    <div class="max-h-[300px] overflow-y-auto custom-scrollbar">
      <div v-if="loading" class="p-4 text-center text-gray-400 text-xs">Loading...</div>

      <div v-else-if="notifications.length === 0" class="p-6 text-center text-gray-500 text-xs">
        Tidak ada notifikasi baru.
      </div>

      <ul v-else>
        <li
          v-for="notif in notifications"
          :key="notif.id"
          @click="handleMarkRead(notif.id)"
          :class="[
            'p-3 border-b border-white/5 transition flex gap-3',
            !notif.read_at ? 'bg-[#9a203e]/20 cursor-pointer hover:bg-[#9a203e]/30' : 'bg-transparent opacity-70',
          ]">
          <div class="mt-1 shrink-0">
            <span
              :class="!notif.read_at ? 'bg-[#9a203e] text-white' : 'bg-gray-700 text-gray-400'"
              class="flex items-center justify-center w-6 h-6 rounded-full text-[10px] transition-colors">
              {{ !notif.read_at ? "📢" : "✓" }}
            </span>
          </div>

          <div>
            <p
              :class="!notif.read_at ? 'text-white font-medium' : 'text-gray-400'"
              class="text-[13px] mb-1 leading-snug transition-colors">
              {{ notif.data.message || "Pesan baru masuk." }}
            </p>
            <span class="text-[10px] text-gray-500 block">
              {{ formatTime(notif.created_at) }}
            </span>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #1e1e1e;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #4b1a1a;
  border-radius: 4px;
}
</style>
