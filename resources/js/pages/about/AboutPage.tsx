import { useContent } from '../../content';

import { Seo } from '../../lib/seo';
import { Photo } from '../../components/Photo';
import { FounderNote } from '../../components/FounderNote';
import { ButtonLink, CTA, Eyebrow, PageHero, SectionHeading, TextLink } from '../../components/UI';
export default function AboutPage() {
  const { pages, ui, site, routes, sections } = useContent();
  const labels = sections['about'];
  const copy = pages.about;
  return (
    <>
      <Seo />
      <PageHero
        title={copy.headline!}
        text={copy.fields['Founding Narrative']}
        eyebrow={labels.heroEyebrow}
        current={labels.heroCurrent}
        photo="community"
      />
      <FounderNote />
      <section className="section">
        <div className="max-w-7xl mx-auto container about-intro">
          <div>
            <Eyebrow>{labels.introEyebrow}</Eyebrow>
            <h2>
              {ui.about.introTitle[0]}
              <br />
              {ui.about.introTitle[1]}
            </h2>
          </div>
          <div>
            <p className="lead">{copy.fields['Who We Serve']}</p>
            <p>{site.home.introText}</p>
            <TextLink to={routes.programmes}>{labels.programmesButton}</TextLink>
          </div>
        </div>
      </section>
      <section className="section mission-section" id="mission">
        <div className="max-w-7xl mx-auto container mission-grid">
          <div className="mission-panel">
            <Eyebrow>{labels.missionEyebrow}</Eyebrow>
            <h2>{ui.about.missionTitle}</h2>
            <p>{site.about.mission}</p>
          </div>
          <div className="vision-panel">
            <Eyebrow>{labels.visionEyebrow}</Eyebrow>
            <blockquote>“{site.about.vision}”</blockquote>
            <p>{site.about.visionStatus}</p>
          </div>
        </div>
      </section>
      <section className="section">
        <div className="max-w-7xl mx-auto container">
          <SectionHeading
            eyebrow={labels.valuesEyebrow}
            title={ui.about.valuesTitle}
            text={ui.about.valuesText}
          />
          <div className="values-grid">
            {site.about.values.map((item, index) => (
              <div key={item.title}>
                <span className="small-label">0{index + 1}</span>
                <h3>{item.title}</h3>
                <p>{item.text}</p>
              </div>
            ))}
          </div>
        </div>
      </section>
      <section className="section approach-section">
        <div className="max-w-7xl mx-auto container split-grid">
          <Photo id="care" />
          <div>
            <Eyebrow>{labels.approachEyebrow}</Eyebrow>
            <h2>{ui.about.approachTitle}</h2>
            <p>{copy.fields['Our Approach']}</p>
            <ol className="steps">
              {site.about.approach.map((item, index) => (
                <li key={item}>
                  <span>0{index + 1}</span>
                  <h3>{item}</h3>
                </li>
              ))}
            </ol>
            <ButtonLink to={routes.partner}>{labels.partnerButton}</ButtonLink>
            <TextLink to={routes.team}>{labels.teamButton}</TextLink>
          </div>
        </div>
      </section>
      <CTA />
    </>
  );
}
