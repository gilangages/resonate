<script setup>
import Navbar from "./Navbar.vue";
// Import NavbarDashboard (sesuaikan path folder jika perlu)
import NavbarDashboard from "../../Dashboard/Section/NavbarDashboard.vue";
import Footer from "./Footer.vue";
import { ref, onMounted } from "vue";
import Swal from "sweetalert2";

// State untuk Form
const form = ref({
  name: "",
  email: "",
  subject: "Bug Report",
  message: "",
});

const isLoading = ref(false);

// --- LOGIKA UI DROPDOWN ---
const isDropdownOpen = ref(false);
const dropdownOptions = [
  { value: "Bug Report", label: "🐛 Melaporkan Bug / Error" },
  { value: "Feature Request", label: "💡 Saran Fitur Baru" },
  { value: "Report Content", label: "🚨 Lapor Konten Tidak Pantas" },
  { value: "General Question", label: "👋 Pertanyaan Umum" },
  { value: "Other", label: "Lainnya" },
];

const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value;
};

const selectOption = (value) => {
  form.value.subject = value;
  isDropdownOpen.value = false;
};

// Logic Kirim Pesan
const submitContact = async () => {
  if (!form.value.name || !form.value.email || !form.value.message) {
    Swal.fire({
      icon: "warning",
      title: "Data Belum Lengkap",
      text: "Mohon isi nama, email, dan pesan Anda.",
      confirmButtonColor: "#9a203e",
    });
    return;
  }

  isLoading.value = true;

  try {
    const response = await fetch(`${import.meta.env.VITE_APP_PATH}/contact`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify(form.value),
    });

    const result = await response.json();

    if (response.ok) {
      Swal.fire({
        icon: "success",
        title: "Terkirim!",
        text: "Laporanmu telah masuk ke email kami. Terima kasih!",
        confirmButtonColor: "#9a203e",
      });
      // Reset form
      form.value = {
        name: "",
        email: "",
        subject: "Bug Report",
        message: "",
      };
    } else {
      throw new Error(result.message || "Gagal mengirim pesan");
    }
  } catch (error) {
    console.error(error);
    Swal.fire({
      icon: "error",
      title: "Gagal",
      text: "Terjadi kesalahan saat mengirim pesan. Coba lagi nanti.",
      confirmButtonColor: "#9a203e",
    });
  } finally {
    isLoading.value = false;
  }
};

// --- LOGIKA CEK LOGIN ---
const isLoggedIn = ref(false);

onMounted(() => {
  const token = localStorage.getItem("token");
  if (token) {
    isLoggedIn.value = true;
  }
});
</script>

<template>
  <div
    class="min-h-screen flex flex-col bg-[#0f0505] font-['Poppins'] selection:bg-[#9a203e] selection:text-white pb-28 md:pb-0">
    <NavbarDashboard class="sticky top-0 z-50" v-if="isLoggedIn" />
    <Navbar class="sticky top-0 z-50" v-else />

    <div
      class="flex-grow flex flex-col items-center justify-center px-6 py-12 md:px-8 mt-16 relative overflow-x-hidden">
      <div class="relative z-10 text-center max-w-4xl mx-auto mb-16 animate-fade-in-up" style="animation-delay: 0.1s">
        <h1 class="text-4xl md:text-6xl font-extrabold text-[#9a203e] mb-6 tracking-tight drop-shadow-sm">
          Tentang Resonate
        </h1>

        <div class="space-y-6 text-base md:text-lg text-[#cccccc] leading-relaxed font-light">
          <p>
            Resonate lahir dari sebuah kesadaran sederhana: terkadang, kata-kata saja tidak cukup untuk mewakili apa
            yang kita rasakan.
          </p>
          <p>
            Kami percaya bahwa setiap perasaan layak untuk didengar. Entah itu surat cinta yang tak terkirim, curahan
            hati yang berat, atau sekadar lelucon untuk menghibur diri sendiri. Namun, teks seringkali terasa datar.
            Itulah mengapa di sini, kata-kata digabungkan dengan musik.
          </p>
        </div>
      </div>

      <div
        class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-6xl w-full relative z-10 animate-fade-in-up"
        style="animation-delay: 0.3s">
        <div
          class="group bg-[#1c1516] p-8 rounded-3xl border border-[#9a203e]/20 hover:border-[#9a203e] transition-all duration-300">
          <div
            class="w-14 h-14 rounded-2xl bg-[#0f0505] border border-[#9a203e]/20 flex items-center justify-center mb-6 group-hover:bg-[#9a203e] group-hover:text-white transition-all duration-300">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-7 w-7 text-[#9a203e] group-hover:text-white transition-colors duration-300"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
            </svg>
          </div>
          <h3 class="text-xl font-bold text-white mb-3">Amplifikasi Rasa</h3>
          <p class="text-sm text-gray-400 leading-relaxed group-hover:text-gray-300 transition-colors">
            Musik adalah bahasa universal. Dengan menyematkan lagu dari Deezer, pesanmu tidak hanya dibaca, tapi juga
            "dirasakan".
          </p>
        </div>

        <div
          class="group bg-[#1c1516] p-8 rounded-3xl border border-[#9a203e]/20 hover:border-[#9a203e] transition-all duration-300">
          <div
            class="w-14 h-14 rounded-2xl bg-[#0f0505] border border-[#9a203e]/20 flex items-center justify-center mb-6 group-hover:bg-[#9a203e] group-hover:text-white transition-all duration-300">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-7 w-7 text-[#9a203e] group-hover:text-white transition-colors duration-300"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
            </svg>
          </div>
          <h3 class="text-xl font-bold text-white mb-3">Tanpa Penghakiman</h3>
          <p class="text-sm text-gray-400 leading-relaxed group-hover:text-gray-300 transition-colors">
            Lewat fitur Anonim, Kami menciptakan ruang aman. Kamu bebas menjadi dirimu sendiri tanpa perlu khawatir
            tentang identitas.
          </p>
        </div>

        <div
          class="group bg-[#1c1516] p-8 rounded-3xl border border-[#9a203e]/20 hover:border-[#9a203e] transition-all duration-300">
          <div
            class="w-14 h-14 rounded-2xl bg-[#0f0505] border border-[#9a203e]/20 flex items-center justify-center mb-6 group-hover:bg-[#9a203e] group-hover:text-white transition-all duration-300">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-7 w-7 text-[#9a203e] group-hover:text-white transition-colors duration-300"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h3 class="text-xl font-bold text-white mb-3">Jejak Digital</h3>
          <p class="text-sm text-gray-400 leading-relaxed group-hover:text-gray-300 transition-colors">
            Anggap ini sebagai jurnal emosionalmu. Tempat di mana kenangan visual dan audio tersimpan rapi untuk masa
            depan.
          </p>
        </div>
      </div>

      <div class="mt-24 w-full max-w-3xl animate-fade-in-up relative z-10" style="animation-delay: 0.5s">
        <div
          class="bg-[#1c1516] border border-[#9a203e]/30 rounded-[2rem] p-8 md:p-12 shadow-[0_0_40px_-10px_rgba(154,32,62,0.1)] relative overflow-hidden group hover:border-[#9a203e] transition-colors duration-300">
          <div class="text-center mb-10 relative z-10">
            <h2 class="text-3xl font-bold text-white mb-3 tracking-tight">Kirim Laporan & Masukan</h2>
            <p class="text-gray-400 text-sm md:text-base max-w-lg mx-auto">
              Menemukan error, konten tidak pantas, atau punya ide fitur? Kirim pesan di bawah.
              <br />
              <span
                class="text-[#ff4d6d] font-semibold text-xs mt-2 inline-block bg-[#0f0505] px-3 py-1 rounded-full border border-[#9a203e]/20">
                *Pesan dikirim langsung ke email pribadi developer.
              </span>
            </p>
          </div>

          <form @submit.prevent="submitContact" class="space-y-6 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-2 group/input">
                <label class="text-xs font-bold text-[#9a203e] uppercase tracking-widest ml-1">Nama</label>
                <input
                  v-model="form.name"
                  type="text"
                  placeholder="Nama Kamu"
                  class="w-full bg-[#0f0505] border border-gray-700/50 rounded-xl px-4 py-4 text-white placeholder:text-gray-600 focus:border-[#9a203e] focus:outline-none focus:ring-1 focus:ring-[#9a203e] transition-all" />
              </div>

              <div class="space-y-2 group/input">
                <label class="text-xs font-bold text-[#9a203e] uppercase tracking-widest ml-1">Email</label>
                <input
                  v-model="form.email"
                  type="email"
                  placeholder="emailkamu@gmail.com"
                  class="w-full bg-[#0f0505] border border-gray-700/50 rounded-xl px-4 py-4 text-white placeholder:text-gray-600 focus:border-[#9a203e] focus:outline-none focus:ring-1 focus:ring-[#9a203e] transition-all" />
              </div>
            </div>

            <div class="space-y-2 relative group/input">
              <label class="text-xs font-bold text-[#9a203e] uppercase tracking-widest ml-1">Kategori</label>

              <div
                @click="toggleDropdown"
                class="w-full bg-[#0f0505] border border-gray-700/50 rounded-xl px-4 py-4 text-white cursor-pointer flex justify-between items-center hover:border-[#9a203e] transition-all relative z-20"
                :class="{ 'border-[#9a203e] ring-1 ring-[#9a203e]': isDropdownOpen }">
                <span class="truncate">
                  {{ dropdownOptions.find((o) => o.value === form.subject)?.label || form.subject }}
                </span>

                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-[#9a203e] transition-transform duration-300"
                  :class="{ 'rotate-180': isDropdownOpen }"
                  viewBox="0 0 20 20"
                  fill="currentColor">
                  <path
                    fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
                </svg>
              </div>

              <div
                v-if="isDropdownOpen"
                class="absolute left-0 right-0 top-full mt-2 bg-[#1c1516] border border-[#9a203e]/30 rounded-xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.8)] z-50 overflow-hidden animate-fade-in-fast">
                <ul class="py-1">
                  <li
                    v-for="option in dropdownOptions"
                    :key="option.value"
                    @click="selectOption(option.value)"
                    class="px-4 py-3 text-white hover:bg-[#9a203e]/20 hover:text-[#ff4d6d] cursor-pointer transition-colors border-b border-white/5 last:border-0 text-sm md:text-base flex items-center gap-2">
                    {{ option.label }}
                    <span v-if="form.subject === option.value" class="ml-auto text-[#9a203e] font-bold">✓</span>
                  </li>
                </ul>
              </div>

              <div
                v-if="isDropdownOpen"
                @click="isDropdownOpen = false"
                class="fixed inset-0 z-10 cursor-default"></div>
            </div>

            <div class="space-y-2 group/input">
              <label class="text-xs font-bold text-[#9a203e] uppercase tracking-widest ml-1">Pesan</label>
              <textarea
                v-model="form.message"
                rows="5"
                placeholder="Tulis pesanmu di sini secara detail..."
                class="w-full bg-[#0f0505] border border-gray-700/50 rounded-xl px-4 py-4 text-white placeholder:text-gray-600 focus:border-[#9a203e] focus:outline-none focus:ring-1 focus:ring-[#9a203e] transition-all resize-none"></textarea>
            </div>

            <button
              type="submit"
              :disabled="isLoading"
              class="w-full py-4 bg-[#9a203e] hover:bg-[#7a1830] text-white font-bold rounded-xl transition-all shadow-[0_5px_20px_-5px_rgba(154,32,62,0.4)] hover:shadow-[0_8px_30px_-5px_rgba(154,32,62,0.6)] flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed mt-4 transform active:scale-[0.98]">
              <span v-if="isLoading" class="flex items-center gap-2">
                <svg
                  class="animate-spin h-5 w-5 text-white"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Mengirim...
              </span>
              <span v-else class="flex items-center gap-2">
                Kirim Pesan
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-4 w-4"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  stroke-width="3">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
              </span>
            </button>
          </form>
        </div>
      </div>

      <div class="mt-12 w-full max-w-2xl text-center animate-fade-in-up relative z-10" style="animation-delay: 0.6s">
        <div class="flex flex-col items-center gap-4">
          <h3 class="text-white font-bold text-lg">Dukungan Untuk Developer ☕</h3>

          <p class="text-gray-400 text-sm max-w-lg mx-auto leading-relaxed">
            Resonate dikembangkan dan dikelola seorang diri (Solo Dev) dengan sumber daya mandiri. Jika tempat ini
            bermakna untukmu, dukungan darimu akan menjadi bahan bakar semangat bagi developer untuk terus merawat,
            memperbaiki, dan menjaga Resonate agar tetap ada.
          </p>

          <a
            href="https://saweria.co/qbdian"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-3 px-8 py-3 bg-[#eeb424] hover:bg-[#d4a01e] text-[#0f0505] font-bold rounded-full transition-all transform hover:scale-105 shadow-[0_0_20px_-5px_rgba(238,180,36,0.4)] mt-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path
                fill-rule="evenodd"
                d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                clip-rule="evenodd" />
            </svg>
            Traktir Kopi / Dukung
          </a>
        </div>
      </div>
      <div class="mt-16 text-center relative z-10 animate-fade-in-up" style="animation-delay: 0.7s">
        <p class="text-[10px] text-gray-500 uppercase tracking-[0.3em] mb-4">Developed Solo By</p>

        <div
          class="card-abdian-glow inline-flex items-center justify-center gap-3 px-8 py-3 rounded-full bg-white/5 border border-white/10 cursor-default group">
          <div class="w-2 h-2 rounded-full bg-[#9a203e]"></div>
          <span class="font-semibold text-white tracking-widest group-hover:text-[#ff4d6d] transition-colors">
            Abdian
          </span>
        </div>
      </div>
    </div>

    <Footer />
  </div>
</template>

<style scoped>
/* Style tetap sama seperti sebelumnya */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(40px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.animate-fade-in-up {
  opacity: 0;
  animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes fadeInFast {
  from {
    opacity: 0;
    transform: translateY(-5px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.animate-fade-in-fast {
  animation: fadeInFast 0.2s ease-out forwards;
}

@keyframes abdian-pulse {
  0%,
  100% {
    border-color: rgba(255, 255, 255, 0.1);
    box-shadow: 0 0 0 transparent;
  }
  50% {
    border-color: rgba(154, 32, 62, 0.6);
    box-shadow: 0 0 15px rgba(154, 32, 62, 0.3);
    background-color: rgba(154, 32, 62, 0.1);
  }
}

.card-abdian-glow {
  animation: abdian-pulse 3s infinite ease-in-out;
}
</style>
