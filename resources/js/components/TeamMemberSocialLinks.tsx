import type { Person, PersonSocialPlatform } from '../types/content';
import { safeExternalUrl } from '../content/selectors';
import { cn } from '../lib/utils';
import {
  FacebookLogo,
  InstagramLogo,
  LinkedinLogo,
  XLogo,
  YoutubeLogo,
  TiktokLogo,
  Globe,
  Mail,
} from './icons';

const platforms = [
  { key: 'linkedin', label: 'LinkedIn', Icon: LinkedinLogo },
  { key: 'facebook', label: 'Facebook', Icon: FacebookLogo },
  { key: 'instagram', label: 'Instagram', Icon: InstagramLogo },
  { key: 'x', label: 'X', Icon: XLogo },
  { key: 'youtube', label: 'YouTube', Icon: YoutubeLogo },
  { key: 'tiktok', label: 'TikTok', Icon: TiktokLogo },
  { key: 'website', label: 'Website', Icon: Globe },
] satisfies { key: PersonSocialPlatform; label: string; Icon: typeof Globe }[];

export function TeamMemberSocialLinks({
  person,
  compact = false,
  className,
}: {
  person: Person;
  compact?: boolean;
  className?: string;
}) {
  const links = platforms.flatMap((platform) => {
    const url = safeExternalUrl(person.socialLinks?.[platform.key] ?? '');
    return url ? [{ ...platform, url }] : [];
  });
  if (!links.length && !person.email) return null;

  return (
    <nav
      className={cn('person-social-links', compact && 'person-social-links-compact', className)}
      aria-label={`${person.title}'s contact and social profiles`}
    >
      {person.email && (
        <a
          href={`mailto:${person.email}`}
          aria-label={`Email ${person.title}`}
          title={`Email ${person.title}`}
        >
          <Mail size={20} aria-hidden="true" />
          {!compact && <span>{person.email}</span>}
        </a>
      )}
      {links.map(({ key, label, Icon, url }) => (
        <a
          key={key}
          href={url}
          target="_blank"
          rel="noopener noreferrer me"
          aria-label={`${person.title} — ${label} (opens in a new tab)`}
          title={`${person.title} on ${label}`}
        >
          <Icon size={20} aria-hidden="true" />
          {!compact && <span>{label}</span>}
        </a>
      ))}
    </nav>
  );
}
