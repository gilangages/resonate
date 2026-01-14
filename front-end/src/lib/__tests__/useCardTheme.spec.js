import { describe, it, expect, beforeEach, vi } from "vitest";
import { useCardTheme } from "../useCardTheme";
import { store } from "../store";
import * as UserApi from "../api/UserApi"; // Impor untuk di-mock

// Mock store
vi.mock("../store", () => ({
  store: {
    user: { card_theme: "red" },
  },
}));

// Mock API
vi.mock("../api/UserApi", () => ({
  userUpdateProfile: vi.fn().mockResolvedValue({ ok: true }),
}));

describe("useCardTheme Logic Test", () => {
  let themeLogic;

  beforeEach(() => {
    localStorage.clear();
    vi.clearAllMocks();

    // Reset state manual karena globalThemePreference adalah singleton
    themeLogic = useCardTheme();
    themeLogic.globalThemePreference.value = "red";
    store.user.card_theme = "red";
  });

  it('harus menggunakan tema "red" sebagai default', () => {
    expect(themeLogic.globalThemePreference.value).toBe("red");
  });

  it("harus memperbarui tema saat setTheme dipanggil", async () => {
    await themeLogic.setTheme("blue");

    // Cek preference lokal
    expect(themeLogic.globalThemePreference.value).toBe("blue");
    // Cek apakah API dipanggil
    expect(UserApi.userUpdateProfile).toHaveBeenCalled();
    // Cek apakah store diperbarui
    expect(store.user.card_theme).toBe("blue");
  });

  it("harus sinkron ketika store user berubah", async () => {
    // Simulasi perubahan tema dari sisi lain (misal update profil)
    store.user.card_theme = "purple";

    // Tunggu microtask agar watcher di useCardTheme.js berjalan
    await Promise.resolve();

    expect(themeLogic.globalThemePreference.value).toBe("purple");
  });
});
