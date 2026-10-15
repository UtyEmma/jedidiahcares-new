import type { EventStatus } from '../types/content';
import { eventStatusLabels } from '../lib/events';

export function EventStatusBadge({ status }: { status: EventStatus }) {
  return <span className={`event-status event-status-${status}`}>{eventStatusLabels[status]}</span>;
}
