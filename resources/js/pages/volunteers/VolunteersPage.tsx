import { useContent } from '../../content';

import { ArrowUpRight, ShieldCheck } from '../../components/icons';
import { safeExternalUrl } from '../../content';
import { Seo } from '../../lib/seo';
import { EmailComposer } from '../../components/Actions';
import {
  CTA,
  Eyebrow,
  FAQList,
  Icon,
  PageHero,
  SectionHeading,
  TextLink,
} from '../../components/UI';

export default function VolunteersPage() {
  const { site, pages, ui, routes } = useContent();
  const external = safeExternalUrl(site.volunteering.externalInterestFormUrl);
  return (
    <>
      <Seo />
      <PageHero
        title={pages.volunteers.headline!}
        text={pages.volunteers.fields['Intro']}
        eyebrow="VOLUNTEER WITH US"
        current="Volunteering"
        photo="care"
      />
      <section className="section">
        <div className="max-w-7xl mx-auto container">
          <SectionHeading
            eyebrow="PUT YOUR SKILLS TO WORK"
            title={ui.volunteer.rolesTitle}
            text={pages.volunteers.fields['What Volunteering Involves']}
          />
          <div className="pathways">
            {site.volunteering.roles.map((item, index) => (
              <article className={`pathway pathway-${index}`} key={item.title}>
                <Icon name={item.icon} size={34} />
                <h3>{item.title}</h3>
                <p>{item.description}</p>
              </article>
            ))}
          </div>
        </div>
      </section>
      <section className="section soft-section">
        <div className="max-w-7xl mx-auto container volunteer-grid">
          <div>
            <Eyebrow>YOUR NEXT STEP</Eyebrow>
            <h2>
              {ui.volunteer.nextTitle[0]}
              <br />
              {ui.volunteer.nextTitle[1]}
            </h2>
            <ol className="steps">
              {site.volunteering.steps.map((item, index) => (
                <li key={item}>
                  <span>0{index + 1}</span>
                  <h3>{item}</h3>
                </li>
              ))}
            </ol>
            <div className="safeguarding-note">
              <ShieldCheck size={26} aria-hidden="true" />
              <p>{ui.volunteer.safeguarding}</p>
            </div>
            <TextLink to={routes.safeguarding}>Safeguarding information</TextLink>
          </div>
          <div>
            {external ? (
              <div className="giving-panel">
                <h2>{ui.volunteer.externalTitle}</h2>
                <p>{ui.volunteer.externalText}</p>
                <a
                  className="button button-primary"
                  href={external}
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  Open volunteer interest form
                  <ArrowUpRight size={19} aria-hidden="true" />
                </a>
              </div>
            ) : (
              <EmailComposer topic="Volunteering" />
            )}
          </div>
        </div>
      </section>
      <section className="section">
        <div className="max-w-7xl mx-auto container">
          <FAQList topic="volunteers" />
        </div>
      </section>
      <CTA />
    </>
  );
}
