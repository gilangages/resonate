<script setup>
import { ref } from "vue";

defineProps({
  searchQuery: {
    type: String,
    default: "",
  },
  sortBy: {
    type: String,
    default: "newest",
  },
  placeholder: {
    type: String,
    default: "Cari pesan...",
  },
});

const emit = defineEmits(["update:searchQuery", "update:sortBy", "search"]);

// State untuk Custom Dropdown
const showSortDropdown = ref(false);

const sortLabel = {
  newest: "TERBARU",
  oldest: "TERLAMA",
};

const onSearchInput = (e) => {
  emit("update:searchQuery", e.target.value);
  emit("search");
};

const handleSelectSort = (value) => {
  emit("update:sortBy", value);
  emit("search");
  showSortDropdown.value = false; // Tutup dropdown setelah memilih
};
</script>

<template>
  <div
    class="mb-8 flex flex-col md:flex-row gap-4 items-center justify-between sticky top-[60px] md:top-[64px] z-40 bg-[#0f0505]/95 backdrop-blur py-4 border-b border-[#2c2021]">
    <div class="relative w-full md:w-96 group">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="18"
        height="18"
        viewBox="0 0 24 24"
        fill="none"
        stroke="#666"
        stroke-width="2"
        class="absolute left-3 top-1/2 -translate-y-1/2 group-focus-within:stroke-[#9a203e] transition-colors">
        <circle cx="11" cy="11" r="8"></circle>
        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
      </svg>
      <input
        :value="searchQuery"
        @input="onSearchInput"
        type="text"
        :placeholder="placeholder"
        class="w-full bg-[#1c1516] border border-[#2c2021] rounded-full py-2.5 pl-10 pr-4 text-white text-sm focus:outline-none focus:border-[#9a203e] transition-all placeholder-[#555]" />
    </div>

    <div class="flex gap-3 w-full md:w-auto items-center relative z-40">
      <div class="relative w-full md:w-auto min-w-[140px]">
        <button
          @click="showSortDropdown = !showSortDropdown"
          class="w-full bg-[#1c1516] text-[#e5e5e5] text-xs font-bold uppercase tracking-wider border border-[#2c2021] rounded-lg px-4 py-2.5 focus:outline-none focus:border-[#9a203e] cursor-pointer flex items-center justify-center gap-2 hover:bg-[#251a1c] transition-colors">
          <span>{{ sortLabel[sortBy] || "TERBARU" }}</span>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="14"
            height="14"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="transition-transform duration-200"
            :class="showSortDropdown ? 'rotate-180' : 'rotate-0'">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </button>

        <Transition name="fade">
          <div
            v-if="showSortDropdown"
            class="absolute top-full mt-2 right-0 w-full bg-[#1c1516] border border-[#2c2021] rounded-lg shadow-xl overflow-hidden z-50 flex flex-col">
            <button
              @click="handleSelectSort('newest')"
              class="px-4 py-3 text-xs font-bold text-[#e5e5e5] uppercase tracking-wider hover:bg-[#9a203e] hover:text-white transition-colors text-center border-b border-[#2c2021]/50 last:border-0"
              :class="{ 'bg-[#2c2021]': sortBy === 'newest' }">
              Terbaru
            </button>
            <button
              @click="handleSelectSort('oldest')"
              class="px-4 py-3 text-xs font-bold text-[#e5e5e5] uppercase tracking-wider hover:bg-[#9a203e] hover:text-white transition-colors text-center"
              :class="{ 'bg-[#2c2021]': sortBy === 'oldest' }">
              Terlama
            </button>
          </div>
        </Transition>

        <div
          v-if="showSortDropdown"
          class="fixed inset-0 z-40 bg-transparent cursor-default"
          @click="showSortDropdown = false"></div>
      </div>

      <slot name="actions"></slot>
    </div>
  </div>
</template>

<style scoped>
/* Animasi halus untuk dropdown */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-5px);
}
</style>
