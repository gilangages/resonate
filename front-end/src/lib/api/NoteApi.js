export const noteCreate = async (
  token,
  {
    content,
    recipient,
    initial_name,
    spotify_track_id,
    spotify_track_name,
    spotify_artist,
    spotify_album_image,
    spotify_preview_url,
  }
) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/notes`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
    body: JSON.stringify({
      content,
      recipient,
      initial_name,
      spotify_track_id,
      spotify_track_name,
      spotify_artist,
      spotify_album_image,
      spotify_preview_url,
    }),
  });
};

export const searchMusic = async (token, { query }) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/music/search?q=${encodeURIComponent(query)}`, {
    method: "GET",
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
  });
};
// Function khusus untuk ambil note milik sendiri (panggil endpoint /notes/my)
export const myNoteList = async (token, page = 1) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/notes/my?page=${page}`, {
    method: "GET",
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
  });
};

export const noteDelete = async (token, id) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/notes/${id}`, {
    method: "DELETE",
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
  });
};

export const noteDetail = async (token, id) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/notes/${id}`, {
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
    spotify_track_id,
    spotify_track_name,
    spotify_artist,
    spotify_album_image,
    spotify_preview_url,
  }
) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/notes/${id}`, {
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
      spotify_track_id,
      spotify_track_name,
      spotify_artist,
      spotify_album_image,
      spotify_preview_url,
    }),
  });
};

export const noteList = async (token, page = 1) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/notes?page=${page}`, {
    method: "GET",
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
  });
};
