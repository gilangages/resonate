<script setup>
import { reactive } from "vue";
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

async function handleSubmit() {
  if (user.password !== user.password_confirmation) {
    await alertError("Password do not match");
    return;
  }

  const response = await userRegister(user);
  const responseBody = await response.json();
  console.log(responseBody);

  if (response.ok) {
    await alertSuccess("User created successfully");
    await router.push({
      path: "/login",
    });
  } else {
    // INI LOGIKANYA:
    // 1. Cek apa ada field 'errors' (validasi)?
    // 2. Kalau ada, ambil value pertama, lalu ambil pesan pertama array-nya.
    // 3. Kalau gak ada, ambil 'responseBody.message'.
    const pesanError = responseBody.errors ? Object.values(responseBody.errors)[0][0] : responseBody.message;

    await alertError(pesanError);
  }
}
</script>
<template>
  <!-- FORM CONTAINER -->
  <div class="flex justify-center items-center min-h-screen -mt-[2.8em] sm:-mt-[3.5em] lg:-mt-[2.5em]">
    <div
      class="bg-[#1c1516] text-[#e5e5e5] text-[14px] rounded-[30px] w-full max-w-[420px] px-3 py-6 mx-6 sm:max-w-[560px] sm:mx-0">
      <!-- TITLE -->
      <div class="flex flex-col items-center text-[#9a203e] mt-[-1em]">
        <h1 class="text-[24px] font-bold leading-tight">Daftar</h1>
        <p class="mt-1 text-[12px] text-[#8c8a8a] leading-normal">Daftar akun Resonate</p>
      </div>

      <!-- FORM -->
      <form v-on:submit.prevent="handleSubmit">
        <div class="mt-2">
          <!-- Nama -->
          <div class="m-[14px]">
            <label>Nama</label>
            <br />
            <input
              type="text"
              v-model="user.name"
              placeholder="Masukkan nama"
              class="w-full mt-1 px-[1em] py-[1em] bg-[#2b2122] text-[#e5e5e5] rounded-[10px] caret-[#e5e5e5] focus:outline focus:outline-2 focus:outline-[#9a203e]" />
          </div>

          <!-- Email -->
          <div class="m-[14px]">
            <label>Email</label>
            <br />
            <input
              v-model="user.email"
              type="email"
              placeholder="nama@gmail.com"
              class="w-full mt-1 px-[1em] py-[1em] bg-[#2b2122] text-[#e5e5e5] rounded-[10px] caret-[#e5e5e5] focus:outline focus:outline-2 focus:outline-[#9a203e]" />
          </div>

          <!-- Password -->
          <div class="m-[14px]">
            <label>Password</label>
            <br />
            <input
              v-model="user.password"
              type="password"
              placeholder="Minimal 8 karakter"
              class="w-full mt-1 px-[1em] py-[1em] bg-[#2b2122] text-[#e5e5e5] rounded-[10px] caret-[#e5e5e5] focus:outline focus:outline-2 focus:outline-[#9a203e]" />
          </div>

          <!-- Konfirmasi Password -->
          <div class="m-[14px]">
            <label>Konfirmasi Password</label>
            <br />
            <input
              v-model="user.password_confirmation"
              type="password"
              placeholder="Masukkan ulang kata sandi"
              class="w-full mt-1 px-[1em] py-[1em] bg-[#2b2122] text-[#e5e5e5] rounded-[10px] caret-[#e5e5e5] focus:outline focus:outline-2 focus:outline-[#9a203e]" />
          </div>
        </div>

        <!-- BUTTON -->
        <div class="px-[1em] mt-[2em] w-full">
          <button
            type="submit"
            class="cursor-pointer block w-full text-center bg-[#9a203e] hover:bg-[#7d1a33] text-[#e5e5e5] font-bold p-[12px] rounded-[15px]">
            Daftar
          </button>
        </div>
      </form>

      <!-- LOGIN -->
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
