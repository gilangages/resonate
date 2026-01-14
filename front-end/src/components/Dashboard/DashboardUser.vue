<script setup>
import { ref, onMounted } from "vue";
import EmptyContent from "./Section/EmptyContent.vue";
import FillContent from "./Section/FillContent.vue"; // Import FillContent
import Menu from "./Section/Menu.vue";
// import { getNotes } from "../../lib/api/NoteApi"; // (Opsional) Jika mau cek data asli saat refresh

// State untuk menentukan tampilan
const hasContent = ref(false);

// Fungsi saat pesan berhasil dibuat dari EmptyContent
const onNoteCreated = () => {
  hasContent.value = true; // Ubah tampilan jadi FillContent
};

// (Opsional - Best Practice)
// Saat halaman direfresh, cek dulu ke backend: User punya note gak?
// Kalau punya, langsung set hasContent = true.
/*
onMounted(async () => {
  try {
     // Contoh logic:
     // const data = await getNotes(token);
     // if (data.length > 0) hasContent.value = true;
  } catch (e) { ... }
});
*/
</script>

<template>
  <Menu />

  <FillContent v-if="hasContent" />

  <EmptyContent v-else @note-created="onNoteCreated" />
</template>
