<script setup>
// belum ada logic, fokus ke layout dulu
</script>

<template>
  <div class="min-h-screen bg-[#0f0505] font-poppins flex flex-col">
    <!-- NAVBAR -->
    <div class="bg-[#180808] p-[12px] flex items-center justify-between">
      <h1 class="text-[#9a203e] text-[16px] ml-[1em] sm:ml-[3em] sm:text-[24px]">Resonate</h1>
      <img src="../assets/img/me.jpg" alt="me" class="w-[40px] h-[40px] rounded-full object-cover block sm:mr-[1em]" />
    </div>

    <!-- FORM CONTAINER -->
    <div class="flex justify-center items-center min-h-screen text-[#e5e5e5]">
      <div
        class="bg-[#1c1516] rounded-[20px] w-full max-w-[420px] mx-[24px] p-[12px] flex flex-col sm:max-w-[600px] sm:m-[2em] sm:p-[2em]">
        <h1 class="text-center mt-[4px]">Edit Profile</h1>

        <!-- IMAGE -->
        <div class="flex flex-col items-center justify-center text-[#e5e5e5] mt-[-8px]">
          <img src="../assets/img/me.jpg" alt="me" class="w-[102px] h-[102px] rounded-full object-cover block" />
          <p>Ganti Foto</p>
        </div>

        <!-- INFO DASAR -->
        <div class="info-dasar">
          <h2 class="mb-[8px]">Informasi Dasar</h2>

          <div class="input">
            <label for="nama">Nama</label>
            <br />
            <input
              id="nama"
              type="text"
              value="Hani"
              class="w-full bg-[#2b2122] text-[#e5e5e5] caret-[#e5e5e5] rounded-[15px] p-[1em] my-[8px] mb-[20px] border-none focus:outline-[2px] focus:outline-[#9a203e]" />
          </div>

          <button class="bg-[#9a203e] text-[#e5e5e5] font-bold rounded-[8px] p-[8px] hover:bg-[#7d1a33] cursor-pointer">
            Simpan Profile
          </button>
        </div>

        <!-- KEAMANAN -->
        <div class="keamanan mt-[2em]">
          <h2 class="mb-[8px]">Keamanan</h2>

          <div class="input">
            <label for="email">Email</label>
            <br />
            <input
              id="email"
              type="email"
              value="hani@gmail.com"
              disabled
              class="w-full bg-[#2b2122] text-[#e5e5e5] rounded-[15px] p-[1em] my-[8px] mb-[20px] border-none" />
          </div>

          <div class="input">
            <label>Password baru</label>
            <br />
            <input
              type="password"
              placeholder="Kosongkan jika tidak ingin mengganti"
              class="w-full bg-[#2b2122] text-[#e5e5e5] caret-[#e5e5e5] rounded-[15px] p-[1em] my-[8px] mb-[20px] border-none focus:outline-[2px] focus:outline-[#9a203e]" />
          </div>

          <div class="input">
            <label>Konfirmasi Password</label>
            <br />
            <input
              type="password"
              placeholder="Masukkan ulang kata sandi"
              class="w-full bg-[#2b2122] text-[#e5e5e5] caret-[#e5e5e5] rounded-[15px] p-[1em] my-[8px] mb-[20px] border-none focus:outline-[2px] focus:outline-[#9a203e]" />
          </div>

          <button class="bg-[#9a203e] text-[#e5e5e5] font-bold rounded-[8px] p-[8px] hover:bg-[#7d1a33] cursor-pointer">
            Ganti Password
          </button>
        </div>
      </div>
    </div>

    <!-- FOOTER -->
    <div id="footer">
      <p class="text-center text-[#e5e5e5] text-[10px] my-[1em] mb-[2em] sm:mb-[1em]">
        © 2026
        <span class="text-[#9a203e] sm:text-[12px]">Resonate</span>
      </p>
    </div>
  </div>
</template>

<style scoped></style>
