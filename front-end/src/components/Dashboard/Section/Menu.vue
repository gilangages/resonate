<script setup></script>
<template>
  <div class="flex justify-center m-[2em]">
    <RouterLink
      to="/dashboard/global"
      class="bg-[#1c1516] px-[12px] py-[12px] text-[#e5e5e5] text-[16px] font-semibold rounded-l-[10px] hover:bg-[#130f0f] hover:text-[#cdcccc] no-underline">
      Jelajahi Pesan
    </RouterLink>

    <RouterLink
      to="/dashboard"
      class="bg-[#9a203e] px-[12px] py-[12px] text-[#e5e5e5] text-[16px] font-semibold rounded-r-[10px] hover:bg-[#821c35] hover:text-[#cdcccc] no-underline">
      Pesan Saya
    </RouterLink>
  </div>
</template>
