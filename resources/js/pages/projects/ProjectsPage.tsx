import { useContent } from '../../content';
import { useState } from 'react';

import { filterProjects } from '../../content/selectors';
import { Seo } from '../../lib/seo';
import { CTA, EmptyState, PageHero, SearchField } from '../../components/UI';
import { SelectField } from '../../components/SelectField';
import { ProjectCard } from '../../components/ProjectCard';
import { projectStatusLabels } from '../../lib/projects';

export default function ProjectsPage() {
  const { projects, pages, programmes } = useContent();
  const [search, setSearch] = useState('');
  const [stage, setStage] = useState('');
  const [programme, setProgramme] = useState('');
  const entries = filterProjects(projects, search, stage, programme);
  const reset = () => {
    setSearch('');
    setStage('');
    setProgramme('');
  };
  const filtered = Boolean(search || stage || programme);
  return (
    <>
      <Seo />
      <PageHero
        title={pages.projects.headline!}
        text={pages.projects.fields['Intro']}
        eyebrow="PROJECTS & CAMPAIGNS"
        current="Projects"
      />
      <section className="section">
        <div className="max-w-7xl mx-auto container">
          <div className="filters">
            <SearchField label="Search projects" value={search} onChange={setSearch} />
            <SelectField
              label="Programme"
              value={programme}
              onValueChange={setProgramme}
              allLabel="All programmes"
              options={programmes.map((item) => ({ value: item.id, label: item.title }))}
            />
            <SelectField
              label="Project status"
              value={stage}
              onValueChange={setStage}
              allLabel="All statuses"
              options={Object.entries(projectStatusLabels).map(([value, label]) => ({
                value,
                label,
              }))}
            />
            {filtered && (
              <button className="filter-reset" onClick={reset}>
                Clear filters
              </button>
            )}
          </div>
          <p className="result-count" role="status">
            {entries.length} {entries.length === 1 ? 'project' : 'projects'}
            {filtered ? ' match your filters' : ''}
          </p>
          {entries.length ? (
            <div className="cards-grid">
              {entries.map((item) => (
                <ProjectCard key={item.id} project={item} />
              ))}
            </div>
          ) : (
            <EmptyState kind="projects" filtered={filtered} reset={reset} />
          )}
        </div>
      </section>
      <CTA />
    </>
  );
}
