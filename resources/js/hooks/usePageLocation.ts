import { useMemo } from 'react';
import { usePage } from '@inertiajs/react';

export function usePageLocation() {
  const { url } = usePage();
  return useMemo(() => {
    const location = new URL(url, 'http://localhost');
    return {
      pathname: location.pathname,
      search: location.search,
      hash: location.hash,
      url,
    };
  }, [url]);
}
