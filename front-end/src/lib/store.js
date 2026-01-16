import { ref } from "vue";

export const userState = ref({
  name: "",
  email: "",
  avatar: null,
  photo_url: "",
});
/**
 * Mendapatkan URL Avatar
 * Logika: Jika user punya file avatar, load dari storage.
 * Jika tidak, load dari photo_url (DiceBear) yang dikirim backend.
 */
export const getAvatarUrl = (avatarName) => {
  // 1. Jika user sudah upload foto (avatarName terisi)
  if (avatarName) {
    // Ganti URL ini sesuai domain backend kamu saat deploy nanti.
    // Hapus '/api' jika base URL kamu mengarah ke sana. Kita butuh root domain.
    const baseUrl = "http://localhost:8000";
    return `${baseUrl}/storage/${avatarName}`;
  }

  // 2. Jika belum upload, kembalikan Generated Avatar dari backend (DiceBear)
  // Pastikan userState sudah terisi data dari backend
  return userState.value.photo_url || "https://api.dicebear.com/9.x/notionists/svg?seed=Guest";
};
