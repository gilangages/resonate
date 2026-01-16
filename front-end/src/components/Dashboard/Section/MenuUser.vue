<script setup></script>

<template>
  <div class="relative w-full flex flex-col min-h-screen">
    <div
      class="sticky top-[63px] z-40 w-full bg-[#0f0505]/95 backdrop-blur-md border-b border-[#2c2021]/30 transition-all duration-500">
      <div class="relative flex justify-center items-center h-[80px] w-full overflow-hidden">
        <div class="flex gap-0">
          <RouterLink
            to="/dashboard/global"
            class="rounded-l-[10px] bg-[#1c1516] p-[12px] text-[16px] font-semibold text-[#e5e5e5] hover:bg-[#130f0f] hover:text-[#cdcccc] transition-colors shadow-lg border-r border-[#2c2021]">
            Jelajahi Pesan
          </RouterLink>

          <RouterLink
            to="/dashboard"
            class="rounded-r-[10px] bg-[#9a203e] p-[12px] text-[16px] font-semibold text-[#e5e5e5] hover:bg-[#821c35] hover:text-[#cdcccc] transition-colors shadow-lg">
            Pesan Saya
          </RouterLink>
        </div>
      </div>
    </div>

    <div class="flex-1 w-full mt-4">
      <slot />
    </div>
  </div>
</template>
