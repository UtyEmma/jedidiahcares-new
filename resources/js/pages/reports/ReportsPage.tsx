import { useContent } from '../../content';

import { ArrowUpRight, ShieldCheck } from '../../components/icons';

import { Seo } from '../../lib/seo';
import { ProjectCard } from '../../components/ProjectCard';
import { ShareButtons } from '../../components/ShareButtons';
import { CTA, EmptyState, Eyebrow, PageHero, SectionHeading, TextLink } from '../../components/UI';

export default function ReportsPage() {
  const { pages, ui, metrics, reports, projects, routes, sections } = useContent();
  const labels = sections['reports'];
  return (
    <>
      <Seo />
      <PageHero
        title={pages.impact.headline!}
        text={pages.impact.fields['Introduction']}
        eyebrow={labels.heroEyebrow}
        current={labels.heroCurrent}
      />
      <section className="section">
        <div className="max-w-7xl mx-auto container">
          <div className="impact-feature">
            <ShieldCheck size={56} weight="regular" aria-hidden="true" />
            <div>
              <Eyebrow>{labels.evidenceEyebrow}</Eyebrow>
              <h2>{ui.impact.evidenceTitle}</h2>
              <p>{pages.impact.fields['Reflection']}</p>
            </div>
          </div>
          {metrics.length > 0 ? (
            <div className="metrics-grid">
              {metrics.map((item) => (
                <div key={item.id}>
                  <strong>
                    {item.value.toLocaleString()} {item.unit}
                  </strong>
                  <h2>{item.label}</h2>
                  <p>{item.period}</p>
                  <small>Source: {item.source}</small>
                </div>
              ))}
            </div>
          ) : (
            <div className="impact-editorial">
              <h2>{ui.impact.progressTitle}</h2>
              <p>{ui.impact.progressText}</p>
              <TextLink to={routes.programmes}>{labels.programmesButton}</TextLink>
            </div>
          )}
        </div>
      </section>
      <section className="section soft-section">
        <div className="max-w-7xl mx-auto container">
          <SectionHeading eyebrow={labels.reportsEyebrow} title={ui.impact.reportsTitle} />
          {reports.length > 0 ? (
            <div className="reports-grid">
              {reports.map((report) => (
                <article key={report.id}>
                  <p className="small-label">{report.period}</p>
                  <h3>{report.title}</h3>
                  <p>{report.summary}</p>
                  <a className="text-link" href={report.path} download>
                    Download report · {report.size}
                    <ArrowUpRight size={18} aria-hidden="true" />
                  </a>
                </article>
              ))}
            </div>
          ) : (
            <EmptyState kind="reports" />
          )}
          <ShareButtons
            title="Our reports"
            description={pages.impact.fields['Introduction']}
            label="Share our reports"
          />
        </div>
      </section>
      {projects.some((item) => item.stage === 'completed') && (
        <section className="section">
          <div className="max-w-7xl mx-auto container">
            <SectionHeading eyebrow={labels.projectsEyebrow} title={ui.impact.projectsTitle} />
            <div className="cards-grid">
              {projects
                .filter((item) => item.stage === 'completed')
                .map((item) => (
                  <ProjectCard key={item.id} project={item} />
                ))}
            </div>
          </div>
        </section>
      )}
      <CTA />
    </>
  );
}
