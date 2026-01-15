<script setup>
import { reactive, ref } from "vue";
import { userRegister } from "../../lib/api/UserApi";
import { alertError, alertSuccess } from "../../lib/alert";
import { useRouter } from "vue-router";

const router = useRouter();
const user = reactive({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

// State untuk toggle visibility
const showPassword = ref(false);
const showConfirmPassword = ref(false);

async function handleSubmit() {
  if (user.password !== user.password_confirmation) {
    await alertError("Password harus sama!");
    return;
  }

  const response = await userRegister(user);
  const responseBody = await response.json();
  console.log(responseBody);

  if (response.ok) {
    await alertSuccess("Akun berhasil dibuat! Silakan masuk.");
    await router.push({
      path: "/login",
    });
  } else {
    const pesanError = responseBody.errors ? Object.values(responseBody.errors)[0][0] : responseBody.message;
    await alertError(pesanError);
  }
}
</script>

<template>
  <div class="flex justify-center items-center min-h-screen -mt-[2.8em] sm:-mt-[3.5em] lg:-mt-[2.5em]">
    <div
      class="bg-[#1c1516] text-[#e5e5e5] text-[14px] rounded-[30px] w-full max-w-[420px] px-3 py-6 mx-6 sm:max-w-[560px] sm:mx-0">
      <div class="flex flex-col items-center text-[#9a203e] mt-[-1em]">
        <h1 class="text-[24px] font-bold leading-tight">Daftar</h1>
        <p class="mt-1 text-[12px] text-[#8c8a8a] leading-normal">Daftar akun Resonate</p>
      </div>

      <form v-on:submit.prevent="handleSubmit">
        <div class="mt-2">
          <div class="m-[14px]">
            <label>Nama</label>
            <br />
            <input
              type="text"
              v-model="user.name"
              placeholder="Masukkan nama"
              class="w-full mt-1 px-[1em] py-[1em] bg-[#2b2122] text-[#e5e5e5] rounded-[10px] caret-[#e5e5e5] focus:outline focus:outline-2 focus:outline-[#9a203e]" />
          </div>

          <div class="m-[14px]">
            <label>Email</label>
            <br />
            <input
              v-model="user.email"
              type="email"
              required
              placeholder="nama@gmail.com"
              class="w-full mt-1 px-[1em] py-[1em] bg-[#2b2122] text-[#e5e5e5] rounded-[10px] caret-[#e5e5e5] focus:outline focus:outline-2 focus:outline-[#9a203e]" />
          </div>

          <div class="m-[14px]">
            <label>Password</label>
            <div class="relative mt-1">
              <input
                v-model="user.password"
                :type="showPassword ? 'text' : 'password'"
                required
                placeholder="Minimal 8 karakter"
                class="w-full px-[1em] py-[1em] bg-[#2b2122] text-[#e5e5e5] rounded-[10px] caret-[#e5e5e5] focus:outline focus:outline-2 focus:outline-[#9a203e] pr-10" />

              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 px-3 flex items-center text-[#8c8a8a] hover:text-[#9a203e] focus:outline-none cursor-pointer transition-colors">
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

          <div class="m-[14px]">
            <label>Konfirmasi Password</label>
            <div class="relative mt-1">
              <input
                v-model="user.password_confirmation"
                :type="showConfirmPassword ? 'text' : 'password'"
                required
                placeholder="Masukkan ulang kata sandi"
                class="w-full px-[1em] py-[1em] bg-[#2b2122] text-[#e5e5e5] rounded-[10px] caret-[#e5e5e5] focus:outline focus:outline-2 focus:outline-[#9a203e] pr-10" />

              <button
                type="button"
                @click="showConfirmPassword = !showConfirmPassword"
                class="absolute inset-y-0 right-0 px-3 flex items-center text-[#8c8a8a] hover:text-[#9a203e] focus:outline-none cursor-pointer transition-colors">
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
        </div>

        <div class="px-[1em] mt-[2em] w-full">
          <button
            type="submit"
            class="cursor-pointer block w-full text-center bg-[#9a203e] hover:bg-[#7d1a33] text-[#e5e5e5] font-bold p-[12px] rounded-[15px]">
            Daftar
          </button>
        </div>
      </form>

      <div class="text-center mt-4">
        <p class="text-[#8c8a8a]">
          Sudah punya akun?
          <RouterLink to="/login" class="text-[#9a203e] font-semibold hover:underline">Masuk</RouterLink>
        </p>
      </div>
    </div>
  </div>
</template>

<style scoped></style>
