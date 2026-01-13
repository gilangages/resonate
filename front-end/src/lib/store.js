import { useLocalStorage } from "@vueuse/core";

export const userState = useLocalStorage("user-data", {
  name: "",
  email: "",
  avatar: null,
});

export const getAvatarUrl = (avatarPath) => {
  if (!avatarPath) {
    // Arahkan ke gambar default lokal kamu
    return new URL("../assets/img/me.jpg", import.meta.url).href;
  }
  // Sesuaikan URL backend kamu (biasanya localhost:8000)
  return `${import.meta.env.VITE_APP_PATH || "http://localhost:8000"}/storage/${avatarPath}`;
};
