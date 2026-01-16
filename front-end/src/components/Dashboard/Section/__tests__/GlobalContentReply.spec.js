import { describe, it, expect, vi } from "vitest";
import { mount, flushPromises } from "@vue/test-utils";
import GlobalContent from "../GlobalContent.vue";
import { userDetail } from "../../../../lib/api/UserApi";
// Import fungsi aslinya untuk kita manipulasi mock-nya di dalam test
import { deleteReplyApi, noteDetail, noteList } from "../../../../lib/api/NoteApi";

// Setup Mock Modul
vi.mock("../../../../lib/api/NoteApi", () => ({
  // Definisikan semua fungsi yang diimport component
  noteList: vi.fn(),
  searchMusic: vi.fn(),
  noteDetail: vi.fn(),
  noteCreate: vi.fn(),
  noteDelete: vi.fn(),
  createReply: vi.fn(),
  deleteReplyApi: vi.fn(),
}));

vi.mock("../../../../lib/api/UserApi", () => ({
  userDetail: vi.fn(),
}));

vi.mock("../../../../lib/useShareImage", () => ({
  useShareImage: () => ({
    captureRef: { value: null },
    generateImageFile: vi.fn(),
    triggerNativeShare: vi.fn(),
    isDownloading: { value: false },
  }),
}));

vi.mock("../../../../lib/alert", () => ({
  alertSuccess: vi.fn(),
  alertError: vi.fn(),
  alertConfirm: vi.fn().mockResolvedValue(true),
}));

describe("GlobalContent Reply Logic", () => {
  it("shows delete button for own reply and calls delete API", async () => {
    // 1. Setup Return Value Mock
    // Pastikan deleteReplyApi mengembalikan object { ok: true }
    vi.mocked(deleteReplyApi).mockResolvedValue({ ok: true });

    // Mock data user
    vi.mocked(userDetail).mockResolvedValue({
      ok: true,
      json: async () => ({ data: { id: 100, role: "user" } }),
    });

    // Mock noteList agar tidak error saat mounted
    vi.mocked(noteList).mockResolvedValue({
      ok: true,
      json: async () => ({ data: [] }),
    });

    // Mock noteDetail (dipanggil setelah delete sukses)
    vi.mocked(noteDetail).mockResolvedValue({
      ok: true,
      json: async () => ({ data: { id: 1, replies: [] } }),
    });

    const wrapper = mount(GlobalContent, {
      global: {
        stubs: {
          Teleport: true,
          Transition: true,
        },
      },
    });

    wrapper.vm.token = "mock-token";
    await wrapper.vm.fetchCurrentUser();

    // 2. Setup Note dengan Reply
    wrapper.vm.selectedNote = {
      id: 1,
      replies: [
        { id: 501, user_id: 100, music_track_name: "My Reply" },
        { id: 502, user_id: 200, music_track_name: "Other Reply" },
      ],
    };

    wrapper.vm.showModal = true;
    await wrapper.vm.$nextTick();

    // 3. Cari tombol hapus (untuk user_id 100)
    const deleteBtns = wrapper.findAll('button[title="Hapus Balasan"]');
    expect(deleteBtns.length).toBe(1);

    // 4. Klik hapus
    await deleteBtns[0].trigger("click");

    // Tunggu async process selesai
    await flushPromises();

    // 5. Pastikan API terpanggil dengan argumen yang benar
    expect(deleteReplyApi).toHaveBeenCalledWith("mock-token", 501);
  });
});
