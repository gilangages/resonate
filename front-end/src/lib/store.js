import { ref } from "vue";

export const userState = ref({
  name: "",
  email: "",
  avatar: null, // Pastikan ini namanya 'avatar', bukan 'author_avatar' biar konsisten
  photo_url: "",
});

export const resetUserState = () => {
  userState.value = {
    name: "",
    email: "",
    avatar: null,
    photo_url: "",
  };
};

/**
 * Mendapatkan URL Avatar yang Aman
 */
export const getAvatarUrl = (avatarName) => {
  // 1. Jika avatarName kosong/null/undefined, kembalikan DiceBear (atau photo_url)
  if (!avatarName) {
    return userState.value.photo_url || "https://api.dicebear.com/9.x/notionists/svg?seed=Guest";
  }

  // 2. Jika avatarName SUDAH berupa URL lengkap (ada 'http'), langsung kembalikan
  if (avatarName.startsWith("http")) {
    return avatarName;
  }

  // 3. Jika masih path biasa ('avatars/foto.jpg'), tempelkan domain backend
  // Pastikan VITE_APP_PATH di .env tidak berakhiran slash '/', atau atur manual di sini
  return `${import.meta.env.VITE_APP_PATH}/storage/${avatarName}`;
};
