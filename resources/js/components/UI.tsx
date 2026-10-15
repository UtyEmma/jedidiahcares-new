import type { BaseEntry, Programme } from '../types/content';
import { useContent } from '../content';
import { useState, type ReactNode } from 'react';
import { Link } from '@inertiajs/react';
import {
  ArrowUpRight,
  ArrowRight,
  BookOpen,
  Heart,
  UsersRound,
  HandHeart,
  Sparkles,
  Flower2,
  ShieldCheck,
  Copy,
  Check,
  Search,
} from './icons';

import { Art } from './Art';
import { Photo } from './Photo';
import { BrandAsterisk } from './BrandAsterisk';
import { Button } from './ui/button';
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from './ui/accordion';
export function Icon({ name, size = 24 }: { name: string; size?: number }) {
  const Component =
    (
      {
        book: BookOpen,
        heart: Heart,
        people: UsersRound,
        hands: HandHeart,
        spark: Sparkles,
        flower: Flower2,
        shield: ShieldCheck,
      } as Record<string, typeof Heart>
    )[name] ?? Heart;
  return <Component size={size} weight="regular" aria-hidden="true" />;
}
export function ButtonLink({
  to,
  children,
  variant = 'primary',
  className = '',
}: {
  to: string;
  children: ReactNode;
  variant?: 'primary' | 'lemon' | 'outline' | 'white';
  className?: string;
}) {
  return (
    <Button
      asChild
      variant={variant === 'lemon' ? 'secondary' : variant === 'outline' ? 'outline' : 'default'}
      className={`button button-${variant} ${className}`}
    >
      <Link href={to}>
        {children}
        <ArrowUpRight size={19} aria-hidden="true" />
      </Link>
    </Button>
  );
}
export function TextLink({
  to,
  children,
  className = '',
}: {
  to: string;
  children: ReactNode;
  className?: string;
}) {
  return (
    <Link className={`text-link ${className}`} href={to}>
      {children}
      <ArrowRight size={18} aria-hidden="true" />
    </Link>
  );
}
export function Eyebrow({ children }: { children: ReactNode }) {
  return (
    <p className="eyebrow">
      <span aria-hidden="true" />
      {children}
    </p>
  );
}
export function Breadcrumbs({
  current,
  parent,
}: {
  current: string;
  parent?: { label: string; href: string };
}) {
  return (
    <nav className="breadcrumbs" aria-label="Breadcrumb">
      <Link href="/">Home</Link>
      <span aria-hidden="true">/</span>
      {parent && (
        <>
          <Link href={parent.href}>{parent.label}</Link>
          <span aria-hidden="true">/</span>
        </>
      )}
      <span aria-current="page">{current}</span>
    </nav>
  );
}
export function PageHero({
  title,
  text,
  eyebrow,
  art,
  photo,
  image,
  current,
  children,
}: {
  title: string;
  text?: string;
  eyebrow: string;
  art?: string;
  photo?: string;
  image?: { src: string; alt: string };
  current?: string;
  children?: ReactNode;
}) {
  return (
    <section className={`page-hero ${art || photo || image ? 'page-hero-art' : ''}`}>
      <div className="max-w-7xl mx-auto container">
        <Breadcrumbs current={current ?? eyebrow} />
        <div className="page-hero-grid">
          <div data-entrance>
            <Eyebrow>{eyebrow}</Eyebrow>
            <h1>{title}</h1>
            {text && <p className="lead">{text}</p>}
            {children}
          </div>
          {image ? (
            <figure className="context-photo page-hero-photo">
              <img
                src={image.src}
                alt={image.alt}
                width="800"
                height="600"
                loading="eager"
                fetchPriority="high"
                decoding="async"
              />
            </figure>
          ) : photo ? (
            <Photo id={photo} className="page-hero-photo" priority />
          ) : (
            art && <Art variant={art} />
          )}
        </div>
      </div>
    </section>
  );
}
export function SectionHeading({
  id,
  eyebrow,
  title,
  text,
  action,
}: {
  id?: string;
  eyebrow: string;
  title: string;
  text?: string;
  action?: { label: string; to: string };
}) {
  return (
    <div className="section-heading" data-entrance>
      <div>
        <Eyebrow>{eyebrow}</Eyebrow>
        <h2 id={id}>{title}</h2>
        {text && <p>{text}</p>}
      </div>
      {action && <TextLink to={action.to}>{action.label}</TextLink>}
    </div>
  );
}
export function ProgrammeRow({
  programme,
  compact = false,
}: {
  programme: Programme;
  compact?: boolean;
}) {
  return (
    <article className={`programme-row ${compact ? 'programme-row-compact' : ''}`} data-entrance>
      <div className="programme-number">
        {programme.number}
        <Icon name={programme.icon} size={32} />
      </div>
      <div className="programme-row-copy">
        <p className="small-label">{programme.summary}</p>
        <h3>
          <Link href={`/programmes/${programme.slug}`}>{programme.title}</Link>
        </h3>
        <p>{programme.shortTitle}</p>
      </div>
      <Link
        className="round-link"
        href={`/programmes/${programme.slug}`}
        aria-label={`Explore ${programme.title}`}
      >
        <ArrowUpRight aria-hidden="true" />
      </Link>
    </article>
  );
}
export function EmptyState({
  kind,
  filtered = false,
  reset,
}: {
  kind: 'projects' | 'stories' | 'events' | 'team' | 'gallery' | 'reports';
  filtered?: boolean;
  reset?: () => void;
}) {
  const { site } = useContent();
  const content = site.empty[kind];
  return (
    <div className="empty-state" data-entrance>
      <div className="empty-icon">
        <Icon name={kind === 'stories' || kind === 'reports' ? 'book' : 'spark'} size={32} />
      </div>
      <p className="small-label">
        {filtered ? 'TRY A DIFFERENT SEARCH' : 'THOUGHTFULLY PREPARED. SHARED WITH CARE.'}
      </p>
      <h2>{filtered ? 'No matching results.' : content.title}</h2>
      <p>
        {filtered
          ? 'Try a different keyword or clear your filters to see all results.'
          : content.text}
      </p>
      {filtered && reset ? (
        <button className="button button-outline" onClick={reset}>
          Clear filters
          <ArrowRight size={18} aria-hidden="true" />
        </button>
      ) : (
        <TextLink to="/programmes">Explore our programme areas</TextLink>
      )}
    </div>
  );
}
export function FAQList({ topic = 'programmes' }: { topic?: string }) {
  const { faq, ui } = useContent();
  const entries = faq.filter((item) => item.topic === topic);
  if (!entries.length) return null;
  return (
    <div className="faq-layout">
      <div data-entrance>
        <Eyebrow>A LITTLE MORE CLARITY</Eyebrow>
        <h2>{ui.faq.title}</h2>
        <p>{ui.faq.text}</p>
      </div>
      <Accordion type="multiple" className="accordions">
        {entries.map((item) => (
          <AccordionItem key={item.id} value={item.id}>
            <AccordionTrigger>{item.question}</AccordionTrigger>
            <AccordionContent>{item.answer}</AccordionContent>
          </AccordionItem>
        ))}
      </Accordion>
    </div>
  );
}
export function CTA({
  title: suppliedTitle,
  text: suppliedText,
}: {
  title?: string;
  text?: string;
}) {
  const { site } = useContent();
  const title = suppliedTitle ?? site.home.finalTitle;
  const text = suppliedText ?? site.home.finalText;
  return (
    <section className="cta-section">
      <div className="max-w-7xl mx-auto container">
        <div className="cta-band" data-entrance>
          <div>
            <Eyebrow>COMPASSION INTO ACTION</Eyebrow>
            <h2>{title}</h2>
            <p>{text}</p>
          </div>
          <ButtonLink to="/get-involved" variant="lemon">
            Find your way to help
          </ButtonLink>
          <BrandAsterisk className="cta-flower" />
        </div>
      </div>
    </section>
  );
}
export function Media({
  src,
  alt = '',
  variant = 'education',
  className = '',
}: {
  src?: string;
  alt?: string;
  variant?: string;
  className?: string;
}) {
  const [failed, setFailed] = useState(false);
  return src && !failed ? (
    <img
      className={`content-image ${className}`}
      src={src}
      alt={alt}
      width="800"
      height="600"
      loading="lazy"
      onError={() => setFailed(true)}
    />
  ) : (
    <Art variant={variant} className={className} />
  );
}
export function EntryCard({
  entry,
  to,
  label,
  badge,
  children,
}: {
  entry: BaseEntry;
  to: string;
  label: string;
  badge?: ReactNode;
  children?: ReactNode;
}) {
  return (
    <article className="entry-card" data-entrance>
      <Link href={to} tabIndex={-1} aria-hidden="true">
        <Media src={entry.coverImage} alt={entry.coverAlt} />
      </Link>
      <div className="entry-card-body">
        {badge && <div className="entry-card-badge">{badge}</div>}
        <p className="small-label">{label}</p>
        <h2>
          <Link href={to}>{entry.title}</Link>
        </h2>
        <p>{entry.summary}</p>
        {children}
        <TextLink to={to}>Read more</TextLink>
      </div>
    </article>
  );
}
export function SearchField({
  value,
  onChange,
  label,
}: {
  value: string;
  onChange: (value: string) => void;
  label: string;
}) {
  return (
    <label className="search-field">
      <Search size={20} aria-hidden="true" />
      <span className="sr-only">{label}</span>
      <input
        type="search"
        placeholder={label}
        value={value}
        onChange={(event) => onChange(event.target.value)}
      />
    </label>
  );
}
export function CopyButton({ value, label = 'Copy' }: { value: string; label?: string }) {
  const [status, setStatus] = useState('');
  async function copy() {
    try {
      await navigator.clipboard.writeText(value);
      setStatus('Copied');
    } catch {
      setStatus('Copy unavailable. Please select and copy the text.');
    }
  }
  return (
    <div className="copy-control">
      <button className="button button-outline" type="button" onClick={copy}>
        {status === 'Copied' ? (
          <Check size={17} aria-hidden="true" />
        ) : (
          <Copy size={17} aria-hidden="true" />
        )}
        {label}
      </button>
      <span role="status">{status}</span>
    </div>
  );
}
