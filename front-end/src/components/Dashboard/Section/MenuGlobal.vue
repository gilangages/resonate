<script setup>
import { useLocalStorage } from "@vueuse/core";
import { userDetail } from "../../../lib/api/UserApi";
import { onBeforeMount, ref } from "vue";
import { userState } from "../../../lib/store";

const token = useLocalStorage("token", "");
const name = ref("");

const storedAnimName = sessionStorage.getItem("last_anim_name");
const currentNameInMemory = userState.value?.name;

let initialMenuState = false;
if (storedAnimName) {
  if (currentNameInMemory && currentNameInMemory !== storedAnimName) {
    initialMenuState = false;
  } else {
    initialMenuState = true;
  }
} else {
  initialMenuState = false;
}

const showMenu = ref(initialMenuState);
const showIntro = ref(!initialMenuState);

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
      userState.value = userData;
      const newName = userData.name;
      const lastAnimName = sessionStorage.getItem("last_anim_name");

      if (lastAnimName !== newName) {
        name.value = newName;
        showMenu.value = false;
        showIntro.value = true;
        await handleAnimation(newName);
      } else {
        name.value = newName;
        showMenu.value = true;
        showIntro.value = false;
      }
    } else {
      showMenu.value = true;
      showIntro.value = false;
    }
  } catch (e) {
    showMenu.value = true;
    showIntro.value = false;
  }
}

async function handleAnimation(userName) {
  displayDetail.value = "";
  showIntro.value = true;
  showMenu.value = false;

  isTextVisible.value = true;
  const welcomeText = `Selamat Datang, <span style="color: #9a203e; font-weight: bold; font-style: normal;">${userName}</span>`;
  await typeWriter(welcomeText, 50);
  await wait(400);
  isTextVisible.value = false;
  await wait(300);

  isTextVisible.value = true;
  await typeWriter("Apa kabarmu hari ini?", 50);
  await wait(400);
  isTextVisible.value = false;
  await wait(300);

  isTextVisible.value = true;
  await typeWriter("Mulailah menulis pesan dan jangan lupa sisipkan lagu ya ^_^", 40);
  await wait(500);

  // TRANSISI AKHIR
  isTextVisible.value = false; // Teks hilang dulu (fade out)
  await wait(500); // Beri waktu fade out teks selesai

  showIntro.value = false; // Matikan wadah intro

  // Langsung nyalakan menu tanpa delay, karena kita pakai absolute positioning di CSS untuk transisi mulus
  showMenu.value = true;

  sessionStorage.setItem("last_anim_name", userName);
}

onBeforeMount(async () => {
  await fetchUser();
});
</script>

<template>
  <div class="relative w-full flex flex-col min-h-screen">
    <div
      class="sticky top-[63px] z-40 w-full bg-[#0f0505]/95 backdrop-blur-md border-b border-[#2c2021]/30 transition-all duration-500">
      <div class="relative flex justify-center items-center h-[80px] w-full overflow-hidden">
        <Transition name="fade">
          <div v-if="showIntro" class="absolute inset-0 flex items-center justify-center text-center px-6">
            <Transition name="text-fade">
              <p
                v-if="isTextVisible"
                v-html="displayDetail"
                class="text-[13px] text-white font-medium leading-relaxed max-w-[300px] italic"></p>
            </Transition>
          </div>

          <div v-else-if="showMenu" class="absolute inset-0 flex items-center justify-center gap-0">
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
    </div>

    <div class="flex-1 w-full mt-4">
      <slot />
    </div>
  </div>
</template>

<style scoped>
/* Transisi Container Utama (Intro <-> Menu) */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease, transform 0.5s ease;
}

.fade-enter-from {
  opacity: 0;
  transform: scale(0.95); /* Efek zoom-in sedikit saat menu muncul */
}

.fade-leave-to {
  opacity: 0;
  /* Tidak perlu transform saat keluar agar tidak "terbang" */
}

/* Transisi Teks Ketik-Ketik */
.text-fade-enter-active,
.text-fade-leave-active {
  transition: opacity 0.3s ease;
}
.text-fade-enter-from,
.text-fade-leave-to {
  opacity: 0;
}

p {
  color: #ffffff;
  text-rendering: optimizeLegibility;
}
</style>
