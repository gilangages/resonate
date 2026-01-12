<script setup>
import { ref, onMounted } from "vue";
import { getNotifications, markNotificationsRead, markAllNotificationsRead } from "../../../lib/api/NotificationApi";
import { useLocalStorage } from "@vueuse/core";
import { formatTime } from "../../../lib/dateFormatter";

const token = useLocalStorage("token", "");
const notifications = ref([]);
const loading = ref(false);

const emit = defineEmits(["read", "read-all", "close"]); // Tambahkan emit 'close' jika ingin menutup dropdown saat link diklik

const fetchNotifs = async () => {
  loading.value = true;
  try {
    const res = await getNotifications(token.value);
    const json = await res.json();
    if (res.ok) {
      // json.data akan berisi array 10 item dari backend
      notifications.value = json.data;
    }
  } catch (err) {
    console.error("Gagal ambil notif", err);
  } finally {
    loading.value = false;
  }
};

const handleMarkRead = async (notifId) => {
  const index = notifications.value.findIndex((n) => n.id === notifId);
  if (index !== -1 && !notifications.value[index].read_at) {
    try {
      notifications.value[index].read_at = new Date().toISOString();
      emit("read");
      await markNotificationsRead(token.value, notifId);
    } catch (err) {
      console.error("Gagal update status read", err);
    }
  }
};

const markAllRead = async () => {
  const hasUnread = notifications.value.some((n) => !n.read_at);
  if (!hasUnread) return;

  const now = new Date().toISOString();
  notifications.value.forEach((n) => {
    if (!n.read_at) n.read_at = now;
  });

  emit("read-all");

  try {
    await markAllNotificationsRead(token.value);
  } catch (err) {
    console.error("Gagal mark all read", err);
  }
};

const getBgClass = (notif) => {
  if (notif.read_at) return "bg-transparent opacity-60";
  if (notif.data.type === "alert") return "bg-red-900/40 border-l-4 border-red-500 hover:bg-red-900/50";
  return "bg-[#9a203e]/20 hover:bg-[#9a203e]/30";
};

onMounted(() => {
  fetchNotifs();
});
</script>
<template>
  <div
    class="absolute -right-4 sm:right-0 mt-[12px] w-[85vw] sm:w-[360px] bg-[#1e1e1e] border border-[#4b1a1a] rounded-xl shadow-2xl overflow-hidden z-50 ring-1 ring-white/10">
    <div class="p-4 border-b border-[#4b1a1a] bg-[#2c0f0f] flex justify-between items-center">
      <h3 class="font-bold text-[#e5e5e5] text-sm">Notifikasi</h3>

      <button
        v-if="notifications.some((n) => !n.read_at)"
        @click="markAllRead"
        class="text-[10px] text-red-400 hover:text-white underline cursor-pointer">
        Tandai semua dibaca
      </button>
    </div>

    <div class="max-h-[350px] overflow-y-auto custom-scrollbar">
      <div v-if="loading" class="p-6 text-center text-gray-400 text-xs">Loading...</div>

      <div v-else-if="notifications.length === 0" class="p-8 text-center flex flex-col items-center gap-2">
        <span class="text-2xl">📭</span>
        <span class="text-gray-500 text-xs">Belum ada notifikasi.</span>
      </div>

      <ul v-else>
        <li
          v-for="notif in notifications"
          :key="notif.id"
          @click="handleMarkRead(notif.id)"
          :class="['p-4 border-b border-white/5 transition flex gap-4 cursor-pointer', getBgClass(notif)]">
          <div class="mt-1 shrink-0">
            <div
              v-if="notif.data.type === 'alert'"
              class="flex items-center justify-center w-8 h-8 rounded-full bg-red-500/20 text-red-500 border border-red-500/50">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M18 6 6 18" />
                <path d="m6 6 12 12" />
              </svg>
            </div>
            <div
              v-else
              :class="!notif.read_at ? 'bg-[#9a203e] text-white' : 'bg-gray-700 text-gray-400'"
              class="flex items-center justify-center w-8 h-8 rounded-full transition-colors text-xs">
              {{ !notif.read_at ? "📢" : "✓" }}
            </div>
          </div>

          <div class="flex-1">
            <h4 :class="!notif.read_at ? 'text-white' : 'text-gray-400'" class="font-bold text-[13px] mb-0.5">
              {{ notif.data.title || "Info" }}
            </h4>
            <p :class="!notif.read_at ? 'text-gray-200' : 'text-gray-500'" class="text-[12px] leading-relaxed">
              {{ notif.data.message }}
            </p>
            <div v-if="notif.data.reason" class="mt-2 p-2 bg-red-500/10 rounded border border-red-500/20">
              <p class="text-[11px] text-red-300">
                <span class="font-bold">Alasan:</span>
                {{ notif.data.reason }}
              </p>
            </div>
            <span class="text-[10px] text-gray-600 block mt-2">
              {{ formatTime(notif.created_at) }}
            </span>
          </div>
        </li>
      </ul>
    </div>

    <div class="p-3 border-t border-[#4b1a1a] bg-[#2c0f0f] text-center">
      <router-link
        to="/dashboard/notifications"
        class="text-[11px] text-red-400 hover:text-red-300 font-medium transition flex items-center justify-center gap-1">
        Lihat Semua Notifikasi
        <span>→</span>
      </router-link>
    </div>
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #1e1e1e;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #4b1a1a;
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #6e2626;
}
</style>
