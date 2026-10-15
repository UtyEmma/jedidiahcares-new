import { useEffect, type RefObject } from 'react';

/** Adds motion to selected content as it enters view, without hiding static HTML. */
export function useEntranceMotion(container: RefObject<HTMLElement | null>, route: string) {
  useEffect(() => {
    const root = container.current;
    if (!root || !('IntersectionObserver' in window) || !('animate' in Element.prototype)) return;

    const preference = window.matchMedia('(prefers-reduced-motion: reduce)');
    const animations = new Map<HTMLElement, Animation>();
    const targets = Array.from(root.querySelectorAll<HTMLElement>('[data-entrance]'));
    let observer: IntersectionObserver | undefined;
    let frame = 0;

    function stop() {
      window.cancelAnimationFrame(frame);
      observer?.disconnect();
      animations.forEach((animation) => animation.cancel());
      animations.clear();
    }

    function start() {
      stop();
      if (preference.matches) return;
      observer = new IntersectionObserver(
        (entries) => {
          let order = 0;
          entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            const element = entry.target as HTMLElement;
            observer?.unobserve(element);
            // Keyboard navigation should never wait for an entrance effect.
            if (element.contains(document.activeElement)) return;
            const animation = element.animate(
              [
                { opacity: 0, transform: 'translateY(14px)' },
                { opacity: 1, transform: 'translateY(0)' },
              ],
              {
                duration: 420,
                delay: Math.min(order++ * 45, 135),
                easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
                fill: 'backwards',
              },
            );
            animations.set(element, animation);
            animation.finished.then(
              () => animations.delete(element),
              () => animations.delete(element),
            );
          });
        },
        { rootMargin: '0px 0px -24px 0px', threshold: 0 },
      );
      frame = window.requestAnimationFrame(() => {
        targets.forEach((element) => observer?.observe(element));
      });
    }

    function showFocusedContent(event: FocusEvent) {
      if (!(event.target instanceof Node)) return;
      animations.forEach((animation, element) => {
        if (element.contains(event.target as Node)) {
          animation.cancel();
          animations.delete(element);
        }
      });
    }

    start();
    preference.addEventListener('change', start);
    root.addEventListener('focusin', showFocusedContent);
    return () => {
      stop();
      preference.removeEventListener('change', start);
      root.removeEventListener('focusin', showFocusedContent);
    };
  }, [container, route]);
}
