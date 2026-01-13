<script setup>
import { useLocalStorage } from "@vueuse/core";
import { onBeforeMount, ref } from "vue";
import { userDetail, userUpdatePassword, userUpdateProfile } from "../../lib/api/UserApi";
import { alertError, alertSuccess } from "../../lib/alert";

const token = useLocalStorage("token", "");
const name = ref("");
const email = ref("");
const password = ref("");
const password_confirmation = ref("");

async function fetchUser() {
  const response = await userDetail(token.value);
  const responseBody = await response.json();
  console.log(responseBody);

  if (response.ok) {
    name.value = responseBody.data.name;
    email.value = responseBody.data.email;
  } else {
    const pesanError = responseBody.errors ? Object.values(responseBody.errors)[0][0] : responseBody.message;
    await alertError(pesanError);
  }
}

async function handleChangeName() {
  const response = await userUpdateProfile(token.value, { name: name.value });
  const responseBody = await response.json();
  console.log(responseBody);

  if (response.ok) {
    await alertSuccess("Profile updated successfully");
  } else {
    const pesanError = responseBody.errors ? Object.values(responseBody.errors)[0][0] : responseBody.message;
    await alertError(pesanError);
  }
}

async function handleChangePassword() {
  if (password.value !== password_confirmation) {
    await alertError("Password do not match");
    return;
  }

  const response = await userUpdatePassword(token.value, { password: password.value });
  const responseBody = await response.json();
  console.log(responseBody);

  if (response.ok) {
    password.value = "";
    password_confirmation.value = "";
    await alertSuccess("Password updated successfully");
  } else {
    const pesanError = responseBody.errors ? Object.values(responseBody.errors)[0][0] : responseBody.message;
    await alertError(pesanError);
  }
}

onBeforeMount(async () => {
  await fetchUser();
});
</script>
<template>
  <!-- FORM CONTAINER -->
  <div class="flex justify-center items-center min-h-screen text-[#e5e5e5]">
    <div
      class="bg-[#1c1516] rounded-[20px] w-full max-w-[420px] mx-[24px] p-[12px] flex flex-col sm:max-w-[600px] sm:m-[2em] sm:p-[2em]">
      <h1 class="text-center mt-[4px] text-[#9a203e] font-bold text-3xl">Edit Profile</h1>

      <!-- IMAGE -->
      <div class="flex flex-col items-center justify-center text-[#e5e5e5] py-6">
        <img src="../../assets/img/me.jpg" alt="me" class="w-[102px] h-[102px] rounded-full object-cover block" />
        <p class="py-2">Ganti Foto</p>
      </div>

      <!-- INFO DASAR -->
      <form v-on:submit.prevent="handleChangeName" class="info-dasar">
        <h2 class="mb-[8px] font-bold text-xl">Informasi Dasar</h2>

        <div class="input">
          <label for="nama">Nama</label>
          <br />
          <input
            id="nama"
            type="text"
            v-model="name"
            class="w-full bg-[#2b2122] text-[#e5e5e5] caret-[#e5e5e5] rounded-[15px] p-[1em] my-[8px] mb-[20px] border-none focus:outline-[2px] focus:outline-[#9a203e]" />
        </div>

        <button
          type="submit"
          class="bg-[#9a203e] text-[#e5e5e5] font-semibold rounded-[8px] p-[8px] hover:bg-[#7d1a33] cursor-pointer">
          Simpan Profile
        </button>
      </form>

      <!-- KEAMANAN -->
      <form v-on:submit.prevent="handleChangePassword" class="keamanan mt-[2em]">
        <h2 class="mb-[8px] font-bold text-xl">Keamanan</h2>

        <div class="input">
          <label for="email">Email</label>
          <br />
          <input
            id="email"
            type="email"
            v-model="email"
            disabled
            class="w-full bg-[#2b2122] text-[#e5e5e5] rounded-[15px] p-[1em] my-[8px] mb-[20px] border-none" />
        </div>

        <div class="input">
          <label>Password baru</label>
          <br />
          <input
            type="password"
            v-model="password"
            placeholder="Kosongkan jika tidak ingin mengganti"
            class="w-full bg-[#2b2122] text-[#e5e5e5] caret-[#e5e5e5] rounded-[15px] p-[1em] my-[8px] mb-[20px] border-none focus:outline-[2px] focus:outline-[#9a203e]" />
        </div>

        <div class="input">
          <label>Konfirmasi Password</label>
          <br />
          <input
            type="password"
            v-model="password_confirmation"
            placeholder="Masukkan ulang kata sandi"
            class="w-full bg-[#2b2122] text-[#e5e5e5] caret-[#e5e5e5] rounded-[15px] p-[1em] my-[8px] mb-[20px] border-none focus:outline-[2px] focus:outline-[#9a203e]" />
        </div>

        <button
          type="submit"
          class="font-semibold bg-[#9a203e] text-[#e5e5e5] font-bold rounded-[8px] p-[8px] hover:bg-[#7d1a33] cursor-pointer">
          Ganti Password
        </button>
      </form>
    </div>
  </div>
</template>
