import { usePage } from '@inertiajs/react';
import type { SiteContent, InterfaceCopy, WebsiteRoutes } from '../types/site';
import type {
  Programme,
  Project,
  Story,
  Event,
  Person,
  GalleryCollection,
  Partner,
  Policy,
  FAQ,
  ImpactMetric,
  Report,
  PageCopy,
  NavItem,
} from '../types/content';
import type { WebsitePageProps } from '../types/inertia';
export { publicEntries, verifiedMetrics, safeExternalUrl } from './selectors';

export interface WebsiteContent {
  now: number;
  site: SiteContent;
  routes: WebsiteRoutes;
  navigation: {
    primary: NavItem[];
    footer: { title: string; links: NavItem[] }[];
    policies: NavItem[];
  };
  pages: Record<string, PageCopy>;
  ui: InterfaceCopy;
  sections: Record<string, Record<string, string>>;
  programmes: Programme[];
  projects: Project[];
  stories: Story[];
  events: Event[];
  people: Person[];
  galleries: GalleryCollection[];
  selectedGallery: GalleryCollection | null;
  partners: Partner[];
  policies: Policy[];
  faq: FAQ[];
  metrics: ImpactMetric[];
  reports: Report[];
  media: Record<
    string,
    {
      src: string;
      smallSrc?: string;
      alt: string;
      credit: string;
      source: string;
      width: number;
      height: number;
      caption: string;
      representative: boolean;
      generated?: boolean;
      objectPosition?: string;
    }
  >;
}

export function useContent() {
  const content = usePage<WebsitePageProps>().props.content;
  return {
    ...content,
    findProgramme: (slug: string) => content.programmes.find((item) => item.slug === slug),
    findProject: (slug: string) => content.projects.find((item) => item.slug === slug),
    findStory: (slug: string) => content.stories.find((item) => item.slug === slug),
    findEvent: (slug: string) => content.events.find((item) => item.slug === slug),
    findPerson: (slug: string) => content.people.find((item) => item.slug === slug),
  };
}
