import { useContent } from '../../content';
import type { WebsitePageProps } from '../../types/inertia';

import { usePage } from '@inertiajs/react';

import { Seo } from '../../lib/seo';
import { ProjectCard } from '../../components/ProjectCard';
import { ShareButtons } from '../../components/ShareButtons';
import {
  ButtonLink,
  CTA,
  Eyebrow,
  FAQList,
  Icon,
  PageHero,
  SectionHeading,
  TextLink,
} from '../../components/UI';
import NotFoundPage from '../not-found/NotFoundPage';

export default function ProgrammeDetailPage() {
  const { findProgramme, projects, ui, programmes, routes } = useContent();
  const { slug = '' } = usePage<WebsitePageProps>().props;
  const programme = findProgramme(slug);
  if (!programme) return <NotFoundPage />;
  const related = projects.filter((item) => item.programmeIds.includes(programme.id));
  return (
    <>
      <Seo />
      <PageHero
        title={programme.heroTitle}
        text={programme.summary}
        eyebrow={programme.title}
        current={programme.title}
        art={programme.illustration}
        photo={programme.photoId}
        image={
          programme.coverImage
            ? { src: programme.coverImage, alt: programme.coverAlt ?? programme.title }
            : undefined
        }
      >
        <div className="hero-actions">
          <ButtonLink to={routes.contact}>Enquire about this programme</ButtonLink>
        </div>
      </PageHero>
      <section className="section">
        <div className="max-w-7xl mx-auto container programme-detail-grid">
          <div className="prose">
            <Eyebrow>THE PURPOSE</Eyebrow>
            <h2>{programme.title}</h2>
            <p className="lead">{programme.introduction}</p>
            {programme.sections.map((section) => (
              <section key={section.heading}>
                <h2>{section.heading}</h2>
                <p>{section.text}</p>
              </section>
            ))}
          </div>
          <aside className="programme-aside">
            <Icon name={programme.icon} size={40} />
            <h2>{ui.programme.helpTitle}</h2>
            <ul>
              {programme.activities.map((item) => (
                <li key={item}>
                  <span aria-hidden="true">↗</span>
                  {item}
                </li>
              ))}
            </ul>
            <p>{ui.programme.arrangements}</p>
            <TextLink to={routes.partner}>Partner on this programme</TextLink>
            <TextLink to={routes.volunteers}>Share your skills</TextLink>
            <ShareButtons
              title={programme.title}
              description={programme.summary}
              label="Share this programme"
            />
          </aside>
        </div>
      </section>
      {related.length > 0 && (
        <section className="section">
          <div className="max-w-7xl mx-auto container">
            <SectionHeading eyebrow="RELATED PROJECTS" title={ui.programme.relatedTitle} />
            <div className="cards-grid">
              {related.map((item) => (
                <ProjectCard key={item.id} project={item} />
              ))}
            </div>
          </div>
        </section>
      )}
      <section className="section soft-section">
        <div className="max-w-7xl mx-auto container">
          <FAQList />
        </div>
      </section>
      <section className="section">
        <div className="max-w-7xl mx-auto container">
          <SectionHeading eyebrow="KEEP EXPLORING" title={ui.programme.exploreTitle} />
          <div className="related-programmes">
            {programmes
              .filter((item) => item.id !== programme.id)
              .map((item) => (
                <TextLink key={item.id} to={item.detailUrl}>
                  {item.title}
                </TextLink>
              ))}
          </div>
        </div>
      </section>
      <CTA />
    </>
  );
}
