// src/lib/useCardTheme.js
import { computed } from "vue";

// Definisi tema dipindah ke sini (state global/const)
const cardThemes = [
  {
    // 1. RED (Original)
    id: "red",
    bg: "bg-[#1c1516]",
    border: "border-[#2c2021]",
    hover: "hover:border-[#9a203e]/50 hover:shadow-[0_15px_40px_-10px_rgba(154,32,62,0.3)]",
    gradient: "from-[#9a203e]/10",
    text: "text-[#9a203e]",
    text_hover: "group-hover/card:text-[#9a203e]",
    bg_color: "bg-[#9a203e]",
    btn_bg: "bg-[#9a203e]/10",
    btn_border: "border-[#9a203e]/30",
    btn_text: "text-[#9a203e]",
    btn_hover: "hover:bg-[#9a203e]",
    modal_btn: "bg-[#9a203e] hover:bg-[#7d1a33] shadow-[0_0_20px_rgba(154,32,62,0.3)]",
    shadow: "shadow-[0_0_50px_-12px_rgba(154,32,62,0.6)]",
  },
  {
    // 2. ORANGE (Hangat)
    id: "orange",
    bg: "bg-[#191210]",
    border: "border-[#38221c]",
    hover: "hover:border-[#f97316]/50 hover:shadow-[0_15px_40px_-10px_rgba(249,115,22,0.3)]",
    gradient: "from-[#f97316]/10",
    text: "text-[#f97316]",
    text_hover: "group-hover/card:text-[#f97316]",
    bg_color: "bg-[#f97316]",
    btn_bg: "bg-[#f97316]/10",
    btn_border: "border-[#f97316]/30",
    btn_text: "text-[#f97316]",
    btn_hover: "hover:bg-[#f97316]",
    modal_btn: "bg-[#f97316] hover:bg-[#c2410c] shadow-[0_0_20px_rgba(249,115,22,0.3)]",
    shadow: "shadow-[0_0_50px_-12px_rgba(249,115,22,0.6)]",
  },
  {
    // 3. GREEN (Alam)
    id: "green",
    bg: "bg-[#051810]",
    border: "border-[#142e1e]",
    hover: "hover:border-[#22c55e]/50 hover:shadow-[0_15px_40px_-10px_rgba(34,197,94,0.3)]",
    gradient: "from-[#22c55e]/10",
    text: "text-[#22c55e]",
    text_hover: "group-hover/card:text-[#22c55e]",
    bg_color: "bg-[#22c55e]",
    btn_bg: "bg-[#22c55e]/10",
    btn_border: "border-[#22c55e]/30",
    btn_text: "text-[#22c55e]",
    btn_hover: "hover:bg-[#22c55e]",
    modal_btn: "bg-[#22c55e] hover:bg-[#15803d] shadow-[0_0_20px_rgba(34,197,94,0.3)]",
    shadow: "shadow-[0_0_50px_-12px_rgba(34,197,94,0.6)]",
  },
  {
    // 4. TEAL (Segar)
    id: "teal",
    bg: "bg-[#042f2e]",
    border: "border-[#115e59]",
    hover: "hover:border-[#14b8a6]/50 hover:shadow-[0_15px_40px_-10px_rgba(20,184,166,0.3)]",
    gradient: "from-[#14b8a6]/10",
    text: "text-[#14b8a6]",
    text_hover: "group-hover/card:text-[#14b8a6]",
    bg_color: "bg-[#14b8a6]",
    btn_bg: "bg-[#14b8a6]/10",
    btn_border: "border-[#14b8a6]/30",
    btn_text: "text-[#14b8a6]",
    btn_hover: "hover:bg-[#14b8a6]",
    modal_btn: "bg-[#14b8a6] hover:bg-[#0f766e] shadow-[0_0_20px_rgba(20,184,166,0.3)]",
    shadow: "shadow-[0_0_50px_-12px_rgba(20,184,166,0.6)]",
  },
  {
    // 5. BLUE (Langit)
    id: "blue",
    bg: "bg-[#0f172a]",
    border: "border-[#1e293b]",
    hover: "hover:border-[#38bdf8]/50 hover:shadow-[0_15px_40px_-10px_rgba(56,189,248,0.3)]",
    gradient: "from-[#38bdf8]/10",
    text: "text-[#38bdf8]",
    text_hover: "group-hover/card:text-[#38bdf8]",
    bg_color: "bg-[#38bdf8]",
    btn_bg: "bg-[#38bdf8]/10",
    btn_border: "border-[#38bdf8]/30",
    btn_text: "text-[#38bdf8]",
    btn_hover: "hover:bg-[#38bdf8]",
    modal_btn: "bg-[#38bdf8] hover:bg-[#0284c7] shadow-[0_0_20px_rgba(56,189,248,0.3)]",
    shadow: "shadow-[0_0_50px_-12px_rgba(56,189,248,0.6)]",
  },
  {
    // 6. INDIGO (Misterius - BARU)
    id: "indigo",
    bg: "bg-[#1e1b4b]", // Very dark indigo
    border: "border-[#312e81]",
    hover: "hover:border-[#818cf8]/50 hover:shadow-[0_15px_40px_-10px_rgba(129,140,248,0.3)]",
    gradient: "from-[#818cf8]/10",
    text: "text-[#818cf8]",
    text_hover: "group-hover/card:text-[#818cf8]",
    bg_color: "bg-[#818cf8]",
    btn_bg: "bg-[#818cf8]/10",
    btn_border: "border-[#818cf8]/30",
    btn_text: "text-[#818cf8]",
    btn_hover: "hover:bg-[#818cf8]",
    modal_btn: "bg-[#6366f1] hover:bg-[#4338ca] shadow-[0_0_20px_rgba(129,140,248,0.3)]",
    shadow: "shadow-[0_0_50px_-12px_rgba(129,140,248,0.6)]",
  },
  {
    // 7. PURPLE (Elegan)
    id: "purple",
    bg: "bg-[#16121d]",
    border: "border-[#2d2438]",
    hover: "hover:border-[#a855f7]/50 hover:shadow-[0_15px_40px_-10px_rgba(168,85,247,0.3)]",
    gradient: "from-[#a855f7]/10",
    text: "text-[#a855f7]",
    text_hover: "group-hover/card:text-[#a855f7]",
    bg_color: "bg-[#a855f7]",
    btn_bg: "bg-[#a855f7]/10",
    btn_border: "border-[#a855f7]/30",
    btn_text: "text-[#a855f7]",
    btn_hover: "hover:bg-[#a855f7]",
    modal_btn: "bg-[#a855f7] hover:bg-[#7e22ce] shadow-[0_0_20px_rgba(168,85,247,0.3)]",
    shadow: "shadow-[0_0_50px_-12px_rgba(168,85,247,0.6)]",
  },
  {
    // 8. PINK (Romantis - BARU)
    id: "pink",
    bg: "bg-[#1f1216]", // Dark Pinkish Black
    border: "border-[#382226]", // Dark Pink Border
    hover: "hover:border-[#ec4899]/50 hover:shadow-[0_15px_40px_-10px_rgba(236,72,153,0.3)]",
    gradient: "from-[#ec4899]/10",
    text: "text-[#ec4899]",
    text_hover: "group-hover/card:text-[#ec4899]",
    bg_color: "bg-[#ec4899]",
    btn_bg: "bg-[#ec4899]/10",
    btn_border: "border-[#ec4899]/30",
    btn_text: "text-[#ec4899]",
    btn_hover: "hover:bg-[#ec4899]",
    modal_btn: "bg-[#ec4899] hover:bg-[#be185d] shadow-[0_0_20px_rgba(236,72,153,0.3)]",
    shadow: "shadow-[0_0_50px_-12px_rgba(236,72,153,0.6)]",
  },
  {
    // 9. AMBER (Mewah/Emas)
    // Warna dasar coklat keemasan gelap agar text putih tetap terbaca
    id: "amber",
    bg: "bg-[#271c19]",
    border: "border-[#451a03]",
    hover: "hover:border-[#f59e0b]/50 hover:shadow-[0_15px_40px_-10px_rgba(245,158,11,0.3)]",
    gradient: "from-[#f59e0b]/10",
    text: "text-[#f59e0b]",
    text_hover: "group-hover/card:text-[#f59e0b]",
    bg_color: "bg-[#f59e0b]",
    btn_bg: "bg-[#f59e0b]/10",
    btn_border: "border-[#f59e0b]/30",
    btn_text: "text-[#f59e0b]",
    btn_hover: "hover:bg-[#f59e0b]",
    modal_btn: "bg-[#f59e0b] hover:bg-[#b45309] text-black shadow-[0_0_20px_rgba(245,158,11,0.3)]", // Text black khusus kuning biar kontras
    shadow: "shadow-[0_0_50px_-12px_rgba(245,158,11,0.6)]",
  },
  {
    // 10. CYAN (Futuristik)
    // Beda dengan Teal (Teal lebih ke hijau, Cyan lebih ke biru neon)
    id: "cyan",
    bg: "bg-[#083344]",
    border: "border-[#164e63]",
    hover: "hover:border-[#06b6d4]/50 hover:shadow-[0_15px_40px_-10px_rgba(6,182,212,0.3)]",
    gradient: "from-[#06b6d4]/10",
    text: "text-[#06b6d4]",
    text_hover: "group-hover/card:text-[#06b6d4]",
    bg_color: "bg-[#06b6d4]",
    btn_bg: "bg-[#06b6d4]/10",
    btn_border: "border-[#06b6d4]/30",
    btn_text: "text-[#06b6d4]",
    btn_hover: "hover:bg-[#06b6d4]",
    modal_btn: "bg-[#06b6d4] hover:bg-[#0891b2] shadow-[0_0_20px_rgba(6,182,212,0.3)]",
    shadow: "shadow-[0_0_50px_-12px_rgba(6,182,212,0.6)]",
  },
  {
    // 11. LIME (Electric/Energetic)
    // Nuansa hijau stabilo/acid yang sangat pop
    id: "lime",
    bg: "bg-[#1a2e05]", // Dark Moss
    border: "border-[#365314]",
    hover: "hover:border-[#84cc16]/50 hover:shadow-[0_15px_40px_-10px_rgba(132,204,22,0.3)]",
    gradient: "from-[#84cc16]/10",
    text: "text-[#84cc16]",
    text_hover: "group-hover/card:text-[#84cc16]",
    bg_color: "bg-[#84cc16]",
    btn_bg: "bg-[#84cc16]/10",
    btn_border: "border-[#84cc16]/30",
    btn_text: "text-[#84cc16]",
    btn_hover: "hover:bg-[#84cc16]",
    // Text tombol hitam agar kontras dengan warna lime yang terang
    modal_btn: "bg-[#84cc16] hover:bg-[#4d7c0f] text-black shadow-[0_0_20px_rgba(132,204,22,0.3)]",
    shadow: "shadow-[0_0_50px_-12px_rgba(132,204,22,0.6)]",
  },
  {
    // 12. SLATE / SILVER (Monochrome/Premium)
    // Tema tanpa warna (Grayscale), memberikan kesan futuristik/metalik
    id: "slate",
    bg: "bg-[#0f172a]", // Very dark slate (hampir hitam)
    border: "border-[#334155]",
    hover: "hover:border-[#cbd5e1]/50 hover:shadow-[0_15px_40px_-10px_rgba(203,213,225,0.3)]", // Putih keabuan
    gradient: "from-[#cbd5e1]/10",
    text: "text-[#cbd5e1]", // Slate-300
    text_hover: "group-hover/card:text-[#cbd5e1]",
    bg_color: "bg-[#cbd5e1]",
    btn_bg: "bg-[#cbd5e1]/10",
    btn_border: "border-[#cbd5e1]/30",
    btn_text: "text-[#cbd5e1]",
    btn_hover: "hover:bg-[#cbd5e1]",
    // Tombol putih/silver dengan text hitam
    modal_btn: "bg-[#e2e8f0] hover:bg-[#94a3b8] text-black shadow-[0_0_20px_rgba(226,232,240,0.3)]",
    shadow: "shadow-[0_0_50px_-12px_rgba(226,232,240,0.6)]",
  },
];

// Composable function
export function useCardTheme() {
  // Fungsi utama untuk mendapatkan tema berdasarkan ID
  const getTheme = (id) => {
    if (!id) return cardThemes[0];
    const index = id % cardThemes.length;
    return cardThemes[index];
  };

  // Helper untuk mendapatkan tema note yang sedang dipilih (untuk Modal)
  // Perlu passing 'selectedNote' yang berupa Ref atau reactive object
  const getSelectedTheme = (selectedNote) => {
    return computed(() => {
      // Handle jika selectedNote null atau selectedNote.value null
      const note = selectedNote?.value || selectedNote;
      if (!note) return cardThemes[0];
      return getTheme(note.id);
    });
  };

  return {
    cardThemes, // Opsional jika ingin akses raw array
    getTheme,
    getSelectedTheme,
  };
}
