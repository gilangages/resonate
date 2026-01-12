import { customFetch } from "./BaseApi";

// 1. Ambil semua notifikasi milik user
export const getNotifications = async (token) => {
  return await customFetch(`${import.meta.env.VITE_APP_PATH}/notifications`, {
    method: "GET",
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
  });
};

// 2. Hitung jumlah notifikasi yang belum dibaca (untuk badge lonceng, jika nanti mau dipasang)
export const getUnreadCount = async (token, id = null) => {
  return await customFetch(`${import.meta.env.VITE_APP_PATH}/notifications/mark-read`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
    body: JSON.stringify(body),
  });
};

// 3. Tandai notifikasi sebagai sudah dibaca
// Jika id kosong, berarti "Mark All as Read"
export const markNotificationsRead = async (token, id = null) => {
  const body = id ? { id } : {};

  return await customFetch(`${import.meta.env.VITE_APP_PATH}/notifications/mark-read`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
      Authorization: `Bearer ${token}`, // Token wajib dikirim
    },
    body: JSON.stringify(body),
  });
};
