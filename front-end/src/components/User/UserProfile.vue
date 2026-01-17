<script setup>
import { useLocalStorage } from "@vueuse/core";
import { onMounted, ref } from "vue";
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

// --- STATE LOADING ---
const isLoading = ref(true);

// --- STATE TOGGLE PASSWORD ---
const showPassword = ref(false);
const showConfirmPassword = ref(false);

// ... (SEMUA FUNCTION FETCH, UPDATE, DLL TETAP SAMA, TIDAK ADA YANG DIUBAH DI LOGIC SINI) ...
async function fetchUser() {
  isLoading.value = true;
  try {
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
  } catch (error) {
    console.error(error);
    await alertError("Gagal memuat data user.");
  } finally {
    isLoading.value = false;
  }
}

function triggerFileInput() {
  fileInput.value.click();
}

async function handleFileChange(event) {
  const file = event.target.files[0];
  if (!file) return;
  try {
    const response = await userUpdatePhoto(token.value, file);
    const responseBody = await response.json();
    console.log(responseBody);
    if (response.ok) {
      userState.value = responseBody.data;
      await alertSuccess("Foto profil berhasil diperbarui!");
    } else {
      throw new Error(responseBody.message);
    }
  } catch (error) {
    await alertError(error.message || "Gagal upload foto");
  } finally {
    event.target.value = null;
    if (fileInput.value) fileInput.value.value = null;
  }
}

async function handleChangeName() {
  const response = await userUpdateProfile(token.value, { name: name.value });
  const responseBody = await response.json();
  console.log(responseBody);
  if (response.ok) {
    await alertSuccess("Nama berhasil diubah.");
    userState.value.name = name.value;
  } else {
    const pesanError = responseBody.errors ? Object.values(responseBody.errors)[0][0] : responseBody.message;
    await alertError(pesanError);
  }
}

async function handleChangePassword() {
  if (password.value !== password_confirmation.value) {
    await alertError("Password tidak sama!");
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
    await alertSuccess("Password berhasil diperbarui!");
  } else {
    const pesanError = responseBody.errors ? Object.values(responseBody.errors)[0][0] : responseBody.message;
    await alertError(pesanError);
  }
}

const openPreview = () => {
  showImageModal.value = true;
};
const closePreview = () => {
  showImageModal.value = false;
};

onMounted(() => {
  fetchUser();
});
</script>

<template>
  <div class="flex justify-center items-start min-h-screen text-[#e5e5e5] font-jakarta pt-10 sm:pt-0">
    <div
      class="bg-[#1c1516] rounded-[20px] w-full max-w-[420px] mx-[24px] p-[12px] flex flex-col sm:max-w-[600px] sm:m-[2em] sm:p-[2em] min-h-[600px] transition-all duration-300 relative">
      <RouterLink
        to="/dashboard"
        class="absolute top-4 md:top-8 left-6 p-2 rounded-full text-[#8c8a8a] hover:text-[#9a203e] hover:bg-[#2b2122] transition-all duration-300 group z-20">
        <div
          class="tooltip-container-top group-hover:-translate-x-1 transition-transform"
          data-title="Kembali ke Dashboard">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
          </svg>
        </div>
      </RouterLink>
      <h1 class="text-center mt-[4px] text-[#9a203e] font-bold text-3xl">Edit Profil</h1>

      <Transition name="fade" mode="out-in">
        <div v-if="isLoading" key="skeleton" class="animate-pulse w-full">
          <div class="flex flex-col items-center justify-center py-2 sm:py-6">
            <div class="w-[102px] h-[102px] rounded-full bg-[#2b2122]"></div>
            <div class="mt-4 h-4 w-24 bg-[#2b2122] rounded"></div>
          </div>
          <div class="mt-4">
            <div class="h-6 w-32 bg-[#2b2122] rounded mb-4"></div>
            <div class="h-4 w-12 bg-[#2b2122] rounded mb-2"></div>
            <div class="h-[50px] w-full bg-[#2b2122] rounded-[15px] mb-6"></div>
            <div class="h-[40px] w-32 bg-[#2b2122] rounded-[8px]"></div>
          </div>
          <div class="mt-[2em]">
            <div class="h-6 w-32 bg-[#2b2122] rounded mb-4"></div>
            <div class="mb-4">
              <div class="h-4 w-12 bg-[#2b2122] rounded mb-2"></div>
              <div class="h-[50px] w-full bg-[#2b2122] rounded-[15px]"></div>
            </div>
            <div class="mb-4">
              <div class="h-4 w-24 bg-[#2b2122] rounded mb-2"></div>
              <div class="h-[50px] w-full bg-[#2b2122] rounded-[15px]"></div>
            </div>
            <div class="h-[40px] w-40 bg-[#2b2122] rounded-[8px] mt-4"></div>
          </div>
        </div>

        <div v-else key="content" class="w-full">
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
              <div class="relative my-[8px] mb-[20px]">
                <input
                  :type="showPassword ? 'text' : 'password'"
                  required
                  v-model="password"
                  placeholder="Kosongkan jika tidak ingin mengganti"
                  class="w-full bg-[#2b2122] text-[#e5e5e5] caret-[#e5e5e5] rounded-[15px] p-[1em] border-none focus:outline-[2px] focus:outline-[#9a203e] pr-[3.5em]" />

                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute inset-y-0 right-0 px-4 flex items-center text-[#8c8a8a] hover:text-[#9a203e] transition-colors cursor-pointer"
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

            <div class="input">
              <label>Konfirmasi Password</label>
              <div class="relative my-[8px] mb-[20px]">
                <input
                  :type="showConfirmPassword ? 'text' : 'password'"
                  required
                  v-model="password_confirmation"
                  placeholder="Masukkan ulang kata sandi"
                  class="w-full bg-[#2b2122] text-[#e5e5e5] caret-[#e5e5e5] rounded-[15px] p-[1em] border-none focus:outline-[2px] focus:outline-[#9a203e] pr-[3.5em]" />

                <button
                  type="button"
                  @click="showConfirmPassword = !showConfirmPassword"
                  class="absolute inset-y-0 right-0 px-4 flex items-center text-[#8c8a8a] hover:text-[#9a203e] transition-colors cursor-pointer"
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

            <button
              type="submit"
              class="font-semibold bg-[#9a203e] text-[#e5e5e5] font-bold rounded-[8px] p-[8px] hover:bg-[#7d1a33] cursor-pointer w-full sm:w-auto">
              Ganti Password
            </button>
          </form>
        </div>
      </Transition>
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

<style scoped></style>
