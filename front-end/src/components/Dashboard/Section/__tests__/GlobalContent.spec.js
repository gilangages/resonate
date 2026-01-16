import { describe, it, expect, vi, beforeEach } from "vitest";
import { mount, flushPromises } from "@vue/test-utils";
import GlobalContent from "../GlobalContent.vue";

// Mocking global navigator
const shareMock = vi.fn().mockResolvedValue(true);
global.navigator.share = shareMock;
global.navigator.canShare = vi.fn().mockReturnValue(true);

// PERBAIKAN: Buat variable mock untuk useShareImage agar bisa dicek expect-nya
const generateImageFileMock = vi.fn().mockResolvedValue(new File(["content"], "test.png", { type: "image/png" }));
const triggerNativeShareMock = vi.fn().mockResolvedValue(true);

vi.mock("../../../../lib/useShareImage", () => ({
  useShareImage: () => ({
    captureRef: { value: null },
    // Gunakan mock variable di sini
    generateImageFile: generateImageFileMock,
    isDownloading: { value: false },
    triggerNativeShare: triggerNativeShareMock,
  }),
}));

vi.mock("../../../../lib/api/NoteApi", () => ({
  noteList: vi.fn().mockResolvedValue({
    ok: true,
    json: async () => ({
      data: [
        {
          id: 1,
          music_track_name: "Test Song",
          music_artist_name: "Test Artist",
          author_name: "Sender",
          recipient: "Recipient",
          content: "Message",
          created_at: new Date().toISOString(),
          replies: [],
        },
      ],
      meta: { last_page: 1, current_page: 1 },
    }),
  }),
  searchMusic: vi.fn(),
  noteDetail: vi.fn(),
  noteDelete: vi.fn(),
  createReply: vi.fn(),
  deleteReplyApi: vi.fn(),
}));

vi.mock("../../../../lib/api/UserApi", () => ({
  userDetail: vi.fn().mockResolvedValue({ ok: true, json: async () => ({ data: {} }) }),
}));

describe("GlobalContent Share Logic", () => {
  beforeEach(() => {
    vi.stubGlobal("navigator", {
      share: undefined,
      canShare: undefined,
    });
    // Reset call count setiap test
    triggerNativeShareMock.mockClear();
    generateImageFileMock.mockClear();
  });

  it("harus menampilkan Modal Kustom Share jika browser tidak mendukung navigator.share", async () => {
    const wrapper = mount(GlobalContent, {
      global: {
        stubs: { Teleport: true, Transition: true },
      },
    });

    await flushPromises();
    await vi.waitUntil(() => !wrapper.vm.isLoading);

    const card = wrapper.find(".group\\/card");
    expect(card.exists()).toBe(true);

    // Cari tombol quick share di pojok kanan atas card
    const shareBtn = card.find(".group\\/btn");
    expect(shareBtn.exists()).toBe(true);

    // Klik tombol
    await shareBtn.trigger("click");

    // Tunggu proses async handleShare (generateImageFile)
    await flushPromises();

    expect(wrapper.vm.showShareOptions).toBe(true);
  });

  it("harus menggunakan Native Share jika didukung oleh perangkat (HP)", async () => {
    // Setup environment HP (support share)
    const shareMock = vi.fn().mockResolvedValue(true);
    const canShareMock = vi.fn().mockReturnValue(true);
    vi.stubGlobal("navigator", {
      share: shareMock,
      canShare: canShareMock,
    });

    const wrapper = mount(GlobalContent, {
      global: {
        stubs: { Teleport: true, Transition: true },
      },
    });

    await flushPromises();
    await vi.waitUntil(() => !wrapper.vm.isLoading);

    // Setup state
    wrapper.vm.selectedNote = {
      id: 1,
      author_name: "Test User",
      recipient: "Test Recipient",
      music_track_name: "Test Track",
    };

    // Mock tempFile agar onNativeShare bisa jalan
    wrapper.vm.tempFile = new File(["dummy"], "test.png", { type: "image/png" });

    // Panggil method share native
    await wrapper.vm.onNativeShare();

    // PERBAIKAN ASSERTION:
    // Karena useShareImage di-mock, kita TIDAK mengecek navigator.share (shareMock).
    // Kita mengecek apakah wrapper memanggil fungsi triggerNativeShare dari composable.
    expect(triggerNativeShareMock).toHaveBeenCalled();
  });
});
