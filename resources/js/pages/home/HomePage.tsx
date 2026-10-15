import { useContent } from '../../content';

import { ArrowDown, ArrowUpRight, Heart, ShieldCheck } from '../../components/icons';
import { Link } from '@inertiajs/react';

import { Seo } from '../../lib/seo';
import { Photo } from '../../components/Photo';
import { BrandAsterisk } from '../../components/BrandAsterisk';
import { FounderNote } from '../../components/FounderNote';
import { PartnersSection } from '../../components/PartnersSection';
import { EventCard } from '../../components/EventCard';
import { ProjectCard } from '../../components/ProjectCard';
import { useEventTime } from '../../hooks/useEventTime';
import { filterEvents } from '../../content/selectors';
import {
  ButtonLink,
  CTA,
  EntryCard,
  Eyebrow,
  Icon,
  ProgrammeRow,
  SectionHeading,
  TextLink,
} from '../../components/UI';
export default function HomePage() {
  const { site, ui, programmes, projects, metrics, stories, events } = useContent();
  const now = useEventTime();
  const upcomingEvents = filterEvents(events, '', 'upcoming', '', now).slice(0, 3);
  const copy = site.home;
  return (
    <>
      <Seo />
      <section className="home-hero">
        <div className="max-w-7xl mx-auto container hero-grid">
          <div className="hero-copy" data-entrance>
            <Eyebrow>{copy.eyebrow}</Eyebrow>
            <h1>
              {copy.heroLines[0]}
              <br />
              <span className="highlight">{copy.heroLines[1]}</span>
              <br />
              {copy.heroLines[2]}
              <span className="lemon-period">.</span>
            </h1>
            <p>{copy.heroText}</p>
            <div className="hero-actions">
              <ButtonLink to="/programmes">Explore our work</ButtonLink>
              <ButtonLink to="/get-involved" variant="outline">
                Get involved
              </ButtonLink>
            </div>
            <a className="scroll-cue" href="#our-purpose">
              <span>
                <ArrowDown size={16} aria-hidden="true" />
              </span>
              {ui.home.scroll}
            </a>
          </div>
          <div className="hero-visual" data-entrance>
            <Photo id={copy.heroPhotoId} priority className="hero-photo" />
            <div className="hero-art-label">
              <Heart size={23} weight="regular" aria-hidden="true" />
              <p>{copy.artCaption}</p>
              <span>{site.brand.publicName}</span>
            </div>
            <span className="hero-side-note">{copy.heroAside}</span>
            <BrandAsterisk className="hero-asterisk" />
          </div>
        </div>
      </section>
      <div className="values-strip">
        <div className="max-w-7xl mx-auto container">
          {copy.values.map((value) => (
            <div key={value}>
              <BrandAsterisk />
              {value}
            </div>
          ))}
        </div>
      </div>
      <section className="section intro-section" id="our-purpose">
        <div className="max-w-7xl mx-auto container intro-grid">
          <div>
            <Eyebrow>{copy.introEyebrow}</Eyebrow>
            <h2>{copy.introHeadline}</h2>
          </div>
          <div className="intro-copy" data-entrance>
            <p>{copy.introText}</p>
            <TextLink to="/about">Meet our organisation</TextLink>
            <div className="intro-footnote">
              <span aria-hidden="true" />
              <p>
                {ui.home.faithLine}
                <br />
                {ui.home.careLine}
              </p>
            </div>
          </div>
        </div>
      </section>
      <FounderNote compact />
      <section className="section programme-section">
        <div className="max-w-7xl mx-auto container">
          <SectionHeading
            eyebrow={copy.programmeEyebrow}
            title={copy.programmeHeadline}
            text={copy.programmeText}
            action={{ label: 'All programmes', to: '/programmes' }}
          />
          <div className="home-programme-layout">
            <div className="programme-note">
              <BrandAsterisk className="big-asterisk" />
              <p className="small-label">{ui.home.programmeNoteEyebrow}</p>
              <h3>{ui.home.programmeNoteTitle}</h3>
              <p>{ui.home.programmeNoteText}</p>
              <Link href="/programmes" aria-label="Discover our four programme areas">
                <ArrowUpRight size={27} aria-hidden="true" />
              </Link>
            </div>
            <div>
              {programmes.map((programme) => (
                <ProgrammeRow key={programme.id} programme={programme} compact />
              ))}
            </div>
          </div>
        </div>
      </section>
      <section className="section home-feature">
        <div className="max-w-7xl mx-auto container feature-grid">
          <div className="feature-art">
            <Photo id={copy.featurePhotoId} className="feature-photo" />
          </div>
          <div className="feature-copy" data-entrance>
            <Eyebrow>{copy.featureEyebrow}</Eyebrow>
            <h2>{copy.featureTitle}</h2>
            <p>{copy.featureText}</p>
            <div className="feature-detail">
              <Icon name="book" size={28} />
              <span>Education & Scholarships</span>
            </div>
            <ButtonLink to="/programmes/education">Discover education support</ButtonLink>
          </div>
        </div>
      </section>
      {projects.length > 0 && (
        <section className="section">
          <div className="max-w-7xl mx-auto container">
            <SectionHeading
              eyebrow="PROJECTS & CAMPAIGNS"
              title="A purpose behind every project."
              action={{ label: 'Explore projects', to: '/projects' }}
            />
            <div className="cards-grid">
              {projects.slice(0, 3).map((item) => (
                <ProjectCard key={item.id} project={item} />
              ))}
            </div>
          </div>
        </section>
      )}
      <section className="section evidence-section">
        <div className="max-w-7xl mx-auto container evidence-grid">
          <div>
            <Eyebrow>{copy.evidenceEyebrow}</Eyebrow>
            <h2>{copy.evidenceTitle}</h2>
            <p>{copy.evidenceText}</p>
            <TextLink to="/reports">Our approach to impact</TextLink>
          </div>
          <div className="evidence-note">
            <ShieldCheck size={44} weight="regular" aria-hidden="true" />
            <h3>
              {ui.home.evidenceNoteTitle[0]}
              <br />
              {ui.home.evidenceNoteTitle[1]}
            </h3>
            <p>{ui.home.evidenceNoteText}</p>
            <div className="evidence-rule" />
            <TextLink to="/projects">Explore our projects</TextLink>
          </div>
        </div>
        {metrics.length > 0 && (
          <div className="max-w-7xl mx-auto container metrics-grid">
            {metrics.map((item) => (
              <div key={item.id}>
                <strong>
                  {item.value.toLocaleString()} {item.unit}
                </strong>
                <p>{item.label}</p>
                <small>
                  {item.period} · {item.source}
                </small>
              </div>
            ))}
          </div>
        )}
      </section>
      {stories.length > 0 && (
        <section className="section">
          <div className="max-w-7xl mx-auto container">
            <SectionHeading
              eyebrow="STORIES & UPDATES"
              title="A closer look at compassion in action."
              action={{ label: 'All stories', to: '/stories' }}
            />
            <div className="cards-grid">
              {stories.slice(0, 3).map((item) => (
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
      {upcomingEvents.length > 0 && (
        <section className="section">
          <div className="max-w-7xl mx-auto container">
            <SectionHeading
              eyebrow="COME TOGETHER"
              title="Upcoming opportunities."
              action={{ label: 'All events', to: '/events' }}
            />
            <div className="cards-grid">
              {upcomingEvents.map((item) => (
                <EventCard key={item.id} event={item} now={now} />
              ))}
            </div>
          </div>
        </section>
      )}
      <section className="section involvement-section">
        <div className="max-w-7xl mx-auto container">
          <SectionHeading
            eyebrow={copy.involvementEyebrow}
            title={copy.involvementTitle}
            text={copy.involvementText}
          />
          <div className="pathways">
            {site.involvement.map((item, index) => (
              <article key={item.href} className={`pathway pathway-${index}`}>
                <Icon name={item.icon} size={36} />
                <h3>{item.title}</h3>
                <p>{item.description}</p>
                <TextLink to={item.href}>{item.label}</TextLink>
              </article>
            ))}
          </div>
        </div>
      </section>
      <PartnersSection />
      <CTA />
    </>
  );
}
