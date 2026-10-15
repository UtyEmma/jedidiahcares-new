import { Link } from '@inertiajs/react';
import type { Project } from '../types/content';
import { MapPin } from './icons';
import { ProjectFunding } from './ProjectFunding';
import { ProjectStatusBadge } from './ProjectStatusBadge';
import { Media, TextLink } from './UI';

export function ProjectCard({ project }: { project: Project }) {
  const to = `/projects/${project.slug}`;

  return (
    <article className="entry-card project-card" data-entrance>
      <div className="project-card-media">
        <Link href={to} className="project-card-image-link" tabIndex={-1} aria-hidden="true">
          <Media src={project.coverImage} alt={project.coverAlt} />
        </Link>
        <ProjectStatusBadge stage={project.stage} />
      </div>
      <div className="entry-card-body">
        {project.location && (
          <p className="project-card-location">
            <MapPin size={17} aria-hidden="true" />
            {project.location}
          </p>
        )}
        <h2>
          <Link href={to}>{project.title}</Link>
        </h2>
        <p className="project-card-excerpt">{project.summary}</p>
        <ProjectFunding project={project} />
        <TextLink to={to}>Explore project</TextLink>
      </div>
    </article>
  );
}
