<script setup>
import { onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useLocalStorage } from "@vueuse/core";

const router = useRouter();
const route = useRoute();
const tokenStorage = useLocalStorage("token", "");

onMounted(() => {
  const token = route.query.token;
  const name = route.query.name;
  const error = route.query.error;

  if (token) {
    // Simpan token
    tokenStorage.value = token;

    // (Opsional) Simpan nama sementara, nanti akan di-refresh oleh API UserDetail
    console.log("Login Google Sukses:", name);

    // Redirect ke dashboard
    // Gunakan window.location agar state benar-benar fresh atau router.push
    window.location.href = "/dashboard/global";
  } else if (error) {
    alert("Login Gagal: " + error);
    router.push("/login");
  } else {
    router.push("/login");
  }
});
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-[#0f0505] text-white">
    <div class="flex flex-col items-center gap-4">
      <div class="w-10 h-10 border-4 border-[#9a203e] border-t-transparent rounded-full animate-spin"></div>
      <p>Memproses login...</p>
    </div>
  </div>
</template>
