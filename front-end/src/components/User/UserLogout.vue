<script setup>
import { useLocalStorage } from "@vueuse/core";
import { userLogout } from "../../lib/api/UserApi";
import { useRouter } from "vue-router";
import { onBeforeMount } from "vue";
import { alertError } from "../../lib/alert";
// 1. Import 'store' untuk mengosongkan data user
import { store, resetUserState } from "../../lib/store";
import { useCardTheme } from "../../lib/useCardTheme";

const token = useLocalStorage("token", "");
const router = useRouter();
// 2. Gunakan setThemeLocally untuk reset warna ke merah
const { setThemeLocally } = useCardTheme();

async function handleLogout() {
  try {
    const response = await userLogout(token.value);

    // Kita tetap bersihkan data di client meskipun API logout gagal/error (force logout)
    token.value = "";
    sessionStorage.clear();

    // 3. Update Store: Set user jadi null (ini otomatis hapus localStorage 'user')
    store.setUser(null);

    // 4. Reset UserState lama (jika kamu masih pakai userState ref)
    resetUserState();

    // 5. Reset Tema ke Merah (Default)
    setThemeLocally("red");

    await router.push("/login");
  } catch (err) {
    console.error("Logout Error:", err);
    // Jika error jaringan, tetap paksa keluar di sisi client
    token.value = "";
    store.setUser(null);
    setThemeLocally("red");
    router.push("/login");
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
