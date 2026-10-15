# Jedidiah Cares Global Outreach for Women, Youth and Children

A Laravel 13 + Inertia 3 + React 19 charity website adapted from `Jedidiah_Cares_Frontend_Only_Codex_PRD.md`, with TypeScript, Vite, Tailwind CSS v4, locally owned shadcn/Radix button, accordion and Select components, Phosphor icons, a Filament 5 CMS for dynamic content and SEO, and JSON-owned static page copy.

The design preserves the supplied navy/lemon logo, self-hosts Manrope and Inter, and combines editorial layouts, contextual photography and original illustrations. Header and section containers use `max-w-7xl mx-auto`.

The full organisation name is **Jedidiah Cares Global Outreach for Women, Youth and Children**, configured as `site.brand.publicName`. Use it in visible organisation references, founder attribution, hero branding, footer identity and accessibility labels. `site.brand.shortName` is reserved for compact browser/search title suffixes; the supplied raster logo retains its existing wordmark. Structured data identifies the organisation by its full name.

## Develop and build

Use PHP 8.4+ for the supplied dependency lock, Composer 2 and Node 22.12+ (Node 24 is also supported). The local conversion was verified with PHP 8.4, Laravel 13.36 and Inertia 3.

```sh
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan cms:import-content
php artisan cms:create-admin
npm install
npm run build
composer run dev
```

For an existing checkout with a configured `.env`, keep that file and its application key. Laravel prints its local URL; the default port is 8000 and it selects a free port when necessary. Set `APP_URL` to the address you use. Laravel Herd can serve the project from its `public/` directory; run `npm run dev` alongside Herd for Vite and hot reloading.

`composer run dev` starts Laravel and Vite together. The Inertia Vite plugin renders initial pages on the server during development without a separate SSR process. To review the production bundles locally, stop Vite and run `composer run dev:ssr`; it builds assets and starts Laravel plus the production SSR process.

```sh
npm run typecheck
npm run lint
npm run build
php artisan route:list --except-vendor
vendor/bin/pint app config routes
```

`npm run build` produces browser assets in `public/build/` and the SSR bundle in `bootstrap/ssr/`. `npm run format` formats frontend source. The website uses the database for dynamic content and SEO, with file sessions/cache and a synchronous queue. Run migrations and the content import before serving it. Automated tests, test scaffolding and test packages are intentionally omitted, as requested.

## Routes and interactions

Routes include `/`, `/about`, `/programmes`, all four programme detail pages, `/projects`, project details, `/reports`, `/stories`, story details, `/events`, event details, `/get-involved`, `/donations`, contextual giving, `/volunteers`, `/partner`, `/team`, `/gallery`, `/contact`, all four policy pages and a branded 404. Legacy `/blog` paths redirect to stories; `/impact` redirects to `/reports`.

The logo links to Home; the current primary menu contains About Us, Our Programmes, Our Impact, Get Involved and Contact. Menu groups toggle by clicking their entire label/button on desktop and mobile; links live inside the dropdowns. Overview destinations use titles such as “Our story”, “Explore our programmes” and “Ways to get involved”. Our Impact groups Our Reports, Projects, Stories & Updates, Events & Activities and Photo Gallery. A back-to-top button appears after scrolling more than two viewport heights and honours reduced-motion preferences. Body text defaults to 18px; compact labels and controls use 14px with generous touch targets.

Each route page has its own folder and default-exported module under `resources/js/pages`, including the four policy pages and contextual project donations. Home lives in `resources/js/pages/home/HomePage.tsx`; programme details live in `resources/js/pages/programme-detail/ProgrammeDetailPage.tsx`. The Inertia entry point, `resources/js/app.tsx`, loads pages on demand and applies a persistent shared layout. Policy and donation views share presentation components in `resources/js/components`.

Laravel owns all routing in `routes/web.php`, with the page map in `config/website.php`. Controllers supply route parameters and SEO metadata through Inertia. Internal links use Inertia navigation; external links, email and telephone links retain native browser behaviour. Approved detail records are checked on the server, and unknown or unpublished slugs return the branded page with HTTP 404. Legacy paths issue HTTP 301 redirects.

Selected hero content, section headings, founder notes and cards use a short entrance effect when they first enter the viewport. Menus, Select popups and FAQs have brief opening transitions. Motion uses browser APIs and CSS, adds no animation dependency, respects `prefers-reduced-motion` and cancels an entrance if keyboard focus reaches its content. Static HTML remains visible when JavaScript or motion support is unavailable. Adjust timings in `resources/js/hooks/useEntranceMotion.ts` and `resources/js/styles/motion.css`; `data-entrance` opts an element into the scroll entrance.

Projects support keyword/programme/status filters, image-overlay Upcoming/Ongoing/Completed badges and four-line card excerpts. Optional CMS funding targets, amounts raised and currencies produce funding progress on cards and detail pages. Stories support keyword/category/year filters. Events show all published records by default, with combined keyword/programme/status filters and Upcoming, Ongoing or Past badges calculated from their dates and declared timezone. Programme associations are managed in the event CMS form. Filter dropdowns and enquiry topic menus use shadcn/Radix Select with Phosphor indicators, 14px text, keyboard selection and scrolling for long menus. “All…” items and clearing filters reset the existing empty-string filter state. Topic selects retain their selected value in the static HTML and are explicitly disabled when an enquiry destination is unavailable. FAQs use shadcn/Radix accordions. Mobile navigation and galleries use accessible native dialogs with Escape handling. Unknown or unpublished slugs return 404; empty collections have honest, useful states.

## Content maintenance

Dynamic collections are managed through the Filament admin interface at `/admin`. React receives published database records through typed Inertia props and `useContent()`. CMS changes appear without rebuilding frontend assets. See [CMS_GUIDE.md](CMS_GUIDE.md) for setup, staff access, publishing, galleries, upload limits, SEO and hosting.

People records include optional social profile and website links, displayed on member cards and profiles. Stories, projects, events, programmes, profiles, galleries and reports provide Facebook, X, LinkedIn, WhatsApp, email and copy-link sharing. Share URLs use the public SEO canonical address; galleries preserve the selected tab. Clipboard failures offer a manual-copy field.

Gallery photos use a responsive masonry layout without visible captions or credit labels. Full-size previews use `lightbox.js-react`, loaded on demand, with zoom, keyboard navigation and focus management. Video playback and transcripts remain available. CMS metadata and publication checks are retained. Lightbox licence configuration is documented in [CMS_GUIDE.md](CMS_GUIDE.md#galleries).

Uploaded images are automatically stored as compressed WebP, bounded to 1920 × 1920 pixels with proportions/transparency preserved, and receive smaller 640-pixel thumbnails. This covers direct/chunked uploads, posters, portraits, logos and seeded images. Videos, PDFs and captions keep their formats. Run `php artisan cms:optimize-images --dry-run` or `php artisan cms:optimize-images` for existing uploads; originals are backed up privately before replacement. Settings are in `config/cms-media.php`; PHP GD needs WebP support.

Published images and thumbnails are actual saved files served directly at `/storage/images/{uuid}/photo.webp` and `/storage/images/{uuid}/thumbnails/photo-thumb.webp`. Encoding happens when images are saved, never when visitors load them. Run `php artisan storage:link` and `php artisan cms:sync-public-images` when upgrading; deployment includes both steps. The private CMS disk remains the source for editing/draft previews. Publishing/restoring copies eligible images into `storage/app/public/images`; hiding, archiving, consent withdrawal and deletion remove those public copies after commit. Old `/media/{uuid}/original` image links redirect to their saved public file. Videos, PDFs and unpublished previews retain controlled routes. Keep the public storage directory persistent alongside private uploads; see the CMS guide for static-server caching settings.

Static page copy stays in `resources/js/data/site.json`, `pages.json` and `interface.json`; policy wording stays in `policies.json` and navigation stays in `navigation.json`. Administrators can manage the site title, description, brand images/colours, contact details and social links under **Website Settings → General & Brand**. JSON supplies initial defaults; layouts and page copy have no CMS editor. Separate settings screens manage SEO, header/footer scripts and robots.txt.

Existing collection JSON files and `database/seeders/data/website-content.json` serve as import sources. Default media is grouped in `database/seeders/assets`, with local references and a checksum manifest. Run `php artisan db:seed` or `php artisan cms:import-content` to import records and copy these files into private CMS storage without network access. The bundle includes 78 images; two unavailable legacy portraits are recorded in the manifest. Use `php artisan cms:bundle-seed-media` only when collecting new source files or repairing the bundle. See the [asset bundle guide](database/seeders/assets/README.md) and [CMS setup instructions](CMS_GUIDE.md#collection-seeders-and-source-media). Repeating a seed preserves CMS edits and avoids duplicate media. Collection data is not bundled into public JavaScript, and draft records are not sent to visitors. Programmes, projects, reports, stories, events, people, galleries, partners, FAQs and impact metrics are dynamic; only published, eligible records are exposed. Galleries require media visibility, consent, captions and image alternative text.

After editing JSON/code, run validation, typecheck, lint and build, then manually review affected pages. Upload new dynamic media through the CMS rather than placing private drafts in `public/`.

Contextual website photography uses six AI-generated scenes commissioned for Jedidiah Cares, configured in `resources/js/data/media.json` and bundled as responsive WebP files in `public/images/generated/`. Each programme has imagery suited to its subject, including girls’ wellbeing. The homepage education feature uses its own reading scene. Existing activity galleries, campaign artwork and real portraits remain organisation-sourced. See [MEDIA_CREDITS.md](MEDIA_CREDITS.md) for the asset map, provenance and complete generation prompts.

## Contact and giving

Email, phone, Enugu address and social profile URLs come from the organisation’s [public website](https://jedidiahcares.org/), checked on 14 October 2026. They appear in the top bar and footer; the full address appears in the footer and Contact page. Social profiles open in a new tab. These are the destinations listed by the organisation; social platform availability has not been independently confirmed. The top bar also includes a volunteering CTA on larger screens.

Set the public email and other contact details in General & Brand; blank values are hidden. Composers keep inputs in memory and open an encoded `mailto:` draft. Visitors review and send in their own email app. The site saves and sends nothing. The button reads “Submit message”; the explanatory notice and copy-email action were removed at the user’s request. A direct-email link remains. The Contact page also includes linked social profiles for messaging and connection; these open the profile rather than an unverified direct-message endpoint. People records have optional public email addresses, initially blank, displayed alongside their social links when supplied.

Giving uses `giving.enabled` plus an approved HTTPS provider URL or bank-transfer record. A bank record needs `approved`, `accountName`, `bank`, `accountNumber` and `instructions`. External volunteer/partner forms are configurable and labelled. There is no payment collection, transaction simulation or server-side form handling.

No giving URL or bank details are configured. Giving remains unavailable until an approved channel is supplied.

## Founder and partners

`site.founderNote` supplies the brief note on Home and About. Dr. Ebere Ubesie’s founder identity and title were supplied by the project owner. Her portrait was retrieved from the organisation’s existing website. The note wording remains organisation-focused demo copy; no draft notice is shown in the interface. Set `enabled` to control the section; `approved` records editorial status internally.

The founder’s photo is bundled at `public/images/team/ebere-ubesie.jpg`. `portraitPath` and `portraitAlt` in `site.json` configure the image on both pages. If the image fails to load, name initials fill the frame. No stock person is identified as the founder.

`people.json` contains the four directors and four volunteers listed on the [current website](https://jedidiahcares.org/), checked on 14 October 2026. Biographies are concise summaries of the public profiles; outdated student-year and employment-stage claims are omitted. Records retain their original IDs, source URLs and source portrait URLs. Public personal email/phone fields were not imported. The founder and executive director portraits are bundled locally. Six source portraits are hosted on an old admin domain that currently fails its TLS connection; those cards use initials until valid replacement image paths are supplied.

Partner sections appear on Home and `/partner`. Add partner records through the CMS and confirm publication rights for any logo. Empty sections display a partnership invitation instead of invented logos. Each published record can include a summary, bundled logo and HTTPS website URL.

Every published person has a profile at `/team/{slug}`, linked from the people cards. Profiles show the CMS name, role, full biography and approved portrait, with initials when a portrait is unavailable. Paragraph breaks in the biography are preserved. Related people and a link to contact the organisation complete the page. Profile metadata and social previews use the person's public details; optional search overrides appear in the Our People editor. Published profiles join the sitemap when global indexing is enabled, and renamed profile slugs retain redirects. Draft, archived and deleted people return 404.

## Server rendering and SEO

Laravel serves the root Blade template at `resources/views/app.blade.php`; it replaces the former `index.html`. Inertia renders the React content on the server for initial visits and hydrates it for client navigation. Development SSR is provided by the Inertia Vite plugin. Production SSR uses the generated bundle and a background Node process:

```sh
php artisan inertia:start-ssr
```

Keep that process running under a process supervisor in production and restart it after each asset build. It binds to loopback only. If SSR is unavailable, Inertia falls back to client rendering; Laravel still supplies the page's title, description, canonical URL, social metadata and indexing policy in the initial HTML. Inertia's `Head` updates this metadata when visitors navigate.

`app/Support/WebsiteContent.php` reads static JSON copy and published database content, and enforces publication rules for detail routes. `SeoController` serves `/robots.txt` and `/sitemap.xml` dynamically. Approved public pages and records appear in the sitemap, and approved pages can emit Organization or Article structured data. The PNG social preview image remains bundled.

**Search indexing is enabled by default.** Published pages emit indexable metadata and structured data, published detail URLs appear in the sitemap, and public media no longer carries a blanket `noindex` header. The Robots.txt screen starts with crawlable public defaults and supports `{site_url}` for the canonical address. Admin, upload utilities, unavailable records and duplicate donation utility pages remain excluded. The SEO screen still permits deliberate site-wide or per-page exclusion. Set the canonical domain in SEO and the runtime address in `APP_URL`. Search ranking and Lighthouse scores have not been measured or guaranteed.

Administrators can add complete trusted HTML/JavaScript tags under **Website Settings → Scripts**. Header code is inserted before `</head>` and footer code before `</body>` on public pages only. A toggle pauses both. Code is not included in Inertia props or admin pages, and executes once per full document load; analytics integrations can subscribe to Inertia navigation events.

For deployment, configure `.env` and bootstrap `vendor/` with `composer install --no-dev --prefer-dist --no-interaction`, then run:

```sh
php artisan cms:deploy --dry-run
php artisan cms:deploy --force
# Optional: create an administrator using private prompts after deployment succeeds
php artisan cms:deploy --force --create-admin
```

This enters maintenance mode, installs locked dependencies, builds the client and SSR assets, clears stale bootstrap caches, creates a key only when missing, migrates, seeds all collections/settings without overwriting edits, links public storage, saves published image/thumbnail files, publishes admin assets, caches release metadata and signals supervised workers/SSR. Use `--skip-dependencies --skip-build` for a prepared release, `--skip-reload` when processes are managed elsewhere, or `--no-maintenance` for an isolated release directory. Add `--create-admin` to run secure name/email/password prompts after successful deployment; it requires an interactive terminal and cannot be combined with `--no-interaction`. If account creation fails, the deployed website stays available and the administrator command can be retried separately. See [CMS_GUIDE.md](CMS_GUIDE.md) for first-install requirements and failure recovery. No hosting deployment is performed automatically.

## Laravel hosting

This application now needs a PHP-capable Laravel host. Point the web server's document root at `public/`. Laravel's supplied `.htaccess` routes requests through `public/index.php` on Apache; Nginx should use Laravel's standard front-controller configuration. The previous standalone static-hosting configuration has been removed.

Install production dependencies, configure `.env` with `APP_ENV=production`, `APP_DEBUG=false` and the public `APP_URL`, preserve the deployment's `APP_KEY`, and build browser and SSR assets. A typical release includes:

```sh
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan cms:import-content
php artisan optimize
```

Keep `storage/` and `bootstrap/cache/` writable by PHP. Keep `node_modules` available to the Node SSR process because its bundle imports runtime packages. Restart the supervised SSR process after a release. Configure the database, run migrations, keep CMS media storage persistent and enable the scheduler as described in the CMS guide. Nothing has been deployed by this task.

## Media and launch status

Photographs are locally bundled WebP assets with 600-pixel mobile versions. Visible captions describe programme themes; image alternative text describes the scene. Stock-photo notices and credits are kept out of the interface at the user’s request. These images do not identify Jedidiah Cares participants or provide proof of delivered work. Sources and licences are in [MEDIA_CREDITS.md](MEDIA_CREDITS.md).

The supplied `assets/icon.webp` is copied unchanged to `public/icon.webp` and used as the favicon in `resources/views/app.blade.php`. Laravel serves it directly from `public/`; the content validator checks that the configured favicon exists.

Organisational prose, mission wording, founder’s note, giving and authentic activity records still need review. Publicly listed contact/social details and team roles should be reconfirmed before launch. Projects, stories, events, partners, gallery and reports remain empty until records are supplied. Missing content uses contextual introductions and contact invitations without invented dates, outcomes, people or collaborators.

Privacy, safeguarding, terms of use/giving and accessibility now contain full website copy in `resources/js/data/policies.json`, with section navigation, update dates and contextual contact links. Privacy and accessibility enquiry topics are available in the Contact form. All four use `info@jedidiahcares.org`, as specified by the project owner. Review the wording against organisational procedures before launch; the source references and points requiring confirmation are in [POLICY_CONTENT_NOTES.md](POLICY_CONTENT_NOTES.md).

Manual browser review uses Safari desktop/responsive mode; see [QA_NOTES.md](QA_NOTES.md) for the actual checks and limits. No automated tests have been implemented.
