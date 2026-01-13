export const userRegister = async ({ name, email, password, password_confirmation }) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/users`, {
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
  return await fetch(`${import.meta.env.VITE_APP_PATH}/users/login`, {
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
  return await fetch(`${import.meta.env.VITE_APP_PATH}/users/current`, {
    method: "GET",
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
  });
};

export const userUpdateProfile = async (token, { name }) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/users/current`, {
    method: "PATCH",
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
    body: JSON.stringify({
      name,
    }),
  });
};

export const userUpdatePassword = async (token, { password, password_confirmation }) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/users/current`, {
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

  return await fetch(`${import.meta.env.VITE_APP_PATH}/users/current`, {
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
  return await fetch(`${import.meta.env.VITE_APP_PATH}/users/logout`, {
    method: "DELETE",
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
  });
};
