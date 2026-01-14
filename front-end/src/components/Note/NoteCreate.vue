<script setup>
import CardNoteCreate from "./Section/CardNoteCreate.vue";

// Definisikan event yang diteruskan
const emit = defineEmits(["note-created", "close-modal"]);
</script>

<template>
  <CardNoteCreate @create-success="$emit('note-created')" @go-back="$emit('close-modal')" />
</template>
