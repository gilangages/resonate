<script setup>
import { ref, onMounted } from "vue";
import { getAllNotifications, markNotificationsRead, markAllNotificationsRead } from "../../lib/api/NotificationApi";
import { useLocalStorage } from "@vueuse/core";
import { formatTime } from "../../lib/dateFormatter";
import { alertConfirm } from "../../lib/alert";

const token = useLocalStorage("token", "");
const notifications = ref([]);
const pagination = ref({});
const loading = ref(false);

// Ambil data notifikasi berdasarkan halaman
const fetchNotifs = async (page = 1) => {
  loading.value = true;
  try {
    const res = await getAllNotifications(token.value, page);
    const json = await res.json();
    if (res.ok) {
      notifications.value = json.data; // Array data
      pagination.value = {
        // Info halaman (Laravel Pagination)
        current_page: json.current_page,
        last_page: json.last_page,
        next_page_url: json.next_page_url,
        prev_page_url: json.prev_page_url,
      };

      // Scroll ke atas setiap ganti halaman
      window.scrollTo({ top: 0, behavior: "smooth" });
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
    notifications.value[index].read_at = new Date().toISOString();
    try {
      await markNotificationsRead(token.value, notifId);
      // Trigger update navbar juga di sini agar sinkron real-time
      window.dispatchEvent(new Event("notification-updated"));
    } catch (err) {
      console.error(err);
    }
  }
};

const handleMarkAllRead = async () => {
  // PERBAIKAN 1: Tambahkan 'await'.
  // Kita tampung dulu hasilnya ke variabel.
  const confirmed = await alertConfirm("Tandai semua notifikasi (termasuk di halaman lain) sebagai sudah dibaca?");

  // Jika user klik No/Cancel, hentikan proses di sini
  if (!confirmed) return;

  // Update UI lokal (Optimistic Update)
  notifications.value.forEach((n) => (n.read_at = new Date().toISOString()));

  try {
    await markAllNotificationsRead(token.value);

    // PERBAIKAN 2: Beritahu Navbar untuk refresh jumlah notif
    window.dispatchEvent(new Event("notification-updated"));
  } catch (err) {
    console.error(err);
    // Opsional: Jika gagal, kembalikan status read_at ke null (rollback)
  }
};

// Style Helper (sama dengan dropdown tapi disesuaikan)
const getBgClass = (notif) => {
  if (notif.read_at) return "bg-[#1e1e1e]/50 opacity-70 border-l-4 border-transparent";
  if (notif.data.type === "alert") return "bg-red-900/20 border-l-4 border-red-500";
  return "bg-[#9a203e]/10 border-l-4 border-[#9a203e]";
};

onMounted(() => {
  fetchNotifs(1);
});
</script>

<template>
  <div class="max-w-4xl mx-auto p-6 min-h-screen text-[#e5e5e5]">
    <div class="flex justify-between items-center mb-8 pb-4 border-b border-[#4b1a1a]">
      <div>
        <h1 class="text-2xl font-bold">Semua Notifikasi</h1>
        <p class="text-sm text-gray-400 mt-1">Riwayat aktivitas dan pemberitahuan Anda.</p>
      </div>
      <button
        @click="handleMarkAllRead"
        class="px-4 py-2 text-xs font-medium bg-[#2c0f0f] border border-[#4b1a1a] rounded-lg hover:bg-[#4b1a1a] hover:text-white transition text-red-400">
        ✓ Tandai Semua Dibaca
      </button>
    </div>

    <div v-if="loading" class="py-20 text-center">
      <div class="animate-spin w-8 h-8 border-2 border-red-500 border-t-transparent rounded-full mx-auto mb-4"></div>
      <p class="text-gray-500 text-sm">Memuat notifikasi...</p>
    </div>

    <div v-else-if="notifications.length === 0" class="py-20 text-center bg-[#1e1e1e] rounded-xl border border-[#333]">
      <span class="text-4xl block mb-2">📭</span>
      <p class="text-gray-400">Tidak ada notifikasi untuk ditampilkan.</p>
    </div>

    <div v-else class="space-y-3">
      <div
        v-for="notif in notifications"
        :key="notif.id"
        @click="handleMarkRead(notif.id)"
        :class="[
          'p-5 rounded-lg border border-white/5 cursor-pointer transition hover:scale-[1.01]',
          getBgClass(notif),
        ]">
        <div class="flex gap-4">
          <div class="shrink-0 pt-1">
            <div
              v-if="notif.data.type === 'alert'"
              class="flex items-center justify-center w-10 h-10 rounded-full bg-red-500/20 text-red-500 border border-red-500/50">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2">
                <path d="M18 6 6 18" />
                <path d="m6 6 12 12" />
              </svg>
            </div>
            <div
              v-else
              :class="!notif.read_at ? 'bg-[#9a203e] text-white' : 'bg-gray-700 text-gray-400'"
              class="flex items-center justify-center w-10 h-10 rounded-full transition-colors">
              {{ !notif.read_at ? "📢" : "✓" }}
            </div>
          </div>

          <div class="flex-1">
            <div class="flex justify-between items-start">
              <h3 :class="!notif.read_at ? 'text-white font-bold' : 'text-gray-400 font-medium'" class="text-base">
                {{ notif.data.title || "Info Baru" }}
              </h3>
              <span class="text-xs text-gray-500 whitespace-nowrap ml-2">
                {{ formatTime(notif.created_at) }}
              </span>
            </div>

            <p :class="!notif.read_at ? 'text-gray-200' : 'text-gray-500'" class="text-sm mt-1 leading-relaxed">
              {{ notif.data.message }}
            </p>

            <div v-if="notif.data.reason" class="mt-3 p-3 bg-black/20 rounded border border-red-500/10 inline-block">
              <p class="text-xs text-red-300">
                <span class="font-bold uppercase text-[10px] tracking-wide opacity-70 mr-1">Alasan:</span>
                {{ notif.data.reason }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div
      v-if="!loading && notifications.length > 0"
      class="flex flex-wrap justify-center items-center gap-2 sm:gap-4 mt-8 pb-10">
      <button
        :disabled="!pagination.prev_page_url"
        @click="fetchNotifs(pagination.current_page - 1)"
        class="flex items-center justify-center gap-2 px-3 py-2 bg-[#2c0f0f] border border-[#4b1a1a] rounded text-sm text-gray-300 hover:bg-[#4b1a1a] disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap transition-colors">
        <span>&larr;</span>
        <span>Sebelumnya</span>
      </button>

      <span class="text-xs text-gray-500 mx-2 text-center">
        Hal {{ pagination.current_page }} / {{ pagination.last_page }}
      </span>

      <button
        :disabled="!pagination.next_page_url"
        @click="fetchNotifs(pagination.current_page + 1)"
        class="flex items-center justify-center gap-2 px-3 py-2 bg-[#2c0f0f] border border-[#4b1a1a] rounded text-sm text-gray-300 hover:bg-[#4b1a1a] disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap transition-colors">
        <span>Berikutnya</span>
        <span>&rarr;</span>
      </button>
    </div>
  </div>
</template>
