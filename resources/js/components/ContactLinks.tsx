import { useContent } from '../content';
import { Link } from '@inertiajs/react';
import {
  Mail,
  Phone,
  MapPin,
  FacebookLogo,
  InstagramLogo,
  LinkedinLogo,
  XLogo,
  ArrowUpRight,
} from './icons';
import { safeExternalUrl } from '../content';
import { cn } from '@/lib/utils';

const socialPlatforms = [
  { key: 'facebook', label: 'Facebook', Icon: FacebookLogo },
  { key: 'instagram', label: 'Instagram', Icon: InstagramLogo },
  { key: 'linkedin', label: 'LinkedIn', Icon: LinkedinLogo },
  { key: 'x', label: 'X', Icon: XLogo },
] as const;

export function SocialLinks({ className }: { className?: string }) {
  const { site } = useContent();
  const links = socialPlatforms.flatMap((platform) => {
    const url = safeExternalUrl(site.social[platform.key]);
    return url ? [{ ...platform, url }] : [];
  });
  if (!links.length) return null;
  return (
    <div
      className={cn('social-links', className)}
      role="group"
      aria-label={`${site.brand.publicName} social profiles`}
    >
      {links.map(({ key, label, Icon, url }) => (
        <a
          key={key}
          href={url}
          target="_blank"
          rel="noopener noreferrer"
          aria-label={`${site.brand.publicName} on ${label} (opens in a new tab)`}
          title={label}
        >
          <Icon size={22} weight="regular" aria-hidden="true" />
        </a>
      ))}
    </div>
  );
}

export function ContactLinks({
  compact = false,
  className,
}: {
  compact?: boolean;
  className?: string;
}) {
  const { site } = useContent();
  const email = site.contact.emailVerified && site.contact.email;
  const phone = site.contact.phoneVerified && site.contact.phone;
  const address = site.contact.locationVerified && site.contact.address;
  return (
    <div className={cn('contact-links', compact && 'contact-links-compact', className)}>
      {email && (
        <a href={`mailto:${email}`}>
          <Mail size={20} aria-hidden="true" />
          <span>{email}</span>
        </a>
      )}
      {phone && (
        <a href={`tel:${phone.replace(/[\s()-]/g, '')}`}>
          <Phone size={20} aria-hidden="true" />
          <span>{phone}</span>
        </a>
      )}
      {!compact && address && (
        <div className="contact-address">
          <MapPin size={21} aria-hidden="true" />
          <span>{address}</span>
        </div>
      )}
      {!email &&
        !phone &&
        (compact ? (
          <Link href={site.topBar.contactHref}>
            <Mail size={20} aria-hidden="true" />
            {site.topBar.contactLabel}
          </Link>
        ) : (
          <p>{site.contact.unavailable}</p>
        ))}
    </div>
  );
}

export function SocialMessaging() {
  const { site } = useContent();
  const links = socialPlatforms.flatMap((platform) => {
    const url = safeExternalUrl(site.social[platform.key]);
    return url ? [{ ...platform, url }] : [];
  });
  if (!links.length) return null;
  return (
    <section className="social-messaging" aria-labelledby="social-messaging-title">
      <h3 id="social-messaging-title">{site.contact.socialHeading}</h3>
      <p>{site.contact.socialText}</p>
      <div className="social-message-links">
        {links.map(({ key, label, Icon, url }) => (
          <a key={key} href={url} target="_blank" rel="noopener noreferrer">
            <Icon size={24} aria-hidden="true" />
            <span>{label}</span>
            <ArrowUpRight size={18} aria-hidden="true" />
            <span className="sr-only">: {site.brand.publicName} profile (opens in a new tab)</span>
          </a>
        ))}
      </div>
    </section>
  );
}
