import { Link } from '@inertiajs/react';
import { useContent } from '../content';
import type { Event } from '../types/content';
import { dateLabel } from '../lib/format';
import { getEventStatus } from '../lib/events';
import { EntryCard } from './UI';
import { EventStatusBadge } from './EventStatusBadge';

export function EventCard({ event, now }: { event: Event; now: number }) {
  const { programmes } = useContent();
  const related = programmes.filter((programme) => event.programmeIds.includes(programme.id));

  return (
    <EntryCard
      entry={event}
      to={`/events/${event.slug}`}
      label={`${dateLabel(event.startDate, event.timezone)} · ${event.timezone}`}
      badge={<EventStatusBadge status={getEventStatus(event, now)} />}
    >
      {related.length > 0 && (
        <ul className="event-programmes" aria-label="Programmes">
          {related.map((programme) => (
            <li key={programme.id}>
              <Link href={`/programmes/${programme.slug}`}>{programme.title}</Link>
            </li>
          ))}
        </ul>
      )}
    </EntryCard>
  );
}
