import { describe, it, expect } from "vitest";
import { mount } from "@vue/test-utils";
import FAQ from "../FAQ.vue";

describe("FAQ.vue", () => {
  it("renders correctly", () => {
    const wrapper = mount(FAQ);
    expect(wrapper.text()).toContain("Pertanyaan Umum");
  });

  it("toggles answer visibility when question clicked", async () => {
    const wrapper = mount(FAQ);

    // Ambil tombol pertanyaan pertama
    const firstButton = wrapper.findAll(".cursor-pointer")[0];

    // Ambil span teks di dalamnya
    const questionText = firstButton.find("span");

    // Cek awal (tertutup) - seharusnya tidak ada class text active
    expect(questionText.classes()).not.toContain("text-[#9a203e]");

    // Klik tombol
    await firstButton.trigger("click");

    // Cek setelah klik (harus terbuka/aktif) - warna teks berubah
    expect(questionText.classes()).toContain("text-[#9a203e]");
  });

  it("contains relevant content about Deezer", () => {
    const wrapper = mount(FAQ);
    expect(wrapper.text()).toContain("Deezer");
  });
});
