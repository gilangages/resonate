import { describe, it, expect } from "vitest";
import { computed, ref } from "vue";

// Simulasi logika computed yang ada di FillContent.vue
describe("Masonry Column Logic", () => {
  // Setup data dummy 4 note
  const notes = ref([
    { id: 1, text: "Note 1" },
    { id: 2, text: "Note 2" },
    { id: 3, text: "Note 3" },
    { id: 4, text: "Note 4" },
  ]);

  it("Desktop (width >= 768): Membagi menjadi 3 kolom", () => {
    const width = ref(1024); // Simulasi layar Desktop

    // Logika yang sama persis dengan di component
    const columns = computed(() => {
      if (width.value < 768) return [notes.value];

      const cols = [[], [], []];
      notes.value.forEach((note, index) => {
        cols[index % 3].push(note);
      });
      return cols;
    });

    // Kolom 1 harus berisi index 0 (Note 1) dan index 3 (Note 4)
    expect(columns.value[0]).toHaveLength(2);
    expect(columns.value[0][0].id).toBe(1);
    expect(columns.value[0][1].id).toBe(4);

    // Kolom 2 harus berisi index 1 (Note 2)
    expect(columns.value[1]).toHaveLength(1);
    expect(columns.value[1][0].id).toBe(2);

    // Kolom 3 harus berisi index 2 (Note 3)
    expect(columns.value[2]).toHaveLength(1);
    expect(columns.value[2][0].id).toBe(3);
  });

  it("Mobile (width < 768): Tetap 1 kolom utuh", () => {
    const width = ref(400); // Simulasi layar Mobile

    const columns = computed(() => {
      if (width.value < 768) return [notes.value];

      const cols = [[], [], []];
      notes.value.forEach((note, index) => {
        cols[index % 3].push(note);
      });
      return cols;
    });

    // Harus return array berisi 1 array saja (kolom tunggal)
    expect(columns.value).toHaveLength(1);

    // Kolom tunggal itu harus berisi SEMUA note secara urut
    expect(columns.value[0]).toHaveLength(4);
    expect(columns.value[0][0].id).toBe(1);
    expect(columns.value[0][1].id).toBe(2);
    expect(columns.value[0][2].id).toBe(3);
  });
});
