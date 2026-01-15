// Tambahkan mock noteDelete di bagian atas
vi.mock("../../../../lib/api/NoteApi", () => ({
  // ... mock yg lama
  noteDelete: vi.fn(), // <--- Tambah ini
}));

// Tambahkan mock userDetail di bagian atas
vi.mock("../../../../lib/api/UserApi", () => ({
  userDetail: vi.fn(),
}));

import { userDetail, noteDelete } from "../../../../lib/api/UserApi"; // Sesuaikan import path jika perlu

// ...

it("shows delete button for own reply and calls delete API", async () => {
  const wrapper = mount(GlobalContent);

  // 1. Mock Current User
  userDetail.mockResolvedValue({
    ok: true,
    json: async () => ({ data: { id: 100, role: "user" } }),
  });
  await wrapper.vm.fetchCurrentUser(); // Trigger fetch manual di test

  // 2. Setup Note dengan Reply milik user (id 100)
  wrapper.vm.selectedNote = {
    id: 1,
    replies: [
      { id: 501, user_id: 100, music_track_name: "My Reply" }, // Milik sendiri
      { id: 502, user_id: 200, music_track_name: "Other Reply" }, // Milik orang lain
    ],
  };
  wrapper.vm.showModal = true;
  await wrapper.vm.$nextTick();

  // 3. Cari tombol hapus
  const deleteBtns = wrapper.findAll('button[title="Hapus Balasan"]');

  // Seharusnya cuma ada 1 tombol hapus (untuk id 501), id 502 tidak boleh ada tombolnya
  expect(deleteBtns.length).toBe(1);

  // 4. Mock Delete Success
  noteDelete.mockResolvedValue({ ok: true });
  window.confirm = vi.fn(() => true); // Mock confirm dialog

  // 5. Klik hapus
  await deleteBtns[0].trigger("click");

  expect(noteDelete).toHaveBeenCalledWith("fake-token", 501);
});
