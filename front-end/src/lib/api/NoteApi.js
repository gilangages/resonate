export const noteCreate = async (
  token,
  { content, recipient, spotify_track_id, spotify_track_name, spotify_artist, spotify_album_image }
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
      spotify_track_id,
      spotify_track_name,
      spotify_artist,
      spotify_album_image,
    }),
  });
};
