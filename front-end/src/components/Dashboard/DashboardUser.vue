<script setup>
import { ref } from "vue";
import FillContent from "./Section/FillContent.vue"; // Kita hanya butuh ini
import NoteCreate from "../Note/NoteCreate.vue";
import NoteEdit from "../Note/NoteEdit.vue";

const showModal = ref(false);
const modalType = ref("create");
const selectedNoteData = ref(null);

// Kita bisa refresh data lewat key-changing pattern atau expose function,
// tapi cara paling simpel di Vue 3 composition API dgn component child:
const fillContentRef = ref(null);

const openCreateModal = () => {
  modalType.value = "create";
  selectedNoteData.value = null;
  showModal.value = true;
};

const openEditModal = (note) => {
  modalType.value = "edit";
  selectedNoteData.value = note;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  selectedNoteData.value = null;
};

const handleDataChanged = async () => {
  closeModal();
  // Panggil fungsi fetch ulang di dalam child component (FillContent)
  // Pastikan di FillContent.vue kamu men-expose function fetchNoteList,
  // ATAU cara gampang: reload page / biarkan reactivity jalan jika pakai store.
  // Tapi karena kita pakai fetch manual, kita bisa trigger fetch lewat ref:
  if (fillContentRef.value) {
    await fillContentRef.value.fetchNoteList(true);
  }
};
</script>

<template>
  <div>
    <FillContent ref="fillContentRef" @open-modal="openCreateModal" @edit-note="openEditModal" />
  </div>

  <div
    v-if="showModal"
    class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm transition-opacity p-4"
    @click.self="closeModal">
    <div class="relative w-full max-w-2xl transform transition-all">
      <NoteCreate v-if="modalType === 'create'" @note-created="handleDataChanged" @close-modal="closeModal" />
      <NoteEdit
        v-else-if="modalType === 'edit'"
        :selected-note="selectedNoteData"
        @note-updated="handleDataChanged"
        @close-modal="closeModal" />
    </div>
  </div>
</template>
