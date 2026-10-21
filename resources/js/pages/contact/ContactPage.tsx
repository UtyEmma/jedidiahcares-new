import { useContent } from '../../content';

import { usePageLocation } from '../../hooks/usePageLocation';

import { SocialMessaging } from '../../components/ContactLinks';
import { Seo } from '../../lib/seo';
import { EmailComposer } from '../../components/Actions';
import { Eyebrow, Icon, PageHero, TextLink } from '../../components/UI';

export default function ContactPage() {
  const { site, pages, ui, routes, sections } = useContent();
  const labels = sections['contact'];
  const location = usePageLocation();
  const search = new URLSearchParams(location.search);
  const requested = search.get('topic') ?? 'General enquiry';
  const topic = site.contact.topics.includes(requested) ? requested : 'General enquiry';
  return (
    <>
      <Seo />
      <PageHero
        title={pages.contact.headline!}
        text={pages.contact.fields['Intro']}
        eyebrow={labels.heroEyebrow}
        current={labels.heroCurrent}
      />
      <section className="section">
        <div className="max-w-7xl mx-auto container contact-grid">
          <div>
            <Eyebrow>{labels.introEyebrow}</Eyebrow>
            <h2>
              {ui.contact.title[0]}
              <br />
              {ui.contact.title[1]}
            </h2>
            <p>{ui.contact.guidance}</p>
            <div className="contact-details">
              {site.contact.emailVerified && site.contact.email && (
                <div>
                  <p className="small-label">EMAIL</p>
                  <a href={`mailto:${site.contact.email}`}>{site.contact.email}</a>
                </div>
              )}
              {site.contact.phoneVerified && site.contact.phone && (
                <div>
                  <p className="small-label">PHONE</p>
                  <a href={`tel:${site.contact.phone.replace(/[\s()-]/g, '')}`}>
                    {site.contact.phone}
                  </a>
                </div>
              )}
              {site.contact.locationVerified && site.contact.address && (
                <div>
                  <p className="small-label">LOCATION</p>
                  <p>{site.contact.address}</p>
                </div>
              )}
              {!site.contact.emailVerified &&
                !site.contact.phoneVerified &&
                !site.contact.locationVerified && (
                  <div className="contact-pending">
                    <Icon name="hands" size={32} />
                    <h3>{ui.contact.pendingTitle}</h3>
                    <p>{site.contact.unavailable}</p>
                  </div>
                )}
            </div>
            <SocialMessaging />
            <div className="contact-paths">
              <TextLink to={routes.programmes}>{labels.programmesButton}</TextLink>
              <TextLink to={routes.partner}>{labels.partnerButton}</TextLink>
              <TextLink to={routes.volunteers}>{labels.volunteersButton}</TextLink>
            </div>
          </div>
          <EmailComposer key={topic} topic={topic} />
        </div>
      </section>
    </>
  );
}
