// src/lib/api/BaseApi.js
import router from "../../main";

export async function customFetch(url, options = {}) {
  const response = await fetch(url, options);

  // Jika token expired atau tidak valid
  if (response.status === 401) {
    localStorage.removeItem("token");
    sessionStorage.clear();

    // Redirect ke login
    router.push("/login");
    // Atau jika router diimport dengan betul: router.push('/login');

    return Promise.reject("Unauthorized - Redirecting to login");
  }

  return response;
}
