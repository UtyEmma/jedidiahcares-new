import { useState } from 'react';
import type { Person } from '../types/content';
import { cn } from '../lib/utils';

export function TeamMemberPortrait({
  person,
  priority = false,
  className,
}: {
  person: Person;
  priority?: boolean;
  className?: string;
}) {
  const [failedImage, setFailedImage] = useState('');
  const initials = person.title
    .replace(/^(?:Dr\.|Prof\.|Rev\. Can\.|Mrs\.|Mr\.)\s*/i, '')
    .split(/[\s-]+/)
    .filter(Boolean)
    .map((part) => part[0])
    .slice(0, 2)
    .join('');

  return (
    <div className={cn('team-member-portrait', className)}>
      {person.coverImage && failedImage !== person.coverImage ? (
        <img
          src={person.coverImage}
          alt={person.coverAlt || `Portrait of ${person.title}`}
          width="600"
          height="750"
          loading={priority ? 'eager' : 'lazy'}
          fetchPriority={priority ? 'high' : 'auto'}
          decoding="async"
          onError={() => setFailedImage(person.coverImage!)}
        />
      ) : (
        <div className="team-member-initials" aria-hidden="true">
          <span>{initials}</span>
        </div>
      )}
    </div>
  );
}
