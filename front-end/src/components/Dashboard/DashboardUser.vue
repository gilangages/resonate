<script setup>
import { ref, onMounted } from "vue";
import { useLocalStorage } from "@vueuse/core";
import { myNoteList } from "../../lib/api/NoteApi";
import EmptyContent from "./Section/EmptyContent.vue";
import FillContent from "./Section/FillContent.vue";
import Menu from "./Section/Menu.vue";
import NoteCreate from "../Note/NoteCreate.vue"; // Import Modal disini

const token = useLocalStorage("token", "");
const hasNotes = ref(false);
const isLoading = ref(true);
const showModal = ref(false); // State Modal di Parent

// --- FUNGSI CEK DATA ---
const checkUserNotes = async () => {
  isLoading.value = true;
  try {
    const response = await myNoteList(token.value);
    const result = await response.json();
    if (response.ok && result.data && result.data.length > 0) {
      hasNotes.value = true;
    } else {
      hasNotes.value = false;
    }
  } catch (error) {
    console.error("Error checking notes:", error);
    hasNotes.value = false;
  } finally {
    isLoading.value = false;
  }
};

// --- KONTROL MODAL ---
const openModal = () => {
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

// Jika note berhasil dibuat: Tutup modal & Refresh data
const onNoteCreated = async () => {
  closeModal();
  await checkUserNotes(); // Ini otomatis ganti Empty -> Fill
};

onMounted(() => {
  checkUserNotes();
});
</script>

<template>
  <Menu />

  <div v-if="isLoading" class="flex h-[80vh] w-full items-center justify-center">
    <p class="text-[#8c8a8a] text-lg animate-pulse">Memuat cerita kamu...</p>
  </div>

  <div v-else>
    <FillContent v-if="hasNotes" @open-modal="openModal" />

    <EmptyContent v-else @note-created="openModal" />
  </div>

  <div
    v-if="showModal"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity p-4"
    @click.self="closeModal">
    <div class="relative w-full max-w-2xl transform transition-all">
      <NoteCreate @note-created="onNoteCreated" @close-modal="closeModal" />
    </div>
  </div>
</template>
