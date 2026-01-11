<script setup>
import { ref } from "vue";
import { forgotPassword } from "../../lib/api/UserApi";
import { alertError, alertSuccess } from "../../lib/alert";

const email = ref("");
const isLoading = ref(false);

async function handleSubmit() {
  isLoading.value = true;
  try {
    const response = await forgotPassword(email.value);
    const result = await response.json();

    if (response.ok) {
      await alertSuccess("Link reset password telah dikirim ke email Anda.");
      email.value = "";
    } else {
      await alertError(result.message || "Gagal mengirim link reset.");
    }
  } catch (error) {
    await alertError("Terjadi kesalahan sistem.");
  } finally {
    isLoading.value = false;
  }
}
</script>

<template>
  <div class="flex justify-center items-center min-h-screen text-[#e5e5e5] -mt-16 font-jakarta">
    <div class="bg-[#1c1516] rounded-[20px] w-full max-w-[560px] mx-[24px] p-[1em] shadow-2xl border border-[#2b2122]">
      <h1 class="text-center text-[#9a203e] font-bold text-3xl mb-2">Lupa Password</h1>
      <p class="text-center text-[#8c8a8a] text-sm mb-8">Masukkan emailmu untuk menerima link reset password.</p>

      <form @submit.prevent="handleSubmit">
        <div class="mb-6">
          <label class="block mb-2 text-sm font-medium">Alamat Email</label>
          <input
            type="email"
            v-model="email"
            required
            placeholder="nama@email.com"
            class="w-full bg-[#2b2122] text-[#e5e5e5] rounded-[15px] p-[1em] border-none focus:outline-[2px] focus:outline-[#9a203e]" />
        </div>

        <button
          type="submit"
          :disabled="isLoading"
          class="w-full bg-[#9a203e] text-white font-bold py-4 rounded-[15px] hover:bg-[#7d1a33] transition-all disabled:opacity-50">
          {{ isLoading ? "Mengirim..." : "Kirim Link Reset" }}
        </button>

        <div class="mt-6 text-center">
          <RouterLink to="/login" class="text-sm text-[#8c8a8a] hover:text-[#9a203e] transition-colors">
            Kembali ke Login
          </RouterLink>
        </div>
      </form>
    </div>
  </div>
</template>
