import { useContent } from '../../content';
import { useState } from 'react';

import { filterEvents } from '../../content/selectors';
import { useEventTime } from '../../hooks/useEventTime';
import { Seo } from '../../lib/seo';
import { CTA, EmptyState, PageHero, SearchField } from '../../components/UI';
import { SelectField } from '../../components/SelectField';
import { EventCard } from '../../components/EventCard';

export default function EventsPage() {
  const { events, pages, programmes, sections } = useContent();
  const labels = sections['events'];
  const [search, setSearch] = useState('');
  const [status, setStatus] = useState('');
  const [programme, setProgramme] = useState('');
  const now = useEventTime();
  const entries = filterEvents(events, search, status, programme, now, programmes);
  const filtered = Boolean(search || status || programme);
  const reset = () => {
    setSearch('');
    setStatus('');
    setProgramme('');
  };
  return (
    <>
      <Seo />
      <PageHero
        title={pages.events.headline!}
        text={pages.events.fields['Intro']}
        eyebrow={labels.heroEyebrow}
        current={labels.heroCurrent}
      />
      <section className="section">
        <div className="max-w-7xl mx-auto container">
          <div className="filters">
            <SearchField label="Search events" value={search} onChange={setSearch} />
            <SelectField
              label="Programme"
              value={programme}
              onValueChange={setProgramme}
              allLabel="All programmes"
              options={programmes.map((item) => ({ value: item.id, label: item.title }))}
            />
            <SelectField
              label="Event status"
              value={status}
              onValueChange={setStatus}
              allLabel="All statuses"
              options={[
                { value: 'upcoming', label: 'Upcoming' },
                { value: 'ongoing', label: 'Ongoing' },
                { value: 'past', label: 'Past' },
              ]}
            />
            {filtered && (
              <button className="filter-reset" onClick={reset}>
                Clear filters
              </button>
            )}
          </div>
          <p className="result-count" role="status">
            {entries.length} {entries.length === 1 ? 'event' : 'events'}
            {filtered ? ' match your filters' : ''}
          </p>
          {entries.length ? (
            <div className="cards-grid">
              {entries.map((item) => (
                <EventCard key={item.id} event={item} now={now} />
              ))}
            </div>
          ) : (
            <EmptyState kind="events" filtered={filtered} reset={reset} />
          )}
        </div>
      </section>
      <CTA />
    </>
  );
}
