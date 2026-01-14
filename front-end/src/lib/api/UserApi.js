import { customFetch } from "./BaseApi";

export const userRegister = async ({ name, email, password, password_confirmation }) => {
  return await customFetch(`${import.meta.env.VITE_APP_PATH}/users`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
    },
    body: JSON.stringify({
      name,
      email,
      password,
      password_confirmation,
    }),
  });
};

export const userLogin = async ({ email, password }) => {
  return await customFetch(`${import.meta.env.VITE_APP_PATH}/users/login`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
    },
    body: JSON.stringify({
      email,
      password,
    }),
  });
};

export const userDetail = async (token) => {
  return await customFetch(`${import.meta.env.VITE_APP_PATH}/users/current`, {
    method: "GET",
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
  });
};

export const userUpdateProfile = async (token, data) => {
  return await customFetch(`${import.meta.env.VITE_APP_PATH}/users/current`, {
    method: "PATCH",
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
    body: JSON.stringify(data),
  });
};

export const userUpdatePassword = async (token, { password, password_confirmation }) => {
  return await customFetch(`${import.meta.env.VITE_APP_PATH}/users/current`, {
    method: "PATCH",
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
    body: JSON.stringify({
      password,
      password_confirmation,
    }),
  });
};

export const userUpdatePhoto = async (token, file) => {
  const formData = new FormData();
  formData.append("avatar", file);
  // Method PATCH kadang bermasalah dengan FormData di beberapa server setting,
  // tapi di Laravel modern biasanya aman. Jika error, gunakan POST dengan _method: PATCH
  formData.append("_method", "PATCH");

  return await customFetch(`${import.meta.env.VITE_APP_PATH}/users/current`, {
    method: "POST", // Gunakan POST tapi dengan spoofing method PATCH di formData
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
      // JANGAN SET Content-Type! Browser akan otomatis set boundary multipart/form-data
    },
    body: formData,
  });
};

export const userLogout = async (token) => {
  return await customFetch(`${import.meta.env.VITE_APP_PATH}/users/logout`, {
    method: "DELETE",
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
  });
};

export const forgotPassword = (email) => {
  return customFetch(`${import.meta.env.VITE_APP_PATH}/forgot-password`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ email }),
  });
};

export const resetPassword = (data) => {
  return customFetch(`${import.meta.env.VITE_APP_PATH}/reset-password`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data),
  });
};

export const getAdminUsers = async (token, page = 1, search = "") => {
  const params = new URLSearchParams({ page, search });
  return customFetch(`${import.meta.env.VITE_APP_PATH}/admin/users?${params.toString()}`, {
    method: "GET",
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
  });
};

export const deleteUserByAdmin = async (token, id) => {
  return customFetch(`${import.meta.env.VITE_APP_PATH}/admin/users/${id}`, {
    method: "DELETE",
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
  });
};

// Function Restore User (Untuk Admin)
export const restoreUserByAdmin = (token, userId) => {
  return fetch(`${import.meta.env.VITE_APP_PATH}/admin/users/${userId}/restore`, {
    method: "PATCH", // Kita pakai PATCH sesuai route
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${token}`,
    },
  });
};

// Function Kirim Banding (Public)
export const sendAppeal = (email, reason) => {
  return fetch(`${import.meta.env.VITE_APP_PATH}/users/appeal`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
    },
    body: JSON.stringify({
      email,
      message: reason,
    }),
  });
};
