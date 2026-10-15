import { Link } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { usePageLocation } from '../hooks/usePageLocation';

export function NavLink({
  href,
  end = false,
  children,
}: {
  href: string;
  end?: boolean;
  children: ReactNode;
}) {
  const { pathname } = usePageLocation();
  const path = href.split(/[?#]/)[0];
  const active = pathname === path || (!end && pathname.startsWith(`${path}/`));
  return (
    <Link
      href={href}
      className={active ? 'active' : undefined}
      aria-current={active ? 'page' : undefined}
    >
      {children}
    </Link>
  );
}
