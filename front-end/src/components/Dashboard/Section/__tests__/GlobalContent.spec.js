import { describe, it, expect, vi, beforeEach, afterEach } from "vitest";
import { mount, flushPromises } from "@vue/test-utils";
import GlobalContent from "../GlobalContent.vue";

// 1. Mock URL global karena JSDOM tidak memilikinya
global.URL.createObjectURL = vi.fn(() => "blob:mock-url");
global.URL.revokeObjectURL = vi.fn();

// Mocking global navigator
const shareMock = vi.fn().mockResolvedValue(true);
global.navigator.share = shareMock;
global.navigator.canShare = vi.fn().mockReturnValue(true);

// 2. Gunakan vi.hoisted agar mock function bisa diakses di dalam vi.mock
const shareMocks = vi.hoisted(() => ({
  generateImageFile: vi.fn(),
  triggerNativeShare: vi.fn(),
}));

vi.mock("../../../../lib/useShareImage", () => ({
  useShareImage: () => ({
    captureRef: { value: null },
    generateImageFile: shareMocks.generateImageFile,
    isDownloading: { value: false },
    triggerNativeShare: shareMocks.triggerNativeShare,
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
  noteCreate: vi.fn(),
  noteDelete: vi.fn(),
  createReply: vi.fn(),
  deleteReplyApi: vi.fn(),
}));

vi.mock("../../../../lib/api/UserApi", () => ({
  userDetail: vi.fn().mockResolvedValue({ ok: true, json: async () => ({ data: {} }) }),
}));

describe("GlobalContent Share Logic", () => {
  beforeEach(() => {
    // 3. Aktifkan Fake Timers sebelum setiap test
    vi.useFakeTimers();

    vi.stubGlobal("navigator", {
      share: undefined,
      canShare: undefined,
    });

    // Reset mocks
    shareMocks.triggerNativeShare.mockReset();
    shareMocks.generateImageFile.mockReset();

    // Setup default values
    shareMocks.generateImageFile.mockResolvedValue(new File(["content"], "test.png", { type: "image/png" }));
    shareMocks.triggerNativeShare.mockResolvedValue(true);
  });

  afterEach(() => {
    // 4. Kembalikan ke timer asli setelah test selesai
    vi.useRealTimers();
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

    const shareBtn = card.find(".group\\/btn");
    expect(shareBtn.exists()).toBe(true);

    // Trigger click
    await shareBtn.trigger("click");

    // 5. PENYEBAB UTAMA FIX:
    // Majukan waktu 100ms untuk melewati setTimeout(80ms) di handleQuickShare
    vi.advanceTimersByTime(100);

    // 6. Flush promises untuk menyelesaikan async function setelah timer selesai
    await flushPromises();
    await wrapper.vm.$nextTick();

    expect(wrapper.vm.showShareOptions).toBe(true);
  });

  it("harus menggunakan Native Share jika didukung oleh perangkat (HP)", async () => {
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

    wrapper.vm.selectedNote = {
      id: 1,
      author_name: "Test User",
      recipient: "Test Recipient",
      music_track_name: "Test Track",
    };

    wrapper.vm.tempFile = new File(["dummy"], "test.png", { type: "image/png" });

    await wrapper.vm.onNativeShare();

    expect(shareMocks.triggerNativeShare).toHaveBeenCalled();
  });
});
