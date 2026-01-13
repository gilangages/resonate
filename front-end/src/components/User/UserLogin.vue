<script setup>
import { reactive } from "vue";
import { userLogin } from "../../lib/api/UserApi";
import { useRouter } from "vue-router";
import { alertError } from "../../lib/alert";
import { useLocalStorage } from "@vueuse/core";

const router = useRouter();
const token = useLocalStorage("token", "");
const user = reactive({
  email: "",
  password: "",
});

async function handleSubmit() {
  const response = await userLogin(user);
  const responseBody = await response.json();
  console.log(responseBody);

  if (response.ok) {
    token.value = responseBody.token;
    await router.push({
      path: "/dashboard",
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
      <!-- TITLE -->
      <div class="flex flex-col items-center text-[#9a203e]">
        <h1 class="text-[24px] font-bold">Masuk</h1>
        <p class="text-[12px] text-[#8c8a8a]">Masuk ke akunmu</p>
      </div>

      <!-- FORM -->
      <form v-on:submit.prevent="handleSubmit" class="mt-4">
        <!-- EMAIL -->
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

        <!-- PASSWORD -->
        <div class="m-[14px]">
          <label for="password">Password</label>
          <input
            v-model="user.password"
            id="password"
            required
            type="password"
            placeholder="Masukkan kata sandi"
            class="mt-1 w-full rounded-[10px] bg-[#2b2122] px-4 py-[1em] text-[#e5e5e5] focus:outline-none focus:ring-2 focus:ring-[#9a203e] caret-[#e5e5e5]" />
        </div>

        <!-- BUTTON -->
        <div class="px-4 mt-8">
          <button
            type="submit"
            class="cursor-pointer w-full bg-[#9a203e] hover:bg-[#7d1a33] text-[#e5e5e5] font-bold py-3 rounded-[15px]">
            Masuk
          </button>
        </div>
      </form>

      <!-- REGISTER -->
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
