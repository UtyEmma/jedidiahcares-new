import { useEffect, useId, useLayoutEffect, useMemo, useRef, useState } from 'react';
import { initLightboxJS, SlideshowLightbox } from 'lightbox.js-react';
import type { GalleryMedia } from '../../types/content';
import { ChevronLeft, ChevronRight, SearchMinus, SearchPlus, X } from '../icons';

let initializedLicense: string | undefined;

export default function GalleryLightbox({
  photos,
  title,
  startingIndex,
  onClose,
}: {
  photos: GalleryMedia[];
  title: string;
  startingIndex: number;
  onClose: () => void;
}) {
  const opened = useRef(false);
  const descriptionId = useId();
  const [currentIndex, setCurrentIndex] = useState(startingIndex);
  const [reducedMotion, setReducedMotion] = useState(
    () => window.matchMedia('(prefers-reduced-motion: reduce)').matches,
  );
  // Keep CMS captions/credits out of the preview; alternative text remains available.
  const images = useMemo(() => photos.map(({ src, alt }) => ({ src, alt })), [photos]);

  useEffect(() => {
    const key = import.meta.env.VITE_LIGHTBOX_LICENSE_KEY?.trim();
    if (key && initializedLicense !== key) {
      initLightboxJS(
        key,
        import.meta.env.VITE_LIGHTBOX_PLAN_TYPE === 'team' ? 'team' : 'individual',
      );
      initializedLicense = key;
    }
  }, []);

  useEffect(() => {
    const preference = window.matchMedia('(prefers-reduced-motion: reduce)');
    const update = () => setReducedMotion(preference.matches);
    preference.addEventListener('change', update);
    return () => preference.removeEventListener('change', update);
  }, []);

  useLayoutEffect(() => {
    const app = document.getElementById('app');
    const wasInert = app?.inert ?? false;
    if (app) app.inert = true;

    function containFocus(event: KeyboardEvent) {
      if (event.key !== 'Tab') return;
      const modal = document.getElementById('lightboxContainer');
      if (!modal) return;
      const controls = Array.from(
        modal.querySelectorAll<HTMLElement>('button, a[href], [tabindex]'),
      ).filter(
        (element) =>
          element.tabIndex >= 0 &&
          !element.hasAttribute('disabled') &&
          element.getClientRects().length > 0,
      );
      const first = controls[0];
      const last = controls[controls.length - 1];
      const active = document.activeElement;
      if (
        first &&
        last &&
        (!modal.contains(active) ||
          active === modal ||
          (event.shiftKey ? active === first : active === last))
      ) {
        event.preventDefault();
        event.stopPropagation();
        (event.shiftKey ? last : first).focus();
      }
    }

    document.addEventListener('keydown', containFocus, true);
    return () => {
      if (app) app.inert = wasInert;
      document.removeEventListener('keydown', containFocus, true);
    };
  }, []);

  return (
    <SlideshowLightbox
      images={images}
      open
      startingSlideIndex={startingIndex}
      lightboxIdentifier="jedidiah-gallery-preview"
      theme="night"
      arrowStyle="dark"
      backgroundColor="rgba(9, 18, 35, 0.90)"
      iconColor="#ffffff"
      textColor="#ffffff"
      showThumbnails={true}
      showThumbnailIcon={false}
      showSlideshowIcon={false}
      showFullScreenIcon={false}
      displayMetadata={false}
      downloadImages={false}
      modalClose="clickOutside"
      disableAnim={reducedMotion}
      imgAnimation={reducedMotion ? 'fade' : undefined}
      nextArrow={<ChevronRight size={28} aria-hidden="true" />}
      prevArrow={<ChevronLeft size={28} aria-hidden="true" />}
      closeComponent={
        <button type="button" className="gallery-lightbox-close" aria-label="Close">
          <X size={24} aria-hidden="true" />
        </button>
      }
      zoomInComponent={<SearchPlus size={24} aria-hidden="true" />}
      zoomOutComponent={<SearchMinus size={24} aria-hidden="true" />}
      captionStyle={{ display: 'none' }}
      lightboxFooterComponent={
        <p id={descriptionId} className="sr-only" role="status">
          Photo {currentIndex + 1} of {photos.length}. {photos[currentIndex]?.alt}
        </p>
      }
      onNext={(index: number) => setCurrentIndex(index)}
      onPrev={(index: number) => setCurrentIndex(index)}
      onOpen={() => {
        opened.current = true;
        requestAnimationFrame(() => {
          const modal = document.getElementById('lightboxContainer');
          modal?.setAttribute('aria-label', `${title} — photo preview`);
          modal?.setAttribute('aria-describedby', descriptionId);
          modal?.querySelector<HTMLElement>('button[aria-label="Close"]')?.focus();
        });
      }}
      onClose={() => {
        // The package also emits onClose on its initial closed render.
        if (opened.current) onClose();
      }}
    />
  );
}
