import { describe, it, expect, vi, beforeEach } from "vitest";

// Kita simulasi fungsi guard yang ada di main.js
// Karena fungsi itu ada di dalam main.js (tidak diexport), kita copy logic-nya ke sini untuk ditest
const adminGuard = (next) => {
  const user = JSON.parse(localStorage.getItem("user"));
  if (user && user.role === "admin") {
    next();
  } else {
    // alertError("Hanya admin yang boleh masuk!"); // Kita mock alert ini
    next("/dashboard/global");
  }
};

describe("Admin Route Guard Logic", () => {
  let nextMock;

  beforeEach(() => {
    // Reset mock dan storage sebelum setiap test
    nextMock = vi.fn();
    localStorage.clear();
    vi.clearAllMocks();
  });

  it("HARUS mengizinkan akses (next) jika role adalah admin", () => {
    // 1. Setup: Simpan user admin di localStorage
    const adminUser = { name: "Admin", role: "admin" };
    localStorage.setItem("user", JSON.stringify(adminUser));

    // 2. Jalankan guard
    adminGuard(nextMock);

    // 3. Assert: next() dipanggil tanpa argumen (artinya lolos)
    expect(nextMock).toHaveBeenCalledWith();
    expect(nextMock).not.toHaveBeenCalledWith("/dashboard/global");
  });

  it("HARUS redirect ke /dashboard/global jika role BUKAN admin", () => {
    // 1. Setup: Simpan user biasa di localStorage
    const normalUser = { name: "User", role: "user" };
    localStorage.setItem("user", JSON.stringify(normalUser));

    // 2. Jalankan guard
    adminGuard(nextMock);

    // 3. Assert: next dipanggil dengan rute redirect
    expect(nextMock).toHaveBeenCalledWith("/dashboard/global");
  });

  it("HARUS redirect ke /dashboard/global jika tidak ada user di storage", () => {
    // 1. Setup: Storage kosong
    localStorage.clear();

    // 2. Jalankan guard
    adminGuard(nextMock);

    // 3. Assert
    expect(nextMock).toHaveBeenCalledWith("/dashboard/global");
  });
});
