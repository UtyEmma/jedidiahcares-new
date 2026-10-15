---
title: "Jedidiah Cares — Frontend-Only React Website PRD for Codex"
product: "Jedidiah Cares Global Outreach public website"
version: "2.0"
document_status: "Implementation-ready frontend-only working PRD; organisational copy subject to approval"
last_updated: "2026-10-14"
primary_domain: "https://jedidiahcares.org"
reference_repository: "https://github.com/UtyEmma/jedidiahcares (content/design reference only)"
implementation_target: "Standalone React + Vite + TypeScript + React Router SPA with local JSON data"
backend: "NONE"
---

# Jedidiah Cares — Frontend-Only Website PRD (Codex)

> **MANDATORY SCOPE OVERRIDE — FRONTEND ONLY:** Create a standalone **React + Vite + TypeScript + React Router** project. All website content is stored in **local JSON** or locally bundled assets. **No Laravel, PHP, Inertia, database, server, CMS, authentication, API implementation, payment processor, or server-side form handling.** The previously shared Laravel repository is a historical and content reference **only**. This PRD v2.0 entirely supersedes the architecture and integration instructions in the earlier PRD v1.0.

**Live domain:** https://jedidiahcares.org  
**Reference repo:** https://github.com/UtyEmma/jedidiahcares (private; optional research input, not an application to migrate or modify)  
**Design assets:** Provided original `logo.webp`, accompanying CSS tokens, plus approved photographs added under `public/images/`.  
**Primary deliverable:** A polished, deployable, content-rich, accessible, responsive **static frontend** ready for Vite production build.  
**Explicitly prohibited:** Fake form success screens, browser-exposed payment credentials, mock impact totals represented as real, and fake checkout flows.

## 0. Instructions for Codex

1. **Start a fresh frontend project**, ideally a new repository or directory. Do **not** rewrite the historic Laravel app in place unless separately and explicitly requested.
2. Read this PRD in full before implementing. Confirm `logo.webp` and source assets exist, then plan the site shells, JSON structure and routes.
3. Use `npm` (or the project's approved package manager). Recommended base: **React 18+**, **TypeScript**, **Vite**, **React Router**, **Tailwind CSS** plus the supplied CSS custom properties, **Lucide React** icons. Do not introduce a component framework that fights the visual brief; prefer bespoke components.
4. **No backend of any kind.** No `api/` routes, backend mocks, auth, dashboard, database, admin CMS, GraphQL, Firebase, Supabase, server actions, serverless functions, express, PHP, or any calls that rely on an unavailable service.
5. **All displayed content is local JSON** imported by typed TypeScript modules. Programmes, projects, stories, events, FAQs, people, contact information, CTA configuration, impact and partner listings must not be hardcoded across multiple components. JSON should be the editorial source of truth.
6. Keep pages complete, beautiful and usable with static data alone. No spinner waiting for a non-existent API; all routes should render from bundled content.
7. **Truthful functionality:** donation buttons only link to an approved external URL or a verified contact method; contact/volunteer/partnership actions use an email (`mailto:`) handoff or a supplied approved third-party URL. Do not say a message was sent until an external provider actually confirms it. See §8.
8. **Truthful content:** only verified public accomplishments may be presented as completed. Use clear proposed/planned status for programme proposals. Never invent children served, donor counts, fundraising totals, testimonials, awards, partner logos or programme results.
9. Adhere closely to the navy + lemon **logo-derived palette** and dignity-centred charity art direction in §4. Do not substitute an arbitrary green/gold palette from a previous draft.
10. Build in phases: project skeleton → design tokens/layout → all pages/content → media/refinement → tests/SEO → production static build. Run lint, typecheck, tests and build, reporting actual results. No production deployment without permission.
11. Content/asset approval is required before claiming the live site is launch-ready. Editable JSON `isPublished` flags should keep unverified entries off public pages.

### 0.1 Non-negotiable scope table

| Area | Required | Specifically NOT required |
|---|---|---|
| Application | React SPA created with Vite and TypeScript | Laravel, Inertia, PHP, Next.js, Remix, SSR server |
| Navigation | React Router with nested routes and `Link`/`NavLink` | Server-defined routes or Ziggy |
| Data | Local `src/data/*.json` consumed through typed selectors | Backend APIs, DB, CMS, remote fetch |
| User state | Local component state for tabs/search/filters/mobile menu | Login, persistent user accounts, donor profiles |
| Forms | Display-only or `mailto:`/external approved handoff | POST requests, email delivery, database storage |
| Donations | Information plus external verified link / bank details only if approved | Card processing, Flutterwave SDK, transaction simulation |
| Content updates | Edit JSON in code repository and redeploy | Admin dashboard or content editor backend |
| Hosting | Built `dist/` on static hosting with SPA rewrites | PHP server or application runtime |
| Images | Bundled approved photos, logo and safe fallbacks | Unreviewed random beneficiary photos, live media API |
| Testing | Vitest/RTL and browser smoke tests | Laravel/PHPUnit feature tests |

### 0.2 Definition of done for this scope

- `npm install`, `npm run dev`, `npm run build`, `npm run typecheck` and `npm run lint` work.
- Every documented public route renders directly in the browser and after refresh on a properly configured static host.
- All page content derives from JSON with typed contracts and sensible empty states; there are no HTTP requests for content.
- Donor, volunteer and contact CTAs have **real, clearly labelled destinations** or a safe disabled/fallback state; no pretend form submission.
- All logo/brand, accessibility, safeguarding and evidence requirements are met or explicitly flagged for approval.

---

# 1. Product vision and organisational identity

## 1.1 Project description

Redesign **jedidiahcares.org** into a beautiful, modern, credible and easy-to-maintain nonprofit digital platform that communicates the organisation's purpose: serving children, young people and women through education, drug abuse prevention, health and personal development education, practical community support, and empowerment. The experience must convey the warmth of a grassroots charity and the accountability expected by schools, NGOs, CSR partners, funders and institutional collaborators.

The project should modernise, **not erase**, the existing digital identity. The original homepage message is a valuable brand anchor:

> **Building the future, one person at a time.**

The founder's original vision reported in organisational records and the seed data is:

> **To produce agents of change who in turn will transform the world.**

Use the homepage line as the primary hero. Keep the vision identifiable as the formal vision (subject to leadership approval of punctuation and final wording).

## 1.2 Identity and naming

- **Public brand:** Jedidiah Cares. The submitted wordmark spells **Jedidiah Cares Global Outreach**.
- **Organisation:** Faith-inspired nonprofit working with children, youth and women.
- **Origin:** Historical records place its founding/inception in **May 2021**; confirm before publishing a timeline.
- **Registered legal entity:** Historical source documents contain inconsistent names (including variations of Jedidiah Global Care Initiative / Jedidiah Cares Global Outreach) and registration number IT-158937; verify against an up-to-date official certificate before publishing legal identity, registration metadata or issuing donation receipts.
- **Geography:** Work is documented in/around Enugu State and some historical school-support activity in Anambra and Lagos; avoid implying national operating coverage until verified.

## 1.3 Mission, values and positioning

**Formal mission, historical seed wording:** Addressing challenges facing national development by equipping children, youth and women with the necessary resources to discover and live out their potential in Christ. The historic wording requires editorial polishing and formal approval; do not silently replace a board-approved statement.

**Proposed concise public expression:**

> Inspired by Christian faith and compassion, we work with children, young people and women to overcome barriers to education, wellbeing and opportunity through practical support, prevention and empowerment.

**Brand values:** compassion, dignity, empowerment, integrity, community, faith in action, and accountability. Treat exact values as an editorial proposal until ratified.

**Suggested supporting line:** *Compassion in action. Potential in every life.* This is an optional campaign/section expression, not a substitute for the original vision or primary hero statement.

### Original thematic focus (must remain recognisable)

1. **Education** → public name: **Education & Scholarships**.
2. **Drug Abuse Prevention** → **Youth Development & Drug Abuse Prevention**.
3. **Sex Education and Empowerment** → **Girls' Health, Safety & Empowerment**; content remains appropriately broad, inclusive and age-appropriate.
4. **Youth and Women Empowerment** → **Women & Community Empowerment**, with youth development also visible in the second programme.

Preserve original thematic names in `src/data/programmes.json` alongside approved public-facing labels, so the historical organisational structure stays traceable.

## 1.4 Primary audiences and jobs-to-be-done

| Audience | User need | Site action |
|---|---|---|
| Community member, parent or beneficiary | Understand eligibility and find help or a programme contact | Browse programme → enquire |
| Individual supporter | Understand genuine work and find a trusted giving channel | Read project → follow verified external donation link or contact organisation |
| Institutional funder / CSR team | Evaluate track record, controls and partnership fit | Review impact/report → partnership enquiry |
| Volunteer / skilled professional | Find meaningful ways to contribute | Read opportunities → open email app or approved external form |
| School / community partner | Plan safe collaborative programmes | Programme details → host-a-session enquiry |
| Journalist / general visitor | Find accurate announcements, photographs and organisational facts | Stories / event archives / contact |
| Website maintainer | Keep information current without any server or CMS | Edit validated local JSON and media assets in Git |

## 1.5 Success outcomes

- Visitors understand **who**, **what**, **where** and **why** within the initial viewport and first scroll.
- Programme and project pages show context, clear status, evidence and pathways to act.
- Site provides safe, honest hand-offs for donations, volunteering and enquiries without pretending to process submissions itself.
- Partners can evaluate governance and reach the organisation through a dedicated route.
- A maintainer can update copy, statuses, media and page sections by editing local JSON without editing React components.
- Accessibility, security, responsive performance and accurate claims are verified before launch.

---


# 2. Product scope, reference context and frontend technology

## 2.1 How to use the existing GitHub repository

The private `UtyEmma/jedidiahcares` repository was reviewed to understand the organisational context and original website. It contains the original homepage line, formal vision, mission, four thematic focus areas, image/logo assets, and a set of page types (Home, About, Projects, Events, Volunteer, Gallery, Blog, Team, Donations and Contact). **This new project is NOT a migration of its Laravel application.**

**Permitted reuse:** approved words, original organisational history, the logo, editorial structure, images that have redistribution/consent clearance, legacy route slugs for visitor familiarity.  
**Do not copy:** Laravel code, controllers, payment logic, seed passwords, user tables, donor records, production content that contains private data, or existing admin dashboard.

The reference repository **does not disclose the current production database**, so JSON fixtures must be hand-curated from approved content. `src/data` is the source of truth for this new build.

## 2.2 Project structure (recommended; flexible with justification)

```text
jedidiah-cares-frontend/
├── public/
│   ├── logo.webp
│   ├── favicon.svg                    # derived only if authorised; otherwise use approved icon
│   ├── images/
│   │   ├── programmes/
│   │   ├── projects/
│   │   ├── stories/
│   │   ├── team/
│   │   └── placeholders/
│   ├── reports/                       # approved static PDF reports only
│   └── robots.txt
├── src/
│   ├── app/
│   │   ├── App.tsx
│   │   ├── router.tsx                  # React Router route objects
│   │   └── providers.tsx              # only if needed
│   ├── components/
│   │   ├── layout/                     # Header, Footer, MobileNav, SiteLayout
│   │   ├── common/                     # Container, SectionHeading, ButtonLink, Breadcrumbs
│   │   ├── editorial/                  # StoryCard, Quote, MediaFigure, CTA
│   │   ├── programmes/                 # ProgrammeCard, ProgrammeHero
│   │   ├── projects/                   # ProjectCard, StatusPill, ProjectFacts
│   │   └── interactive/                # Filters, Accordion, ContactEmailComposer
│   ├── pages/
│   │   ├── HomePage.tsx
│   │   ├── AboutPage.tsx
│   │   ├── ProgrammesPage.tsx
│   │   ├── ProgrammeDetailPage.tsx
│   │   ├── ProjectsPage.tsx
│   │   ├── ProjectDetailPage.tsx
│   │   ├── ImpactPage.tsx
│   │   ├── StoriesPage.tsx
│   │   ├── StoryDetailPage.tsx
│   │   ├── EventsPage.tsx
│   │   ├── EventDetailPage.tsx
│   │   ├── GetInvolvedPage.tsx
│   │   ├── DonationsPage.tsx
│   │   ├── VolunteersPage.tsx
│   │   ├── PartnersPage.tsx
│   │   ├── TeamPage.tsx
│   │   ├── GalleryPage.tsx
│   │   ├── ContactPage.tsx
│   │   ├── PolicyPage.tsx
│   │   └── NotFoundPage.tsx
│   ├── data/
│   │   ├── site.json
│   │   ├── navigation.json
│   │   ├── programmes.json
│   │   ├── projects.json
│   │   ├── stories.json
│   │   ├── events.json
│   │   ├── people.json
│   │   ├── impact.json
│   │   ├── gallery.json
│   │   ├── partners.json
│   │   ├── faq.json
│   │   ├── policies.json
│   │   └── redirects.json
│   ├── content/
│   │   ├── index.ts                    # single typed import/selector module
│   │   └── validators.ts               # validate publication, slugs, referential integrity
│   ├── types/
│   │   └── content.ts
│   ├── lib/
│   │   ├── seo.ts
│   │   ├── mailto.ts
│   │   └── format.ts
│   ├── styles/
│   │   ├── tokens.css
│   │   └── globals.css
│   ├── test/
│   └── main.tsx
├── index.html
├── package.json
├── tsconfig.json
├── vite.config.ts
├── README.md
└── PRD.md
```

This layout is a recommendation, not a need to implement empty folders. Prefer **clear responsibilities, maintainability and editorial JSON updates** over excess abstraction. Organise imports using `@/` if useful. No `.env` values should be needed for content. Optional environment variables may contain *public deployment URLs only*, never API keys.

## 2.3 Technology and behaviour

- Vite React/TypeScript setup, React Router `createBrowserRouter` + `<RouterProvider />` (or standard `BrowserRouter` approach). Use React Router's native `<Link>` and `<NavLink>` throughout.
- Tailwind CSS recommended for layout, utilities and responsiveness, with the logo-based CSS variables as semantic design tokens; custom CSS is allowed for editorial sections. Do **not** reuse the Bootstrap/jQuery charity theme from the legacy application.
- Lucide icons; lightweight, keyboard-accessible components. No heavy carousel/animation libraries as a prerequisite to browse content.
- Import JSON at build time (`import programmes from '@/data/programmes.json'`), centralise filtering/transformation in typed selectors, and avoid `fetch()` and runtime requests to `/api` or fake endpoints.
- Use React local state only for menu state, tabs, search, galleries and filters. Filter/search across the preloaded local data; avoid implying access to a larger database.
- All content metadata should support approval and provenance. Do not use current browser time to determine whether unverified initiatives were completed.
- Use CSS for subtle reveals, hover transitions and reduced-motion support. Provide full access with JavaScript-driven animation disabled.
- Keep bundle small, optimise images, split routes with `React.lazy` when beneficial, avoid layout shift, and use `loading="lazy"` for below-the-fold photos.

## 2.4 Deployment: static SPA only

`npm run build` must produce `dist/`, a **fully static deployable folder**. Support Netlify, Vercel, Cloudflare Pages or static Apache/Nginx hosting. Configure a history fallback so deep links (`/programmes/education`, `/stories/slug`) serve `index.html`, except actual existing assets. For Apache this may use a bundled rewrite rule; for Netlify a `_redirects` file; for Vercel a documented rewrite config. Do not add a dynamic backend to solve routing.

Document how to configure `VITE_BASE_URL` or Vite's `base` only if hosted in a subdirectory. Treat clean paths as canonical. If a target host cannot rewrite history, discuss `HashRouter` as an explicitly approved compromise; do not silently switch to hash URLs. The legacy Laravel site is not a deployment dependency.

---

# 3. Information architecture and React Router route contract

## 3.1 Navigation

**Primary:** Home · About Us · Our Programmes · Projects · Our Impact · Stories & Updates · Get Involved · Contact.

**Persistent primary action:** Support Our Work (links to `/donations`, where visitors can view verified giving details or follow an external donation link; **not a local checkout**).  
**About submenu:** Our Story, Mission & Vision, Leadership.  
**Our Programmes:** Education & Scholarships; Youth Development & Drug Abuse Prevention; Girls' Health, Safety & Empowerment; Women & Community Empowerment.  
**Get Involved:** Support, Volunteer, Partner, Host a Programme.  
**Stories & Updates:** Articles, Events, Photo Gallery.

Mobile navigation must be keyboard accessible, scrollable, respect safe-area insets and close after route changes. Footer has mission snippet, programme links, public verified contacts, legal/policy pages, and an organisation copyright (never developer attribution in the copyright).

## 3.2 Route map

| Page/route | React component | Content file | Status |
|---|---|---|---|
| `/` | `HomePage` | `site.json` + programme/project/story/event/impact JSON | Required |
| `/about` | `AboutPage` | `site.json` | Required |
| `/programmes` | `ProgrammesPage` | `programmes.json` | Required |
| `/programmes/:slug` | `ProgrammeDetailPage` | `programmes.json` | Required; four canonical slugs |
| `/projects` | `ProjectsPage` | `projects.json` | Required |
| `/projects/:slug` | `ProjectDetailPage` | `projects.json`, `stories.json`, `gallery.json` | Required |
| `/impact` | `ImpactPage` | `impact.json`, `projects.json`, static reports | Required |
| `/stories` | `StoriesPage` | `stories.json` | Required |
| `/stories/:slug` | `StoryDetailPage` | `stories.json` | Required |
| `/events` | `EventsPage` | `events.json` | Required |
| `/events/:slug` | `EventDetailPage` | `events.json` | Required |
| `/get-involved` | `GetInvolvedPage` | `site.json` CTA content | Required |
| `/donations` | `DonationsPage` | approved giving configuration | Required; no payment execution |
| `/donations/:projectSlug` | `DonationsPage` (contextual) | project giving info | Optional; no checkout |
| `/volunteers` | `VolunteersPage` | volunteering information and contact config | Required |
| `/partner` | `PartnersPage` | approved partners and outreach copy | Required |
| `/team` | `TeamPage` | `people.json` | Required if roles verified; otherwise safe editorial state |
| `/gallery` | `GalleryPage` | `gallery.json` | Required |
| `/contact` | `ContactPage` | `site.json` | Required |
| `/privacy` | `PolicyPage` | `policies.json` | Required when approved |
| `/safeguarding` | `PolicyPage` | `policies.json` | Required when approved |
| `/donation-terms` | `PolicyPage` | `policies.json` | Required when giving details active |
| `/accessibility` | `PolicyPage` | `policies.json` | Required |
| `/blog` and `/blog/:slug` | `Navigate` to new stories routes | existing names only | Client-side compatibility |
| `*` | `NotFoundPage` | `site.json` | Required |

**Important:** Client-side `<Navigate replace />` is not an HTTP 301. If permanent redirects are necessary for search indexing, separately configure them on the *static host*, without adding application servers. For legacy `/team/:id/:name`, use safe existing data slug lookup or 404, not fabricated profiles. Optional shop/cart/checkout are **out of scope**.

## 3.3 Primary user journeys

1. **Visitor:** arrive → understand mission → explore programme → relevant project/story → get involved or contact.
2. **Supporter:** arrive → read approved impact/project details → `/donations` → choose verified external giving link or contact for donation instructions. The React app never collects credit-card numbers or claims payment completion.
3. **Volunteer:** visit `/volunteers` → review opportunities and safeguarding standards → choose approved external form or open a pre-addressed email from the user’s mail app. Never show "Application received" within the site.
4. **Partner:** visit `/partner` → read opportunities → approved external enquiry link or prefilled email to organisation. No local storage or submission.
5. **Community member:** browse programme eligibility and relevant activity → use verified contact channel for enquiries. No automated promises of support.
6. **Maintainer:** update `src/data/*.json` and approved images → run validation/build → deploy static bundle. No CMS login is involved.

---

# 4. Brand identity, exact logo palette and UI tokens

## 4.1 Colour extraction and priority

The attached `logo.webp` is a transparent **458 × 101 px** wordmark. Pixel sampling of opaque logo regions shows two clear primary colours:

| Token | HEX | RGB | Origin | Use |
|---|---|---|---|---|
| **Jedidiah Navy** | **`#072266`** | 7, 34, 102 | Median opaque wordmark text colour | Primary brand, headings, buttons, navigation, links, dark sections |
| **Care Lemon** | **`#F0EB6F`** | 240, 235, 111 | Median emblem background colour | Signature warm accent, CTA emphasis, highlights, micrographic details |
| Care Teal | `#4F8788` | 79, 135, 136 | Harmonised secondary interpretation of the mixed teal/green emblem strokes | Limited illustration/accent usage only, **not** a second primary brand colour |
| Ink | `#24344A` | 36, 52, 74 | Supporting neutral (designed) | Body text |
| White | `#FFFFFF` | 255, 255, 255 | Supporting neutral | Base canvas, cards |
| Porcelain | `#FBFCF8` | 251, 252, 248 | Supporting neutral | Warm-white sections |
| Lemon Wash | `#FAF9DD` | 250, 249, 221 | Supporting tint | Light highlight areas |
| Navy Wash | `#EDF2FB` | 237, 242, 251 | Supporting tint | Programme cards / soft section backgrounds |
| Border | `#DEE5EC` | 222, 229, 236 | Supporting neutral | Outlines, form separators |
| Muted Text | `#59687C` | 89, 104, 124 | Supporting neutral | Secondary copy on white |
| Success | `#247A5B` | 36, 122, 91 | Functional UI only | Verified success/confirmation |
| Error | `#B42336` | 180, 35, 54 | Functional UI only | Errors and validation |

**Accuracy note:** `#072266` and `#F0EB6F` are pixel-derived approximations of the uploaded raster logo; antialiasing/compression creates nearby variants. Teal and all neutrals are **design interpretations**, not exact corporate-colour claims. Obtain a vector master or brand manual to confirm authoritative print/Pantone values if available.

### Accessibility / contrast

- Navy `#072266` on lemon `#F0EB6F`: approximately **11.76:1**.
- Navy on white: approximately **14.69:1**.
- Body ink `#24344A` on porcelain `#FBFCF8`: approximately **12.24:1**.
- Lemon on white: approximately **1.25:1**: **never use yellow as small body text on white**.
- Teal `#4F8788` on white: approximately **4.07:1**: do not rely on it for normal body-size text requiring 4.5:1.

**Accessible design rule:** Deep navy for text and primary solid buttons; lemon as a background with navy text/icons. White text is appropriate on navy. Never set white text on lemon. Keep colour distinct from status meaning (use icon + label + text).

## 4.2 CSS custom properties — implement as source of truth

```css
:root {
  --jc-navy: #072266;
  --jc-lemon: #F0EB6F;
  --jc-teal: #4F8788;
  --jc-ink: #24344A;
  --jc-white: #FFFFFF;
  --jc-porcelain: #FBFCF8;
  --jc-lemon-wash: #FAF9DD;
  --jc-navy-wash: #EDF2FB;
  --jc-border: #DEE5EC;
  --jc-muted: #59687C;
  --jc-success: #247A5B;
  --jc-danger: #B42336;

  --jc-bg: var(--jc-white);
  --jc-surface: var(--jc-porcelain);
  --jc-foreground: var(--jc-navy);
  --jc-body: var(--jc-ink);
  --jc-accent: var(--jc-lemon);
  --jc-focus: var(--jc-navy);
  --jc-radius-sm: 0.5rem;
  --jc-radius-md: 1rem;
  --jc-radius-lg: 1.5rem;
  --jc-radius-xl: 2rem;
  --jc-content-max: 1200px;
}
```

Keep these variables in `src/styles/tokens.css` and expose matching semantic names in the Tailwind theme (if Tailwind is selected). Do not scatter hex literals through components. A dark section uses navy background with white text, lemon CTA and a clear white/inverse-logo treatment. The original transparent navy logo is intended for a **light surface**, not placed directly on a navy footer.

## 4.3 Typography

- Heading: **Manrope** or **Plus Jakarta Sans** (preferred: Manrope, weights 600–800).
- Body: **Inter** (400–600) for readability, or a close licensed sans alternative.
- Use `font-display: swap`; self-host or use an approved privacy-aware font source.
- Never set decorative serif body type. If any contrasting expressive type is used, restrict it to approved, very small brand moments.
- Responsive display: `clamp(2.6rem, 5.5vw, 5.5rem)`; H1 per page; H2 `clamp(2rem, 4vw, 3.75rem)`; body ~16–18px with readable line-height ~1.6; no fixed font sizes that overflow 320px screens.
- Max prose measure ~65–75 characters and comfortable paragraph rhythm.

## 4.4 Art direction: **Warm clarity, tangible compassion**

The visual goal is a premium editorial nonprofit site that feels **hopeful, open, dignified and trustworthy**, not a fintech landing page, not a generic charity template and not an elaborate motion demo.

- Real community-focused photography first: learning environments, workshops, hands at work, staff and volunteers, appropriate candid group interactions.
- Source photographs from approved Jedidiah events/projects and maintain consent records; use temporary neutral abstract cards on staging rather than fake beneficiaries.
- Complement navy and lemon with generous white space, soft tints, thin separators and oversized typographic statements.
- Large, high-quality imagery in purposeful editorial crops; alternating wide/contained layouts; no universal three-card pattern in every section.
- Optional illustration motifs may borrow the care emblem's hands/people symbolism subtly, never redraw the identity into unrelated shapes.
- Subtle on-scroll reveal (120–320ms), hover shifts (100–180ms), responsive accordions and restrained transitions. All motion must support `prefers-reduced-motion` and never block access to content.
- Use natural, human-centred images; avoid exaggerated sad-child stock photography, fake impact badges or abstract CGI 3D elements.

## 4.5 Logo treatments

1. **Default:** original horizontal transparent `logo.webp` on white/porcelain, maintaining its proportions.
2. **Header desktop:** image width ~190–245px subject to actual responsive fit; height auto.
3. **Mobile:** original horizontal at a practical width or an approved compact emblem version; do not crop wordmark arbitrarily.
4. **Dark navy footer:** place original wordmark in a small white/light capsule or use an official inverse variant if supplied; no blind CSS `filter: invert()`.
5. Ensure accessible `alt="Jedidiah Cares Global Outreach"`, appropriate linked home destination and sensible fallback if image fails.

## 4.6 Key UI components / design language

| Component | Visual and functional requirements |
|---|---|
| Main nav | Spacious logo-left structure, high-contrast navy labels, prominent lemon or navy donation CTA, keyboard-accessible submenu |
| Hero | Real image or carefully layered composite, restrained navy overlay where needed, highly legible copy, two distinct CTAs |
| Programme card | Large relevant imagery, pill/eyebrow, H3, 2–3 lines of outcome-oriented text, consistent learn-more link |
| Project card | Image, real status, audience/location, brief purpose, funding info **only if fundraising**, linked details |
| Impact statistic | Large number **only from approved, cited local JSON data**, unit, measurement date, scope and report/source link |
| Story card | Image, category, publication date, human headline, accessible clickable title |
| Donation module | Approved external payment URL or trusted instructions; **no payment form, transaction state or success claim in this static frontend** |
| Partner/volunteer form | A truthful mailto or approved external form handoff; no simulated database submission or success state |
| Report card | Project link, period, publication date, document format/size, download action |
| Editorial quote | Short high-impact quote; attribution only with permission, no invented testimonials |
| CTA band | Navy background, lemon button, one simple ask, minimal visual clutter |
| Empty state | Realistic explanation and relevant next action, never shame or deceptive urgency |
| Skeleton/loading | Lightweight, ARIA-conscious; no unnecessary animated shimmer for motion-sensitive users |

## 4.7 Grid and responsive behaviour

- 12-column max-width desktop; 8-column tablet; single-column small screens; content max ~1200px, generous but proportional gutters (16px mobile, 24–32px tablet, ~40px desktop).
- Target widths: 320px, 375px, 390px, 768px, 1024px, 1280px, 1440px.
- Use semantic DOM, descriptive link text and focus-visible styles. Mobile menu cannot trap focus incorrectly or hide body content without a working escape/close.
- Images provide `width`/`height` or stable aspect ratio to avoid layout shift, responsive `srcset` when available, appropriate lazy load. Hero is priority-loaded only if truly above fold.

---


# 5. Page-by-page content and interface requirements

## 5.0 Universal page rules

Each page shall have a single meaningful H1, section headlines and concise approved copy from the content JSON; responsive hero/media; meaningful next-step links; per-route title/description/social image; keyboard operability; alt text, image credit/consent where appropriate; graceful missing-data states; and sufficient margin/contrast on 320px screens. Avoid hardcoded random counts, fake organisations and CTA destinations without usable actions. **Appendix A contains the full draft prose to convert into local JSON fields**; this section describes layout and behaviour.

A typical inner page uses a compact branded banner rather than copying the oversized homepage hero. Avoid cookie-cutter repetition: vary image/text rhythms, full-bleed photography, quote moments and feature layouts. Do not overuse sliders, gradients or crowded cards.

## 5.1 Homepage `/`

**Primary H1:** Building the future, one person at a time.  
**Layout:** cinematic, approved community image with navy overlay or photo side-panel; white/light backdrop options if no approved hero photo. Place hero copy and two CTAs: `Explore Our Work` (`/programmes`) and `Get Involved` (`/get-involved`). Underneath include:

1. Brief "Who We Are" split image/editorial description with About link.
2. Four thematic areas, each with descriptive text, purposeful image/illustration and internal link.
3. Spotlight of max 3 published projects; show statuses and location; no fake fundraising progress.
4. Evidence/impact strip showing **verified metrics only**, or editorial narrative if none verified.
5. One feature story or community testimony **only if sourced and consented**.
6. Latest published stories and upcoming events, hide gracefully if lists empty.
7. Volunteer/partner invitation and navy + lemon final CTA.

**Data:** `site.json`, `programmes.json`, `projects.json`, `stories.json`, `events.json`, `impact.json`.  
**Acceptance:** static render uses no network fetch; only `isPublished: true` entries; all CTAs have real destinations; no placeholder statistics.

## 5.2 About Us `/about`

**H1:** Care rooted in faith. Change built through action.  
**Sections:** mission, original vision, history and community roots, whom we serve, operating values, philosophy of dignity, approach to working with communities, people/leadership teaser. Optional timeline entries only for verified events, e.g. organisational beginnings in 2021 if approved.  
**Design:** navy type on porcelain background, asymmetrical storytelling with carefully captioned imagery, large pull quote, distinct mission/vision panels.
**Data:** `site.json`/`people.json`.  
**Acceptance:** organisation legal name and public history remain approval-gated; faith identity clear but welcoming.

## 5.3 Programmes index `/programmes`

**H1:** Four areas. One shared purpose.  
**Sections:** overview, four featured programmes, short description of community-driven approach, CTA for schools and community partners.  
**Design:** editorial 2-column features or purposeful alternating rows, not identical four tiles; imagery, large programme numbers and light navy tints.  
**Data:** `programmes.json`.

## 5.4 Individual programme pages `/programmes/:slug`

**Four canonical slugs** (all must exist in JSON):

- `education`: **Education & Scholarships**; scholarship support, school engagement, educational access; avoid unverified child totals.
- `youth-drug-prevention`: **Youth Development & Drug Abuse Prevention**; self-esteem, healthy decisions, peer-pressure skills; historic Budding Space idea is planned unless evidence proves delivery.
- `girls-health`: **Girls' Health, Safety & Empowerment**; menstrual/reproductive wellbeing education, agency, safeguarding; 2026 100-girl programme is a **proposal**, never counted as completed impact.
- `community-empowerment`: **Women & Community Empowerment**; practical family support and longer-term opportunity; distinguish recorded relief from future plans.

**Common template:** programme hero and context, why it matters, intended interventions, proven outcomes only, related verified projects/stories, FAQs, contact/partner CTA. The illustrations/photographs should vary meaningfully per programme.  
**Data:** single matching `programmes.json` record and related published projects/stories.  
**Acceptance:** unknown slug -> 404; planned projects carry visibly distinct status.

## 5.5 Projects listing `/projects`

**H1:** See the work. Understand the purpose. Support the progress.  
**Features:** responsive card grid; client-side filtering by programme, state (planned/active/completed), and optional location; optional client-side search; sort newest or relevant; clear empty state.  
**Card:** title, location/period when known, purpose, approved cover photo, honest state. Optional verified fundraising target only if a real link exists.  
**Data:** `projects.json`.  
**Acceptance:** filters work from bundled JSON, no API calls or fabricated records.

## 5.6 Project detail `/projects/:slug`

**H1:** published project title.  
**Sections:** context and target community, timeline, status, intended activities, confirmed results (only where verified), approved photos, related updates, approved report downloads under `/public/reports`, contribution/partnership CTA.  
**Design:** big editorial image, supporting slim facts column, well-spaced narrative and optional image strip.  
**Data:** `projects.json` plus media, reports and relationship ids.  
**Acceptance:** unknown/unpublished slug -> 404. Donation CTA only if approved donation URL/verified giving instructions supplied; **never display client-created transaction status**.

## 5.7 Impact `/impact`

**H1:** Real work. Meaningful progress. Shared responsibility.  
**Sections:** verified source-attributed metrics, delivered projects, documented historical activities, links to approved static reports, qualitative explanation of what impact means.  
**Design:** white canvas; one navy evidence feature, refined typographic values and project reports; no big arbitrary counters.  
**Data:** `impact.json` with each metric gated by `verified` and `isPublished`.  
**Acceptance:** no verified numbers -> present text, linked documented stories and editorial "Further reports will be shared as available" message. Never substitute targets for achieved results.

## 5.8 Stories `/stories` and story detail `/stories/:slug`

**H1 listing:** Stories of compassion, courage and community.  
**Features:** editorial image-led cards, category and date filters from local JSON, simple client-side keyword search, related items. Detail template: cover, editorial attribution when approved, accurate date, safe article body, optional project/programme context, social sharing using current page URL.  
**Data:** `stories.json`. Prefer paragraphs/blocks as arrays in JSON; if rich HTML strings are used, sanitize at build/import time or render as escaped text, not unsafe arbitrary injection.  
**Acceptance:** published-only rendering; safe sensitive storytelling and photo consent; no broken links or false interview attribution.

## 5.9 Events `/events`, `/events/:slug`

**H1 listing:** Where care becomes community action.  
**Sections:** upcoming and past filters derived from actual event dates, approved event cards, event detail with time, place and activity overview, past event outcomes only if verified, optional links to external verified registration services.  
**Data:** `events.json`.  
**Acceptance:** past events never show active site-side registration form; no local form processing. No events -> inviting accurate empty state.

## 5.10 Get Involved `/get-involved`

**H1:** Your compassion can become someone else's opportunity.  
**Sections:** three visually differentiated pathways—Support, Volunteer, Partner; optional Host a Session subsection and practical ways to help; closing genuine CTA.  
**Actions:** internal links to `/donations`, `/volunteers` and `/partner`.  
**Acceptance:** all cards lead to useful finished pages rather than empty lead forms.

## 5.11 Support Our Work `/donations`, optional `/donations/:projectSlug`

**H1:** Your support can create meaningful opportunities.  
**Not a checkout.** Present the programme mission, why donations matter, permitted causes, and one of the following **configuration-driven** actions:

1. When an organisation-approved, working third-party donation URL is in `site.json`, link externally with a clear `Continue to secure donation provider` label; show provider name only if verified.
2. When officially approved bank-transfer information is supplied in JSON, present account name/bank/account number with safe copy controls and explanatory instructions. **Never invent payment details.**
3. When neither is approved, show `Contact us about supporting our work` using confirmed organisation email; suppress donate/pay buttons that imply checkout.

No card inputs, payment SDK, transaction status, fake fundraising progress animation, fake confirmation, simulated receipt or `/donations/success` route. Project contextual page can show purpose without pretending to allocate payment automatically.  
**Acceptance:** no payment personally identifiable data collected locally; no fake checkout; missing config produces an honest fallback.

## 5.12 Volunteer `/volunteers`

**H1:** Your time and talents can help build brighter futures.  
**Sections:** role examples, time commitment and expectations, safeguarding/orientation, volunteer stories (if verified), steps to express interest.  
**Action:** prominent verified `mailto:` handoff to organisation or approved external form. A local optional composer may collect name, interests, location and message **in memory only** to create prefilled email; it must label the action `Open email app to send`, not `Submit application`. No photo upload.  
**Acceptance:** if browser has no email handler, show copyable official address; never show "application received" on-site.

## 5.13 Partner `/partner`

**H1:** Stronger communities begin with meaningful partnerships.  
**Sections:** partnership options (schools/CSR/NGOs/professionals), principles, verified collaborators and projects, practical steps.  
**Action:** `Email us to discuss partnership` with prefilled subject/body, or officially approved external form URL. No fake success notification.  
**Data:** `partners.json` and public email from `site.json`.

## 5.14 Team `/team`

**H1:** The people behind the mission.  
**Sections:** trustees/leadership and team only where current roles and portrait permissions confirmed. Optional short staff profiles and volunteer highlights.  
**Data:** `people.json`.  
**Acceptance:** no personal/private seed contact details; historical roles are not automatically current. Empty state avoids fabricated people.

## 5.15 Gallery `/gallery`

**H1:** Moments of care, learning and community.  
**Features:** approved photographic collections, keyboard-accessible lightbox or in-page expanded view, captions, credit, link to related activity.  
**Data:** `gallery.json` and bundled assets.  
**Acceptance:** missing pictures do not break cards; children/participants are shown only with appropriate permissions.

## 5.16 Contact `/contact`

**H1:** Let's make meaningful change together.  
**Sections:** verified contact email/phone/address, enquiry types, business/organisation contact info, concise response guidance. A simple **client-only email composer** may have name, reply email, subject and message, then create a `mailto:` link. Provide direct click-to-email and click-to-call actions as fallbacks. **No fake "message sent" acknowledgment.**  
**Design:** spacious two-column layout; no map until real address verified. Never re-use the historical New York map embed.  
**Acceptance:** address/name links grounded in `site.json`; safe URL encoding, field validation before opening mail client; user's email app performs actual sending.

## 5.17 Supporting pages `/privacy`, `/safeguarding`, `/donation-terms`, `/accessibility`

Use one reusable `PolicyPage` template and `policies.json` records, with visible last-reviewed date **only if genuinely approved**. Do not invent legal policies. Include an approval-pending template on staging; before public release, get approved texts for any linked live policy. If only `mailto:` is used, privacy copy must truthfully describe the external email-client handoff and any optional analytics.  
**Acceptance:** no unapproved statement of compliance, no policy claims copied blindly from other organisations.

## 5.18 Not found and compatibility routes

Create a warm, accessible branded 404 with links to home/programmes. Support `/blog` → `/stories` and `/blog/:slug` → `/stories/:slug` with client routes and host redirects when available. Do **not** include `/shop`, `/cart`, `/order`, admin or account/login routes; they belong to the historical application, not this project.

---


# 6. Local JSON content model and editorial workflow

## 6.1 Data principle

**All editable site content lives in `src/data/*.json`**. TypeScript components import those files through `src/content/index.ts`; no runtime `fetch`, CMS or database. The JSON is user-maintained source code. Changes go through a Git commit and static redeploy. Use optional `zod` or custom static validation executed as part of CI, if valuable.

Separate content data from presentation logic. Do not repeat cards, titles or copy inside JSX when they belong in JSON. Built-in defaults may exist for essential empty states and the 404 screen. Use ISO dates; ISO date format does not imply that an article was actually published on that date. Never set or infer publication approval from the current date.

## 6.2 Core JSON files and purposes

| File | Data categories | Primary consumers |
|---|---|---|
| `site.json` | identity, hero, homepage section copy, verified public contacts, social URLs, CTAs, donation channel configuration | global shell, home, About, Contact, Donations |
| `navigation.json` | nav sections, child links, footer groups | Header/Footer |
| `programmes.json` | four canonical programmes, original thematic name, slug, content blocks, images, FAQs, related IDs | programmes index/detail |
| `projects.json` | approved activities/campaigns, dates, location, lifecycle state, outcome blocks, reports, links | home, projects, impact |
| `stories.json` | editorial articles, story category, image, alt, content blocks, publish date, linked programme/project | home, listing/detail |
| `events.json` | event details, dates/timezone, location, related project, external registration URL if approved | home, events |
| `people.json` | public staff/trustees names, approved roles, bios, image/consent | About and Team |
| `impact.json` | published, verified metrics with source/period; verified case studies and approved reports | home, Impact |
| `gallery.json` | photo collection, image path, safe caption, consent, related IDs | gallery, projects/stories |
| `partners.json` | partners approved for display, external URLs, logo media rights | partner page |
| `faq.json` | cross-cutting FAQ records and related topic IDs | programmes/get involved |
| `policies.json` | privacy, safeguarding, donation terms, accessibility copy and approval state | policy routes |
| `redirects.json` | legacy URL → local clean route mappings for client compatibility | router/static deploy configuration |

## 6.3 TypeScript contracts (minimal conceptual source)

```ts
export type PublicationState = 'draft' | 'approved' | 'archived';
export type ProjectStage = 'planned' | 'active' | 'completed';
export type ContentBlock =
  | { type: 'paragraph'; text: string }
  | { type: 'heading'; text: string; level: 2 | 3 }
  | { type: 'bullets'; items: string[] }
  | { type: 'quote'; text: string; attribution?: string };

export interface BaseEntry {
  id: string;
  slug: string;
  publicationState: PublicationState;
  isPublished: boolean;
  title: string;
  summary: string;
  coverImage?: string;
  coverAlt?: string;
}

export interface Programme extends BaseEntry {
  originalThematicName: string;
  heroTitle?: string;
  introduction: string;
  sections: { heading: string; blocks: ContentBlock[] }[];
  relatedProjectIds: string[];
}

export interface Project extends BaseEntry {
  programmeIds: string[];
  stage: ProjectStage;
  location?: string;
  startDate?: string;
  endDate?: string;
  intendedActivities: string[];
  verifiedOutcomes?: { description: string; source?: string }[];
  reportPaths?: string[];
  approvedDonationUrl?: string;
}

export interface ImpactMetric {
  id: string;
  label: string;
  value: number;
  unit?: string;
  period: string;
  source: string;
  verified: boolean;
  isPublished: boolean;
}
```

Create additional `Story`, `Event`, `Person`, `GalleryCollection` and `Policy` interfaces reflecting their JSON. **Data should be cast/validated intentionally**, not blindly rendered as `any`.

## 6.4 Example `site.json` (safe launch defaults)

```json
{
  "brand": {
    "publicName": "Jedidiah Cares",
    "wordmarkName": "Jedidiah Cares Global Outreach",
    "primaryHero": "Building the future, one person at a time.",
    "tagline": "Compassion in action. Potential in every life.",
    "logoPath": "/logo.webp"
  },
  "contact": {
    "email": "",
    "phone": "",
    "address": "",
    "locationVerified": false
  },
  "giving": {
    "enabled": false,
    "externalDonationUrl": "",
    "providerName": "",
    "bankTransfer": null
  },
  "volunteering": {
    "externalInterestFormUrl": ""
  },
  "partnership": {
    "externalEnquiryFormUrl": ""
  },
  "social": {
    "facebook": "",
    "instagram": "",
    "linkedin": "",
    "x": ""
  }
}
```

Empty values are intentional pending verification. Do **not** render empty contact details as invented alternatives; show a suitable notice or hide the direct action. The real organisation email must be approved before enabling email composers.

## 6.5 Example `programmes.json` entry

```json
[
  {
    "id": "education",
    "slug": "education",
    "title": "Education & Scholarships",
    "originalThematicName": "Education",
    "summary": "Supporting opportunities to learn and thrive.",
    "introduction": "We work to reduce barriers to education and help children and young people pursue their potential.",
    "coverImage": "/images/programmes/education.webp",
    "coverAlt": "Approved photograph of a community education activity",
    "publicationState": "approved",
    "isPublished": true,
    "sections": [],
    "relatedProjectIds": []
  }
]
```

This is a **schema example**, not a claim that the image asset exists. Use a graceful abstract background until a consent-cleared photo is supplied. All four approved programmes must be represented.

## 6.6 Publishing safety and validation

Implement one shared `publicEntries()` helper or typed equivalent to include `publicationState === 'approved' && isPublished === true`. Project `stage === 'planned'` can be public, but must show a `Planned` label. No `verified === false` impact metric may render anywhere. Unknown IDs, slugs and missing media must not crash pages. Cross-references (project → programme, story → project, asset path → file) should be audited with a validation script.

Use the following fact classification in JSON where relevant: `documented_history`, `confirmed_activity`, `planned_initiative`, `unverified`. The 2026 draft **100-girl** grant programme is `planned_initiative`; the proposed **₦3,900,000** budget is **not income raised**. Do not state an initiative happened simply because its date has passed.

Do not put private donor identities, unpublished minors' personal data, home addresses or organisational credentials into public JSON. All files in `src/data` will be bundled and readable by any visitor.

## 6.7 Content authoring workflow (no admin/CMS)

1. Maintainer edits the correct JSON file and adds approved asset(s) under `public/images` or `public/reports`.
2. Mark item `isPublished: true` and `publicationState: "approved"` only after written editorial and photo consent approval.
3. Run `npm run validate:content`, `npm run typecheck`, `npm run test` and `npm run build`.
4. Preview locally and check routes, text, links and mobile layouts.
5. Commit/review and deploy regenerated `dist/`.

Codex should implement content scripts only if they run locally at build/check time. **Do not create an admin panel to edit JSON.**

---

# 7. Frontend functional specification

## 7.1 Application routes and navigation

- React Router nested public layout; not-found fallback; scroll to top when route changes (with hash-anchor support).
- Main/header navigation, appropriate active styling via `NavLink`, accessible dropdown and mobile drawer.
- Breadcrumbs on inner pages, programme/project/story internal cross-linking, desktop and mobile accessible share/copy actions.
- Internal links should not reload the full page except when a real document download or external destination is intentionally used.

## 7.2 Client-side discovery

- Projects: local JSON filter by stage/programme and search by title/summary; reset filters, empty-state and visible result counts.
- Stories: local category/date and search controls; stable sorting.
- Events: upcoming/past derived from valid local dates with user-visible timezone.
- Gallery: client-side lightbox, keyboard closing and focus restoration (or native semantic expansion if library unavailable).
- If the JSON list is empty, no placeholder fake entries are inserted just to fill the visual layout.

## 7.3 Forms and outbound actions — no backend

**Preferred:** direct contact links with an optional in-browser email composer (`mailto:`). This does not send messages: it opens the user's default mail application, where they must press Send. The UI wording must be `Open email app` or `Compose email`, **not** `Submit`, `Send Message` or `We have received your request`.

Optional composer fields: name, reply email, topic, message. Validate basic input locally and build an encoded email with official destination loaded from verified `site.json`. Never store submitted values; no `localStorage`, analytics payload, auto-save, backend endpoint or silent third-party forwarding. Provide a `Copy email address` fallback for devices without configured mail clients. Don't use `window.alert('Success')`.

**Third-party forms:** Show an external link **only if the organisation supplies and approves the actual URL**; disclose that a third party will handle their input. Never fabricate external links.

**Donations:** Verified external donation URL or approved bank transfer info; no client collection of personal/payment details, payment status, receipts or transaction calls. If there is no approved giving channel, visitors can be invited to contact the organisation. A project page may link to `/donations/:projectSlug` for context, but must not imply automatic earmarking unless officially supported externally.

## 7.4 Content integrity and graceful states

- Only approved, public records render; use content provenance and approved image alt/captions.
- Unknown slug → 404. Empty list → intentional editorial state with relevant CTA. Missing image → tasteful aspect-ratio fallback without a generic fake beneficiary portrait.
- **No login/signup, admin dashboard, donations ledger, order checkout, user profile, server uploads, user comments, email subscription form or form submission.**

---


# 8. SEO, accessibility, privacy, responsiveness and performance

## 8.1 Accessibility requirements

Target WCAG 2.2 AA: semantic headings and landmarks, keyboard-visible focus, menus controllable by keyboard, correct button/link usage, alt text for meaningful images, form labels, 44px touch targets where practical, robust contrast, no motion dependence, reduced-motion styles, lightbox focus behaviour, skip-to-content link, and `aria-current` navigation indication. Do not use lemon-coloured text on white.

## 8.2 SEO for a client-side React Router SPA

- Update `document.title` and page-specific meta descriptions on navigation; static `index.html` must provide reasonable site-wide fallback metadata and social preview image.
- Provide real `<link rel="canonical">` values based on clean production paths; do not create misleading metadata for draft pages.
- Provide a **build-time-generated** `sitemap.xml` using **approved public routes and slugs from local JSON**, and `robots.txt` pointing to it. This is a Node/build script, not a server/API.
- Static SPAs may be less reliably indexed than prerendered HTML. For launch-critical organic search, treat **optional build-time prerendering/SSG as a follow-up decision**, not permission to introduce a backend or SSR server; keep React Router. If prerendering is not implemented, document the SEO limitation honestly.
- Ensure headings and link text describe actual programmes; include `Organization` and `Article` structured data **only with accurate, approved public fields**. Do not invent founding or corporate data.

## 8.3 Visual performance

- Use locally bundled optimised AVIF/WebP where possible, responsive source sizes, preload/priority for only the main hero/logo, lazy image loading, font-display swap, lazy route imports where helpful, and stable image aspect ratios.
- No enormous bundles for simple carousels, map embeds, animations or filters. Don't add video autoplay to important pages. Prefer icons/vector motifs and actual media.
- Target Lighthouse ≥90 Performance on key representative mobile pages under reasonable conditions, ≥95 Accessibility, with regressions documented. Treat scores as goals not untested claims.

## 8.4 Privacy and safeguarding

- Static JSON is public: store only publicly approved information; no private emails or phone numbers of staff or minors, no donor lists, contact submission contents or private grant drafts.
- Images with children require documented consent and suitable contextual captions; no health disclosures, identifying sensitive details or publication of personal vulnerabilities.
- No analytics SDK, tracking cookies or third-party embeds unless explicitly approved. Default to a privacy-respecting static site and necessary browser features only.
- If approved external donation or enquiry forms are used, label the handoff; third-party privacy practices and policy copy must be reviewed.

## 8.5 Responsive targets

Test viewport widths **320, 375, 390, 768, 1024, 1280 and 1440 px**; include touch and keyboard navigation, landscape orientation and reduced-motion mode. Ensure hero typography does not overflow and sticky CTAs do not cover content.

---

# 9. Tests, scripts, QA and static launch

## 9.1 Required package scripts

```json
{
  "scripts": {
    "dev": "vite",
    "build": "tsc -b && vite build",
    "preview": "vite preview",
    "typecheck": "tsc --noEmit",
    "lint": "eslint .",
    "test": "vitest run",
    "validate:content": "node scripts/validate-content.mjs"
  }
}
```

Adjust `tsc -b`/`tsc --noEmit` to whichever TypeScript config Vite scaffolds so both commands work; do not leave inconsistent scripts. Add validation and sitemap generation to build workflow if implemented. Use Playwright or an equivalent browser-smoke approach for key journeys.

## 9.2 Automated test matrix

| Test | Scenario | Required outcome |
|---|---|---|
| Routing | Navigate `/`, `/about`, `/programmes/education`, `/projects`, `/stories`, `/contact` | Correct public page, active nav, title and scroll reset |
| Deep links | Directly reload `/programmes/education` via configured static host | SPA renders; no 404 from hosting |
| Unknown slugs | Visit `/programmes/not-real` or unpublished project | Branded 404; not leaked draft |
| Approval | `isPublished: false` or `publicationState: draft` | Item absent from lists and detail routes |
| Evidence | Unverified metric in JSON | Not rendered in Home or Impact |
| Search/filter | Filter project stage and programme, clear filters | Deterministic results; accurate empty state |
| Donation config | No external giving URL | No clickable checkout; honest contact fallback |
| Donation config | Approved giving URL supplied | Visible labelled outbound link with safe URL handling |
| Contact | Compose email with valid fields | Correct percent-encoded `mailto:` link; no "Sent" toast |
| Contact | Missing organisation email | Composer disabled and honest contact-unavailable state |
| Images | Missing optional image | Styled fallback, descriptive text, no broken image icon |
| Mobile nav | Open, keyboard navigate, close/Escape, change route | Focus behaviour and no obscured content |
| Accessibility | axe smoke / keyboard checks on core pages | No serious/critical failures |
| Performance | Home, programme, project, story detail | Optimised images, stable layout; record Lighthouse |
| Build | All JSON present, valid, slugs unique and relationships resolvable | Passing typecheck, validation, lint and build |

## 9.3 Static deployment checklist

- [ ] All public copy and photographs approved; no fabricated projects, counts, partners or people.
- [ ] Original logo copied accurately, CSS tokens used; hero/footer have legible contrast.
- [ ] Local JSON slugs, URLs and links validated; no network dependency for content.
- [ ] Donation/volunteer/partner/contact CTAs honestly reflect approved functionality.
- [ ] Responsive layouts, keyboard navigation and reduced motion tested.
- [ ] `npm run validate:content`, `npm run typecheck`, `npm run lint`, `npm run test`, `npm run build` executed successfully or exact failures reported.
- [ ] Static host rewrite configured for React Router deep-link refresh.
- [ ] Sitemap and fallback metadata valid for approved published pages.
- [ ] No PHP, Inertia, database, admin panel, secret key or payment SDK in build.
- [ ] Built `dist/` previewed on target static hosting and deployment/rollback explained.

---

# 10. Codex implementation phases / working backlog

## Phase 0 — Audit inputs and initialise (must complete first)

- [ ] Confirm clean work directory/new project; don't modify old Laravel repository in place.
- [ ] Copy `logo.webp` and `brand-tokens.css` into new project assets/styles.
- [ ] Audit source material for approved facts, past activities and proposed initiatives. List missing images/contact/giving/policy approvals.
- [ ] Initialise Vite React TypeScript, React Router, styling system, aliases, ESLint, Vitest; run baseline build.
- [ ] Establish typed JSON schemas and content loader; seed four programmes and draft copy from Appendix A.

## Phase 1 — Visual system and shell

- [ ] Create navy/lemon semantic tokens, fonts and consistent spacing/radius/shadows.
- [ ] Implement SiteLayout/Header/Footer/mobile navigation, CTA buttons, status pills and SEO helper.
- [ ] Set media container shapes, hero composition and accessible UI primitives; support reduced motion.
- [ ] Build responsive 320–1440px shell and inspect actual rendered views.

## Phase 2 — Publish core pages

- [ ] Home, About, Programmes index and four programme pages.
- [ ] Projects index and detail, Impact, Stories index and detail, Events index and detail.
- [ ] Get Involved, Support, Volunteer, Partner, Team, Gallery, Contact, policy templates and 404.
- [ ] Connect cross-links by approved JSON IDs/slugs; robust empty states and accurate page metadata.

## Phase 3 — Client-only interactions and editorial refinement

- [ ] Search/filter/accordion/lightbox, local in-memory email composer, copy email/address actions.
- [ ] Implement approved external giving/contact links only; no checkout or mock submission.
- [ ] Curate image choices and captions, validate consent-sensitive material, replace placeholders when approved.
- [ ] Verify programme statuses and report links; no decorative impact metrics.

## Phase 4 — QA, SEO and handover

- [ ] Static sitemap generator, robots, title/meta handling, redirects and history fallback docs.
- [ ] Typecheck/lint/unit/browser checks, a11y and performance testing, mobile visual QA.
- [ ] Build production `dist/`, document hosting and maintenance, produce screenshots and completion report.
- [ ] Get editorial approval before public publishing; do not deploy automatically.

## Deferred / explicit exclusions

No Laravel/PHP, Inertia, admin CMS, authentication, server routes, REST API, donor profiles, payment verification, Flutterwave, user management, event registration database, actual email sending, ecommerce checkout, or app-based analytics/tracking without a separately approved requirement.

---

# 11. Acceptance criteria and scope guardrails

| ID | Acceptance criterion | Evidence |
|---|---|---|
| FE-01 | Fresh React/Vite/TypeScript project; React Router active | Repo dependencies, running dev server |
| FE-02 | No server, Laravel, API, database or CMS dependencies | Source tree, `package.json`, bundle review |
| FE-03 | Logo-derived navy/lemon applied consistently | CSS variables and key-page screenshots |
| FE-04 | All essential page types/routes implemented | Automated route smoke tests |
| FE-05 | Four original programme themes correctly represented | `programmes.json`, four detail pages |
| FE-06 | Content maintained in typed, valid JSON files | Content loader, validation script |
| FE-07 | Unapproved and unverified metrics/records hidden | Selector tests |
| FE-08 | Static filters/search work locally without a request | Interaction tests |
| FE-09 | External donation actions only; no payment simulation | Giving component tests |
| FE-10 | Contact/volunteer/partner handoff uses real mail app or approved link; no fake success | Component/QA tests |
| FE-11 | Accessible semantic/mobile UI, respecting reduced motion | Keyboard/axe checks |
| FE-12 | Correct page titles and host-configured deep links | Browser tests/hosting docs |
| FE-13 | Real media only if approved, no fabricated impact/partner logos | Editorial sign-off checklist |
| FE-14 | `npm run build` produces deployable static `dist` | Build report |
| FE-15 | No shop/cart/admin routes unless future scope approved | Router inspection |
| FE-16 | Change instructions explain JSON content updates and static redeploy | README |

**Global acceptance condition:** The new project must remain **100% frontend-only**. Any request to introduce an API, backend, database, CMS, payment processing, authentication or real form delivery is a **separate change request**. Do not build one speculatively.

---

# 12. Editorial facts and approvals

| Item | What is known / proposed | Publishing rule |
|---|---|---|
| Public brand | Jedidiah Cares; logo wordmark: Jedidiah Cares Global Outreach | Use as displayed; verify formal legal name independently |
| Incorporation number | Historical materials mention IT-158937 | Confirm certificate before footer/legal display |
| Founding | May 2021 in source materials | Confirm before historical timeline |
| Mission and vision | Original official/historical copy and optional polished website expression | Leadership review required |
| Current leadership | Old repository seeds include historic people/roles | Confirm up-to-date role and consent |
| Public contacts | Old documents / repo not sufficient proof of current contact details | Leave JSON empty until verified; never invent |
| Donations | Old repo had Flutterwave integration | **Not part of this new frontend**. Need approved external giving URL or bank account info |
| Girls' health initiative | 2026 draft targeted 100 girls, projected ₦3.9m | Label **proposed**, not delivered or funded |
| Scholarship and food assistance | Supported by historical documents | Verify amount, period, photographs before numeric public claims |
| The Budding Space | Historic plan | Label planned; don't imply delivered |
| 2026 outreach collaboration | External reference exists | Check role, date and photo permission before claiming |
| Photos/videos | Mixed old asset folder, permissions not uniformly known | Show only approved authentic imagery; safe fallback otherwise |
| Faith language | Christian-inspired purpose | Preserve respectfully, remain welcoming to everyone |
| Policies | Full approved legal/safeguarding texts not supplied | Stage draft; obtain approval before public use |

---

# Appendix A — Proposed page-by-page editorial content source

The following page copy has been adapted into Markdown from the previously prepared Word website brief. It is included so that Codex has a **single working source** for both functionality and copy. All text remains **draft pending approval**. Use it to populate editable local JSON strings and the React pages. The existing repository is a **research reference only**, not a runtime dependency. Refer to §5 for design layouts, JSON fields, routes and acceptance criteria.


## A.01 Homepage

*URL: /  |  Primary audience: All visitors*

**Hero Headline**

Building the future, one person at a time.

**Hero Supporting Copy**

Every child, young person and woman deserves the opportunity to thrive. Through education, practical assistance, prevention and empowerment, Jedidiah Cares works with communities to help people discover their potential and build more hopeful futures.

**Introduction / Who We Are**

Rooted in faith. Guided by compassion. Committed to action. We believe care becomes meaningful when it meets real needs and opens doors to opportunity. That is why we work alongside underserved communities to support learning, wellbeing, confidence and self-reliance.

**Programme Section**

Four areas. One shared purpose. From scholarships and youth development to girls’ health and women’s empowerment, our programmes respond to challenges that shape individual lives and community futures.

**Impact Section**

Progress is made one opportunity at a time. Explore the outreach activities, education support and partnerships that reflect our commitment to practical, community-centred change.

**Stories Section**

Every intervention has a story. Meet the people, volunteers and partners whose experiences reveal what compassion in action can look like.

**Final Invitation**

Be part of the change. Your time, partnership and support can help more children, young people and women access opportunities to thrive.

**Publishing note:** Draft copy. Store approved strings in local JSON; use §5 for the page layout, JSON references, CTAs and acceptance criteria. Publish factual claims and photographs only after approval.

## A.02 About Jedidiah Cares

*URL: /about  |  Primary audience: Visitors, partners, prospective staff and volunteers*

**Page Opener**

Care rooted in faith. Change built through action.

**Founding Narrative**

Jedidiah Cares began with a conviction that people should not be defined by the challenges they face. Through community assistance and education support, the organisation makes compassion tangible while investing in people’s future potential.

**Who We Serve**

We work with children, young people and women facing barriers to education, information and opportunity.

**Our Vision**

To produce agents of change who in turn will transform the world.

**Our Mission — Proposed Web Reading**

Inspired by Christian faith and compassion, we respond to the challenges facing children, young people and women through education, prevention, practical support and empowerment opportunities that enable them to realise their potential and contribute to their communities.

**Our Values**

Compassion, dignity, empowerment, integrity and community guide how we respond, collaborate and report our work.

**Our Approach**

We listen, collaborate, act and learn alongside communities.

**Publishing note:** Draft copy. Store approved strings in local JSON; use §5 for the page layout, JSON references, CTAs and acceptance criteria. Publish factual claims and photographs only after approval.

## A.03 Our Programmes

*URL: /programmes  |  Primary audience: Community members, schools, funders and volunteers*

**Hero**

Creating opportunities where they matter most.

**Intro**

Every challenge has a human story. Our four programme areas are designed to address barriers that affect learning, healthy decisions, personal dignity and community wellbeing. We combine immediate support with learning, practical tools and partnerships that help people move forward.

**Closing Invitation**

Do you work with a school, community group or institution facing one of these challenges? We welcome opportunities to listen, learn and collaborate on practical solutions.

**Publishing note:** Draft copy. Store approved strings in local JSON; use §5 for the page layout, JSON references, CTAs and acceptance criteria. Publish factual claims and photographs only after approval.

## A.04 Education & Scholarships

*URL: /programmes/education  |  Primary audience: Families, schools, sponsors*

**Headline**

Education opens doors. Opportunity helps children walk through them.

**Overview**

Access to education can change the direction of a child’s life, yet financial pressure and unequal opportunities can make school participation difficult. Jedidiah Cares seeks to support children with educational assistance, scholarships and community partnerships that help them continue learning and pursuing their aspirations.

**Our Approach**

We identify needs through trusted school and community relationships; consider available resources and eligibility; and provide practical support designed to ease educational barriers. We aim to nurture confidence and a love of learning, not simply cover immediate costs.

**Evidence And Stories**

Historical organisational records describe scholarships supporting one child in Lagos State and seven children in Ogboji, Anambra State. These should be documented as past interventions after confirming dates, details and publication permissions.

**How To Participate**

Individuals, schools and sponsors can explore education-focused campaigns, contribute learning resources or enquire about collaborative support.

**Publishing note:** Draft copy. Store approved strings in local JSON; use §5 for the page layout, JSON references, CTAs and acceptance criteria. Publish factual claims and photographs only after approval.

## A.05 Youth Development & Drug Abuse Prevention

*URL: /programmes/youth-drug-prevention  |  Primary audience: Young people, teachers, youth organisations*

**Headline**

Helping young people grow with confidence and purpose.

**Overview**

Young people need safe spaces to ask questions, develop confidence and make informed choices. Our youth development and drug abuse prevention work focuses on self-awareness, positive identity, accurate information and the life skills that help young people navigate pressure and uncertainty.

**The Budding Space**

The Budding Space is an initiative described in the organisation’s historical plans. Its concept combines conversations about self-esteem, resilience, peer influence and the risks associated with substance use. Before presenting it as an active programme, its current format and delivery history should be confirmed.

**What Participants Gain**

Opportunities to learn, reflect, ask informed questions, understand risks, strengthen decision-making and find supportive relationships.

**Community Partnership**

We welcome schools, community groups and appropriate practitioners who can help deliver thoughtful, age-appropriate preventive education.

**Publishing note:** Draft copy. Store approved strings in local JSON; use §5 for the page layout, JSON references, CTAs and acceptance criteria. Publish factual claims and photographs only after approval.

## A.06 Girls’ Health, Safety & Empowerment

*URL: /programmes/girls-health  |  Primary audience: Girls, caregivers, schools and funders*

**Headline**

Every girl deserves dignity, knowledge and the confidence to thrive.

**Overview**

Girls in underserved communities can face barriers to reliable menstrual and reproductive health information, safe choices and affordable hygiene materials. Jedidiah Cares works toward practical, age-appropriate responses that support health awareness, personal dignity, confidence and self-protection.

**What A Session May Include**

Menstrual health education; puberty and body awareness; personal safety and consent; confidence-building conversations; practical reusable pad-making skills when appropriate; and distribution of relevant support materials.

**A Proposed Project**

A 2026 grant proposal sets out a six-week empowerment intervention targeting 100 girls in rural Enugu State. Until implementation is confirmed, the website must label this as a proposed initiative and should not report the intended participants as beneficiaries reached.

**Our Commitment**

Sessions should be age-appropriate, evidence-informed, culturally respectful and delivered with safeguarding safeguards and appropriate professional input.

**Publishing note:** Draft copy. Store approved strings in local JSON; use §5 for the page layout, JSON references, CTAs and acceptance criteria. Publish factual claims and photographs only after approval.

## A.07 Women & Community Empowerment

*URL: /programmes/community-empowerment  |  Primary audience: Women, families, community leaders and partners*

**Headline**

Standing with communities. Strengthening what is possible.

**Overview**

Strong communities grow when women and families have access to support, practical resources and opportunities to develop their own capacity. Jedidiah Cares responds to immediate community needs while seeking pathways to stronger wellbeing, dignity and resilience.

**Our Work**

Our organisational records describe food support for women in Enugu North and recurring seasonal assistance for households in Enugu. These activities provide an important foundation for future skill-building and community partnerships.

**How We Work**

We listen to the communities we serve, identify practical gaps, collaborate with local partners and seek support that responds to immediate needs without losing sight of longer-term opportunity.

**Join The Effort**

Community partners, volunteers and supporters can contribute knowledge, materials, services and funding to carefully defined interventions.

**Publishing note:** Draft copy. Store approved strings in local JSON; use §5 for the page layout, JSON references, CTAs and acceptance criteria. Publish factual claims and photographs only after approval.

## A.08 Projects & Campaigns

*URL: /projects  |  Primary audience: Donors, partners, general visitors*

**Headline**

See the work. Understand the purpose. Support the progress.

**Intro**

Our projects bring specific community needs into focus. Some support immediate assistance, while others create opportunities through education, prevention and practical skills. Each project has a defined purpose, a clear stage and updates that help supporters follow its progress.

**Status Labels**

Proposed • Fundraising • Planned • Ongoing • Completed • Archived. Completed projects should lead with results rather than a “Donate Now” button.

**Archive Invitation**

Explore the projects we have undertaken, the communities involved and the lessons shaping the next steps of our work.

**Publishing note:** Draft copy. Store approved strings in local JSON; use §5 for the page layout, JSON references, CTAs and acceptance criteria. Publish factual claims and photographs only after approval.

## A.09 Project Detail / Campaign Landing

*URL: /projects/{slug}  |  Primary audience: Donors, institutions, community members*

**Project Headline Example**

Supporting Girls’ Health and Confidence in Rural Enugu

**Project Description Example**

This proposed initiative combines age-appropriate menstrual health education, personal safety sessions and reusable pad-making workshops. It aims to equip girls with knowledge, confidence and practical resources that can support their wellbeing and participation in school.

**The Need**

Describe the local barrier with cautious, credible data; include source, publication date and location for third-party statistics.

**Our Response**

Outline key activities, who will deliver them, who the programme is intended for, where it will take place and how participation will be managed.

**Progress And Accountability**

For active projects, show updates, funds received where independently reconciled, milestones and approved photo evidence. For completed projects, show final delivery results, report downloads, learning and acknowledgements.

**Supporting Action**

Invite contributions toward specific budget items or approved campaigns, or direct visitors to partnership enquiries when donations are not open.

**Publishing note:** Draft copy. Store approved strings in local JSON; use §5 for the page layout, JSON references, CTAs and acceptance criteria. Publish factual claims and photographs only after approval.

## A.10 Our Impact

*URL: /impact  |  Primary audience: Funders, donors, institutions and media*

**Headline**

Measured through action. Remembered through people.

**Introduction**

Behind every number is a person, a school, a family or a community. We are committed to sharing what we do, what we learn and where we still need to grow.

**Impact Summary**

Present approved verified totals by reporting period, grouped into individuals reached, scholarships supported, community interventions delivered, volunteers engaged and partner organisations involved. Show what each measure actually means.

**Reflection**

Our mission calls us to do more than report activities. We also seek to understand whether our efforts are helping people gain access, confidence, knowledge and opportunities.

**Publishing note:** Draft copy. Store approved strings in local JSON; use §5 for the page layout, JSON references, CTAs and acceptance criteria. Publish factual claims and photographs only after approval.

## A.11 Stories & Updates

*URL: /stories  (existing: /blog)  |  Primary audience: Public, volunteers, media and partners*

**Headline**

Stories from the heart of our work.

**Introduction**

From classrooms and community outreach to the people who make each intervention possible, our stories offer a closer look at the work of Jedidiah Cares.

**Categories**

Programme Update • Community Story • Event Recap • Partnership • Organisation News • Insight & Learning.

**Editorial Promise**

We share stories with care, seek appropriate consent and focus on the experiences, contributions and dignity of the people and communities involved.

**Publishing note:** Draft copy. Store approved strings in local JSON; use §5 for the page layout, JSON references, CTAs and acceptance criteria. Publish factual claims and photographs only after approval.

## A.12 Events & Activities

*URL: /events  and  /events/{slug}  |  Primary audience: Participants, volunteers, schools and partners*

**Headline**

Come together. Learn together. Make change together.

**Intro**

Workshops, community outreach, youth conversations and collaborative events create opportunities for learning and collective action.

**Upcoming Events**

Show approved future event date, local time, location or virtual format, target audience, description, organiser, safeguarding considerations and registration deadline.

**Past Events**

Show archived events with actual attendance where verified, selected photos, short outcomes and a link to a full recap.

**Participation Message**

We welcome appropriate involvement from individuals, schools, institutions and volunteers who share a commitment to dignified, community-centred service.

**Publishing note:** Draft copy. Store approved strings in local JSON; use §5 for the page layout, JSON references, CTAs and acceptance criteria. Publish factual claims and photographs only after approval.

## A.13 Get Involved

*URL: /get-involved  |  Primary audience: Prospective supporters, partners and volunteers*

**Headline**

There is more than one way to make a difference.

**Introduction**

Compassion becomes action through people who give their time, resources, skills and ideas. Whether you are an individual, a school, a company or a community organisation, there is room to contribute to meaningful work.

**Ways To Get Involved**

Make a Donation — Support an approved project or general mission needs. Volunteer — Contribute time and skills to community activities. Partner With Us — Develop practical initiatives together. Sponsor an Opportunity — Support education or other structured programmes when a verified campaign is open.

**Assurance**

Every opportunity for engagement should be clearly described, with realistic expectations and a reliable point of contact.

**Publishing note:** Draft copy. Store approved strings in local JSON; use §5 for the page layout, JSON references, CTAs and acceptance criteria. Publish factual claims and photographs only after approval.

## A.14 Donate / Support Our Work

*URL: /donations  (existing route)  |  Primary audience: Individual and corporate donors*

**Headline**

Your support can create meaningful opportunities.

**Intro**

Every contribution helps make thoughtful community interventions possible. You can choose to support an open project or contribute to the general work of Jedidiah Cares.

**Impact Explanation**

Before linking visitors to an independently operated donation channel, explain what the selected project aims to deliver, its status and any approved funding purpose. Avoid promising a fixed result per naira unless the underlying costing is approved.

**Trust Statement**

Transactions should be processed securely. Donors should receive confirmation, and the organisation should make project updates or periodic reports accessible where practical.

**Thank-You Content**

Thank you for standing with Jedidiah Cares. Your support helps turn compassion into opportunities for children, young people and women. We will share relevant updates as projects progress.

**Publishing note:** Draft copy. Store approved strings in local JSON; use §5 for the page layout, JSON references, CTAs and acceptance criteria. Publish factual claims and photographs only after approval.

## A.15 Volunteer With Us

*URL: /volunteers  |  Primary audience: Prospective volunteers*

**Headline**

Your time and talents can help build brighter futures.

**Intro**

Volunteers make community engagement possible. From supporting outreach and learning activities to helping with logistics, communication and specialist skills, there are many ways to contribute meaningfully.

**What Volunteering Involves**

Opportunities depend on current programmes, location, experience and safeguarding requirements. Some roles involve direct community participation; others support planning, communications, logistics or technical work.

**How It Works**

Tell us about your interest and skills → We review your application → We discuss a suitable role → Selected volunteers complete any required orientation or safeguarding checks.

**Closing Message**

We value people who are dependable, respectful and ready to learn with communities.

**Publishing note:** Draft copy. Store approved strings in local JSON; use §5 for the page layout, JSON references, CTAs and acceptance criteria. Publish factual claims and photographs only after approval.

## A.16 Partner With Us

*URL: /partner  |  Primary audience: CSR teams, schools, NGOs, government and funders*

**Headline**

Stronger communities begin with meaningful partnerships.

**Intro**

Lasting change requires shared expertise, community trust and practical resources. Jedidiah Cares welcomes conversations with organisations that want to advance education, youth development, girls’ wellbeing and community empowerment.

**Partnership Possibilities**

Programme sponsorship; co-designed school and community interventions; in-kind or technical support; professional volunteering; community referrals and evidence-based learning.

**How We Collaborate**

We begin by understanding needs and alignment, agree clear responsibilities and safeguarding requirements, design measurable activities and document delivery and learning.

**Invitation**

Tell us about your organisation, the communities you serve and the difference you hope to make together.

**Publishing note:** Draft copy. Store approved strings in local JSON; use §5 for the page layout, JSON references, CTAs and acceptance criteria. Publish factual claims and photographs only after approval.

## A.17 Leadership & People

**Headline:** The people behind the mission.

Our work is shaped by people who contribute their experience, time, values and care to the communities we serve. Meet the directors, team members and approved volunteer leaders working together to translate compassion into action.

**Editor note:** Confirm current titles, biographies, consent and image rights. Keep governance separate from day-to-day staff. Do not expose private contact details copied from historical seed data.

## A.18 Photo Gallery

**Headline:** Moments of care, learning and community.

Explore selected photographs from outreach programmes, educational engagements, volunteer activities and community collaborations. Each gallery should connect to the story of a real project or event and explain when and where it took place, where safe to share.

**Editor note:** Use only consent-cleared photographs and accurate descriptions, with special safeguards for identifiable children and sensitive programme beneficiaries.

## A.19 Contact

**Headline:** Let's make meaningful change together.

Have a question about our programmes, want to explore a partnership or need to reach our team? Share a few details and we'll direct your enquiry to the right person.

**Editor note:** Confirm official public email, phone and address. Replace the old New York map embed with a verified real location or no map.

## A.20 Historical Shop (Excluded)

The legacy website included an optional shop. **The new frontend-only site must not implement a shop, cart, order history or checkout.** This section exists only to preserve the editorial context from the previous brief.

---


# Appendix B — Implementation reference and handover

## B.1 Suggested import/selector pattern

```ts
import programmesData from '@/data/programmes.json';
import projectsData from '@/data/projects.json';
import storiesData from '@/data/stories.json';
import type { Programme, Project, Story } from '@/types/content';

const isPublic = <T extends { isPublished: boolean; publicationState: string }>(item: T) =>
  item.isPublished && item.publicationState === 'approved';

export const programmes = (programmesData as Programme[]).filter(isPublic);
export const projects = (projectsData as Project[]).filter(isPublic);
export const stories = (storiesData as Story[]).filter(isPublic);
export const findProgramme = (slug: string) => programmes.find(item => item.slug === slug);
```

**Note:** `Story` is a contract to implement; add runtime validation during build if reliable editorial error detection is required. Importing all content into the browser can expose draft text even if filtered. **For truly private draft content, keep it outside `src/data` / public build entirely**, or use a build-time generation script that emits approved items only. `isPublished` flags control *display*, not confidentiality. This is a public static website.

## B.2 Sample route architecture

```tsx
import { createBrowserRouter, Navigate } from 'react-router-dom';
import SiteLayout from '@/components/layout/SiteLayout';
import HomePage from '@/pages/HomePage';
import ProgrammeDetailPage from '@/pages/ProgrammeDetailPage';
import NotFoundPage from '@/pages/NotFoundPage';
// Import remaining pages defined in §3.

export const router = createBrowserRouter([
  {
    element: <SiteLayout />,
    children: [
      { path: '/', element: <HomePage /> },
      { path: '/programmes/:slug', element: <ProgrammeDetailPage /> },
      { path: '/blog', element: <Navigate to='/stories' replace /> },
      { path: '*', element: <NotFoundPage /> }
    ]
  }
]);
```

This snippet is intentionally partial. Implement **every route** in §3 with real page components and meaningful content.

## B.3 Suggested reusable components

`SiteLayout`, `Header`, `Footer`, `MobileNav`, `Container`, `SectionHeading`, `Hero`, `ButtonLink`, `Breadcrumbs`, `ProgrammeCard`, `ProjectCard`, `ImpactEvidence`, `StoryCard`, `EventCard`, `TeamCard`, `PhotoGrid`, `StatusBadge`, `EmailComposer`, `OutboundAction`, `EmptyState`, `PolicyArticle`, `ScrollToTop`, `SeoHead`, `MediaFigure`, `ApprovedReportCard`.

Avoid a giant conditional single-page file and avoid implementing presentation data in TSX constants scattered throughout the app. Use controlled `content/index.ts` selectors for approved content and references.

## B.4 Codex completion report template

When finished, give a succinct report covering:

- Created files and page routes.
- Components/data schemas and how to edit them.
- Brand colour usage and screenshots of homepage/mobile and key internal pages.
- Which content/assets/contact/giving links remain pending approval.
- Commands executed and actual test/build results.
- Static hosting rewrite requirements and environment specifics.
- Known limitations (e.g. forms require email client, payment redirects externally, SPA crawling limitations).

## B.5 Source references (research only)

The prior legacy website source was inspected at `https://github.com/UtyEmma/jedidiahcares`, including its homepage hero, original organisational seed data and public-facing page structure. For this frontend build **do not install, run, migrate, call or modify that Laravel application's backend**. The uploaded Word strategy brief informed the editorial copy in Appendix A; this new v2 PRD supersedes its technology assumptions. Original uploaded `logo.webp` and the companion `brand-tokens.css` define the primary design anchors `#072266` and `#F0EB6F`.

**Final directive:** Build a premium, welcoming and authentic Jedidiah Cares **static frontend**. Preserve its mission and factual history, use React Router and editable local JSON, and keep any backend/payment/form-processing requirements out of scope.
