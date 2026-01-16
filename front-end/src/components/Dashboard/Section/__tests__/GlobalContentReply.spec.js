import { describe, it, expect, vi } from "vitest";
import { mount, flushPromises } from "@vue/test-utils";
import GlobalContent from "../GlobalContent.vue";
import { userDetail } from "../../../../lib/api/UserApi";
import { deleteReplyApi } from "../../../../lib/api/NoteApi";

// PERBAIKAN: Definisikan mock function di luar agar bisa di-spy dengan mudah
const deleteReplyApiMock = vi.fn().mockResolvedValue({ ok: true });

vi.mock("../../../../lib/api/NoteApi", () => ({
  noteList: vi.fn().mockResolvedValue({ ok: true, json: async () => ({ data: [] }) }),
  searchMusic: vi.fn(),
  noteDetail: vi.fn(),
  noteDelete: vi.fn(),
  createReply: vi.fn(),
  // PERBAIKAN: Gunakan mock yang sudah didefinisikan dengan default value
  deleteReplyApi: deleteReplyApiMock,
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
    const wrapper = mount(GlobalContent, {
      global: {
        stubs: {
          Teleport: true,
          Transition: true,
        },
      },
    });

    // 1. Mock Current User
    userDetail.mockResolvedValue({
      ok: true,
      json: async () => ({ data: { id: 100, role: "user" } }),
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

    // 3. Cari tombol hapus
    const deleteBtns = wrapper.findAll('button[title="Hapus Balasan"]');
    expect(deleteBtns.length).toBe(1);

    // 4. Klik hapus
    await deleteBtns[0].trigger("click");

    // Tunggu async process selesai
    await flushPromises();

    // 5. Pastikan API terpanggil
    expect(deleteReplyApiMock).toHaveBeenCalledWith(expect.anything(), 501);
  });
});
