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
