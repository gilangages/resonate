<script setup lang="ts">
import { onBeforeMount, ref, computed } from "vue";
import { useRoute } from "vue-router";
import { userState, getAvatarUrl } from "../../../lib/store";
import { userDetail } from "../../../lib/api/UserApi";
import { useLocalStorage } from "@vueuse/core";
import { alertError } from "../../../lib/alert";

const route = useRoute();
const token = useLocalStorage("token", "");
const showDropdown = ref(false);

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

const toggleDropdown = () => {
  showDropdown.value = !showDropdown.value;
};

const closeDropdown = () => {
  showDropdown.value = false;
};

onBeforeMount(async () => {
  await fetchUser();
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
        active-class="bg-[#9a203e] text-white"
        class="px-4 py-2 text-[14px] font-medium text-[#cdcccc] rounded-lg hover:text-white transition-all duration-300">
        Jelajahi
      </RouterLink>
      <RouterLink
        to="/dashboard"
        exact-active-class="bg-[#9a203e] text-white"
        class="px-4 py-2 text-[14px] font-medium text-[#cdcccc] rounded-lg hover:text-white transition-all duration-300">
        Pesan Saya
      </RouterLink>
    </div>

    <div class="relative mr-[1em]">
      <div class="tooltip-container cursor-pointer rounded-full" :data-title="userState.name" @click="toggleDropdown">
        <img :src="getAvatarUrl(userState.avatar)" alt="me" class="w-[40px] h-[40px] rounded-full object-cover block" />
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

        <hr class="border-[#4b1a1a] my-[4px]" />

        <RouterLink
          to="/dashboard/users/logout"
          class="block p-[6px] text-[#e5e5e5] font-medium rounded-[10px] hover:bg-[#4b1a1a]">
          Log out
        </RouterLink>
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
