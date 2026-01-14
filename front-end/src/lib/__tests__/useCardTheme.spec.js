import { describe, it, expect, beforeEach, vi } from "vitest";
import { useCardTheme } from "../useCardTheme";

describe("useCardTheme Logic Test", () => {
  beforeEach(() => {
    localStorage.clear();
    vi.clearAllMocks();
  });

  it('harus menggunakan tema "red" sebagai default untuk user baru', () => {
    const { globalThemePreference, initTheme } = useCardTheme();
    initTheme(123); // Simulasi User ID 123
    expect(globalThemePreference.value).toBe("red");
  });

  it("harus menyimpan dan memuat tema yang dipilih user (Persistence)", () => {
    const { setTheme, globalThemePreference, initTheme } = useCardTheme();
    const userId = 456;

    initTheme(userId);
    setTheme("blue");
    expect(globalThemePreference.value).toBe("blue");

    // Simulasi Refresh: Panggil initTheme lagi dengan ID yang sama
    initTheme(userId);
    expect(globalThemePreference.value).toBe("blue");
  });

  it("harus memisahkan tema antar user yang berbeda", () => {
    const { setTheme, initTheme, globalThemePreference } = useCardTheme();

    // User A pilih biru
    initTheme("user_A");
    setTheme("blue");

    // User B login (harus default merah)
    initTheme("user_B");
    expect(globalThemePreference.value).toBe("red");

    // User A balik lagi (harus tetap biru)
    initTheme("user_A");
    expect(globalThemePreference.value).toBe("blue");
  });
});
