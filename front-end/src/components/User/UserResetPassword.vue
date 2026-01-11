<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { resetPassword } from "../../lib/api/UserApi";
import { alertError, alertSuccess } from "../../lib/alert";

const route = useRoute();
const router = useRouter();
const showPassword = ref(false);
const showConfirmPassword = ref(false);

const form = ref({
  token: "",
  email: "",
  password: "",
  password_confirmation: "",
});

onMounted(() => {
  form.value.token = route.query.token;
  form.value.email = route.query.email;
});

async function handleReset() {
  try {
    const response = await resetPassword(form.value);
    const result = await response.json();

    if (response.ok) {
      await alertSuccess("Password berhasil direset. Silakan login.");
      router.push("/login");
    } else {
      await alertError(result.message || "Gagal mereset password.");
    }
  } catch (error) {
    await alertError("Terjadi kesalahan sistem.");
  }
}
</script>

<template>
  <div class="flex justify-center items-center min-h-screen text-[#e5e5e5] font-jakarta -mt-16 md:-mt-16">
    <div class="bg-[#1c1516] rounded-[20px] w-full max-w-[560px] mx-[24px] p-[2em] border border-[#2b2122] shadow-2xl">
      <h1 class="text-center text-[#9a203e] font-bold text-3xl mb-8">Atur Ulang Password</h1>

      <form @submit.prevent="handleReset" class="space-y-5">
        <div class="px-4 py-3 text-sm text-[#8c8a8a] bg-[#2b2122] rounded-xl border border-[#9a203e]/30">
          Reset password untuk:
          <strong class="text-[#e5e5e5] block mt-1">{{ form.email }}</strong>
        </div>

        <div>
          <label class="block text-xs font-bold text-[#8c8a8a] uppercase tracking-wider mb-2">Password Baru</label>
          <div class="relative">
            <input
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              required
              placeholder="Masukkan password baru"
              class="w-full px-4 py-3 bg-[#2b2122] text-[#e5e5e5] rounded-[12px] focus:outline-none focus:ring-2 focus:ring-[#9a203e] placeholder-[#555] pr-10 transition-all" />

            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute inset-y-0 right-0 px-3 flex items-center text-[#8c8a8a] hover:text-[#9a203e] transition-colors cursor-pointer"
              title="Lihat Password">
              <svg
                v-if="showPassword"
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
              <svg
                v-else
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round">
                <path
                  d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
              </svg>
            </button>
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-[#8c8a8a] uppercase tracking-wider mb-2">
            Konfirmasi Password Baru
          </label>
          <div class="relative">
            <input
              v-model="form.password_confirmation"
              :type="showConfirmPassword ? 'text' : 'password'"
              required
              placeholder="Ulangi password baru"
              class="w-full px-4 py-3 bg-[#2b2122] text-[#e5e5e5] rounded-[12px] focus:outline-none focus:ring-2 focus:ring-[#9a203e] placeholder-[#555] pr-10 transition-all" />

            <button
              type="button"
              @click="showConfirmPassword = !showConfirmPassword"
              class="absolute inset-y-0 right-0 px-3 flex items-center text-[#8c8a8a] hover:text-[#9a203e] transition-colors cursor-pointer"
              title="Lihat Password">
              <svg
                v-if="showConfirmPassword"
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
              <svg
                v-else
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round">
                <path
                  d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
              </svg>
            </button>
          </div>
        </div>

        <div class="pt-4">
          <button
            type="submit"
            class="w-full bg-[#9a203e] hover:bg-[#821c35] text-white font-bold py-3.5 rounded-[12px] transition-transform active:scale-95 shadow-lg shadow-[#9a203e]/20 cursor-pointer">
            Perbarui Password
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
