import { ref } from "vue";
import { toPng } from "html-to-image";

export function useShareImage() {
  const isDownloading = ref(false);
  const captureRef = ref(null);

  const downloadImage = async (fileName = "music-note-card") => {
    if (!captureRef.value) return;

    isDownloading.value = true;

    try {
      const node = captureRef.value;

      // 1. Cari elemen scrollable
      const scrollableContent = node.querySelector(".overflow-y-auto");

      // 2. Hitung tinggi total
      let contentHeight = node.clientHeight;

      if (scrollableContent) {
        // Hitung teks yang ngumpet
        const extraHeight = scrollableContent.scrollHeight - scrollableContent.clientHeight;

        // --- PERBAIKAN DI SINI ---
        // Kita KURANGI 60px (estimasi tinggi tombol yang di-hide)
        // Kita TAMBAH 30px (biar pas untuk rounded corner bawah)
        // Jadi totalnya: Tinggi Awal + Ekstra Teks - 30px
        contentHeight = node.clientHeight + extraHeight - 30;
      }

      await new Promise((resolve) => setTimeout(resolve, 500));

      const dataUrl = await toPng(node, {
        cacheBust: true,
        pixelRatio: 3,
        backgroundColor: null,
        height: contentHeight, // Tinggi yang sudah dikurangi
        skipAutoScale: true,

        style: {
          height: `${contentHeight}px`,
          maxHeight: "none",
          overflow: "hidden",
          borderRadius: "24px",
        },

        onclone: (clonedDoc) => {
          const clonedNode = clonedDoc.querySelector(".overflow-y-auto");
          if (clonedNode) {
            clonedNode.style.overflow = "visible";
            clonedNode.style.height = "auto";
            clonedNode.style.maxHeight = "none";

            // 3. Kurangi padding ini biar ga terlalu jauh (tadi 50px)
            clonedNode.style.paddingBottom = "30px";

            // Paksa flex untuk mengisi ruang sisa tapi tidak berlebih
            clonedNode.style.flex = "1 1 auto";

            // Radius bawah
            clonedNode.style.borderBottomLeftRadius = "24px";
            clonedNode.style.borderBottomRightRadius = "24px";
          }

          // Hide tombol
          const footer = clonedDoc.querySelector(".exclude-from-capture");
          if (footer) footer.style.display = "none";
        },

        filter: (child) => {
          if (child.classList && child.classList.contains("exclude-from-capture")) {
            return false;
          }
          return true;
        },
      });

      const link = document.createElement("a");
      link.download = `${fileName}.png`;
      link.href = dataUrl;
      link.click();
    } catch (error) {
      console.error("Error detail:", error);
      alert("Gagal menyimpan. Coba refresh halaman.");
    } finally {
      isDownloading.value = false;
    }
  };

  return {
    captureRef,
    downloadImage,
    isDownloading,
  };
}
