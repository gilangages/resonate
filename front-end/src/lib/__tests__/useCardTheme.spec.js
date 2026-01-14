import { describe, it, expect, beforeEach } from "vitest";
import { useCardTheme } from "../useCardTheme";

describe("useCardTheme", () => {
  let { getTheme, setThemeLocally, globalThemePreference, cardThemes } = useCardTheme();

  beforeEach(() => {
    // Reset state sebelum setiap test
    setThemeLocally("red");
  });

  it("mengembalikan tema merah (default) jika preference user adalah 'red'", () => {
    setThemeLocally("red");
    const theme = getTheme(123); // ID note 123
    expect(theme.id).toBe("red");
  });

  it("mengembalikan tema biru jika preference user adalah 'blue'", () => {
    setThemeLocally("blue");
    const theme = getTheme(999); // ID note berapapun
    expect(theme.id).toBe("blue");
  });

  it("mengembalikan tema warna-warni (berbeda-beda) jika preference adalah 'random'", () => {
    setThemeLocally("random");
    expect(globalThemePreference.value).toBe("random");

    // Simulasi Note dengan ID berbeda
    const noteId1 = 1; // 1 % 12 = 1 -> theme index 1 (orange)
    const noteId2 = 2; // 2 % 12 = 2 -> theme index 2 (green)
    const noteId3 = 12; // 12 % 12 = 0 -> theme index 0 (red)

    const theme1 = getTheme(noteId1);
    const theme2 = getTheme(noteId2);
    const theme3 = getTheme(noteId3);

    // Pastikan ID temanya sesuai urutan di array cardThemes
    expect(theme1.id).toBe(cardThemes[1].id); // orange
    expect(theme2.id).toBe(cardThemes[2].id); // green
    expect(theme3.id).toBe(cardThemes[0].id); // red

    // Pastikan tema 1 dan tema 2 berbeda (warna-warni)
    expect(theme1.id).not.toBe(theme2.id);
  });
});
