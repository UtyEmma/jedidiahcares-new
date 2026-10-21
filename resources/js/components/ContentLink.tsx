import { Link } from '@inertiajs/react';
import type { AnchorHTMLAttributes } from 'react';

/** Editable content destinations may be internal paths or complete external HTTPS URLs. */
type ContentLinkProps = Pick<
  AnchorHTMLAttributes<HTMLAnchorElement>,
  'children' | 'className' | 'aria-label' | 'aria-hidden' | 'tabIndex' | 'title' | 'target' | 'rel'
> & { href: string };

export function ContentLink(props: ContentLinkProps) {
  const Component = props.href.startsWith('https://') ? 'a' : Link;
  return <Component {...props} />;
}
