import { ref } from "vue";
import { toBlob } from "html-to-image";
import { alertError } from "./alert";

export function useShareImage() {
  const isDownloading = ref(false);
  const captureRef = ref(null);

  const blobToFile = (blob, fileName) => {
    return new File([blob], fileName, { type: blob.type });
  };

  /**
   * Fungsi ini hanya fokus generate gambar dan return File object.
   * Tidak melakukan share/download otomatis di sini agar lebih fleksibel.
   */
  const generateImageFile = async (fileName = "music-note-card") => {
    if (!captureRef.value) return null;

    isDownloading.value = true;

    try {
      const node = captureRef.value;

      // Tunggu sebentar untuk memastikan render rendering selesai
      await new Promise((resolve) => setTimeout(resolve, 500));

      const scrollableContent = node.querySelector(".overflow-y-auto");

      // UPDATE PERBAIKAN: Hitung total tinggi SEMUA elemen yang di-exclude
      // Bukan hanya satu elemen, tapi loop semua yang punya class 'exclude-from-capture'
      let totalExcludedHeight = 0;
      const excludedElements = node.querySelectorAll(".exclude-from-capture");

      excludedElements.forEach((el) => {
        const style = window.getComputedStyle(el);
        // Abaikan elemen yang absolute (seperti tombol close di pojok kanan atas)
        // karena elemen absolute tidak mempengaruhi tinggi layout container
        if (style.position === "absolute" || style.display === "none") return;

        const height = el.offsetHeight;
        const marginTop = parseFloat(style.marginTop) || 0;
        const marginBottom = parseFloat(style.marginBottom) || 0;

        totalExcludedHeight += height + marginTop + marginBottom;
      });

      let contentHeight = node.clientHeight;

      if (scrollableContent) {
        // Hitung tinggi konten yang tersembunyi karena scroll
        const extraHeight = scrollableContent.scrollHeight - scrollableContent.clientHeight;

        // Rumus: (Tinggi Tampil + Sisa Scroll) - Total Tinggi Elemen yang Dibuang
        contentHeight = node.clientHeight + extraHeight - totalExcludedHeight;
      } else {
        // Jika tidak ada scroll, kurangi langsung dengan elemen yang dibuang
        contentHeight = node.clientHeight - totalExcludedHeight;
      }

      // Generate Blob
      const blob = await toBlob(node, {
        cacheBust: true,
        pixelRatio: 3,
        backgroundColor: null,
        height: contentHeight,
        skipAutoScale: true,
        imageTimeout: 5000,
        includeQueryParams: true,
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

            // Padding bawah kita set secukupnya (24px) agar teks tidak mepet border bawah
            // Karena elemen footer asli sudah kita buang perhitungannya di atas
            clonedNode.style.paddingBottom = "24px";

            clonedNode.style.flex = "1 1 auto";
            clonedNode.style.borderBottomLeftRadius = "24px";
            clonedNode.style.borderBottomRightRadius = "24px";
          }

          // Sembunyikan semua elemen exclude di hasil clone (Gambar)
          const elementsToHide = clonedDoc.querySelectorAll(".exclude-from-capture");
          elementsToHide.forEach((el) => (el.style.display = "none"));
        },
        filter: (child) => {
          if (child.classList && child.classList.contains("exclude-from-capture")) {
            return false;
          }
          return true;
        },
      });

      if (!blob) throw new Error("Gagal generate blob gambar");

      return blobToFile(blob, `${fileName}.png`);
    } catch (error) {
      console.error("Error generate image:", error);
      if (error.target && error.target.tagName === "IMG") {
        await alertError("Gagal memuat gambar eksternal. Server menolak akses.");
      } else {
        await alertError("Gagal memproses gambar. Coba refresh halaman.");
      }
      return null;
    } finally {
      isDownloading.value = false;
    }
  };

  const triggerNativeShare = async (file, title = "Resonate Music Note") => {
    if (navigator.share && navigator.canShare && navigator.canShare({ files: [file] })) {
      try {
        await navigator.share({
          files: [file],
          title: title,
          text: "Cek profil musikku di Resonate!",
        });
        return true;
      } catch (err) {
        if (err.name !== "AbortError") {
          console.error("Native share error:", err);
        }
        return false;
      }
    }
    return false;
  };

  return {
    captureRef,
    generateImageFile,
    isDownloading,
    triggerNativeShare,
  };
}
