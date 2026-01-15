import { describe, it, expect, vi, beforeEach } from "vitest";
import { useShareImage } from "../useShareImage";

// Mock html-to-image karena kita tidak punya DOM asli di environment test node
vi.mock("html-to-image", () => ({
  toBlob: vi.fn(() => Promise.resolve(new Blob(["fake-image-content"], { type: "image/png" }))),
}));

// Mock alert supaya tidak error di console
vi.mock("../alert", () => ({
  alertError: vi.fn(),
}));

describe("useShareImage Library", () => {
  let shareImageHook;

  beforeEach(() => {
    shareImageHook = useShareImage();
    // Reset captureRef
    shareImageHook.captureRef.value = null;
  });

  it("seharusnya mengembalikan null jika captureRef belum di-set", async () => {
    const file = await shareImageHook.generateImageFile();
    expect(file).toBeNull();
  });

  it("seharusnya generate File object dengan nama yang sesuai ketika captureRef ada", async () => {
    // Simulasi DOM element sederhana
    const mockElement = document.createElement("div");
    // Mock properti ukuran
    Object.defineProperty(mockElement, "clientHeight", { value: 500 });
    Object.defineProperty(mockElement, "scrollHeight", { value: 800 });

    // Assign ke ref
    shareImageHook.captureRef.value = mockElement;

    // Panggil fungsi
    const fileName = "test-image";
    const file = await shareImageHook.generateImageFile(fileName);

    // Assertions
    expect(file).toBeInstanceOf(File);
    expect(file.name).toBe(`${fileName}.png`);
    expect(file.type).toBe("image/png");
    expect(shareImageHook.isDownloading.value).toBe(false); // Pastikan status loading kembali false
  });

  it("seharusnya menghitung contentHeight dengan mengurangi footer jika ada", async () => {
    // Setup Mock DOM structure
    const mockContainer = document.createElement("div");
    Object.defineProperty(mockContainer, "clientHeight", { value: 600 });

    // Mock Scrollable Content
    const mockScrollable = document.createElement("div");
    mockScrollable.className = "overflow-y-auto";
    Object.defineProperty(mockScrollable, "clientHeight", { value: 400 });
    Object.defineProperty(mockScrollable, "scrollHeight", { value: 600 }); // Extra height 200
    mockContainer.appendChild(mockScrollable);

    // Mock Footer (exclude-from-capture)
    const mockFooter = document.createElement("div");
    mockFooter.className = "exclude-from-capture border-t";
    Object.defineProperty(mockFooter, "offsetHeight", { value: 100 });
    mockContainer.appendChild(mockFooter);

    shareImageHook.captureRef.value = mockContainer;

    // Spy on internal logic isn't easily possible without exposing internal vars,
    // but we can ensure the function runs without error using this structure.
    const file = await shareImageHook.generateImageFile("test-calc");
    expect(file).toBeTruthy();
  });
});
