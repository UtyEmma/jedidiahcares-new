import type { Partner } from '../types/content';
import { useContent } from '../content';
import { useState } from 'react';
import { safeExternalUrl } from '../content';

import { Handshake, ArrowUpRight } from './icons';
import { SectionHeading, ButtonLink } from './UI';

function PartnerCard({ partner, detailed }: { partner: Partner; detailed: boolean }) {
  const [failed, setFailed] = useState(false);
  const url = safeExternalUrl(partner.url ?? '');
  return (
    <article className="partner-card" data-entrance>
      <div className="partner-logo-frame">
        {partner.logo && !failed ? (
          <img
            src={partner.logo}
            alt={partner.title}
            loading="lazy"
            width="260"
            height="100"
            onError={() => setFailed(true)}
          />
        ) : (
          <h3>{partner.title}</h3>
        )}
      </div>
      {detailed && (
        <>
          <h3>{partner.title}</h3>
          <p>{partner.summary}</p>
        </>
      )}
      {url && (
        <a href={url} className="text-link" target="_blank" rel="noopener noreferrer">
          {detailed ? 'Visit partner website' : partner.title}
          <ArrowUpRight size={21} aria-hidden="true" />
          <span className="sr-only"> (opens in a new tab)</span>
        </a>
      )}
    </article>
  );
}

export function PartnersSection({ variant = 'home' }: { variant?: 'home' | 'page' }) {
  const { site, partners } = useContent();
  const copy = site.partnerShowcase;
  const entries = variant === 'home' ? partners.slice(0, 8) : partners;
  return (
    <section
      id={variant === 'home' ? 'our-partners' : 'partners'}
      className={`section partners-showcase partners-showcase-${variant}`}
      aria-labelledby={`partners-title-${variant}`}
    >
      <div className="max-w-7xl mx-auto container">
        <SectionHeading
          id={`partners-title-${variant}`}
          eyebrow={copy.eyebrow}
          title={copy.title}
          text={copy.text}
        />
        {entries.length ? (
          <div className="partner-logo-grid">
            {entries.map((partner) => (
              <PartnerCard key={partner.id} partner={partner} detailed={variant === 'page'} />
            ))}
          </div>
        ) : (
          <div className="partner-invitation" data-entrance>
            <div className="partner-invitation-icon">
              <Handshake size={48} weight="duotone" aria-hidden="true" />
            </div>
            <div>
              <h3>{copy.emptyTitle}</h3>
              <p>{copy.emptyText}</p>
            </div>
            <ButtonLink
              to={variant === 'home' ? '/partner' : '/partner#partnership-enquiry'}
              variant="outline"
            >
              {variant === 'home' ? copy.homeCta : copy.pageCta}
            </ButtonLink>
          </div>
        )}
        {entries.length > 0 && variant === 'home' && (
          <div className="partners-showcase-action">
            <ButtonLink to="/partner" variant="outline">
              {copy.homeCta}
            </ButtonLink>
          </div>
        )}
      </div>
    </section>
  );
}
