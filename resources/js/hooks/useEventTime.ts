import { useEffect, useState } from 'react';
import { useContent } from '../content';

/** Start from the server clock so SSR, hydration and visitor clock settings agree. */
export function useEventTime() {
  const { now: serverTime } = useContent();
  const [clock, setClock] = useState({ asOf: serverTime, time: serverTime });

  useEffect(() => {
    const started = performance.now();
    const update = () => {
      setClock({ asOf: serverTime, time: serverTime + performance.now() - started });
    };
    const timer = window.setInterval(update, 30_000);
    document.addEventListener('visibilitychange', update);
    return () => {
      window.clearInterval(timer);
      document.removeEventListener('visibilitychange', update);
    };
  }, [serverTime]);

  return clock.asOf === serverTime ? clock.time : serverTime;
}
