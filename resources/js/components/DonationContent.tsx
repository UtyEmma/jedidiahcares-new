import type { Project } from '../types/content';
import { useContent } from '../content';

import { Heart } from './icons';

import { Seo } from '../lib/seo';
import { projectStatusLabels } from '../lib/projects';
import { GivingPanel } from './Actions';
import { ButtonLink, Eyebrow, FAQList, Icon, PageHero, TextLink } from './UI';

export default function DonationContent({ project }: { project?: Project }) {
  const { site, pages, ui, routes, sections } = useContent();
  const labels = sections['donations'];
  const config = project?.approvedDonationUrl
    ? {
        ...site.giving,
        enabled: true,
        externalDonationUrl: project.approvedDonationUrl,
      }
    : site.giving;
  return (
    <>
      <Seo />
      <PageHero
        title={pages.donations.headline!}
        text={ui.support.intro}
        eyebrow={labels.heroEyebrow}
        current={labels.heroCurrent}
      />
      <section className="section">
        <div className="max-w-7xl mx-auto container support-grid">
          <div>
            <Eyebrow>{labels.purposeEyebrow}</Eyebrow>
            <h2>{ui.support.purposeTitle}</h2>
            <p>{site.home.introText}</p>
            {project && (
              <div className="context-panel">
                <p className="small-label">
                  PROJECT CONTEXT · {projectStatusLabels[project.stage]}
                </p>
                <h3>{project.title}</h3>
                <p>{project.summary}</p>
                <p>{ui.support.allocationNote}</p>
              </div>
            )}
            <ul className="support-list">
              {ui.support.causes.map((item) => (
                <li key={item.title}>
                  <Icon name={item.icon} size={26} />
                  <div>
                    <h3>{item.title}</h3>
                    <p>{item.text}</p>
                  </div>
                </li>
              ))}
            </ul>
            <TextLink to={routes.reports}>{labels.reportsButton}</TextLink>
          </div>
          <GivingPanel config={config} />
        </div>
      </section>
      <section className="section soft-section">
        <div className="max-w-7xl mx-auto container">
          <FAQList topic="donations" />
        </div>
      </section>
      <section className="section">
        <div className="max-w-7xl mx-auto container support-bottom">
          <Heart size={35} weight="regular" aria-hidden="true" />
          <h2>{ui.support.otherTitle}</h2>
          <p>{ui.support.otherText}</p>
          <ButtonLink to={routes.getInvolved} variant="outline">
            {labels.involvedButton}
          </ButtonLink>
        </div>
      </section>
    </>
  );
}
