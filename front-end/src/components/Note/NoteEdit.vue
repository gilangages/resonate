<script setup>
import CardNoteEdit from "./Section/CardNoteEdit.vue";

const props = defineProps({
  selectedNote: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(["note-updated", "close-modal"]);
</script>

<template>
  <CardNoteEdit :note-data="selectedNote" @update-success="$emit('note-updated')" @go-back="$emit('close-modal')" />
</template>
