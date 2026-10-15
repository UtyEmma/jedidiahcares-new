import { useEffect, useState } from 'react';
import { ArrowUp } from './icons';

export function ScrollToTop() {
  const [visible, setVisible] = useState(false);
  useEffect(() => {
    let frame = 0;
    const update = () => {
      frame = 0;
      setVisible(window.scrollY > window.innerHeight * 2);
    };
    const schedule = () => {
      if (!frame) frame = window.requestAnimationFrame(update);
    };
    update();
    window.addEventListener('scroll', schedule, { passive: true });
    window.addEventListener('resize', schedule);
    return () => {
      window.removeEventListener('scroll', schedule);
      window.removeEventListener('resize', schedule);
      window.cancelAnimationFrame(frame);
    };
  }, []);
  if (!visible) return null;
  return (
    <button
      className="scroll-to-top"
      type="button"
      aria-label="Scroll back to top"
      title="Back to top"
      onClick={() => {
        window.scrollTo({
          top: 0,
          behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches
            ? 'instant'
            : 'smooth',
        });
        document.querySelector<HTMLAnchorElement>('.logo-link')?.focus({ preventScroll: true });
      }}
    >
      <ArrowUp size={24} weight="bold" aria-hidden="true" />
    </button>
  );
}
