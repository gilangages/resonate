export async function customFetch(url, options = {}) {
  const response = await fetch(url, options);

  // Jika token expired atau tidak valid
  if (response.status === 401) {
    localStorage.removeItem("token");
    sessionStorage.clear();

    // Paksa reload ke halaman login
    window.location.href = "/login";
    return Promise.reject("Session expired");
  }

  return response;
}
