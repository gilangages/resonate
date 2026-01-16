<script setup>
import { onBeforeMount, ref } from "vue";
import { userState } from "../../../lib/store";

// Emit event ketika animasi selesai agar konten utama bisa ditampilkan (opsional, tergantung kebutuhan)
const emit = defineEmits(["animation-complete"]);

const displayDetail = ref("");
const isTextVisible = ref(false);
const showIntro = ref(false);

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

async function checkAnimationRequirement() {
  const currentNameInMemory = userState.value?.name;
  if (!currentNameInMemory) return; // Tunggu data user ada

  const storedAnimName = sessionStorage.getItem("last_anim_name");

  // Jika nama user berbeda dengan yg di session storage, jalankan animasi
  if (storedAnimName !== currentNameInMemory) {
    await handleAnimation(currentNameInMemory);
  } else {
    showIntro.value = false;
    emit("animation-complete");
  }
}

async function handleAnimation(userName) {
  showIntro.value = true;
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

  isTextVisible.value = false;
  await wait(500);

  showIntro.value = false; // Sembunyikan overlay
  sessionStorage.setItem("last_anim_name", userName);
  emit("animation-complete");
}

onBeforeMount(() => {
  // Kita asumsikan userState sudah di-fetch oleh Navbar atau DashboardLayout
  // Namun untuk aman, kita watch atau cek langsung
  checkAnimationRequirement();
});
</script>

<template>
  <Transition name="fade">
    <div
      v-if="showIntro"
      class="fixed inset-0 z-[60] flex items-center justify-center bg-[#0f0505]/95 backdrop-blur-md">
      <div class="text-center px-6">
        <Transition name="text-fade">
          <p
            v-if="isTextVisible"
            v-html="displayDetail"
            class="text-[16px] text-white font-medium leading-relaxed max-w-[300px] italic"></p>
        </Transition>
      </div>
    </div>
  </Transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
.text-fade-enter-active,
.text-fade-leave-active {
  transition: opacity 0.3s ease;
}
.text-fade-enter-from,
.text-fade-leave-to {
  opacity: 0;
}
</style>
