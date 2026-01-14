<script setup>
import { useLocalStorage } from "@vueuse/core";
import { userDetail } from "../../../lib/api/UserApi";
import { onBeforeMount, ref } from "vue";

const token = useLocalStorage("token", "");
const name = ref("");

// State Tampilan
const showIntro = ref(true); // Tampilkan Intro di awal
const showMenu = ref(false); // Sembunyikan Menu di awal

// State Teks (untuk efek ketik)
const text1 = ref("");
const textName = ref("");
const text2 = ref("");
const text3 = ref("");

// State Animasi CSS (Agar saat refresh tidak lompat)
const menuTransition = ref("slide-up");

// Helper: Efek Mengetik
const typeWriter = async (targetRef, text, speed = 40) => {
  for (let i = 0; i < text.length; i++) {
    targetRef.value += text.charAt(i);
    await new Promise((resolve) => setTimeout(resolve, speed));
  }
};

// Helper: Delay/Jeda
const wait = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

async function fetchUser() {
  const response = await userDetail(token.value);
  const responseBody = await response.json();

  if (response.ok) {
    const userData = responseBody.data;
    name.value = userData.name;

    // Jalankan logika animasi dengan mengoper nama user
    handleAnimation(userData.name);
  } else {
    // Jika error (misal token expired), langsung tampilkan menu tanpa animasi
    showIntro.value = false;
    menuTransition.value = "";
    showMenu.value = true;
  }
}

async function handleAnimation(userName) {
  // KUNCI UTAMA: Gunakan nama user sebagai key storage
  // Contoh key: "played_anim_Budi"
  const storageKey = `played_anim_${userName}`;
  const hasPlayed = sessionStorage.getItem(storageKey);

  if (hasPlayed) {
    // --- KASUS REFRESH (Sudah pernah main) ---

    // 1. Matikan efek slide (biar menu langsung diam di tempat)
    menuTransition.value = "";

    // 2. Isi teks penuh (opsional, biar kalau intro sekilas muncul, teksnya ada)
    text1.value = "Selamat Datang ";
    textName.value = userName;

    // 3. Sembunyikan Intro, Munculkan Menu Instan
    showIntro.value = false;
    showMenu.value = true;
  } else {
    // --- KASUS LOGIN BARU / USER BARU ---

    // 1. Nyalakan efek slide
    menuTransition.value = "slide-up";

    // 2. Mulai Mengetik
    await typeWriter(text1, "Selamat Datang ");
    await typeWriter(textName, userName);
    await wait(300); // Jeda
    await typeWriter(text2, "Apa kabarmu hari ini?", 30);
    await wait(300);
    await typeWriter(text3, "Mulailah menulis pesan dan jangan lupa sisipkan lagu ya ^_^", 20);
    await wait(1200); // Baca sebentar

    // 3. Intro Hilang (Fade Out)
    showIntro.value = false;

    // 4. Tunggu intro hilang dikit, baru Menu Masuk (Slide Up)
    setTimeout(() => {
      showMenu.value = true;
    }, 500);

    // 5. Simpan status bahwa user INI sudah lihat animasi
    sessionStorage.setItem(storageKey, "true");
  }
}

onBeforeMount(async () => {
  await fetchUser();
});
</script>

<template>
  <Transition name="fade">
    <div v-if="showIntro" class="pt-[1em] text-center text-[12px] text-[#e5e5e5] min-h-[90px] flex flex-col gap-1">
      <p>
        {{ text1 }}
        <span class="font-bold text-[#9a203e]">{{ textName }}</span>
      </p>
      <p class="min-h-[1.5em]">{{ text2 }}</p>
      <p class="min-h-[1.5em]">{{ text3 }}</p>
    </div>
  </Transition>

  <Transition :name="menuTransition">
    <div v-if="showMenu" class="my-[2em] flex justify-center">
      <RouterLink
        to="/dashboard/global"
        class="rounded-l-[10px] bg-[#9a203e] p-[12px] text-[16px] font-semibold text-[#e5e5e5] hover:bg-[#821c35] hover:text-[#cdcccc] transition-colors">
        Jelajahi Pesan
      </RouterLink>
      <RouterLink
        to="/dashboard"
        class="rounded-r-[10px] bg-[#1c1516] p-[12px] text-[16px] font-semibold text-[#e5e5e5] hover:bg-[#130f0f] hover:text-[#cdcccc] transition-colors">
        Pesan Saya
      </RouterLink>
    </div>
  </Transition>
</template>

<style scoped>
/* Transisi Fade Out untuk Intro */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Transisi Slide Up untuk Menu (Hanya jalan jika login pertama) */
.slide-up-enter-active {
  transition: all 0.8s ease-out;
}
.slide-up-leave-active {
  transition: all 0.3s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-up-enter-from {
  transform: translateY(30px); /* Muncul dari bawah */
  opacity: 0;
}
.slide-up-leave-to {
  transform: translateY(-30px);
  opacity: 0;
}
</style>
