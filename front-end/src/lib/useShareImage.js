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
      let contentHeight = node.clientHeight;

      if (scrollableContent) {
        const extraHeight = scrollableContent.scrollHeight - scrollableContent.clientHeight;
        contentHeight = node.clientHeight + extraHeight - 30;
      }

      // Generate Blob
      const blob = await toBlob(node, {
        cacheBust: true,
        pixelRatio: 3,
        backgroundColor: null,
        height: contentHeight,
        skipAutoScale: true,
        imageTimeout: 5000, // Perbesar timeout
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
            clonedNode.style.paddingBottom = "30px";
            clonedNode.style.flex = "1 1 auto";
            clonedNode.style.borderBottomLeftRadius = "24px";
            clonedNode.style.borderBottomRightRadius = "24px";
          }
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

      if (!blob) throw new Error("Gagal generate blob gambar");

      return blobToFile(blob, `${fileName}.png`);
    } catch (error) {
      console.error("Error generate image:", error);
      // Deteksi error gambar (429/CORS)
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
    // Cek dukungan browser
    if (navigator.share && navigator.canShare && navigator.canShare({ files: [file] })) {
      try {
        await navigator.share({
          files: [file],
          title: title,
          text: "Cek profil musikku di Resonate!",
        });
        return true; // Berhasil share
      } catch (err) {
        if (err.name !== "AbortError") {
          console.error("Native share error:", err);
        }
        return false; // Gagal atau dibatalkan
      }
    }
    return false; // Browser tidak support
  };

  return {
    captureRef,
    generateImageFile,
    isDownloading,
    triggerNativeShare,
  };
}
