import type { GivingConfig } from './content';

// Public Inertia contracts. Content values are resolved by Laravel in resources/data.
export interface SiteContent {
  brand: {
    publicName: string;
    wordmarkName: string;
    primaryHero: string;
    tagline: string;
    logoPath: string;
    faviconPath: string;
    shortName: string;
    primaryColor: string;
    accentColor: string;
    footerLogoPath: string;
  };
  seo: {
    url: string;
    description: string;
    socialImage: string;
    editorialApproved: boolean;
  };
  editorial: {
    mediaNote: string;
  };
  contact: {
    email: string;
    emailVerified: boolean;
    phone: string;
    phoneVerified: boolean;
    address: string;
    locationVerified: boolean;
    unavailable: string;
    topics: string[];
    source: string;
    checkedOn: string;
    socialHeading: string;
    socialText: string;
  };
  giving: GivingConfig;
  volunteering: {
    externalInterestFormUrl: string;
    roles: {
      title: string;
      description: string;
      icon: string;
    }[];
    steps: string[];
  };
  partnership: {
    externalEnquiryFormUrl: string;
    options: string[];
    steps: string[];
  };
  social: {
    facebook: string;
    instagram: string;
    linkedin: string;
    x: string;
    youtube: string;
    tiktok: string;
  };
  home: {
    eyebrow: string;
    introEyebrow: string;
    introHeadline: string;
    introText: string;
    heroText: string;
    heroAside: string;
    artCaption: string;
    programmeEyebrow: string;
    programmeHeadline: string;
    programmeText: string;
    featureEyebrow: string;
    featureTitle: string;
    featureText: string;
    evidenceEyebrow: string;
    evidenceTitle: string;
    evidenceText: string;
    involvementEyebrow: string;
    involvementTitle: string;
    involvementText: string;
    finalTitle: string;
    finalText: string;
    values: string[];
    heroPhotoId: string;
    featurePhotoId: string;
    heroLines: string[];
  };
  about: {
    vision: string;
    visionStatus: string;
    mission: string;
    values: {
      title: string;
      text: string;
    }[];
    approach: string[];
  };
  involvement: {
    title: string;
    description: string;
    href: string;
    icon: string;
    label: string;
  }[];
  empty: {
    projects: {
      title: string;
      text: string;
    };
    stories: {
      title: string;
      text: string;
    };
    events: {
      title: string;
      text: string;
    };
    team: {
      title: string;
      text: string;
    };
    gallery: {
      title: string;
      text: string;
    };
    reports: {
      title: string;
      text: string;
    };
  };
  footer: {
    text: string;
    note: string;
  };
  topBar: {
    contactLabel: string;
    contactHref: string;
    ctaLabel: string;
    ctaHref: string;
  };
  founderNote: {
    enabled: boolean;
    approved: boolean;
    label: string;
    headline: string;
    text: string;
    name: string;
    role: string;
    portraitPath: string;
    portraitAlt: string;
    motifLines: string[];
    portraitSource: string;
  };
  partnerShowcase: {
    eyebrow: string;
    title: string;
    text: string;
    emptyTitle: string;
    emptyText: string;
    homeCta: string;
    pageCta: string;
  };
  socialSource: {
    url: string;
    checkedOn: string;
    note: string;
  };
}

export interface InterfaceCopy {
  home: {
    scroll: string;
    programmeNoteEyebrow: string;
    programmeNoteTitle: string;
    programmeNoteText: string;
    faithLine: string;
    careLine: string;
    evidenceNoteTitle: string[];
    evidenceNoteText: string;
  };
  about: {
    introTitle: string[];
    missionTitle: string;
    valuesTitle: string;
    valuesText: string;
    approachTitle: string;
  };
  programme: {
    purpose: string;
    helpTitle: string;
    arrangements: string;
    relatedTitle: string;
    exploreTitle: string;
    hostTitle: string;
  };
  getInvolved: {
    hostTitle: string[];
  };
  support: {
    intro: string;
    purposeTitle: string;
    causes: {
      icon: string;
      title: string;
      text: string;
    }[];
    allocationNote: string;
    otherTitle: string;
    otherText: string;
  };
  volunteer: {
    rolesTitle: string;
    nextTitle: string[];
    safeguarding: string;
    externalTitle: string;
    externalText: string;
  };
  partner: {
    introTitle: string[];
    approachTitle: string;
    externalTitle: string;
    externalText: string;
    collaboratorsTitle: string;
  };
  contact: {
    title: string[];
    guidance: string;
    pendingTitle: string;
  };
  impact: {
    evidenceTitle: string;
    progressTitle: string;
    progressText: string;
    reportsTitle: string;
    projectsTitle: string;
  };
  actions: {
    givingTitle: string;
    handoff: string;
    emailTitle: string;
    emailText: string;
  };
  policy: {
    pages: {
      privacy: {
        intro: string;
        title: string;
        text: string;
        detail: string;
      };
      safeguarding: {
        intro: string;
        title: string;
        text: string;
        detail: string;
      };
      'donation-terms': {
        intro: string;
        title: string;
        text: string;
        detail: string;
      };
      accessibility: {
        intro: string;
        title: string;
        text: string;
        detail: string;
      };
    };
  };
  faq: {
    title: string;
    text: string;
  };
}

export interface WebsiteRoutes {
  home: string;
  about: string;
  programmes: string;
  projects: string;
  reports: string;
  stories: string;
  events: string;
  getInvolved: string;
  donations: string;
  volunteers: string;
  partner: string;
  team: string;
  gallery: string;
  contact: string;
  privacy: string;
  safeguarding: string;
  donationTerms: string;
  accessibility: string;
}
