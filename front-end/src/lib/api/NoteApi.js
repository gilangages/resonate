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

export const noteList = async (token) => {
  return await fetch(`${import.meta.env.VITE_APP_PATH}/notes`, {
    method: "GET",
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
  });
};
