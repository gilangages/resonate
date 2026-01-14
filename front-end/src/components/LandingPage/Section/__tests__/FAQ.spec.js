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

    // Ambil item pertama
    const firstQuestion = wrapper.findAll(".cursor-pointer")[0];

    // Cek awal (tertutup) - logicnya class opacity-0
    expect(firstQuestion.classes()).not.toContain("border-[#9a203e]/50");

    // Klik
    await firstQuestion.trigger("click");

    // Cek setelah klik (harus terbuka/aktif)
    expect(firstQuestion.classes()).toContain("border-[#9a203e]/50");
  });

  it("contains relevant content about Deezer", () => {
    const wrapper = mount(FAQ);
    expect(wrapper.text()).toContain("Deezer");
  });
});
