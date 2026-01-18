<script setup>
// Tidak ada script tambahan
</script>

<template>
  <div class="mt-[4em] mb-[1em] px-6 md:px-8 pt-6 border-t border-[#e5e5e5]/5">
    <div class="flex flex-col md:flex-row items-center justify-between gap-3">
      <div class="text-[#e5e5e5]/50 text-[10px] tracking-wider font-jakarta">
        &copy; 2026
        <span class="text-[#9a203e] font-medium ml-1">Resonate</span>
      </div>

      <a
        href="https://www.deezer.com"
        target="_blank"
        rel="noopener noreferrer"
        class="flex items-center gap-2 opacity-50 hover:opacity-100 transition-opacity duration-300 select-none cursor-pointer"
        title="Visit Deezer.com">
        <span class="text-[9px] uppercase tracking-widest text-[#e5e5e5]/60">Music by</span>
        <img
          src="https://cdn.brandfetch.io/idEUKgCNtu/theme/light/logo.svg?c=1dxbfHSJFAPEGdCLU4o5B"
          alt="Deezer"
          class="h-3 w-auto grayscale hover:grayscale-0 transition-all" />
      </a>

      <router-link
        to="/about"
        class="text-[#e5e5e5]/40 hover:text-[#9a203e] text-[12px] font-medium transition-colors duration-300 py-2">
        About
      </router-link>
    </div>
  </div>
</template>
