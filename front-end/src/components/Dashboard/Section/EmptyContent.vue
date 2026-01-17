<script setup>
// Definisikan emit agar bisa lapor ke DashboardUser.vue
const emit = defineEmits(["note-created"]);

const triggerModal = () => {
  emit("note-created"); // Lapor ke DashboardUser: "Buka modal dong!"
};
</script>

<!-- udah ga dipakai -->

<template>
  <div class="flex flex-col items-center justify-center gap-[3em] mt-[4em] text-[#e5e5e5] sm:mt-[2em]">
    <img src="../../../assets/img/add-note.svg" alt="" class="w-[240px] h-[240px]" />

    <div class="flex flex-col items-center justify-center px-[1em]">
      <h3 class="my-2 font-bold text-2xl">Belum ada pesan</h3>
      <p class="m-0 text-[#8c8a8a] text-center">Sampaikan pesanmu kepada seseorang. Padukan kata dan musik sekarang.</p>
    </div>

    <div class="flex justify-center items-center w-[140px] mt-[-1em] mb-49">
      <button
        @click="triggerModal"
        class="w-full bg-[#9a203e] text-[#e5e5e5] border-none cursor-pointer p-[1em] text-[16px] font-bold rounded-[15px] hover:bg-[#7d1a33]">
        Buat Pesan
      </button>
    </div>
  </div>
</template>
