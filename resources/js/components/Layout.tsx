import type { NavItem } from '../types/content';
import { useContent } from '../content';
import { useEffect, useRef, useState, type ReactNode } from 'react';
import { Link } from '@inertiajs/react';
import { NavLink } from './NavLink';
import { usePageLocation } from '../hooks/usePageLocation';
import { ArrowUpRight, ChevronDown, Heart, Menu, X } from './icons';

import { ButtonLink } from './UI';
import { ContactLinks, SocialLinks } from './ContactLinks';
import { ScrollToTop } from './ScrollToTop';
import { useEntranceMotion } from '../hooks/useEntranceMotion';
function NavGroup({ item }: { item: NavItem }) {
  const group = useRef<HTMLDivElement>(null);
  const [open, setOpen] = useState(false);
  const location = usePageLocation();
  const activeGroup = item.children?.some((child) => {
    const path = child.href.split(/[?#]/)[0];
    return location.pathname === path || location.pathname.startsWith(`${path}/`);
  });
  useEffect(() => setOpen(false), [location]);
  useEffect(() => {
    if (!open) return;
    function dismiss(event: PointerEvent) {
      if (!group.current?.contains(event.target as Node)) setOpen(false);
    }
    document.addEventListener('pointerdown', dismiss);
    return () => document.removeEventListener('pointerdown', dismiss);
  }, [open]);
  return (
    <div
      ref={group}
      className="nav-group"
      onBlur={(event) => {
        if (!event.currentTarget.contains(event.relatedTarget)) setOpen(false);
      }}
      onKeyDown={(event) => {
        if (event.key === 'Escape') {
          setOpen(false);
          event.currentTarget.querySelector<HTMLButtonElement>('button')?.focus();
        }
      }}
    >
      {item.children?.length ? (
        <button
          type="button"
          className={`nav-group-link ${activeGroup ? 'active' : ''}`}
          aria-expanded={open}
          aria-controls={`nav-${item.href.slice(1)}`}
          onClick={() => setOpen((value) => !value)}
        >
          {item.label}
          <ChevronDown size={20} aria-hidden="true" />
        </button>
      ) : (
        <NavLink href={item.href} end={item.href === '/'}>
          {item.label}
        </NavLink>
      )}
      {item.children && (
        <>
          {open && (
            <div className="nav-dropdown" id={`nav-${item.href.slice(1)}`}>
              {item.children.map((child) => (
                <NavLink key={child.href} href={child.href} end={child.href === item.href}>
                  {child.label}
                  <ArrowUpRight size={15} aria-hidden="true" />
                </NavLink>
              ))}
            </div>
          )}
        </>
      )}
    </div>
  );
}
function MobileNavGroup({ item }: { item: NavItem }) {
  const [open, setOpen] = useState(false);
  const location = usePageLocation();
  useEffect(() => setOpen(false), [location]);
  return (
    <div className="mobile-nav-group">
      {item.children?.length ? (
        <>
          <button
            type="button"
            className="mobile-nav-trigger"
            aria-expanded={open}
            aria-controls={`mobile-nav-${item.href.slice(1)}`}
            onClick={() => setOpen((value) => !value)}
          >
            {item.label}
            <ChevronDown size={20} aria-hidden="true" />
          </button>
          {open && (
            <div className="mobile-nav-children" id={`mobile-nav-${item.href.slice(1)}`}>
              {item.children.map((child) => (
                <NavLink key={child.href} href={child.href} end={child.href === item.href}>
                  {child.label}
                </NavLink>
              ))}
            </div>
          )}
        </>
      ) : (
        <NavLink href={item.href} end={item.href === '/'}>
          {item.label}
          <ArrowUpRight size={18} aria-hidden="true" />
        </NavLink>
      )}
    </div>
  );
}
function Header() {
  const { site, navigation } = useContent();
  const dialog = useRef<HTMLDialogElement>(null);
  const toggle = useRef<HTMLButtonElement>(null);
  const location = usePageLocation();
  const [open, setOpen] = useState(false);
  useEffect(() => {
    dialog.current?.close();
    setOpen(false);
  }, [location]);
  function close() {
    dialog.current?.close();
    setOpen(false);
    toggle.current?.focus();
  }
  return (
    <>
      <div className="top-bar">
        <div className="max-w-7xl mx-auto header-container">
          <ContactLinks compact />
          <div className="utility-actions">
            <SocialLinks />
            <Link className="utility-cta" href={site.topBar.ctaHref}>
              {site.topBar.ctaLabel}
              <ArrowUpRight size={20} aria-hidden="true" />
            </Link>
          </div>
        </div>
      </div>
      <header className="site-header">
        <div className="max-w-7xl mx-auto header-container">
          <Link href="/" className="logo-link" aria-label={`${site.brand.publicName} home`}>
            <img src={site.brand.logoPath} width="458" height="101" alt={site.brand.publicName} />
          </Link>
          <nav className="desktop-nav" aria-label="Main navigation">
            {navigation.primary.map((item) => (
              <NavGroup key={item.href} item={item} />
            ))}
          </nav>
          <ButtonLink to="/donations" variant="lemon" className="header-support">
            Support our work
          </ButtonLink>
          <button
            ref={toggle}
            className="mobile-toggle"
            aria-label="Open navigation"
            aria-expanded={open}
            aria-controls="mobile-navigation"
            onClick={() => {
              dialog.current?.showModal();
              setOpen(true);
            }}
          >
            <Menu aria-hidden="true" />
          </button>
        </div>
      </header>
      <dialog
        ref={dialog}
        id="mobile-navigation"
        className="mobile-dialog"
        aria-label="Main navigation"
        onCancel={close}
        onClose={() => {
          setOpen(false);
        }}
        onClick={(event) => {
          if (event.target === event.currentTarget) close();
        }}
      >
        <div className="mobile-dialog-top">
          <img src={site.brand.logoPath} alt={site.brand.publicName} width="458" height="101" />
          <button aria-label="Close navigation" onClick={close}>
            <X aria-hidden="true" />
          </button>
        </div>
        <nav aria-label="Mobile navigation">
          {navigation.primary.map((item) => (
            <MobileNavGroup key={item.href} item={item} />
          ))}
        </nav>
        <ButtonLink to="/donations" variant="lemon">
          Support our work
        </ButtonLink>
      </dialog>
    </>
  );
}
function Footer() {
  const { site, navigation } = useContent();
  return (
    <footer className="site-footer">
      <div className="max-w-7xl mx-auto container">
        <div className="footer-grid">
          <div className="footer-brand">
            <Link href="/" className="footer-logo">
              <img
                src={site.brand.footerLogoPath}
                alt={site.brand.publicName}
                width="458"
                height="101"
              />
            </Link>
            <p className="footer-organisation-name">{site.brand.publicName}</p>
            <p>{site.footer.text}</p>
            <SocialLinks className="footer-social-links" />
            <span className="footer-note">
              <Heart size={15} aria-hidden="true" />
              {site.footer.note}
            </span>
          </div>
          {navigation.footer.map((group) => (
            <div key={group.title}>
              <h2>{group.title}</h2>
              {group.links.map((item) => (
                <Link key={item.href} href={item.href}>
                  {item.label}
                </Link>
              ))}
            </div>
          ))}
          <div className="footer-contact">
            <h2>Let’s connect</h2>
            <ContactLinks />
            <Link href="/contact" className="footer-contact-link">
              Contact information
              <ArrowUpRight size={16} aria-hidden="true" />
            </Link>
            <Link href="/events">Events & activities</Link>
            <Link href="/gallery">Photo gallery</Link>
          </div>
        </div>
        <div className="footer-bottom">
          <p>
            ©{' '}
            {new Intl.DateTimeFormat('en', {
              year: 'numeric',
              timeZone: 'Africa/Lagos',
            }).format(new Date())}{' '}
            {site.brand.publicName}.
          </p>
          <nav aria-label="Policy navigation">
            {navigation.policies.map((item) => (
              <Link href={item.href} key={item.href}>
                {item.label}
              </Link>
            ))}
          </nav>
        </div>
      </div>
    </footer>
  );
}
export function Layout({ children }: { children: ReactNode }) {
  const { pathname } = usePageLocation();
  const main = useRef<HTMLElement>(null);
  const first = useRef(true);
  useEffect(() => {
    if (!first.current) document.getElementById('main-content')?.focus({ preventScroll: true });
    first.current = false;
  }, [pathname]);
  useEntranceMotion(main, pathname);
  return (
    <>
      <a href="#main-content" className="skip-link">
        Skip to content
      </a>
      <Header />
      <main ref={main} id="main-content" tabIndex={-1}>
        {children}
      </main>
      <Footer />
      <ScrollToTop />
    </>
  );
}
