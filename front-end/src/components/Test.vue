<script setup>
import { ref, onMounted } from "vue";

const props = defineProps({
  note: {
    type: Object,
    required: true,
  },
  isPlaying: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["close", "toggle-play"]);

// Format Tanggal
const formatDate = (dateString) => {
  if (!dateString) return "";
  return new Date(dateString).toLocaleDateString("id-ID", {
    weekday: "long",
    day: "numeric",
    month: "long",
    year: "numeric",
  });
};
</script>

<template>
  <div
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 transition-all"
    @click.self="$emit('close')">
    <div
      class="relative w-full max-w-[450px] bg-[#1c1516] rounded-[20px] overflow-hidden shadow-2xl border border-[#3f3233] animate-pop-in">
      <button
        @click="$emit('close')"
        class="absolute top-4 right-4 z-10 p-2 rounded-full bg-black/40 hover:bg-[#9a203e] text-white transition-colors cursor-pointer">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="2.5"
          stroke="currentColor"
          class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

      <div
        class="relative flex flex-col items-center justify-center pt-10 pb-6 px-6 bg-gradient-to-b from-[#9a203e]/20 to-[#1c1516]">
        <div class="relative group cursor-pointer" @click="$emit('toggle-play')">
          <div
            :class="[
              'absolute inset-0 bg-[#9a203e] rounded-full blur-xl opacity-20 transition-opacity',
              isPlaying ? 'opacity-50 animate-pulse' : '',
            ]"></div>
          <img
            :src="note.spotify_album_image"
            :class="[
              'relative w-[180px] h-[180px] rounded-full object-cover shadow-2xl border-4 border-[#100c0d] transition-transform duration-700',
              isPlaying ? 'animate-spin-slow' : '',
            ]"
            alt="Album Art" />
          <div
            class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
            <div class="bg-black/60 rounded-full p-3 backdrop-blur-sm">
              <img :src="isPlaying ? '/src/assets/img/stop.svg' : '/src/assets/img/play.svg'" class="w-8 h-8" />
            </div>
          </div>
        </div>

        <div class="text-center mt-6">
          <h2 class="text-2xl font-bold text-[#e5e5e5] leading-tight">{{ note.spotify_track_name }}</h2>
          <p class="text-lg text-[#9a203e] font-medium mt-1">{{ note.spotify_artist }}</p>
        </div>
      </div>

      <div class="px-8 pb-8">
        <div class="flex items-center gap-2 mb-4 text-sm text-[#8c8a8a]">
          <span>Untuk:</span>
          <span class="text-[#e5e5e5] font-bold bg-[#2b2122] px-2 py-0.5 rounded">{{ note.recipient }}</span>
        </div>

        <div class="relative bg-[#100c0d] p-6 rounded-[15px] border border-[#2c2021]">
          <span class="absolute top-3 left-3 text-[#9a203e] text-4xl leading-none font-serif opacity-50">“</span>

          <p class="text-[#e5e5e5] text-[16px] leading-relaxed font-light italic text-center z-10 relative">
            {{ note.content }}
          </p>

          <span
            class="absolute bottom-[-10px] right-4 text-[#9a203e] text-4xl leading-none font-serif opacity-50 rotate-180">
            “
          </span>
        </div>

        <div class="mt-6 flex items-center justify-between border-t border-[#3f3233] pt-4">
          <div class="flex items-center gap-3">
            <div class="relative">
              <img :src="note.author_avatar" class="w-10 h-10 rounded-full object-cover border border-[#9a203e]" />
              <div class="absolute -bottom-1 -right-1 bg-[#1c1516] rounded-full p-[2px]">
                <img src="../../../assets/img/personal.svg" class="w-3 h-3" />
              </div>
            </div>
            <div class="flex flex-col">
              <span class="text-[10px] text-[#8c8a8a] uppercase tracking-wider">Dari</span>
              <span class="text-[#e5e5e5] font-bold text-sm">{{ note.author || note.initial_name }}</span>
            </div>
          </div>
          <p class="text-[12px] text-[#666] font-mono">{{ formatDate(note.created_at) }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Animasi Muncul (Pop In) */
@keyframes popIn {
  0% {
    opacity: 0;
    transform: scale(0.9) translateY(20px);
  }
  100% {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.animate-pop-in {
  animation: popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
}

/* Animasi Putar Piringan Hitam (Slow) */
@keyframes spinSlow {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin-slow {
  animation: spinSlow 8s linear infinite;
}
</style>
