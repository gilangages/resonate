import { describe, it, expect, vi, beforeEach, afterEach } from "vitest";
import { useShareImage } from "../useShareImage"; // Pastikan path sesuai
import * as htmlToImage from "html-to-image";
import { ref } from "vue";

// Mock html-to-image
vi.mock("html-to-image", () => ({
  toBlob: vi.fn(),
}));

// Mock Alert
vi.mock("../alert", () => ({
  alertError: vi.fn(),
}));

describe("useShareImage Logic", () => {
  let originalNavigator;

  beforeEach(() => {
    // Setup Mock DOM Element
    const mockElement = document.createElement("div");
    Object.defineProperty(mockElement, "clientHeight", { value: 500 });
    Object.defineProperty(mockElement, "querySelector", { value: vi.fn().mockReturnValue(null) });
  });

  afterEach(() => {
    vi.clearAllMocks();
  });

  it("harus mengembalikan File object jika generate berhasil", async () => {
    const { generateImageFile, captureRef } = useShareImage();

    // Setup Refs
    captureRef.value = document.createElement("div");

    // Mock toBlob success
    const mockBlob = new Blob(["dummy-image-content"], { type: "image/png" });
    htmlToImage.toBlob.mockResolvedValue(mockBlob);

    const result = await generateImageFile("test-file");

    // Assertions
    expect(htmlToImage.toBlob).toHaveBeenCalled();
    expect(result).toBeInstanceOf(File);
    expect(result.name).toBe("test-file.png");
    expect(result.type).toBe("image/png");
  });

  it("harus return null dan log error jika html-to-image gagal (misal error CORS)", async () => {
    const { generateImageFile, captureRef } = useShareImage();

    captureRef.value = document.createElement("div");

    // Mock toBlob failure
    const error = new Error("Network Error");
    // Simulasi error CORS pada image
    error.target = { tagName: "IMG" };
    htmlToImage.toBlob.mockRejectedValue(error);

    const result = await generateImageFile("fail-test");

    expect(result).toBeNull();
    // Bisa cek alertError dipanggil jika perlu
  });
});
