import type { Event, EventStatus } from '../types/content';

export const eventStatusLabels: Record<EventStatus, string> = {
  upcoming: 'Upcoming',
  ongoing: 'Ongoing',
  past: 'Past',
};

export function getEventStatus(
  event: Pick<Event, 'startDate' | 'endDate' | 'timezone'>,
  now: number,
): EventStatus {
  const start = new Date(event.startDate).getTime();
  if (!Number.isFinite(start)) return 'past';
  if (start > now) return 'upcoming';

  if (event.endDate) {
    const end = new Date(event.endDate).getTime();
    if (Number.isFinite(end)) return now <= end ? 'ongoing' : 'past';
  }

  // Without an end time, use the event's calendar day, never the visitor's timezone.
  const day = new Intl.DateTimeFormat('en-NG', {
    timeZone: event.timezone,
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
  });
  return day.format(new Date(start)) === day.format(new Date(now)) ? 'ongoing' : 'past';
}
