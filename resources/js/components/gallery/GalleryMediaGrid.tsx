import { lazy, Suspense, useCallback, useEffect, useMemo, useRef, useState } from 'react';
import type { GalleryCollection } from '../../types/content';

const GalleryLightbox = lazy(() => import('./GalleryLightbox'));

export function GalleryMediaGrid({
  gallery,
  switching,
}: {
  gallery: GalleryCollection;
  switching: boolean;
}) {
  const grid = useRef<HTMLDivElement>(null);
  const opener = useRef<HTMLButtonElement | null>(null);
  const [activePhoto, setActivePhoto] = useState<number | null>(null);
  const photos = useMemo(
    () => gallery.media.filter((item) => item.type === 'image'),
    [gallery.media],
  );
  const photoIndexes = new Map(photos.map((photo, index) => [photo.id, index]));
  const closePreview = useCallback(() => {
    setActivePhoto(null);
    requestAnimationFrame(() => {
      if (opener.current?.isConnected) opener.current.focus({ preventScroll: true });
    });
  }, []);

  // Stop playback as soon as a new tab starts loading, and when leaving the gallery.
  useEffect(() => {
    if (switching) grid.current?.querySelectorAll('video').forEach((video) => video.pause());
  }, [switching]);
  useEffect(() => {
    const currentGrid = grid.current;
    return () => currentGrid?.querySelectorAll('video').forEach((video) => video.pause());
  }, []);

  return (
    <>
      <div ref={grid} className="gallery-masonry">
        {gallery.media.map((item) => (
          <div className="gallery-masonry-item" key={item.id}>
            {item.type === 'image' ? (
              <button
                type="button"
                className="gallery-photo-button"
                aria-label={`View ${item.alt}`}
                aria-haspopup="dialog"
                disabled={switching}
                onClick={(event) => {
                  opener.current = event.currentTarget;
                  grid.current?.querySelectorAll('video').forEach((video) => video.pause());
                  setActivePhoto(photoIndexes.get(item.id) ?? 0);
                }}
              >
                <img
                  className="gallery-photo"
                  src={item.thumbnail ?? item.src}
                  alt={item.alt}
                  width="800"
                  height="600"
                  loading="lazy"
                  decoding="async"
                />
              </button>
            ) : (
              <>
                <div className="gallery-video">
                  <video
                    controls
                    playsInline
                    preload="none"
                    poster={item.poster}
                    aria-label={item.caption || 'Gallery video'}
                    onPlay={(event) => {
                      const active = event.currentTarget;
                      grid.current?.querySelectorAll('video').forEach((video) => {
                        if (video !== active) video.pause();
                      });
                    }}
                  >
                    <source src={item.src} type={item.mimeType} />
                    {item.captions && (
                      <track
                        kind="captions"
                        src={item.captions}
                        srcLang="en"
                        label="English"
                        default
                      />
                    )}
                    Your browser cannot play this video. <a href={item.src}>Open the video</a>.
                  </video>
                </div>
                {item.transcript && (
                  <details className="gallery-transcript">
                    <summary>Read video transcript</summary>
                    <p>{item.transcript}</p>
                  </details>
                )}
              </>
            )}
          </div>
        ))}
      </div>
      {activePhoto !== null && !switching && (
        <Suspense
          fallback={
            <div className="gallery-preview-loading">
              <p role="status">Opening photo preview…</p>
              <button type="button" onClick={closePreview}>
                Cancel
              </button>
            </div>
          }
        >
          <GalleryLightbox
            photos={photos}
            title={gallery.title}
            startingIndex={activePhoto}
            onClose={closePreview}
          />
        </Suspense>
      )}
    </>
  );
}
