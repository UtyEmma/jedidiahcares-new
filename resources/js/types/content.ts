export type PublicationState = 'draft' | 'approved' | 'archived';
export interface Publication {
  publicationState: PublicationState;
  isPublished: boolean;
}
export interface Section {
  heading: string;
  text: string;
}
export type ContentBlock =
  | {
      type: 'paragraph' | 'heading' | 'quote';
      text: string;
      attribution?: string;
    }
  | { type: 'bullets'; items: string[] };
export interface BaseEntry extends Publication {
  id: string;
  slug: string;
  title: string;
  summary: string;
  coverImage?: string;
  coverAlt?: string;
}
export interface Programme extends BaseEntry {
  originalThematicName: string;
  shortTitle: string;
  heroTitle: string;
  introduction: string;
  icon: string;
  number: string;
  illustration: string;
  photoId?: string;
  sections: Section[];
  activities: string[];
  relatedProjectIds: string[];
}
export interface Project extends BaseEntry {
  programmeIds: string[];
  stage: 'planned' | 'active' | 'completed';
  location?: string;
  startDate?: string;
  intendedActivities: string[];
  sections?: Section[];
  verifiedOutcomes?: {
    description: string;
    source: string;
    verified: boolean;
  }[];
  reportPaths?: string[];
  approvedDonationUrl?: string;
  fundingTarget?: number | null;
  fundsRaised?: number | null;
  fundingCurrency?: 'NGN' | 'USD' | 'GBP' | 'EUR';
}
export interface Story extends BaseEntry {
  category: string;
  publishedAt: string;
  programmeId?: string;
  projectId?: string;
  author?: string;
  blocks: ContentBlock[];
}
export type EventStatus = 'upcoming' | 'ongoing' | 'past';
export interface Event extends BaseEntry {
  startDate: string;
  endDate?: string;
  timezone: string;
  location: string;
  audience: string;
  registrationUrl?: string;
  sections: Section[];
  programmeIds: string[];
}
export type PersonSocialPlatform =
  'linkedin' | 'facebook' | 'instagram' | 'x' | 'youtube' | 'tiktok' | 'website';
export interface Person extends BaseEntry {
  role: string;
  group: 'leadership' | 'team' | 'volunteers';
  biography: string;
  portraitConsent: boolean;
  email?: string;
  socialLinks?: Partial<Record<PersonSocialPlatform, string>>;
  sourceUrl?: string;
  imageSourceUrl?: string;
  checkedOn?: string;
  sourceNote?: string;
}
export interface GalleryCollection extends BaseEntry {
  isActive: boolean;
  projectId?: string;
  eventId?: string;
  media: GalleryMedia[];
}
export interface GalleryMedia {
  id: string;
  type: 'image' | 'video';
  src: string;
  thumbnail?: string;
  poster?: string;
  captions?: string;
  transcript?: string;
  mimeType: string;
  alt: string;
  caption: string;
  credit: string;
}
export interface Partner extends BaseEntry {
  url?: string;
  logo?: string;
  mediaRightsApproved: boolean;
}
export interface PolicySection extends Section {
  paragraphs?: string[];
  items?: string[];
  links?: { label: string; url: string }[];
}
export interface Policy extends BaseEntry {
  lastReviewed?: string;
  updatedAt?: string;
  sections: PolicySection[];
  contact?: {
    title: string;
    text: string;
    topic: string;
    label: string;
  };
}
export interface FAQ extends Publication {
  id: string;
  topic: string;
  question: string;
  answer: string;
}
export interface ImpactMetric extends Publication {
  id: string;
  label: string;
  value: number;
  unit?: string;
  period: string;
  source: string;
  verified: boolean;
}
export interface Report extends BaseEntry {
  path: string;
  period: string;
  publishedAt: string;
  size: string;
}
export interface PageCopy {
  title: string;
  headline?: string;
  fields: Record<string, string>;
}
export interface NavItem {
  label: string;
  href: string;
  children?: NavItem[];
}
export interface GivingConfig {
  enabled: boolean;
  externalDonationUrl: string;
  providerName: string;
  unavailable: string;
  bankTransfer: null | {
    approved: boolean;
    accountName: string;
    bank: string;
    accountNumber: string;
    instructions: string;
  };
}
