<script setup>
import { ref } from "vue"; // Hapus onMounted jika tidak dipakai logic lain
import FillContent from "./Section/FillContent.vue";
import NoteCreate from "../Note/NoteCreate.vue";
import NoteEdit from "../Note/NoteEdit.vue";
// Hapus import yang tidak diperlukan lagi
// import { getNotifications, markNotificationsRead } from "../../lib/api/NotificationApi";
// import { alertError } from "../../lib/alert";
// import { useLocalStorage } from "@vueuse/core";
import { useRouter } from "vue-router";

const router = useRouter();
const showModal = ref(false);
const modalType = ref("create");
const selectedNoteData = ref(null);
const fillContentRef = ref(null);
// const token = useLocalStorage("token", ""); // Tidak perlu token di sini lagi

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
  if (fillContentRef.value) {
    await fillContentRef.value.fetchNoteList(true);
  }
};

// FUNGSI checkSystemNotifications DIHAPUS SEPENUHNYA
// Alasan: Menyebabkan ketidaksinkronan dengan Navbar dan UX yang mengganggu.

// onMounted DIHAPUS jika isinya hanya checkSystemNotifications
// Jika nanti butuh onMounted untuk hal lain, bisa ditambahkan lagi.
</script>

<template>
  <div>
    <div>
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
    </div>
  </div>
</template>
