import { describe, it, expect } from "vitest";
import { mount } from "@vue/test-utils";
import Fitur from "../Fitur.vue";

describe("Fitur.vue", () => {
  it("renders correctly", () => {
    const wrapper = mount(Fitur);
    expect(wrapper.exists()).toBe(true);
  });

  it("displays exactly 6 feature items", () => {
    const wrapper = mount(Fitur);
    const items = wrapper.findAll(".group");
    expect(items.length).toBe(6);
  });

  it("displays correct feature titles based on project logic", () => {
    const wrapper = mount(Fitur);
    const text = wrapper.text();

    // Pastikan Deezer ada
    expect(text).toContain("Pustaka Deezer");
    // Pastikan Spotify TIDAK ada
    expect(text).not.toContain("Spotify");

    // Pastikan fitur Share & Tema ada
    expect(text).toContain("Share sebagai Gambar");
    expect(text).toContain("Tema & Kustomisasi");
  });

  it("displays the correct notification description (Admin only)", () => {
    const wrapper = mount(Fitur);
    const text = wrapper.text();

    // Pastikan judul notifikasi sesuai revisi
    expect(text).toContain("Info Moderasi");

    // Pastikan deskripsi menyebutkan admin/hapus, BUKAN "balasan pesan"
    expect(text).toContain("dihapus oleh admin");
    expect(text).not.toContain("membalas pesanmu");
  });
});
