<script setup>
import { ref, onBeforeMount } from "vue";

// 1. Definisikan Emits agar bisa komunikasi ke parent
const emit = defineEmits(["close", "submit"]);

// state kirim sebagai
const kirimSebagai = ref("samaran");
const pesan = ref(""); // Tambahkan v-model state
const namaSamaran = ref(""); // Tambahkan v-model state

onBeforeMount(() => {
  kirimSebagai.value = "samaran";
});

// Fungsi handle tombol kembali
const handleClose = () => {
  emit("close"); // Kirim sinyal 'close' ke parent
};

// Fungsi handle submit (opsional, persiapan nanti)
const handleSubmit = () => {
  // Logic kirim data ke API nanti di sini...
  // Setelah sukses:
  // emit('submit');
  // emit('close');
};
</script>

<template>
  <div
    class="w-full max-w-[420px] rounded-[20px] bg-[#1c1516] p-4 sm:max-w-[560px] max-h-[90vh] overflow-y-auto"
    @click.stop>
    <form @submit.prevent="handleSubmit" class="flex flex-col text-[#e5e5e5] font-poppins">
      <h1 class="text-center text-[#9a203e] text-3xl font-bold m-0">Buat Pesan Baru</h1>
      <p class="mt-0 mb-[3em] text-center text-[12px] text-[#8c8a8a]">Pilih lagu dan tulis pesan untuk seseorang.</p>

      <div class="text-[14px]">
        <label>Pilih Lagu</label>
        <input
          type="text"
          required
          placeholder="Ketik judul lagu atau nama penyanyi..."
          class="mt-[6px] mb-[20px] w-full rounded-[10px] bg-[#2b2122] p-4 text-[#e5e5e5] caret-[#e5e5e5] focus:outline focus:outline-2 focus:outline-[#9a203e]" />
      </div>

      <div class="text-[14px]">
        <label>Kepada</label>
        <input
          type="text"
          required
          placeholder="Kepada siapa pesanmu"
          class="mt-[6px] mb-[20px] w-full rounded-[10px] bg-[#2b2122] p-4 text-[#e5e5e5] caret-[#e5e5e5] focus:outline focus:outline-2 focus:outline-[#9a203e]" />
      </div>

      <div class="text-[14px]">
        <label>Pesan</label>
        <textarea
          required
          v-model="pesan"
          placeholder="Tulis pesanmu"
          class="mt-[6px] mb-[20px] h-[120px] w-full resize-y rounded-[10px] bg-[#2b2122] p-4 text-[#e5e5e5] focus:outline focus:outline-2 focus:outline-[#9a203e]"></textarea>
      </div>

      <div class="text-[14px]">
        <label>Kirim Sebagai</label>

        <div class="mt-[6px] mb-[20px] flex items-center gap-2">
          <input type="radio" value="asli" v-model="kirimSebagai" class="cursor-pointer" />
          <span class="mr-[2em]">Hani (Asli)</span>

          <input type="radio" value="samaran" v-model="kirimSebagai" class="cursor-pointer" />
          <span>Nama Samaran</span>
        </div>

        <div v-if="kirimSebagai === 'samaran'">
          <label class="text-[#9a203e]">Nama Samaran</label>
          <input
            type="text"
            v-model="namaSamaran"
            required
            placeholder="Contoh: Secret Admirer..."
            class="mt-[6px] mb-[20px] w-full rounded-[10px] bg-[#2b2122] p-4 text-[#e5e5e5] caret-[#e5e5e5] focus:outline focus:outline-2 focus:outline-[#9a203e]" />
        </div>
      </div>

      <div class="mt-[26px] flex gap-[10px]">
        <button
          type="button"
          @click="handleClose"
          class="w-full rounded-[10px] border border-[#8c8a8a] bg-[#1c1516] px-3 py-3 text-xs font-medium text-[#8c8a8a] hover:border-[#666565] hover:bg-[#120e0e] cursor-pointer">
          Kembali
        </button>

        <button
          type="submit"
          class="w-full rounded-[10px] bg-[#9a203e] px-3 py-3 text-xs font-medium text-[#e5e5e5] hover:bg-[#7d1a33] cursor-pointer">
          Buat Pesan
        </button>
      </div>
    </form>
  </div>
</template>
<style scoped>
/* Kustomisasi Scrollbar khusus Chrome/Safari/Edge */
div::-webkit-scrollbar {
  width: 8px;
}

div::-webkit-scrollbar-track {
  background: #1c1516; /* Warna track sama dengan background card */
  border-radius: 20px;
}

div::-webkit-scrollbar-thumb {
  background-color: #2b2122; /* Warna scrollbar sedikit lebih terang */
  border-radius: 20px;
  border: 2px solid #1c1516;
}

div::-webkit-scrollbar-thumb:hover {
  background-color: #9a203e; /* Warna merah saat di-hover */
}
</style>
