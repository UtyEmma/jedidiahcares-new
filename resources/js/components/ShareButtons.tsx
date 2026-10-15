import { useEffect, useId, useRef, useState } from 'react';
import { usePage } from '@inertiajs/react';
import type { WebsitePageProps } from '../types/inertia';
import { buildShareLinks, publicShareUrl } from '../lib/sharing';
import {
  Check,
  Copy,
  FacebookLogo,
  LinkedinLogo,
  Mail,
  ShareNetwork,
  WhatsappLogo,
  XLogo,
} from './icons';

type ShareProps = {
  title: string;
  description?: string;
  label?: string;
  query?: Record<string, string>;
};

export function ShareButtons(props: ShareProps) {
  const { seo } = usePage<WebsitePageProps>().props;
  const url = publicShareUrl(seo.canonical, props.query);
  if (!url) return null;
  return <SharePanel key={url} {...props} url={url} />;
}

function SharePanel({
  title,
  description,
  label = 'Share this page',
  url,
}: ShareProps & { url: string }) {
  const headingId = useId();
  const linkId = useId();
  const [copyStatus, setCopyStatus] = useState<'idle' | 'copied' | 'manual'>('idle');
  const manualLink = useRef<HTMLInputElement>(null);
  const links = buildShareLinks(url, title, description);
  const platforms = [
    { key: 'facebook', label: 'Facebook', Icon: FacebookLogo },
    { key: 'x', label: 'X', Icon: XLogo },
    { key: 'linkedin', label: 'LinkedIn', Icon: LinkedinLogo },
    { key: 'whatsapp', label: 'WhatsApp', Icon: WhatsappLogo },
    { key: 'email', label: 'Email', Icon: Mail },
  ] as const;

  useEffect(() => {
    if (copyStatus === 'manual') {
      manualLink.current?.focus();
      manualLink.current?.select();
    }
  }, [copyStatus]);

  async function copyLink() {
    try {
      await navigator.clipboard.writeText(url);
      setCopyStatus('copied');
    } catch {
      setCopyStatus('manual');
    }
  }

  return (
    <section className="share-panel" aria-labelledby={headingId}>
      <p className="share-title" id={headingId}>
        <ShareNetwork size={20} aria-hidden="true" />
        {label}
      </p>
      <ul className="share-actions">
        {platforms.map(({ key, label: platform, Icon }) => (
          <li key={key}>
            <a
              className={`share-action share-action-${key}`}
              href={links[key]}
              target={key === 'email' ? undefined : '_blank'}
              rel={key === 'email' ? undefined : 'noopener noreferrer nofollow'}
              aria-label={`Share ${key === 'email' ? 'by' : 'on'} ${platform}${key === 'email' ? '' : ' (opens in a new tab)'}`}
            >
              <Icon size={20} aria-hidden="true" />
              <span>{platform}</span>
            </a>
          </li>
        ))}
        <li>
          <button className="share-action" type="button" onClick={copyLink}>
            {copyStatus === 'copied' ? (
              <Check size={20} aria-hidden="true" />
            ) : (
              <Copy size={20} aria-hidden="true" />
            )}
            {copyStatus === 'copied' ? 'Link copied' : 'Copy link'}
          </button>
        </li>
      </ul>
      <p className="share-feedback" role="status">
        {copyStatus === 'copied'
          ? 'Link copied to clipboard.'
          : copyStatus === 'manual'
            ? 'Select and copy the page link below.'
            : ''}
      </p>
      {copyStatus === 'manual' && (
        <div className="share-manual-copy">
          <label htmlFor={linkId}>Page link</label>
          <input
            id={linkId}
            ref={manualLink}
            type="url"
            value={url}
            readOnly
            onFocus={(event) => event.currentTarget.select()}
          />
        </div>
      )}
    </section>
  );
}
