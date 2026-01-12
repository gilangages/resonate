<script setup>
import { onBeforeMount, ref, computed, onMounted } from "vue";
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
    console.log(json);
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
  await fetchNotifCount(); // Ambil data notif saat load
});
</script>

<template>
  <div
    class="bg-[#180808] p-[12px] flex items-center justify-between relative sticky z-50 top-0 border-b border-[#2c2021]">
    <RouterLink to="/dashboard" data-title="Resonate" class="tooltip-container no-underline">
      <h1 class="text-[#9a203e] text-[16px] ml-[1em] font-bold sm:text-[24px]">Resonate</h1>
    </RouterLink>

    <div
      v-if="showNavigation"
      class="hidden md:flex absolute left-1/2 transform -translate-x-1/2 gap-1 bg-[#0f0505] p-1 rounded-xl border border-[#2c2021]">
      <RouterLink
        to="/dashboard/global"
        data-title="Jelajahi"
        active-class="bg-[#9a203e] text-white"
        class="tooltip-container-mid px-4 py-2 text-[14px] font-medium text-[#cdcccc] rounded-lg hover:text-white transition-all duration-300">
        Jelajahi
      </RouterLink>
      <RouterLink
        to="/dashboard"
        exact-active-class="bg-[#9a203e] text-white"
        data-title="Pesan saya"
        class="tooltip-container-mid px-4 py-2 text-[14px] font-medium text-[#cdcccc] rounded-lg hover:text-white transition-all duration-300">
        Pesan Saya
      </RouterLink>
    </div>

    <div class="flex items-center gap-4 mr-[1em]">
      <div class="relative">
        <button
          @click="toggleNotif"
          class="relative p-2 rounded-full hover:bg-[#2c2021] transition text-[#cdcccc] hover:text-white">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="22"
            height="22"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round">
            <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path>
            <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path>
          </svg>

          <span
            v-if="unreadCount > 0"
            class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full border border-[#180808]">
            {{ unreadCount }}
          </span>
        </button>

        <div v-if="showNotif">
          <div class="fixed inset-0 z-40 cursor-default" @click="showNotif = false"></div>
          <NotificationDropdown @read="decrementCount" @read-all="resetCount" />
        </div>
      </div>
      <div class="relative">
        <div class="tooltip-container cursor-pointer rounded-full" :data-title="userState.name" @click="toggleDropdown">
          <img
            :src="getAvatarUrl(userState.avatar) || getAvatarUrl(userState.photo_url)"
            alt="me"
            class="w-[40px] h-[40px] rounded-full object-cover block border border-[#2c2021]" />
        </div>

        <div
          v-if="showDropdown"
          class="absolute right-0 mt-[12px] w-[200px] rounded-[10px] bg-[#2c0f0f] p-[4px] z-50 shadow-xl border border-[#4b1a1a]">
          <RouterLink
            to="/dashboard/users/profile"
            class="block p-[6px] text-[#e5e5e5] font-medium rounded-[10px] hover:bg-[#4b1a1a]"
            @click="closeDropdown">
            Edit Profil
          </RouterLink>

          <div v-if="userState.role === 'admin'">
            <hr class="border-[#4b1a1a] my-[4px]" />
            <RouterLink
              to="/dashboard/admin"
              class="block p-[6px] text-red-400 font-bold rounded-[10px] hover:bg-[#4b1a1a] flex items-center gap-2"
              @click="closeDropdown">
              Admin Panel
              <span>⚡</span>
            </RouterLink>
          </div>

          <hr class="border-[#4b1a1a] my-[4px]" />

          <RouterLink
            to="/dashboard/users/logout"
            class="block p-[6px] text-[#e5e5e5] font-medium rounded-[10px] hover:bg-[#4b1a1a]">
            Log out
          </RouterLink>
        </div>
      </div>
    </div>
  </div>

  <div
    v-if="showNavigation"
    class="md:hidden fixed bottom-6 left-1/2 transform -translate-x-1/2 z-[60] w-[90%] max-w-[300px] bg-[#180808]/90 backdrop-blur-lg p-1.5 rounded-full border border-[#2c2021] shadow-2xl transition-transform duration-300">
    <div class="grid grid-cols-2 gap-1">
      <RouterLink
        to="/dashboard/global"
        active-class="bg-[#9a203e] text-white shadow-lg"
        class="flex items-center justify-center px-2 py-3 text-[13px] font-medium text-[#cdcccc] rounded-full hover:text-white transition-all duration-300 whitespace-nowrap">
        Jelajahi
      </RouterLink>

      <RouterLink
        to="/dashboard"
        exact-active-class="bg-[#9a203e] text-white shadow-lg"
        class="flex items-center justify-center px-2 py-3 text-[13px] font-medium text-[#cdcccc] rounded-full hover:text-white transition-all duration-300 whitespace-nowrap">
        Pesan Saya
      </RouterLink>
    </div>
  </div>
</template>
