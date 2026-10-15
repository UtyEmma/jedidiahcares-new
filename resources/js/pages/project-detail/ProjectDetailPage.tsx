import { useContent } from '../../content';
import type { WebsitePageProps } from '../../types/inertia';
import { Link, usePage } from '@inertiajs/react';
import { ArrowUpRight } from '../../components/icons';
import { safeExternalUrl } from '../../content';
import { Seo } from '../../lib/seo';
import { ProjectFunding } from '../../components/ProjectFunding';
import { ProjectStatusBadge } from '../../components/ProjectStatusBadge';
import { ShareButtons } from '../../components/ShareButtons';
import { CTA, EntryCard, Media, PageHero, SectionHeading, TextLink } from '../../components/UI';
import NotFoundPage from '../not-found/NotFoundPage';

export default function ProjectDetailPage() {
  const { findProject, stories, programmes } = useContent();
  const { slug = '' } = usePage<WebsitePageProps>().props;
  const project = findProject(slug);
  if (!project) return <NotFoundPage />;
  const related = stories.filter((item) => item.projectId === project.id);
  return (
    <>
      <Seo />
      <PageHero
        title={project.title}
        text={project.summary}
        eyebrow="PROJECTS & CAMPAIGNS"
        current={project.title}
      />
      <section className="section">
        <div className="max-w-7xl mx-auto container">
          <div className="project-detail-media">
            <Media src={project.coverImage} alt={project.coverAlt} />
            <ProjectStatusBadge stage={project.stage} />
          </div>
          <div className="detail-grid">
            <div className="prose max-w-full!">
              {project.sections?.map((section) => (
                <section key={section.heading}>
                  <h2>{section.heading}</h2>
                  <p className="whitespace-pre-wrap">{section.text}</p>
                </section>
              ))}
              {project.intendedActivities.length > 0 && (
                <>
                  <h2>Activities</h2>
                  <ul className="list-disc">
                    {project.intendedActivities.map((item) => (
                      <li key={item}>{item}</li>
                    ))}
                  </ul>
                </>
              )}
              {project.verifiedOutcomes
                ?.filter((item) => item.verified && item.source)
                .map((item) => (
                  <div className="outcome" key={item.description}>
                    <h2>Confirmed result</h2>
                    <p>{item.description}</p>
                    <small>Source: {item.source}</small>
                  </div>
                ))}
              {project.reportPaths?.map((path) => (
                <a className="text-link" key={path} href={path} download>
                  Download project report
                  <ArrowUpRight size={18} aria-hidden="true" />
                </a>
              ))}
            </div>
            <aside className="programme-aside">
              <ProjectFunding project={project} variant="detail" />
              {project.stage === 'planned' && (
                <p>
                  This is a planned initiative. Intended activities do not represent delivered
                  results.
                </p>
              )}
              <dl>
                <dt>Location</dt>
                <dd>{project.location ?? 'To be confirmed'}</dd>
                <dt>Programme</dt>
                <dd>
                  {programmes
                    .filter((item) => project.programmeIds.includes(item.id))
                    .map((item) => (
                      <Link key={item.id} href={`/programmes/${item.slug}`}>
                        {item.title}
                      </Link>
                    ))}
                </dd>
              </dl>
              {safeExternalUrl(project.approvedDonationUrl ?? '') &&
              project.stage !== 'completed' ? (
                <TextLink to={`/donations/${project.slug}`}>Support this programme</TextLink>
              ) : (
                <TextLink to="/partner">Explore a partnership</TextLink>
              )}
            </aside>
          </div>
          <ShareButtons
            title={project.title}
            description={project.summary}
            label="Share this project"
          />
        </div>
      </section>
      {related.length > 0 && (
        <section className="section">
          <div className="max-w-7xl mx-auto container">
            <SectionHeading eyebrow="FOLLOW THE STORY" title="Related updates." />
            <div className="cards-grid">
              {related.map((item) => (
                <EntryCard
                  key={item.id}
                  entry={item}
                  to={`/stories/${item.slug}`}
                  label={item.category}
                />
              ))}
            </div>
          </div>
        </section>
      )}
      <CTA />
    </>
  );
}
