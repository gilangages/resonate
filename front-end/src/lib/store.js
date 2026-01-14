import { reactive, ref } from "vue";

export const userState = ref({
  name: "",
  email: "",
  avatar: null,
  photo_url: "",
});

export const store = reactive({
  user: JSON.parse(localStorage.getItem("user")) || null,

  setUser(userData) {
    this.user = userData;
    if (userData) {
      localStorage.setItem("user", JSON.stringify(userData));
    } else {
      localStorage.removeItem("user");
    }
  },
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
  // 1. Jika avatarName kosong/null/undefined, kembalikan DiceBear
  // (Kita hapus fallback ke userState.photo_url di sini agar lebih fleksibel untuk list user lain)
  if (!avatarName) {
    return "https://api.dicebear.com/9.x/notionists/svg?seed=Guest";
  }

  // 2. Jika avatarName SUDAH berupa URL lengkap (ada 'http'), langsung kembalikan
  // Contoh: Link dari Google atau Dicebear
  if (avatarName.startsWith("http")) {
    return avatarName;
  }

  // 3. FIX: Hapus '/api' dari VITE_APP_PATH agar mengarah ke root folder storage
  // "http://localhost:8000/api" menjadi "http://localhost:8000"
  const baseUrl = import.meta.env.VITE_APP_PATH.replace(/\/api\/?$/, "");

  // 4. Return URL storage yang benar
  return `${baseUrl}/storage/${avatarName}`;
};
