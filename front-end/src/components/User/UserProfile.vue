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

// --- STATE MODAL PREVIEW ---
const showImageModal = ref(false);

async function fetchUser() {
  const response = await userDetail(token.value);
  const responseBody = await response.json();

  if (response.ok) {
    userState.value = responseBody.data;
    name.value = responseBody.data.name;
    email.value = responseBody.data.email;
  } else {
    const pesanError = responseBody.errors ? Object.values(responseBody.errors)[0][0] : responseBody.message;
    await alertError(pesanError);
  }
}

// --- LOGIC UPLOAD ---
function triggerFileInput() {
  fileInput.value.click();
}

async function handleFileChange(event) {
  const file = event.target.files[0];
  if (!file) return;

  try {
    const response = await userUpdatePhoto(token.value, file);
    const responseBody = await response.json();

    if (response.ok) {
      userState.value = responseBody.data;
      await alertSuccess("Foto profil berhasil di perbarui!");
    } else {
      throw new Error(responseBody.message);
    }
  } catch (error) {
    await alertError(error.message || "Gagal upload foto");
  } finally {
    event.target.value = null;
    if (fileInput.value) {
      fileInput.value.value = null;
    }
  }
}

async function handleChangeName() {
  const response = await userUpdateProfile(token.value, { name: name.value });
  const responseBody = await response.json();

  if (response.ok) {
    await alertSuccess("Nama berhasil diubah.");
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

  if (response.ok) {
    password.value = "";
    password_confirmation.value = "";
    await alertSuccess("Password updated successfully");
  } else {
    const pesanError = responseBody.errors ? Object.values(responseBody.errors)[0][0] : responseBody.message;
    await alertError(pesanError);
  }
}

// --- LOGIC PREVIEW IMAGE ---
const openPreview = () => {
  showImageModal.value = true;
};

const closePreview = () => {
  showImageModal.value = false;
};

onBeforeMount(async () => {
  await fetchUser();
});
</script>

<template>
  <div class="flex justify-center items-center min-h-screen text-[#e5e5e5] font-jakarta">
    <div
      class="bg-[#1c1516] rounded-[20px] w-full max-w-[420px] mx-[24px] p-[12px] flex flex-col sm:max-w-[600px] sm:m-[2em] sm:p-[2em]">
      <h1 class="text-center mt-[4px] text-[#9a203e] font-bold text-3xl">Edit Profil</h1>

      <div class="flex flex-col items-center justify-center text-[#e5e5e5] py-6">
        <div class="relative group cursor-zoom-in" @click="openPreview">
          <img
            :src="getAvatarUrl(userState.avatar)"
            alt="profile"
            class="w-[102px] h-[102px] rounded-full object-cover block border-2 border-transparent group-hover:border-[#9a203e]/50 transition-all" />

          <div
            class="absolute inset-0 flex items-center justify-center bg-black/20 rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              stroke="white"
              stroke-width="2">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
          </div>
        </div>

        <p
          class="py-2 cursor-pointer text-[#9a203e] font-semibold hover:text-[#e5e5e5] transition-colors mt-2 text-sm uppercase tracking-wider"
          @click="triggerFileInput">
          Ganti Foto
        </p>
        <input type="file" ref="fileInput" class="hidden" accept="image/*" @change="handleFileChange" />
      </div>

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
          class="bg-[#9a203e] text-[#e5e5e5] font-semibold rounded-[8px] p-[8px] hover:bg-[#7d1a33] cursor-pointer w-full sm:w-auto">
          Simpan Profile
        </button>
      </form>

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
          class="font-semibold bg-[#9a203e] text-[#e5e5e5] font-bold rounded-[8px] p-[8px] hover:bg-[#7d1a33] cursor-pointer w-full sm:w-auto">
          Ganti Password
        </button>
      </form>
    </div>

    <Transition name="fade">
      <div
        v-if="showImageModal"
        class="fixed inset-0 z-[60] flex flex-col items-center justify-center bg-black/95 backdrop-blur-xl p-4 cursor-pointer"
        @click="closePreview">
        <div class="relative flex flex-col items-center w-full max-w-[90vw] max-h-[90vh] cursor-default">
          <img
            :src="getAvatarUrl(userState.avatar)"
            class="w-auto h-auto max-w-full max-h-[80vh] object-contain rounded-lg shadow-2xl"
            @click.stop />

          <p class="text-white/50 text-sm tracking-widest uppercase font-bold mt-4" @click.stop>Foto Profil</p>
        </div>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");

.font-jakarta {
  font-family: "Plus Jakarta Sans", sans-serif;
}

/* Transition Effect */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
