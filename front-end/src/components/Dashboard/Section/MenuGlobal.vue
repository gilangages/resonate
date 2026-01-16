<script setup>
import { useLocalStorage } from "@vueuse/core";
import { userDetail } from "../../../lib/api/UserApi";
import { onBeforeMount, ref } from "vue";
// TAMBAHAN: Import userState untuk cek data realtime di memory
import { userState } from "../../../lib/store";

const token = useLocalStorage("token", "");
const name = ref("");

// --- PERBAIKAN LOGIC ---

// 1. Ambil data history dari Storage & data terbaru dari Memory (Store)
const storedAnimName = sessionStorage.getItem("last_anim_name");
const currentNameInMemory = userState.value?.name;

// 2. Tentukan apakah menu boleh langsung muncul?
let initialMenuState = false;

if (storedAnimName) {
  // Jika ada history di storage...
  if (currentNameInMemory && currentNameInMemory !== storedAnimName) {
    // TAPI data di memory beda (artinya baru edit profil),
    // MAKA sembunyikan menu (False) biar animasi bisa jalan mulus.
    initialMenuState = false;
  } else {
    // Jika data sama atau belum ada di memory (refresh page),
    // Tampilkan menu (True) biar tidak kedip.
    initialMenuState = true;
  }
} else {
  // Login pertama kali
  initialMenuState = false;
}

const showMenu = ref(initialMenuState);
const showIntro = ref(false);
const menuTransition = ref(initialMenuState ? "" : "slide-up");

// State teks
const displayDetail = ref("");
const isTextVisible = ref(false);

const typeWriter = async (text, speed = 50) => {
  displayDetail.value = "";
  let i = 0;
  while (i < text.length) {
    if (text[i] === "<") {
      const closingBracket = text.indexOf(">", i);
      if (closingBracket !== -1) {
        displayDetail.value += text.substring(i, closingBracket + 1);
        i = closingBracket + 1;
        continue;
      }
    }
    displayDetail.value += text[i];
    let currentSpeed = speed;
    if ([",", ".", "?", "^"].includes(text[i])) currentSpeed = speed * 4;
    await new Promise((resolve) => setTimeout(resolve, currentSpeed));
    i++;
  }
};

const wait = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

async function fetchUser() {
  try {
    const response = await userDetail(token.value);
    const responseBody = await response.json();

    if (response.ok) {
      const userData = responseBody.data;
      // Update global store biar sinkron
      userState.value = userData;

      const newName = userData.name;
      const lastAnimName = sessionStorage.getItem("last_anim_name");

      // Jika nama berbeda, jalankan animasi
      if (lastAnimName !== newName) {
        name.value = newName;
        // Pastikan menu sembunyi dulu (meskipun sudah false dari awal, kita pastikan lagi)
        showMenu.value = false;
        await handleAnimation(newName);
      } else {
        // Jika nama sama, pastikan menu tampil
        name.value = newName;
        showMenu.value = true;
      }
    } else {
      showMenu.value = true;
    }
  } catch (e) {
    showMenu.value = true;
  }
}

async function handleAnimation(userName) {
  displayDetail.value = "";
  showMenu.value = false;
  showIntro.value = true;
  menuTransition.value = "slide-up";

  // --- TAHAP 1 ---
  isTextVisible.value = true;
  const welcomeText = `Selamat Datang, <span style="color: #9a203e; font-weight: bold; font-style: normal;">${userName}</span>`;
  await typeWriter(welcomeText, 50);
  await wait(400);
  isTextVisible.value = false;
  await wait(300);

  // --- TAHAP 2 ---
  isTextVisible.value = true;
  await typeWriter("Apa kabarmu hari ini?", 50);
  await wait(400);
  isTextVisible.value = false;
  await wait(300);

  // --- TAHAP 3 ---
  isTextVisible.value = true;
  await typeWriter("Mulailah menulis pesan dan jangan lupa sisipkan lagu ya ^_^", 40);
  await wait(300);

  sessionStorage.setItem("last_anim_name", userName);
  showIntro.value = false;

  setTimeout(() => {
    showMenu.value = true;
  }, 900);
}

onBeforeMount(async () => {
  await fetchUser();
});
</script>

<template>
  <div class="relative min-h-[150px] flex flex-col justify-center items-center w-full">
    <Transition name="fade">
      <div v-if="showIntro" class="absolute top-8 text-center text-[13px] text-white px-6 w-full flex justify-center">
        <Transition name="text-fade">
          <p
            v-if="isTextVisible"
            v-html="displayDetail"
            class="font-medium leading-relaxed max-w-[300px] italic text-white"></p>
        </Transition>
      </div>
    </Transition>

    <Transition :name="menuTransition">
      <div v-if="showMenu" class="my-[2em] flex justify-center relative z-10">
        <RouterLink
          to="/dashboard/global"
          class="rounded-l-[10px] bg-[#9a203e] p-[12px] text-[16px] font-semibold text-[#e5e5e5] hover:bg-[#821c35] hover:text-[#cdcccc] transition-colors shadow-lg">
          Jelajahi Pesan
        </RouterLink>
        <RouterLink
          to="/dashboard"
          class="rounded-r-[10px] bg-[#1c1516] p-[12px] text-[16px] font-semibold text-[#e5e5e5] hover:bg-[#130f0f] hover:text-[#cdcccc] transition-colors shadow-lg border-l border-[#2c2021]">
          Pesan Saya
        </RouterLink>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
/* STYLE SAMA PERSIS */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.8s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.text-fade-enter-active,
.text-fade-leave-active {
  transition: opacity 0.5s ease;
}
.text-fade-enter-from,
.text-fade-leave-to {
  opacity: 0;
}

.slide-up-enter-active {
  transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}
.slide-up-enter-from {
  transform: translateY(30px);
  opacity: 0;
}

p {
  color: #ffffff;
  text-rendering: optimizeLegibility;
}
</style>
