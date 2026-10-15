import type { Publication, ImpactMetric, Project, Story, Event, Programme } from '../types/content';
import { getEventStatus } from '../lib/events';
export const publicEntries = <T extends Publication>(entries: T[]): T[] =>
  entries.filter((item) => item.isPublished && item.publicationState === 'approved');
export const verifiedMetrics = (entries: ImpactMetric[]) =>
  publicEntries(entries).filter((item) => item.verified && item.source && item.period);
export function filterProjects(
  entries: Project[],
  search: string,
  stage: string,
  programme: string,
) {
  const term = search.trim().toLowerCase();
  return publicEntries(entries)
    .filter(
      (item) =>
        (!term ||
          `${item.title} ${item.summary} ${item.location ?? ''}`.toLowerCase().includes(term)) &&
        (!stage || item.stage === stage) &&
        (!programme || item.programmeIds.includes(programme)),
    )
    .sort(
      (a, b) =>
        (b.startDate ?? '').localeCompare(a.startDate ?? '') || a.title.localeCompare(b.title),
    );
}
export function filterStories(entries: Story[], search: string, category: string, year: string) {
  return publicEntries(entries)
    .filter(
      (item) =>
        `${item.title} ${item.summary}`.toLowerCase().includes(search.trim().toLowerCase()) &&
        (!category || item.category === category) &&
        (!year || item.publishedAt.startsWith(year)),
    )
    .sort((a, b) => b.publishedAt.localeCompare(a.publishedAt));
}

export function filterEvents(
  entries: Event[],
  search: string,
  status: string,
  programme: string,
  now: number,
  programmes: Programme[] = [],
) {
  const term = search.trim().toLowerCase();
  const titles = new Map(programmes.map((item) => [item.id, item.title]));
  const order = { ongoing: 0, upcoming: 1, past: 2 };

  return publicEntries(entries)
    .map((event) => ({ event, status: getEventStatus(event, now) }))
    .filter(({ event, status: eventStatus }) => {
      const programmeTitles = (event.programmeIds ?? [])
        .map((id) => titles.get(id) ?? '')
        .join(' ');
      return (
        (!term ||
          `${event.title} ${event.summary} ${event.location} ${event.audience} ${programmeTitles}`
            .toLowerCase()
            .includes(term)) &&
        (!status || eventStatus === status) &&
        (!programme || (event.programmeIds ?? []).includes(programme))
      );
    })
    .sort((a, b) => {
      const group = order[a.status] - order[b.status];
      const dates = new Date(a.event.startDate).getTime() - new Date(b.event.startDate).getTime();
      return (
        group ||
        (a.status === 'past' ? -dates : dates) ||
        a.event.title.localeCompare(b.event.title)
      );
    })
    .map(({ event }) => event);
}
export const safeExternalUrl = (value: string) => {
  try {
    const url = new URL(value);
    return url.protocol === 'https:' ? url.href : undefined;
  } catch {
    return undefined;
  }
};
