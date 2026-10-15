import { useContent } from '../content';

import { ArrowUpRight, ShieldCheck } from './icons';
import { safeExternalUrl } from '../content';
import { Seo } from '../lib/seo';
import { dateLabel } from '../lib/format';
import { ButtonLink, Eyebrow, PageHero, TextLink } from './UI';
import NotFoundPage from '../pages/not-found/NotFoundPage';

const policyTitles: Record<string, string> = {
  privacy: 'Privacy policy',
  safeguarding: 'Safeguarding',
  'donation-terms': 'Terms of use & giving',
  accessibility: 'Accessibility statement',
};

export default function PolicyContent({ policySlug }: { policySlug: string }) {
  const { policies, ui, site } = useContent();
  const key = policySlug;
  const policy = policies.find((item) => item.slug === key);
  const title = policy?.title ?? policyTitles[key];
  if (!title) return <NotFoundPage />;
  const information = ui.policy.pages[key as keyof typeof ui.policy.pages];
  return (
    <>
      <Seo />
      <PageHero
        title={title}
        eyebrow="INFORMATION & RESPONSIBILITY"
        current={title}
        text={policy?.summary ?? information.intro}
      />
      <section className="section">
        <div className="max-w-7xl mx-auto container policy-layout">
          <aside>
            <Eyebrow>POLICIES & INFORMATION</Eyebrow>
            {Object.entries(policyTitles).map(([href, label]) => (
              <TextLink to={`/${href}`} key={href}>
                {label}
              </TextLink>
            ))}
          </aside>
          <article className="prose">
            {policy ? (
              <>
                {policy.updatedAt ? (
                  <p className="small-label">
                    Updated <time dateTime={policy.updatedAt}>{dateLabel(policy.updatedAt)}</time>
                  </p>
                ) : (
                  policy.lastReviewed && (
                    <p className="small-label">Last reviewed: {policy.lastReviewed}</p>
                  )
                )}
                <nav className="policy-contents" aria-label="On this page">
                  <p className="small-label">On this page</p>
                  <ol>
                    {policy.sections.map((section, index) => (
                      <li key={section.heading}>
                        <a href={`#${key}-section-${index + 1}`}>{section.heading}</a>
                      </li>
                    ))}
                  </ol>
                </nav>
                {policy.sections.map((section, index) => (
                  <section key={section.heading} className="policy-section">
                    <h2 id={`${key}-section-${index + 1}`}>{section.heading}</h2>
                    <p>{section.text}</p>
                    {section.items && (
                      <ul>
                        {section.items.map((item) => (
                          <li key={item}>{item}</li>
                        ))}
                      </ul>
                    )}
                    {section.paragraphs?.map((paragraph) => (
                      <p key={paragraph}>{paragraph}</p>
                    ))}
                    {section.links?.map((link) => {
                      const url = safeExternalUrl(link.url);
                      return url ? (
                        <a key={url} className="text-link" href={url}>
                          {link.label}
                          <ArrowUpRight size={18} aria-hidden="true" />
                        </a>
                      ) : null;
                    })}
                  </section>
                ))}
                {policy.contact && (
                  <section className="policy-contact" aria-label={policy.contact.title}>
                    <h2>{policy.contact.title}</h2>
                    <p>{policy.contact.text}</p>
                    {site.contact.emailVerified && site.contact.email && (
                      <a
                        className="text-link"
                        href={`mailto:${site.contact.email}?subject=${encodeURIComponent(policy.contact.topic)}`}
                      >
                        {site.contact.email}
                        <ArrowUpRight size={18} aria-hidden="true" />
                      </a>
                    )}
                    <ButtonLink
                      to={`/contact?topic=${encodeURIComponent(policy.contact.topic)}`}
                      variant="outline"
                    >
                      {policy.contact.label}
                    </ButtonLink>
                  </section>
                )}
              </>
            ) : (
              <div className="policy-pending">
                <ShieldCheck size={40} weight="regular" aria-hidden="true" />
                <h2>{information.title}</h2>
                <p>{information.text}</p>
                <p>{information.detail}</p>
                <TextLink to="/contact">Contact information</TextLink>
              </div>
            )}
          </article>
        </div>
      </section>
    </>
  );
}
