import { useContent } from '../../content';

import { ArrowUpRight } from '../../components/icons';
import { safeExternalUrl } from '../../content';
import { PartnersSection } from '../../components/PartnersSection';
import { Seo } from '../../lib/seo';
import { EmailComposer } from '../../components/Actions';
import { CTA, Eyebrow, Icon, PageHero } from '../../components/UI';

export default function PartnersPage() {
  const { site, pages, ui } = useContent();
  const external = safeExternalUrl(site.partnership.externalEnquiryFormUrl);
  return (
    <>
      <Seo />
      <PageHero
        title={pages.partner.headline!}
        text={pages.partner.fields['Intro']}
        eyebrow="PARTNER WITH US"
        current="Partnerships"
      />
      <section className="section">
        <div className="max-w-7xl mx-auto container partnership-intro">
          <div>
            <Eyebrow>SHARED PURPOSE. PRACTICAL ACTION.</Eyebrow>
            <h2>
              {ui.partner.introTitle[0]}
              <br />
              {ui.partner.introTitle[1]}
            </h2>
            <p>{pages.partner.fields['Partnership Possibilities']}</p>
          </div>
          <div className="partner-options">
            {site.partnership.options.map((item, index) => (
              <div key={item}>
                <span>0{index + 1}</span>
                <h3>{item}</h3>
                <Icon name="hands" size={24} />
              </div>
            ))}
          </div>
        </div>
      </section>
      <section className="section soft-section" id="partnership-enquiry">
        <div className="max-w-7xl mx-auto container volunteer-grid">
          <div>
            <Eyebrow>HOW WE CAN COLLABORATE</Eyebrow>
            <h2>{ui.partner.approachTitle}</h2>
            <p>{pages.partner.fields['How We Collaborate']}</p>
            <ol className="steps">
              {site.partnership.steps.map((item, index) => (
                <li key={item}>
                  <span>0{index + 1}</span>
                  <h3>{item}</h3>
                </li>
              ))}
            </ol>
          </div>
          {external ? (
            <div className="giving-panel">
              <h2>{ui.partner.externalTitle}</h2>
              <p>{ui.partner.externalText}</p>
              <a
                className="button button-primary"
                href={external}
                target="_blank"
                rel="noopener noreferrer"
              >
                Open partnership enquiry form
                <ArrowUpRight size={19} aria-hidden="true" />
              </a>
            </div>
          ) : (
            <EmailComposer topic="Partnership" />
          )}
        </div>
      </section>
      <PartnersSection variant="page" />
      <CTA />
    </>
  );
}
