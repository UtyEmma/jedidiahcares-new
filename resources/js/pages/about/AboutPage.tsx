import { useContent } from '../../content';

import { Seo } from '../../lib/seo';
import { Photo } from '../../components/Photo';
import { FounderNote } from '../../components/FounderNote';
import { ButtonLink, CTA, Eyebrow, PageHero, SectionHeading, TextLink } from '../../components/UI';
export default function AboutPage() {
  const { pages, ui, site, routes } = useContent();
  const copy = pages.about;
  return (
    <>
      <Seo />
      <PageHero
        title={copy.headline!}
        text={copy.fields['Founding Narrative']}
        eyebrow="OUR STORY"
        current="About us"
        photo="community"
      />
      <FounderNote />
      <section className="section">
        <div className="max-w-7xl mx-auto container about-intro">
          <div>
            <Eyebrow>CARE THAT OPENS DOORS</Eyebrow>
            <h2>
              {ui.about.introTitle[0]}
              <br />
              {ui.about.introTitle[1]}
            </h2>
          </div>
          <div>
            <p className="lead">{copy.fields['Who We Serve']}</p>
            <p>{site.home.introText}</p>
            <TextLink to={routes.programmes}>Explore our four programme areas</TextLink>
          </div>
        </div>
      </section>
      <section className="section mission-section" id="mission">
        <div className="max-w-7xl mx-auto container mission-grid">
          <div className="mission-panel">
            <Eyebrow>OUR MISSION</Eyebrow>
            <h2>{ui.about.missionTitle}</h2>
            <p>{site.about.mission}</p>
          </div>
          <div className="vision-panel">
            <Eyebrow>OUR VISION</Eyebrow>
            <blockquote>“{site.about.vision}”</blockquote>
            <p>{site.about.visionStatus}</p>
          </div>
        </div>
      </section>
      <section className="section">
        <div className="max-w-7xl mx-auto container">
          <SectionHeading
            eyebrow="WHAT GUIDES US"
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
            <Eyebrow>OUR APPROACH</Eyebrow>
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
            <ButtonLink to={routes.partner}>Explore a partnership</ButtonLink>
            <TextLink to={routes.team}>The people behind the mission</TextLink>
          </div>
        </div>
      </section>
      <CTA />
    </>
  );
}
