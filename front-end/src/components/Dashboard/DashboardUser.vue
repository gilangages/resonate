<script setup>
import { ref, onMounted } from "vue";
import { useLocalStorage } from "@vueuse/core";
import { myNoteList } from "../../lib/api/NoteApi";
import EmptyContent from "./Section/EmptyContent.vue";
import FillContent from "./Section/FillContent.vue";
import NoteCreate from "../Note/NoteCreate.vue";
import NoteEdit from "../Note/NoteEdit.vue";

const token = useLocalStorage("token", "");
const hasNotes = ref(false);
const isLoading = ref(true);
const showModal = ref(false);
const modalType = ref("create");
const selectedNoteData = ref(null);

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
  await checkUserNotes();
};

onMounted(() => {
  checkUserNotes();
});
</script>

<template>
  <div v-if="isLoading" class="p-4 md:p-8 font-jakarta">
    <div class="columns-1 md:columns-2 lg:columns-3 gap-6 mb-10 space-y-6">
      <div v-for="i in 6" :key="i" class="break-inside-avoid relative">
        <div class="bg-[#1c1516] rounded-[24px] p-6 border border-[#2c2021] animate-pulse h-full">
          <div class="mb-5">
            <div class="h-3 w-10 bg-[#2b2122] rounded mb-2"></div>
            <div class="h-8 w-3/4 bg-[#2b2122] rounded-[8px]"></div>
          </div>

          <div class="flex gap-4 items-center mb-5">
            <div class="w-14 h-14 bg-[#2b2122] rounded-[12px]"></div>
            <div class="flex-1 space-y-2">
              <div class="h-4 w-1/2 bg-[#2b2122] rounded"></div>
              <div class="h-3 w-1/3 bg-[#2b2122] rounded"></div>
            </div>
          </div>

          <div class="h-24 bg-[#2b2122] rounded-[16px] mb-4 w-full"></div>

          <div class="flex flex-col gap-3 pt-4 border-t border-[#2c2021] mt-auto">
            <div class="flex items-center gap-2">
              <div class="w-6 h-6 rounded-full bg-[#2b2122]"></div>
              <div class="h-3 w-20 bg-[#2b2122] rounded"></div>
              <div class="ml-auto h-3 w-16 bg-[#2b2122] rounded"></div>
            </div>

            <div class="flex gap-2 mt-2">
              <div class="flex-1 h-8 bg-[#2b2122] rounded-lg"></div>
              <div class="flex-1 h-8 bg-[#2b2122] rounded-lg"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div v-else>
    <FillContent
      v-if="hasNotes"
      @open-modal="openCreateModal"
      @is-empty="hasNotes = false"
      @edit-note="openEditModal" />

    <EmptyContent v-else @note-created="openCreateModal" />
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
