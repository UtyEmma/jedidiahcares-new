import { useContent } from '../../content';
import type { WebsitePageProps } from '../../types/inertia';
import { usePage } from '@inertiajs/react';
import { ArrowUpRight, CalendarDays, MapPin } from '../../components/icons';
import { safeExternalUrl } from '../../content';
import { Seo } from '../../lib/seo';
import { dateLabel } from '../../lib/format';
import { eventStatusLabels, getEventStatus } from '../../lib/events';
import { useEventTime } from '../../hooks/useEventTime';
import { EventStatusBadge } from '../../components/EventStatusBadge';
import { ShareButtons } from '../../components/ShareButtons';
import { CTA, Eyebrow, Media, PageHero, TextLink } from '../../components/UI';
import NotFoundPage from '../not-found/NotFoundPage';

export default function EventDetailPage() {
  const { findEvent, programmes, site } = useContent();
  const { slug = '' } = usePage<WebsitePageProps>().props;
  const now = useEventTime();
  const event = findEvent(slug);
  if (!event) return <NotFoundPage />;
  const status = getEventStatus(event, now);
  const past = status === 'past';
  const registration = !past ? safeExternalUrl(event.registrationUrl ?? '') : undefined;
  const related = programmes.filter((programme) => event.programmeIds.includes(programme.id));
  const time = (value: string) =>
    new Intl.DateTimeFormat('en-NG', {
      hour: 'numeric',
      minute: '2-digit',
      timeZone: event.timezone,
    }).format(new Date(value));
  return (
    <>
      <Seo />
      <PageHero
        title={event.title}
        text={event.summary}
        eyebrow={`${eventStatusLabels[status].toUpperCase()} ACTIVITY`}
        current="Event"
      />
      <section className="section">
        <div className="max-w-7xl mx-auto container detail-grid">
          <div className="prose">
            <Media src={event.coverImage} alt={event.coverAlt} />
            {event.sections.map((section) => (
              <section key={section.heading}>
                <h2>{section.heading}</h2>
                <p>{section.text}</p>
              </section>
            ))}
          </div>
          <aside className="programme-aside">
            <div className="event-detail-status">
              <EventStatusBadge status={status} />
            </div>
            <Eyebrow>EVENT DETAILS</Eyebrow>
            <div className="event-fact">
              <CalendarDays aria-hidden="true" />
              <div>
                <h2>{dateLabel(event.startDate, event.timezone)}</h2>
                <p>
                  {time(event.startDate)} · {event.timezone}
                </p>
                {event.endDate && (
                  <p>
                    Ends {dateLabel(event.endDate, event.timezone)} · {time(event.endDate)}
                  </p>
                )}
              </div>
            </div>
            <div className="event-fact">
              <MapPin aria-hidden="true" />
              <p>{event.location}</p>
            </div>
            <h3>Who it’s for</h3>
            <p>{event.audience}</p>
            {related.length > 0 && (
              <div className="event-related-programmes">
                <h3>Programmes</h3>
                {related.map((programme) => (
                  <TextLink key={programme.id} to={`/programmes/${programme.slug}`}>
                    {programme.title}
                  </TextLink>
                ))}
              </div>
            )}
            {registration ? (
              <>
                <a
                  href={registration}
                  className="button button-primary"
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  Open external registration
                  <ArrowUpRight size={19} aria-hidden="true" />
                </a>
                <p className="form-note">The external provider handles registration details.</p>
              </>
            ) : (
              <p>
                {past
                  ? 'This activity has ended.'
                  : `Contact ${site.brand.publicName} to find out how you can participate.`}
              </p>
            )}
            <TextLink to="/events">Explore all activities</TextLink>
            <ShareButtons
              title={event.title}
              description={event.summary}
              label="Share this event"
            />
          </aside>
        </div>
      </section>
      <CTA />
    </>
  );
}
