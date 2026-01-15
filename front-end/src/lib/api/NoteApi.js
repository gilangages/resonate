import { customFetch } from "./BaseApi";

export const noteCreate = async (
  token,
  {
    parent_id = null,
    content,
    recipient,
    initial_name,
    music_track_id,
    music_track_name,
    music_artist_name,
    music_album_image,
    music_preview_url,
  }
) => {
  return await customFetch(`${import.meta.env.VITE_APP_PATH}/notes`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
    body: JSON.stringify({
      parent_id,
      content,
      recipient,
      initial_name,
      music_track_id,
      music_track_name,
      music_artist_name,
      music_album_image,
      music_preview_url,
    }),
  });
};

export const searchMusic = async (token, { query }) => {
  return await customFetch(`${import.meta.env.VITE_APP_PATH}/music/search?q=${encodeURIComponent(query)}`, {
    method: "GET",
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
  });
};
// Function khusus untuk ambil note milik sendiri (panggil endpoint /notes/my)
export const myNoteList = async (token, page = 1, search = "", sort = "newest") => {
  const query = new URLSearchParams({
    page: page.toString(),
    search: search,
    sort: sort,
  }).toString();

  return await customFetch(`${import.meta.env.VITE_APP_PATH}/notes/my?${query}`, {
    method: "GET",
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
  });
};

export const noteDelete = async (token, id) => {
  return await customFetch(`${import.meta.env.VITE_APP_PATH}/notes/${id}`, {
    method: "DELETE",
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
  });
};

export const noteDetail = async (token, id) => {
  return await customFetch(`${import.meta.env.VITE_APP_PATH}/notes/${id}`, {
    method: "GET",
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
  });
};

export const noteUpdate = async (
  token,
  id,
  {
    content,
    recipient,
    initial_name,
    music_track_id,
    music_track_name,
    music_artist_name,
    music_album_image,
    music_preview_url,
  }
) => {
  return await customFetch(`${import.meta.env.VITE_APP_PATH}/notes/${id}`, {
    method: "PUT",
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
    body: JSON.stringify({
      content,
      recipient,
      initial_name,
      music_track_id,
      music_track_name,
      music_artist_name,
      music_album_image,
      music_preview_url,
    }),
  });
};

export const noteList = async (token, page = 1, search = "", sort = "newest") => {
  const query = new URLSearchParams({
    page: page.toString(),
    search: search,
    sort: sort,
  }).toString();

  return await customFetch(`${import.meta.env.VITE_APP_PATH}/notes?${query}`, {
    method: "GET",
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
  });
};

export const noteListGlobal = async () => {
  return await customFetch(`${import.meta.env.VITE_APP_PATH}/notes/global`, {
    method: "GET",
    headers: {
      Accept: "application/json",
    },
  });
};

export const noteBulkDelete = async (token, ids) => {
  return await customFetch(`${import.meta.env.VITE_APP_PATH}/notes/bulk-delete`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
    body: JSON.stringify({ ids }),
  });
};

export const deleteNoteByAdmin = async (token, id, reason) => {
  return customFetch(`${import.meta.env.VITE_APP_PATH}/admin/notes/${id}`, {
    method: "DELETE",
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
    body: JSON.stringify({ reason: reason }),
  });
};

// Ambil semua notes (lintas user) untuk admin
export const getAdminNotes = async (token, page = 1, search) => {
  const params = new URLSearchParams({ page, search });
  return await customFetch(`${import.meta.env.VITE_APP_PATH}/admin/notes?${params.toString()}`, {
    method: "GET",
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
  });
};
