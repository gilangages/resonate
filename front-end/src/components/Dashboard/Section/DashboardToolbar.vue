<script setup>
import { ref } from "vue";
import { useCardTheme } from "../../../lib/useCardTheme";

defineProps({
  searchQuery: { type: String, default: "" },
  sortBy: { type: String, default: "newest" },
  placeholder: { type: String, default: "Cari pesan..." },
});

const emit = defineEmits(["update:searchQuery", "update:sortBy", "search"]);

// Ambil logic tema
const { cardThemes, globalThemePreference, setTheme } = useCardTheme();

const showSortDropdown = ref(false);
const showThemeDropdown = ref(false);

const sortLabel = { newest: "TERBARU", oldest: "TERLAMA" };

const onSearchInput = (e) => {
  emit("update:searchQuery", e.target.value);
  emit("search");
};

const handleSelectSort = (value) => {
  emit("update:sortBy", value);
  emit("search");
  showSortDropdown.value = false;
};

const handleSelectTheme = (themeId) => {
  setTheme(themeId);
  showThemeDropdown.value = false;
};

// --- TAMBAHAN BARU: Helper untuk format nama tema ---
const getThemeLabel = (themeId) => {
  if (themeId === "red") return "Red (Default)";
  // Ubah huruf pertama jadi kapital (blue -> Blue)
  return themeId.charAt(0).toUpperCase() + themeId.slice(1);
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
        class="w-full bg-[#1c1516] hover:bg-[#251a1c] border border-[#2c2021] rounded-full py-2.5 pl-10 pr-4 text-white text-sm focus:outline-none focus:border-[#9a203e] transition-all placeholder-[#555]" />
    </div>

    <div class="flex gap-3 w-full md:w-auto items-center relative z-40">
      <div class="relative w-full md:w-auto">
        <button
          @click="
            showThemeDropdown = !showThemeDropdown;
            showSortDropdown = false;
          "
          class="tooltip-container-mid w-full bg-[#1c1516] text-[#e5e5e5] border border-[#2c2021] rounded-lg px-3 py-2.5 focus:outline-none focus:border-[#9a203e] cursor-pointer flex items-center justify-center gap-2 hover:bg-[#251a1c] transition-colors"
          data-title="Ganti Tema Kartu">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="18"
            height="18"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round">
            <circle cx="13.5" cy="6.5" r=".5" fill="currentColor"></circle>
            <circle cx="17.5" cy="10.5" r=".5" fill="currentColor"></circle>
            <circle cx="8.5" cy="7.5" r=".5" fill="currentColor"></circle>
            <circle cx="6.5" cy="12.5" r=".5" fill="currentColor"></circle>
            <path
              d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z"></path>
          </svg>

          <span
            class="w-3 h-3 rounded-full border border-white/20"
            :class="
              globalThemePreference === 'random'
                ? 'bg-gradient-to-tr from-blue-500 via-purple-500 to-orange-500'
                : cardThemes.find((t) => t.id === globalThemePreference)?.bg_color || 'bg-[#9a203e]'
            "></span>
        </button>

        <Transition name="fade">
          <div
            v-if="showThemeDropdown"
            class="absolute top-full mt-2 right-0 w-full md:w-[200px] bg-[#1c1516] border border-[#2c2021] rounded-lg shadow-xl overflow-hidden z-50 flex flex-col max-h-[400px] overflow-y-auto custom-scrollbar">
            <div class="px-4 py-2 text-[10px] text-[#666] font-bold uppercase tracking-wider bg-[#150f10]">
              Pilih Suasana
            </div>

            <button
              @click="handleSelectTheme('random')"
              class="px-4 py-3 text-xs font-bold text-[#e5e5e5] uppercase tracking-wider hover:bg-[#2c2021] transition-colors flex items-center gap-3 text-left border-b border-[#2c2021]/30"
              :class="{ 'bg-[#2c2021]': globalThemePreference === 'random' }">
              <div
                class="w-4 h-4 rounded-full bg-gradient-to-tr from-blue-500 via-purple-500 to-orange-500 border border-white/20"></div>
              <span>Acak (Warna-Warni)</span>
            </button>

            <button
              v-for="theme in cardThemes"
              :key="theme.id"
              @click="handleSelectTheme(theme.id)"
              class="px-4 py-3 text-xs font-bold text-[#e5e5e5] uppercase tracking-wider hover:bg-[#2c2021] transition-colors flex items-center gap-3 text-left"
              :class="{ 'bg-[#2c2021]': globalThemePreference === theme.id }">
              <div :class="[theme.bg_color]" class="w-4 h-4 rounded-full border border-white/20"></div>
              <span>{{ getThemeLabel(theme.id) }}</span>
            </button>
          </div>
        </Transition>

        <div
          v-if="showThemeDropdown"
          class="fixed inset-0 z-40 bg-transparent cursor-default"
          @click="showThemeDropdown = false"></div>
      </div>

      <div class="relative w-full md:w-auto min-w-[140px]">
        <button
          @click="
            showSortDropdown = !showSortDropdown;
            showThemeDropdown = false;
          "
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
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-5px);
}

.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #1c1516;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #2c2021;
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #9a203e;
}
</style>
