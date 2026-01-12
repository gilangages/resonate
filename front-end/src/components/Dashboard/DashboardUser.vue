<script setup>
import { onMounted, ref } from "vue";
import FillContent from "./Section/FillContent.vue";
import NoteCreate from "../Note/NoteCreate.vue";
import NoteEdit from "../Note/NoteEdit.vue";
import { getNotifications, markNotificationsRead } from "../../lib/api/NotificationApi";
import { alertError } from "../../lib/alert"; // Cek file ini, apakah ada logika redirect?
import { userDetail } from "../../lib/api/UserApi";
import { useLocalStorage } from "@vueuse/core";
import { useRouter } from "vue-router"; // Import router jika mau kontrol manual

const router = useRouter();
const showModal = ref(false);
const modalType = ref("create");
const selectedNoteData = ref(null);
const fillContentRef = ref(null);
const token = useLocalStorage("token", "");

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

async function checkSystemNotifications() {
  if (!token.value) {
    console.log("Tidak ada token, stop checking.");
    return;
  }

  try {
    const response = await getNotifications(token.value);

    // Cek jika Unauthorized (Token expired/invalid)
    if (response.status === 401) {
      console.error("Token tidak valid (401).");
      // Biarkan interceptor global yang handle, atau handle manual:
      // token.value = null;
      // router.push('/login');
      return;
    }

    const responseBody = await response.json();
    console.log("Response Notifikasi:", responseBody);

    if (response.ok) {
      const notifications = responseBody.data || [];

      // Cari notifikasi alert yang belum dibaca
      const alertNotif = notifications.find((n) => n.read_at === null && n.data.type === "alert");

      if (alertNotif) {
        const pesanLengkap = `${alertNotif.data.message}\n\nAlasan: ${alertNotif.data.reason}`;

        // Tampilkan alert
        await alertError(pesanLengkap);

        // Tandai sudah dibaca SETELAH user menutup alert
        await markNotificationsRead(token.value, alertNotif.id);
      }
    } else {
      // Perbaikan: gunakan responseBody.message atau text manual
      console.error("Gagal response not OK: ", responseBody);
    }
  } catch (err) {
    // Perbaikan: tangkap error fetch (network error, dsb)
    console.error("Error pada checkSystemNotifications: ", err);
  }
}

onMounted(async () => {
  // Cek notifikasi sistem
  await checkSystemNotifications();
});
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
