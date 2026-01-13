<script setup>
import { onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useLocalStorage } from "@vueuse/core";
import { useCardTheme } from "../../lib/useCardTheme";
import { userDetail } from "../../lib/api/UserApi"; // 👈 Pastikan path ini benar

const router = useRouter();
const route = useRoute();
const tokenStorage = useLocalStorage("token", ""); // VueUse LocalStorage
const { initTheme } = useCardTheme();

onMounted(async () => {
  const token = route.query.token;
  const error = route.query.error;

  // Jika ada token di URL, berarti login Google sukses
  if (token) {
    try {
      // 1. Simpan Token ke LocalStorage
      tokenStorage.value = token;

      // 2. Fetch Data User Terbaru menggunakan endpoint 'userDetail'
      const response = await userDetail(token);
      const responseBody = await response.json();

      if (response.ok) {
        // Cek struktur response API kamu.
        // Biasanya user ada di responseBody.data atau responseBody.user
        // Kita coba ambil yang valid:
        const userData = responseBody.data || responseBody.user || responseBody;

        if (!userData || !userData.id) {
          throw new Error("Data user tidak valid.");
        }

        // 3. Simpan User ke Session Storage (PENTING untuk app state)
        sessionStorage.setItem("user", JSON.stringify(userData));

        // 4. ✅ LOAD TEMA SPESIFIK USER
        // Karena sekarang kita sudah punya ID-nya
        initTheme(userData.id);

        console.log("Login Google Sukses, Theme:", userData.id);

        // 5. Redirect ke Dashboard
        // Pakai window.location agar halaman refresh total & state benar-benar bersih
        window.location.href = "/dashboard/global";
      } else {
        throw new Error(responseBody.message || "Gagal verifikasi user.");
      }
    } catch (err) {
      console.error("Callback Error:", err);
      alert("Gagal memproses login Google: " + err.message);
      // Jika gagal, kembalikan ke login
      router.push("/login");
    }
  }
  // Jika URL mengandung error (misal user cancel google login)
  else if (error) {
    alert("Login Gagal: " + error);
    router.push("/login");
  }
  // Jika tidak ada apa-apa, tendang ke login
  else {
    router.push("/login");
  }
});
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-[#1c1516] text-white">
    <div class="flex flex-col items-center gap-4">
      <div class="w-10 h-10 border-4 border-[#9a203e] border-t-transparent rounded-full animate-spin"></div>
      <p class="text-[#e5e5e5] text-sm animate-pulse">Menyiapkan profil Anda...</p>
    </div>
  </div>
</template>
