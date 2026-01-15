<script setup>
import { reactive, ref } from "vue"; // Tambahkan import ref
import { userLogin } from "../../lib/api/UserApi";
import { useRouter } from "vue-router";
import { alertError, alertSuccess } from "../../lib/alert"; // Import alertSuccess jika ingin notif selamat datang
import { useLocalStorage } from "@vueuse/core";

const router = useRouter();
const token = useLocalStorage("token", "");
const user = reactive({
  email: "",
  password: "",
});

// --- STATE TOGGLE PASSWORD ---
const showPassword = ref(false);

async function handleSubmit() {
  const response = await userLogin(user);
  const responseBody = await response.json();
  console.log(responseBody);

  if (response.ok) {
    token.value = responseBody.token;
    await router.push({
      path: "/dashboard/global",
    });
  } else {
    const pesanError = responseBody.errors ? Object.values(responseBody.errors)[0][0] : responseBody.message;
    await alertError(pesanError);
  }
}
</script>

<template>
  <div class="flex justify-center items-center min-h-screen -mt-[2.8em] sm:-mt-[2em]">
    <div
      class="bg-[#1c1516] text-[14px] text-[#e5e5e5] rounded-[30px] w-full max-w-[420px] px-3 py-3 mx-6 sm:max-w-[560px] sm:mx-0">
      <div class="flex flex-col items-center text-[#9a203e]">
        <h1 class="text-[24px] font-bold">Masuk</h1>
        <p class="text-[12px] text-[#8c8a8a]">Masuk ke akunmu</p>
      </div>

      <form v-on:submit.prevent="handleSubmit" class="mt-4">
        <div class="m-[14px]">
          <label for="email">Email</label>
          <input
            v-model="user.email"
            id="email"
            type="email"
            required
            placeholder="Masukkan alamat email"
            class="mt-1 w-full rounded-[10px] bg-[#2b2122] px-4 py-[1em] text-[#e5e5e5] focus:outline-none focus:ring-2 focus:ring-[#9a203e] caret-[#e5e5e5]" />
        </div>

        <div class="m-[14px]">
          <label for="password">Password</label>
          <div class="relative mt-1">
            <input
              v-model="user.password"
              id="password"
              required
              :type="showPassword ? 'text' : 'password'"
              placeholder="Masukkan kata sandi"
              class="w-full rounded-[10px] bg-[#2b2122] px-4 py-[1em] text-[#e5e5e5] focus:outline-none focus:ring-2 focus:ring-[#9a203e] caret-[#e5e5e5] pr-10" />

            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute inset-y-0 right-0 px-3 flex items-center text-[#8c8a8a] hover:text-[#9a203e] transition-colors cursor-pointer focus:outline-none"
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

        <div class="px-4 mt-8">
          <button
            type="submit"
            class="cursor-pointer w-full bg-[#9a203e] hover:bg-[#7d1a33] text-[#e5e5e5] font-bold py-3 rounded-[15px]">
            Masuk
          </button>
        </div>
      </form>

      <div class="text-center mt-4">
        <p class="text-[#8c8a8a]">
          Belum punya akun?
          <RouterLink to="/register" class="font-semibold text-[#9a203e] hover:underline">Daftar</RouterLink>
        </p>
      </div>
    </div>
  </div>
</template>
<style scoped></style>
