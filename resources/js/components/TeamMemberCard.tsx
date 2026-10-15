import type { Person } from '../types/content';
import { Link } from '@inertiajs/react';
import { ArrowUpRight } from './icons';
import { TeamMemberPortrait } from './TeamMemberPortrait';
import { TeamMemberSocialLinks } from './TeamMemberSocialLinks';

export function TeamMemberCard({ person }: { person: Person }) {
  return (
    <article className="entry-card team-member-card" data-entrance>
      <Link
        href={person.detailUrl}
        className="team-member-card-link"
        aria-label={`${person.title}, ${person.role} — view profile`}
      >
        <TeamMemberPortrait person={person} />
        <div className="entry-card-body">
          <p className="small-label">{person.role}</p>
          <h3>{person.title}</h3>
          <p className="team-member-excerpt">{person.biography}</p>
          <div className="text-link team-member-profile-link" aria-hidden="true">
            View profile
            <ArrowUpRight size={18} />
          </div>
        </div>
      </Link>
      <TeamMemberSocialLinks person={person} compact className="team-member-card-socials" />
    </article>
  );
}
