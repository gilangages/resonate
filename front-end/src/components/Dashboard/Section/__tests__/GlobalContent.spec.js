import { describe, it, expect, vi, beforeEach } from "vitest";
import { mount } from "@vue/test-utils";
import GlobalContent from "../GlobalContent.vue";

// Mocking global navigator
const shareMock = vi.fn().mockResolvedValue(true);
global.navigator.share = shareMock;
global.navigator.canShare = vi.fn().mockReturnValue(true);

// Mock Composable
vi.mock("../../../lib/useShareImage", () => ({
  useShareImage: () => ({
    captureRef: { value: null },
    generateImageFile: vi.fn().mockResolvedValue(new File([], "test.png", { type: "image/png" })),
    isDownloading: { value: false },
  }),
}));

describe("GlobalContent Share Logic", () => {
  let wrapper;

  beforeEach(() => {
    // Reset navigator agar bersih setiap test
    vi.stubGlobal("navigator", {
      share: undefined,
      canShare: undefined,
    });
  });

  it("harus menampilkan Modal Kustom Share jika browser tidak mendukung navigator.share", async () => {
    const wrapper = mount(GlobalContent);

    // Simulasikan klik tombol bagikan
    await wrapper.find(".btn-share").trigger("click");
    await nextTick();

    expect(wrapper.vm.showShareOptions).toBe(true);
  });

  it("harus menggunakan Native Share jika didukung oleh perangkat (HP)", async () => {
    const shareMock = vi.fn().mockResolvedValue(true);
    const canShareMock = vi.fn().mockReturnValue(true);

    // Mocking navigator agar seolah-olah ini HP yang support share
    vi.stubGlobal("navigator", {
      share: shareMock,
      canShare: canShareMock,
    });

    const wrapper = mount(GlobalContent);

    // Pastikan kita menunggu proses async di handleShare
    await wrapper.vm.handleShare();

    expect(shareMock).toHaveBeenCalled();
  });
});
