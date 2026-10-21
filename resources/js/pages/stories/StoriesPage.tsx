import { useContent } from '../../content';
import { useState } from 'react';

import { filterStories } from '../../content/selectors';
import { dateLabel } from '../../lib/format';
import { Seo } from '../../lib/seo';
import { CTA, EmptyState, EntryCard, PageHero, SearchField } from '../../components/UI';
import { SelectField } from '../../components/SelectField';

export default function StoriesPage() {
  const { stories, pages, sections } = useContent();
  const labels = sections['stories'];
  const [search, setSearch] = useState('');
  const [category, setCategory] = useState('');
  const [year, setYear] = useState('');
  const entries = filterStories(stories, search, category, year);
  const filtered = Boolean(search || category || year);
  const reset = () => {
    setSearch('');
    setCategory('');
    setYear('');
  };
  return (
    <>
      <Seo />
      <PageHero
        title={pages.stories.headline!}
        text={pages.stories.fields['Introduction']}
        eyebrow={labels.heroEyebrow}
        current={labels.heroCurrent}
      />
      <section className="section">
        <div className="max-w-7xl mx-auto container">
          <div className="filters">
            <SearchField label="Search stories" value={search} onChange={setSearch} />
            <SelectField
              label="Category"
              value={category}
              onValueChange={setCategory}
              allLabel="All categories"
              options={[...new Set(stories.map((item) => item.category))]
                .sort()
                .map((value) => ({ value, label: value }))}
            />
            <SelectField
              label="Publication year"
              value={year}
              onValueChange={setYear}
              allLabel="All years"
              options={[...new Set(stories.map((item) => item.publishedAt.slice(0, 4)))]
                .sort()
                .reverse()
                .map((value) => ({ value, label: value }))}
            />
            {filtered && (
              <button className="filter-reset" onClick={reset}>
                Clear filters
              </button>
            )}
          </div>
          <p className="result-count" role="status">
            {entries.length} {entries.length === 1 ? 'story' : 'stories'}
          </p>
          {entries.length ? (
            <div className="cards-grid">
              {entries.map((item) => (
                <EntryCard
                  key={item.id}
                  entry={item}
                  to={item.detailUrl}
                  label={`${item.category} · ${dateLabel(item.publishedAt)}`}
                />
              ))}
            </div>
          ) : (
            <EmptyState kind="stories" filtered={filtered} reset={reset} />
          )}
        </div>
      </section>
      <CTA />
    </>
  );
}
