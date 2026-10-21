import { useContent } from '../../content';

import { Seo } from '../../lib/seo';
import { TeamMemberCard } from '../../components/TeamMemberCard';
import { CTA, EmptyState, PageHero, SectionHeading } from '../../components/UI';

export default function TeamPage() {
  const { pages, people, sections } = useContent();
  const labels = sections['team'];
  return (
    <>
      <Seo />
      <PageHero
        title={pages.team.headline!}
        text={pages.team.fields['Intro']}
        eyebrow={labels.heroEyebrow}
        current={labels.heroCurrent}
      />
      <section className="section">
        <div className="max-w-7xl mx-auto container">
          {people.length > 0 ? (
            [
              {
                key: 'leadership',
                eyebrow: labels.leadershipEyebrow,
                title: labels.leadershipTitle,
              },
              { key: 'team', eyebrow: labels.teamEyebrow, title: labels.teamTitle },
              {
                key: 'volunteers',
                eyebrow: labels.volunteersEyebrow,
                title: labels.volunteersTitle,
              },
            ].map((group) => {
              const members = people.filter((item) => item.group === group.key);
              if (!members.length) return null;
              return (
                <section key={group.key} className="team-group">
                  <SectionHeading eyebrow={group.eyebrow} title={group.title} />
                  <div className="team-members-grid">
                    {members.map((person) => (
                      <TeamMemberCard key={person.id} person={person} />
                    ))}
                  </div>
                </section>
              );
            })
          ) : (
            <EmptyState kind="team" />
          )}
        </div>
      </section>
      <CTA />
    </>
  );
}
