<script setup>
// Tidak ada script tambahan
</script>

<template>
  <footer class="mt-[6em] w-full border-t border-[#9a203e]/20 bg-gradient-to-b from-transparent to-[#0F0505]">
    <div class="max-w-7xl mx-auto px-6 py-8 md:py-10 flex flex-col md:flex-row items-center justify-between gap-4">
      <div class="text-[#e5e5e5] text-[10px] tracking-wide opacity-80 hover:opacity-100 transition-opacity">
        &copy; 2026
        <span class="text-[#9a203e] font-semibold text-[12px] ml-1">Resonate</span>
      </div>

      <div>
        <router-link
          to="/about"
          class="text-[#e5e5e5]/60 hover:text-[#9a203e] text-[11px] font-medium tracking-wide transition-all duration-300 relative group">
          About
          <span
            class="absolute -bottom-1 left-0 w-0 h-[1px] bg-[#9a203e] transition-all duration-300 group-hover:w-full"></span>
        </router-link>
      </div>
    </div>
  </footer>
</template>
