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
