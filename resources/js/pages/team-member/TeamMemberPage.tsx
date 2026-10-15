import { Link, usePage } from '@inertiajs/react';
import { useContent } from '../../content';
import type { Person } from '../../types/content';
import type { WebsitePageProps } from '../../types/inertia';
import { Seo } from '../../lib/seo';
import { ChevronLeft } from '../../components/icons';
import { TeamMemberCard } from '../../components/TeamMemberCard';
import { TeamMemberPortrait } from '../../components/TeamMemberPortrait';
import { TeamMemberSocialLinks } from '../../components/TeamMemberSocialLinks';
import { ShareButtons } from '../../components/ShareButtons';
import { BrandAsterisk } from '../../components/BrandAsterisk';
import { Breadcrumbs, ButtonLink, Eyebrow, SectionHeading } from '../../components/UI';
import NotFoundPage from '../not-found/NotFoundPage';

const groups: Record<Person['group'], string> = {
  leadership: 'Our directors',
  team: 'Our team',
  volunteers: 'Our volunteers',
};

export default function TeamMemberPage() {
  const { findPerson, people } = useContent();
  const { slug = '' } = usePage<WebsitePageProps>().props;
  const person = findPerson(slug);
  if (!person) return <NotFoundPage />;

  const biography = (person.biography || person.summary)
    .split(/\r?\n+/)
    .map((paragraph) => paragraph.trim())
    .filter(Boolean);
  const introduction = person.summary.trim();
  const showIntroduction =
    introduction && introduction !== person.role.trim() && introduction !== person.biography.trim();
  const related = people
    .filter((member) => member.id !== person.id)
    .sort((a, b) => Number(b.group === person.group) - Number(a.group === person.group))
    .slice(0, 3);

  return (
    <>
      <Seo />
      <section className="person-profile-hero" aria-labelledby="person-name">
        <div className="max-w-7xl mx-auto container">
          <Breadcrumbs current={person.title} parent={{ label: 'Our people', href: '/team' }} />
          <div className="person-profile-grid">
            <div className="person-profile-visual" data-entrance>
              <TeamMemberPortrait person={person} priority className="person-profile-portrait" />
              <div className="person-profile-signature">
                <BrandAsterisk />
                <p>
                  Compassion in action.
                  <br />
                  Potential in every life.
                </p>
              </div>
            </div>
            <header className="person-profile-heading" data-entrance>
              <Eyebrow>{groups[person.group]}</Eyebrow>
              <h1 id="person-name">{person.title}</h1>
              <p className="person-profile-role">{person.role}</p>
              {showIntroduction && <p className="person-profile-introduction">{introduction}</p>}
              <TeamMemberSocialLinks person={person} className="person-profile-socials" />
            </header>
            <article className="person-profile-copy" data-entrance>
              {biography.length > 0 && (
                <section className="person-profile-biography" aria-labelledby="person-biography">
                  <h2 id="person-biography">Biography</h2>
                  {biography.map((paragraph, index) => (
                    <p key={index}>{paragraph}</p>
                  ))}
                </section>
              )}
              <div className="person-profile-actions">
                <ButtonLink to="/contact">Contact our team</ButtonLink>
                <Link href="/team" className="person-profile-back">
                  <ChevronLeft size={18} aria-hidden="true" />
                  Back to our people
                </Link>
              </div>
              <ShareButtons
                title={person.title}
                description={person.summary}
                label="Share this profile"
              />
            </article>
          </div>
        </div>
      </section>
      {related.length > 0 && (
        <section className="section" aria-labelledby="more-people-title">
          <div className="max-w-7xl mx-auto container">
            <div id="more-people-title">
              <SectionHeading
                eyebrow="A SHARED PURPOSE"
                title="Meet more of our people."
                action={{ label: 'All our people', to: '/team' }}
              />
            </div>
            <div className="team-members-grid person-profile-related">
              {related.map((member) => (
                <TeamMemberCard key={member.id} person={member} />
              ))}
            </div>
          </div>
        </section>
      )}
    </>
  );
}
