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
    console.log(json);
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

// const getBgClass = (notif) => {
//   if (notif.read_at) return "bg-transparent opacity-60";
//   if (notif.data.type === "alert") return "bg-red-900/40 border-l-4 border-red-500 hover:bg-red-900/50";
//   return "bg-[#9a203e]/20 hover:bg-[#9a203e]/30";
// };

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
          :class="[
            'p-3 hover:bg-[#2c2122] cursor-pointer border-b border-[#2c2122] transition-colors',
            notif.read_at ? 'opacity-60' : 'bg-[#1e0f0f]',
          ]">
          <div class="flex gap-3">
            <div class="mt-1 flex-shrink-0">
              <div
                class="w-2 h-2 rounded-full mt-1.5"
                :class="notif.read_at ? 'bg-gray-600' : 'bg-red-500 animate-pulse'"></div>
            </div>

            <div class="flex-1">
              <p class="text-sm font-semibold text-[#e5e5e5] mb-0.5">
                {{ notif.data.title || "Notifikasi Baru" }}
              </p>
              <p class="text-xs text-[#cdcccc] leading-relaxed">
                {{ notif.data.message }}
              </p>
              <p
                v-if="notif.data.reason"
                class="text-[10px] text-red-400 mt-1 font-medium bg-red-900/10 px-1.5 py-0.5 rounded border border-red-900/30 inline-block">
                ⚠️ Alasan: {{ notif.data.reason }}
              </p>

              <div
                v-if="notif.data.appeal_message"
                class="mt-2 p-2 rounded bg-[#2c1a1a] border border-[#4b1a1a] relative">
                <p class="text-[11px] text-gray-300 italic">
                  <span class="font-bold text-[#9a203e] not-italic">Pesan User:</span>
                  "{{ notif.data.appeal_message }}"
                </p>
              </div>
              <p class="text-[10px] text-gray-500 mt-2">
                {{ formatTime(notif.created_at) }}
              </p>
            </div>
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
