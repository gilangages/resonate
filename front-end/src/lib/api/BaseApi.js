// BaseApi.js
export async function customFetch(url, options = {}) {
  const response = await fetch(url, options);

  // Cek apakah ini request ke login?
  const isLoginRequest = url.includes("/users/login");

  // Jika 401 DAN BUKAN request login, baru lakukan logout paksa
  if (response.status === 401 && !isLoginRequest) {
    localStorage.removeItem("token");
    sessionStorage.clear();

    window.location.href = "/login";
    return Promise.reject("Session expired");
  }

  return response;
}
