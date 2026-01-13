<script setup lang="ts">
// belum ada logic, masih UI murni (sesuai HTML asli)
</script>

<template>
  <div class="flex flex-col justify-center items-center bg-[#0f0505] text-[#e5e5e5] font-poppins">
    <!-- FORM CONTAINER -->
    <div class="flex min-h-screen w-full flex-col items-center justify-start px-[24px] py-[40px]">
      <!-- FORM CARD -->
      <div class="w-full max-w-[420px] rounded-[20px] bg-[#1c1516] p-4 sm:max-w-[560px]">
        <!-- TITLE -->
        <h1 class="text-center text-[#9a203e] text-[24px] font-semibold m-0">Buat Pesan Baru</h1>
        <p class="mt-0 mb-[3em] text-center text-[12px] text-[#8c8a8a]">Pilih lagu dan tulis pesan untuk seseorang.</p>

        <!-- PILIH LAGU -->
        <div class="text-[14px]">
          <label>Pilih Lagu</label>
          <input
            type="text"
            placeholder="Ketik judul lagu atau nama penyani..."
            class="mt-[6px] mb-[20px] w-full rounded-[10px] bg-[#2b2122] p-4 text-[#e5e5e5] caret-[#e5e5e5] focus:outline focus:outline-2 focus:outline-[#9a203e]" />
        </div>

        <!-- KEPADA -->
        <div class="text-[14px]">
          <label>Kepada</label>
          <input
            type="text"
            placeholder="Kepada siapa pesanmu"
            class="mt-[6px] mb-[20px] w-full rounded-[10px] bg-[#2b2122] p-4 text-[#e5e5e5] caret-[#e5e5e5] focus:outline focus:outline-2 focus:outline-[#9a203e]" />
        </div>

        <!-- PESAN -->
        <div class="text-[14px]">
          <label>Pesan</label>
          <textarea
            placeholder="Tulis pesanmu"
            class="mt-[6px] mb-[20px] h-[120px] w-full resize-y rounded-[10px] bg-[#2b2122] p-4 text-[#e5e5e5] focus:outline focus:outline-2 focus:outline-[#9a203e]"></textarea>
        </div>

        <!-- KIRIM SEBAGAI -->
        <div class="text-[14px]">
          <label class="kirim">Kirim Sebagai</label>
          <div class="mt-[6px] mb-[20px] flex items-center gap-2">
            <input type="radio" name="kirim" class="cursor-pointer" />
            <span class="mr-[2em]">Hani (Asli)</span>

            <input type="radio" name="kirim" class="cursor-pointer" />
            <span>Nama Samaran</span>
          </div>

          <label class="text-[#9a203e]">Nama Samaran</label>
          <input
            type="text"
            placeholder="Contoh: Secret Admirer, Inisial A, atau kosongkan..."
            class="mt-[6px] mb-[20px] w-full rounded-[10px] bg-[#2b2122] p-4 text-[#e5e5e5] caret-[#e5e5e5] focus:outline focus:outline-2 focus:outline-[#9a203e]" />
        </div>

        <!-- BUTTONS -->
        <div class="mt-[26px] flex gap-[10px]">
          <button
            class="w-full rounded-[10px] border border-[#8c8a8a] bg-[#1c1516] px-3 py-3 font-semibold text-[#8c8a8a] hover:border-[#666565] hover:bg-[#120e0e]">
            Kembali
          </button>

          <button class="w-full rounded-[10px] bg-[#9a203e] px-3 py-3 font-semibold text-[#e5e5e5] hover:bg-[#7d1a33]">
            Buat Pesan
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
