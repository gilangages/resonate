<script setup lang="ts">
import { onBeforeMount, ref } from "vue";
import { userState, getAvatarUrl } from "../../../lib/store";
import { userDetail } from "../../../lib/api/UserApi";
import { useLocalStorage } from "@vueuse/core";
import { alertError } from "../../../lib/alert";

const token = useLocalStorage("token", "");
const showDropdown = ref(false);

async function fetchUser() {
  const response = await userDetail(token.value);
  const responseBody = await response.json();

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
  <div class="bg-[#180808] p-[12px] flex items-center justify-between relative sticky z-50 top-0">
    <RouterLink to="/dashboard" data-title="Resonate" class="tooltip-container no-underline">
      <h1 class="text-[#9a203e] text-[16px] ml-[1em] font-bold sm:text-[24px]">Resonate</h1>
    </RouterLink>

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
</template>

<style scoped></style>
