<script setup>
import CardNoteEdit from "./Section/CardNoteEdit.vue";
// Mock Data (Pura-pura data dari Dashboard)
const dummyNote = {
  id: 123,
  content: "Ini pesan test",
  recipient: "Dia",
  spotify_track_name: "Lagu Galau",
  spotify_artist: "Penyanyi Galau",
  spotify_track_id: "xyz123",
  spotify_album_image: "https://placehold.co/50",
  initial_name: "Rahasia",
};
</script>

<template>
  <CardNoteEdit />
</template>
