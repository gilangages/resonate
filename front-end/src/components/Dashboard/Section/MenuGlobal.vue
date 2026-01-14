<script setup>
import { useLocalStorage } from "@vueuse/core";
import { userDetail } from "../../../lib/api/UserApi";
import { onBeforeMount, ref } from "vue";

const token = useLocalStorage("token", "");
const name = ref("");

// State Tampilan
const showIntro = ref(false);
const showMenu = ref(false);

// State Teks
const text1 = ref("");
const textName = ref("");
const text2 = ref("");
const text3 = ref("");

const menuTransition = ref("slide-up");

// Helper: Efek Mengetik
const typeWriter = async (targetRef, text, speed = 40) => {
  targetRef.value = "";
  for (let i = 0; i < text.length; i++) {
    targetRef.value += text.charAt(i);
    await new Promise((resolve) => setTimeout(resolve, speed));
  }
};

const wait = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

async function fetchUser() {
  try {
    const response = await userDetail(token.value);

    // 1. CEK KEAMANAN: Jika token tidak valid/expired (401)
    if (response.status === 401) {
      localStorage.removeItem("token"); // Hapus token yang sudah basi
      sessionStorage.clear(); // Bersihkan session animasi juga
      window.location.href = "/login"; // Tendang ke halaman login
      return;
    }

    const responseBody = await response.json();

    if (response.ok) {
      const userData = responseBody.data;
      const newName = userData.name;

      // 2. LOGIC ANIMASI: Cek apakah nama sama dengan yang di storage
      const lastAnimName = sessionStorage.getItem("last_anim_name");

      if (lastAnimName === newName) {
        // --- KONDISI REFRESH (Nama masih sama) ---
        name.value = newName;
        showIntro.value = false;
        menuTransition.value = ""; // Langsung muncul tanpa efek slide
        showMenu.value = true;
      } else {
        // --- KONDISI LOGIN BARU ATAU HABIS GANTI NAMA ---
        name.value = newName;
        await handleAnimation(newName);
      }
    } else {
      // Jika error API selain 401 (misal 500), langsung tampilkan menu saja
      showMenu.value = true;
    }
  } catch (e) {
    console.error("Fetch Error:", e);
    showMenu.value = true;
  }
}

async function handleAnimation(userName) {
  // Reset state teks
  text1.value = "";
  textName.value = "";
  text2.value = "";
  text3.value = "";

  // Siapkan tampilan animasi
  showMenu.value = false;
  showIntro.value = true;
  menuTransition.value = "slide-up";

  await wait(500);
  await typeWriter(text1, "Selamat Datang ");
  await typeWriter(textName, userName);
  await wait(300);
  await typeWriter(text2, "Apa kabarmu hari ini?", 30);
  await wait(300);
  await typeWriter(text3, "Mulailah menulis pesan dan jangan lupa sisipkan lagu ya ^_^", 20);
  await wait(1200);

  // Selesai animasi, simpan nama ini ke session sebagai tanda sudah nonton animasi untuk nama ini
  sessionStorage.setItem("last_anim_name", userName);

  // Hilangkan intro, munculkan menu
  showIntro.value = false;
  setTimeout(() => {
    showMenu.value = true;
  }, 500);
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
