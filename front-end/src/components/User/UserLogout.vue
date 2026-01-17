<script setup>
import { useLocalStorage } from "@vueuse/core";
import { userLogout } from "../../lib/api/UserApi";
import { useRouter } from "vue-router";
import { onBeforeMount } from "vue";
import { alertError } from "../../lib/alert";
import { resetUserState } from "../../lib/store";

const token = useLocalStorage("token", "");
const router = useRouter();

async function handleLogout() {
  const response = await userLogout(token.value);
  const responseBody = await response.json();
  console.log(responseBody);

  if (response.ok) {
    token.value = "";
    sessionStorage.clear(); // Ini akan menghapus semua status animasi
    resetUserState();
    await router.push({
      path: "/login",
    });
  } else {
    const pesanError = responseBody.errors ? Object.values(responseBody.errors)[0][0] : responseBody.message;
    await alertError(pesanError);
  }
}

onBeforeMount(async () => {
  await handleLogout();
});
</script>
<template>
  <div class="flex flex-col items-center justify-center min-h-[60vh] text-white">
    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-[#9a203e] mb-4"></div>
    <p class="text-lg font-medium italic">Sedang mengeluarkan akun...</p>
  </div>
</template>
