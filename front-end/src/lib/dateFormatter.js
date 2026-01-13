import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import "dayjs/locale/id"; // Import bahasa Indonesia

// Aktifkan plugin relativeTime (untuk "yang lalu")
dayjs.extend(relativeTime);
dayjs.locale("id"); // Set default bahasa ke Indonesia

export const formatTime = (dateString, relativeTo = null) => {
  if (!dateString) return "";

  const date = dayjs(dateString);
  // Gunakan waktu yang dipassing (reactive) atau waktu sekarang
  const now = relativeTo ? dayjs(relativeTo) : dayjs();

  // Logic ala WhatsApp Status:
  // Jika bedanya lebih dari 24 jam, tampilkan tanggal lengkap
  // Jika kurang dari 24 jam, tampilkan "x jam yang lalu" atau "baru saja"
  if (now.diff(date, "day") >= 1) {
    return date.format("D MMM YYYY • HH:mm"); // Contoh: 12 Jan 2024 • 14:30
  }

  return date.fromNow(); // Contoh: "beberapa detik yang lalu", "5 menit yang lalu"
};

// Fungsi cek apakah sudah diedit
export const isEdited = (createdAt, updatedAt) => {
  if (!createdAt || !updatedAt) return false;

  // Ubah logic: Cek apakah updated_at lebih baru dari created_at (beda > 0 detik)
  // Kita pakai diff dalam satuan 'second' agar edit cepat pun terdeteksi
  return dayjs(updatedAt).diff(dayjs(createdAt), "second") > 0;
};
