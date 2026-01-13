<script setup>
import { onBeforeMount, ref, computed, onMounted, onUnmounted } from "vue";
import { useRoute } from "vue-router";
import { userState, getAvatarUrl } from "../../../lib/store";
import { userDetail } from "../../../lib/api/UserApi";
import { getUnreadCount } from "../../../lib/api/NotificationApi"; // Import API Notif
import NotificationDropdown from "./NotificationDropdown.vue"; // Import Komponen Dropdown
import { useLocalStorage } from "@vueuse/core";
import { alertError } from "../../../lib/alert";

const route = useRoute();
const token = useLocalStorage("token", "");
const showDropdown = ref(false);

// State untuk notifikasi
const showNotif = ref(false);
const unreadCount = ref(0);

// Tentukan halaman mana menu navigasi TIDAK boleh muncul
const showNavigation = computed(() => {
  const hiddenRoutes = ["/dashboard/users/profile", "/dashboard/users/logout"];
  return !hiddenRoutes.includes(route.path);
});

async function fetchUser() {
  const response = await userDetail(token.value);
  const responseBody = await response.json();
  console.log(responseBody);

  if (response.ok) {
    userState.value = responseBody.data;
  } else {
    const pesanError = responseBody.errors ? Object.values(responseBody.errors)[0][0] : responseBody.message;
    await alertError(pesanError);
  }
}

// Fungsi ambil jumlah notif belum dibaca
async function fetchNotifCount() {
  if (!token.value) return;
  try {
    const res = await getUnreadCount(token.value);
    const json = await res.json();
    if (res.ok) {
      unreadCount.value = json.count || 0;
    }
  } catch (e) {
    console.error(e);
  }
}

// Fungsi update badge saat notif dibaca
const decrementCount = () => {
  if (unreadCount.value > 0) unreadCount.value--;
};

const resetCount = () => {
  unreadCount.value = 0;
};

const toggleDropdown = () => {
  showDropdown.value = !showDropdown.value;
  if (showDropdown.value) showNotif.value = false; // Tutup notif jika buka profil
};

const toggleNotif = () => {
  showNotif.value = !showNotif.value;
  if (showNotif.value) showDropdown.value = false; // Tutup profil jika buka notif
};

const closeDropdown = () => {
  showDropdown.value = false;
};

onBeforeMount(async () => {
  await fetchUser();
  await fetchNotifCount();
});

onMounted(() => {
  // Saat ada komponen lain (seperti AllNotification) yang request update, jalankan fetchNotifCount
  window.addEventListener("notification-updated", fetchNotifCount);
});

// Bersihkan listener saat komponen dihancurkan agar tidak memori leak
onUnmounted(() => {
  window.removeEventListener("notification-updated", fetchNotifCount);
});
</script>

<template>
  <div
    class="bg-[#180808] px-4 py-3 flex items-center justify-between sticky z-50 top-0 border-b border-[#2c2021]/80 shadow-sm backdrop-blur-md">
    <RouterLink to="/dashboard" data-title="Resonate" class="tooltip-container no-underline">
      <h1
        class="text-[#9a203e] text-[18px] sm:text-[24px] font-bold tracking-tight hover:text-[#b92b4a] transition-colors">
        Resonate
      </h1>
    </RouterLink>

    <div
      v-if="showNavigation"
      class="hidden md:flex absolute left-1/2 transform -translate-x-1/2 gap-1 bg-[#0f0505]/80 backdrop-blur-sm p-1.5 rounded-xl border border-[#2c2021]">
      <RouterLink
        to="/dashboard/global"
        data-title="Jelajahi"
        active-class="bg-[#9a203e] text-white shadow-md"
        class="tooltip-container-mid px-5 py-2 text-[14px] font-medium text-[#cdcccc] rounded-lg hover:text-white hover:bg-[#2c2021] transition-all duration-300">
        Jelajahi
      </RouterLink>
      <RouterLink
        to="/dashboard"
        exact-active-class="bg-[#9a203e] text-white shadow-md"
        data-title="Pesan saya"
        class="tooltip-container-mid px-5 py-2 text-[14px] font-medium text-[#cdcccc] rounded-lg hover:text-white hover:bg-[#2c2021] transition-all duration-300">
        Pesan Saya
      </RouterLink>
    </div>

    <div class="flex items-center gap-3 sm:gap-4">
      <div class="relative">
        <button
          @click="toggleNotif"
          class="relative p-2 rounded-full hover:bg-[#2c2021] transition-all duration-200 text-[#cdcccc] hover:text-white active:scale-95 group">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="22"
            height="22"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="group-hover:stroke-red-400 transition-colors">
            <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path>
            <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path>
          </svg>

          <span
            v-if="unreadCount > 0"
            class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-600 text-[9px] font-bold text-white ring-2 ring-[#180808]">
            {{ unreadCount > 9 ? "9+" : unreadCount }}
          </span>
        </button>

        <div v-if="showNotif">
          <div class="fixed inset-0 z-40 cursor-default" @click="showNotif = false"></div>
          <NotificationDropdown @read="decrementCount" @read-all="resetCount" />
        </div>
      </div>

      <div class="relative">
        <div
          class="tooltip-container cursor-pointer rounded-full ring-2 ring-transparent hover:ring-[#4b1a1a] transition-all"
          :data-title="userState.name"
          @click="toggleDropdown">
          <img
            :src="userState.avatar ? getAvatarUrl(userState.avatar) : userState.photo_url"
            alt="me"
            class="w-[36px] h-[36px] sm:w-[40px] sm:h-[40px] rounded-full object-cover block border border-[#2c2021] shadow-sm" />
        </div>

        <Transition
          enter-active-class="transition ease-out duration-200"
          enter-from-class="opacity-0 translate-y-2 scale-95"
          enter-to-class="opacity-100 translate-y-0 scale-100"
          leave-active-class="transition ease-in duration-150"
          leave-from-class="opacity-100 translate-y-0 scale-100"
          leave-to-class="opacity-0 translate-y-2 scale-95">
          <div
            v-if="showDropdown"
            class="absolute right-0 mt-3 w-[200px] rounded-xl bg-[#1e1e1e] p-1.5 z-50 shadow-2xl border border-[#4b1a1a] ring-1 ring-white/5 origin-top-right">
            <RouterLink
              to="/dashboard/users/profile"
              class="flex items-center gap-3 px-3 py-2.5 text-sm text-[#e5e5e5] font-medium rounded-lg hover:bg-[#2c2021] hover:text-white transition-colors"
              @click="closeDropdown">
              <span>👤</span>
              Edit Profil
            </RouterLink>

            <div v-if="userState.role === 'admin'">
              <div class="h-px bg-[#4b1a1a] mx-2 my-1"></div>
              <RouterLink
                to="/dashboard/admin"
                class="flex items-center gap-3 px-3 py-2.5 text-sm text-red-400 font-bold rounded-lg hover:bg-[#2c0f0f] hover:text-red-300 transition-colors"
                @click="closeDropdown">
                <span>⚡</span>
                Admin Panel
              </RouterLink>
            </div>

            <div class="h-px bg-[#4b1a1a] mx-2 my-1"></div>

            <RouterLink
              to="/dashboard/users/logout"
              class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-400 font-medium rounded-lg hover:bg-[#2c2021] hover:text-red-400 transition-colors"
              @click="closeDropdown">
              <span>🚪</span>
              Log out
            </RouterLink>
          </div>
        </Transition>
      </div>
    </div>
  </div>

  <div
    v-if="showNavigation"
    class="md:hidden fixed bottom-6 left-1/2 transform -translate-x-1/2 z-[60] w-[90%] max-w-[320px] bg-[#180808]/80 backdrop-blur-xl p-1.5 rounded-full border border-[#2c2021] shadow-2xl ring-1 ring-white/5 transition-transform duration-300">
    <div class="grid grid-cols-2 gap-1">
      <RouterLink
        to="/dashboard/global"
        active-class="bg-[#9a203e] text-white shadow-lg"
        class="flex items-center justify-center px-4 py-3 text-[13px] font-medium text-[#cdcccc] rounded-full hover:text-white transition-all duration-300 whitespace-nowrap">
        Jelajahi
      </RouterLink>

      <RouterLink
        to="/dashboard"
        exact-active-class="bg-[#9a203e] text-white shadow-lg"
        class="flex items-center justify-center px-4 py-3 text-[13px] font-medium text-[#cdcccc] rounded-full hover:text-white transition-all duration-300 whitespace-nowrap">
        Pesan Saya
      </RouterLink>
    </div>
  </div>
</template>

<style>
/* FIX: Menggunakan @media (hover: none) untuk mendeteksi perangkat layar sentuh.
  Jika perangkat tidak punya kursor (seperti HP/Tablet), maka tooltip disembunyikan.
*/
@media (hover: none) {
  .tooltip-container::before,
  .tooltip-container::after,
  .tooltip-container-mid::before,
  .tooltip-container-mid::after {
    display: none !important;
    content: none !important;
  }
}
</style>
