import type { WebsiteContent } from '../content';

export interface SeoMetadata {
  title: string;
  description: string;
  canonical: string;
  robots: string;
  type: string;
  image: string;
  structuredData: Record<string, unknown> | null;
}

export interface WebsitePageProps {
  [key: string]: unknown;
  seo: SeoMetadata;
  content: WebsiteContent;
  slug?: string;
  projectSlug?: string;
}
