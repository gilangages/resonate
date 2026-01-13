<script setup>
import { useLocalStorage } from "@vueuse/core";
import { onBeforeMount, ref } from "vue";
import { userDetail, userUpdatePassword, userUpdatePhoto, userUpdateProfile } from "../../lib/api/UserApi";
import { alertError, alertSuccess } from "../../lib/alert";
import { getAvatarUrl, userState } from "../../lib/store";

const token = useLocalStorage("token", "");
const name = ref("");
const email = ref("");
const fileInput = ref(null);
const password = ref("");
const password_confirmation = ref("");

async function fetchUser() {
  const response = await userDetail(token.value);
  const responseBody = await response.json();
  console.log(responseBody);

  if (response.ok) {
    userState.value = responseBody.data;
    name.value = responseBody.data.name;
    email.value = responseBody.data.email;
  } else {
    const pesanError = responseBody.errors ? Object.values(responseBody.errors)[0][0] : responseBody.message;
    await alertError(pesanError);
  }
}

function triggerFileInput() {
  fileInput.value.click();
}

async function handleFileChange(event) {
  const file = event.target.files[0];

  // Jika user cancel pilih file, stop
  if (!file) return;

  try {
    const response = await userUpdatePhoto(token.value, file);
    const responseBody = await response.json();

    if (response.ok) {
      // 1. Update State
      userState.value = responseBody.data;
      await alertSuccess("Foto profil berhasil diubah!");
    } else {
      throw new Error(responseBody.message);
    }
  } catch (error) {
    await alertError(error.message || "Gagal upload foto");
  } finally {
    // [PENTING] Reset input file agar bisa mendeteksi perubahan selanjutnya
    // Meskipun user memilih file yang sama atau file baru, ini memastikan event @change selalu jalan
    event.target.value = null;

    // Atau jika menggunakan ref:
    if (fileInput.value) {
      fileInput.value.value = null;
    }
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
  if (password.value !== password_confirmation.value) {
    await alertError("Password do not match");
    return;
  }

  const response = await userUpdatePassword(token.value, {
    password: password.value,
    password_confirmation: password_confirmation.value,
  });
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
        <img
          :src="getAvatarUrl(userState.avatar)"
          @click="triggerFileInput"
          alt="profile"
          class="w-[102px] h-[102px] rounded-full object-cover block cursor-pointer" />
        <p class="py-2 cursor-pointer hover:text-[#8c8a8a]" @click="triggerFileInput">Ganti Foto</p>
        <input type="file" ref="fileInput" class="hidden" accept="image/*" @change="handleFileChange" />
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
          <label for="email">
            Email
            <span class="text-[12px] text-[#6b6b6b] ml-1">(Locked)</span>
          </label>
          <br />
          <input
            id="email"
            type="email"
            v-model="email"
            disabled
            class="w-full bg-[#151010] text-[#6b6b6b] border border-[#2b2122] cursor-not-allowed rounded-[15px] p-[1em] my-[8px] mb-[20px] focus:outline-none" />
        </div>

        <div class="input">
          <label>Password baru</label>
          <br />
          <input
            type="password"
            required
            v-model="password"
            placeholder="Kosongkan jika tidak ingin mengganti"
            class="w-full bg-[#2b2122] text-[#e5e5e5] caret-[#e5e5e5] rounded-[15px] p-[1em] my-[8px] mb-[20px] border-none focus:outline-[2px] focus:outline-[#9a203e]" />
        </div>

        <div class="input">
          <label>Konfirmasi Password</label>
          <br />
          <input
            type="password"
            required
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
