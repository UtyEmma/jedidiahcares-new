# Managing Jedidiah Cares Global Outreach for Women, Youth and Children content

The admin interface is available at `/admin`. It manages dynamic records and SEO. Static page copy, founder’s note, policy wording, navigation, contact details and visual layouts stay in JSON and application code.

## First-time setup

Keep an existing `.env` and application key. For a new installation, install Composer/npm dependencies, copy `.env.example` to `.env`, generate an application key, and configure the database and `APP_URL`.

```sh
php artisan migrate --force
php artisan cms:import-content
php artisan cms:create-admin
npm run build
```

The configured SQLite connection creates `database/database.sqlite` during migration. MySQL can also be configured through Laravel’s standard database settings. The import combines the existing collection JSON with a curated snapshot of the organisation’s public website. Repeating it preserves CMS edits, SEO values and records in Trash.

The organisation's full name is **Jedidiah Cares Global Outreach for Women, Youth and Children**. Use it when identifying the organisation in CMS text and media credits. The 15 October name-correction migration expands existing display text while preserving IDs, slugs, source URLs, media consent, funding values and authorship fields. Browser/search title suffixes may use the compact name. The correction is idempotent and remains in place on migration rollback so later editorial changes are preserved.

## Collection seeders and source media

`php artisan db:seed` runs `WebsiteContentSeeder`, using the same importer as `cms:import-content`. The current source catalogue includes four programmes, four people, five FAQs, two projects, one story and three galleries containing 69 photographs. Existing records with matching IDs or slugs are retained; counts can be lower when staff have removed or archived content. The website snapshot was checked on 14 October 2026 and is stored in `database/seeders/data/website-content.json`. Local collection JSON takes precedence over matching snapshot records.

Individual seeders are available for each dynamic collection:

| Collection | Seeder |
| --- | --- |
| Our People | `PeopleSeeder` |
| Programmes | `ProgrammeSeeder` |
| Projects | `ProjectSeeder` |
| Reports | `ReportSeeder` |
| Stories & Updates | `StorySeeder` |
| Events | `EventSeeder` |
| Galleries | `GallerySeeder` |
| Partners | `PartnerSeeder` |
| FAQs | `FaqSeeder` |
| Impact Metrics | `ImpactMetricSeeder` |
| SEO defaults | `SeoSeeder` |

For example:

```sh
php artisan db:seed --class=GallerySeeder
php artisan cms:import-content --collection=projects --collection=stories
php artisan cms:import-content --without-media
```

Referenced source records are included automatically, and relationships are resolved after record creation. No staff accounts or passwords are seeded. Page copy, policies, navigation and design settings remain in JSON/code.

Default images are bundled in `database/seeders/assets`, grouped into people, programmes, projects, stories and individual gallery folders. The current bundle contains 78 images: two portraits, four existing generated programme images, two project images, one story cover and 69 gallery photos. `seedImage`/`seedFile` references and `database/seeders/data/media-manifest.json` resolve sources locally. Original remote URLs remain as provenance; normal seeding never requests them. Actual file types, sizes, checksums and safe local paths are checked before files are copied to the private CMS disk with thumbnails. Include the source bundle and manifest in deployments.

Existing cover uploads are kept. Gallery files retain source ordering, credits, descriptive alternative text and their existing public publication status. They can be hidden or have their consent approval withdrawn in the admin interface. The source website does not supply additional consent documents. Programme images retain their generated/representative flags; they do not become documentary gallery content.

`cms_seed_imports` records source IDs, their matching CMS records and completed media imports. The existing media keys are unchanged by the move to local files. This prevents duplicates and preserves intentional media removal, renamed records and permanent deletion on later runs. Keep this table in database backups. Failed files are reported and retried on the next run; they do not prevent other available media from importing. Portraits for Rev. Can. Chukwudi Chukwubuihem and Dr. Ebes Esho remain unavailable from the legacy image host; the matching main-site storage URLs return 404. Both are listed in the manifest's `missing` section. The bundled founder and executive director portraits remain available, and no substitute people photos are created.

Database seeding with media now works offline. `--without-media` remains available when only records are wanted. To collect new source images or repair a bundle, run `php artisan cms:bundle-seed-media`, optionally with `--collection=galleries` or another collection key. Only this explicit preparation command may download files; it checks verified HTTPS sources, reuses the private download cache and preserves existing valid bundle files. It returns a nonzero status while any requested source remains unavailable, while retaining successful files and recording missing sources in the manifest. Supply missing authorised files with `seedImage`, `seedFile` or `seedDocument` paths relative to `database/seeders/assets`, then rerun bundling and seeding. See the [asset bundle guide](database/seeders/assets/README.md).

The old cache at `storage/app/private/cms-seed-cache` can be removed to reclaim space once the bundle is prepared. Keep the versioned source bundle, plus the CMS originals and thumbnails on the configured `cms` disk. Bundled source files are preserved when imported into the database.

The public events list and project report lists were empty, and no verified impact metrics or partner directory were available. Those seeders still import any records supplied in the respective existing JSON files; they do not generate fictional events, PDFs, partners or impact figures. Project plans are recorded as intended activities, without converting fundraising totals or targets into achieved outcomes.

`cms:create-admin` asks for the name, email and password privately. Passwords must contain at least 12 characters. No default credentials or public registration are provided. To recover a staff password, use `php artisan cms:reset-password staff@example.org`; it prompts privately for the replacement password. Email-based password recovery is not enabled.

## Staff access and publishing

Administrators and editors can create, edit and publish dynamic records, manage uploads, archive content and restore content from Trash. Only administrators can manage staff accounts and permanently delete content records. Staff must have an active account. The application prevents removal or deactivation of the last active administrator.

New records start as Draft. Publishing makes eligible content available on the website. Saving a published record updates it immediately. Archiving or moving a record to Trash removes it from public lists and detail routes. Changing a published detail slug retains a redirect from its previous address. Use Archive when you want to retain relationships; deleting a referenced record clears its associations, and restoring it does not automatically recreate those associations.

The dashboard shows dynamic content counts, recent changes and creation shortcuts. Creator, editor and publisher information remains administrative metadata and is not included in public content props.

## Dynamic collections

Manage Our People, Programmes, Projects, Reports, Stories & Updates, Events, Galleries, Partners, FAQs and Impact Metrics. Structured fields preserve the existing rendering components. Programmes, projects, stories and events have optional search title/description overrides for their detail pages.

- Our People retains leadership, team and volunteer groups; confirm portrait publication consent before making a portrait public.
- Published people appear at `/team/{slug}` with a full biography and related profiles. Use blank lines in Biography for paragraphs. Search appearance overrides control the profile metadata, while the public title and role stay tied to the person's record. Changing a published slug redirects its old profile address.
- In **Our People → Social profiles**, add optional LinkedIn, Facebook, Instagram, X, YouTube, TikTok and personal website URLs. Use complete HTTPS links; leave unused fields blank. Saving a published member updates the links on their profile and people cards immediately. Only supplied accounts appear, and profile metadata includes them as `sameAs` when indexing is enabled. These fields use the existing record data and require no new migration.
- Programme illustrations, decorative icons and numbers remain defined by the JSON design. Programme content and uploaded photographs are editable.
- Projects support programme associations, status, location, activities, documented outcomes and related reports. Status labels are Upcoming, Ongoing and Completed; existing `planned` and `active` records retain their stored values and display the new labels. Project cards show the status over the image and limit their excerpt to four lines. Only verified, source-attributed outcomes appear publicly.
- Under **Project funding**, optionally enter a Funding target, Amount raised and Funding currency (NGN by default; USD, GBP and EUR are also supported). Use confirmed totals and update the amount raised manually. A positive target enables the funding block on cards and the detail page; leaving or clearing the target hides it. Blank amounts raised display as zero. Amounts support up to two decimal places, and negative values are rejected. Progress can exceed 100% in the displayed figures while the visual bar remains full. These fields do not process donations or synchronise with a payment provider.
- Reports accept a PDF up to 20 MB and include a reporting period and publication date. Associate a report with a project to make its download available on the project page.
- Stories use paragraph, heading, quotation and list blocks.
- Events retain dates, timezone, location, audience and optional HTTPS registration links. Assign one or more Programmes to enable programme filtering and related programme links. `/events` shows all published events by default, with search, programme and status filters that work together. Ongoing events appear first, followed by upcoming events in date order and past events with the most recent first.
- Event badges are calculated from the start/end times: Upcoming before the start, Ongoing through the end, and Past afterwards. When no end is supplied, the event remains ongoing until the end of its start day in the selected timezone. Status refreshes every 30 seconds while the page is open and when returning to the tab. Cards and event detail pages use the same status rules.
- Partners require confirmed publication rights for their logos. Impact Metrics require a value, period, evidence source and verification before publishing.

Static page wording and policies are maintained in `resources/js/data/site.json`, `pages.json`, `interface.json` and `policies.json`. Navigation stays in `navigation.json`. CMS edits cannot change these files or override their page text. Retired page-copy/policy database records from the initial CMS implementation are retained but are not exposed or consumed.

## Sharing public content

Stories, projects, events, programmes, people profiles, selected galleries and the reports page include Facebook, X, LinkedIn, WhatsApp, email and Copy link controls. Links use each page's public canonical address from the SEO settings. Gallery links include `?gallery=slug` so the recipient opens the selected tab. Set the global SEO website address to the site's public HTTPS domain.

Copy link confirms when copying succeeds. If the browser cannot access the clipboard, a selected, read-only URL field supports manual copying. Sharing opens the platform's composer or the visitor's email app; the visitor completes sending there. The site embeds no sharing SDK and sends no posts or email on a visitor's behalf.

## Galleries

Create a gallery with its title, description, display order and optional project/event associations. Upload multiple photographs and videos, then save. Open **Gallery media** to edit each file’s caption, credit, image alternative text, visibility and publication consent. Reorder media using the table’s reorder control.

A gallery appears as a tab when its status is Published and **Show gallery tab** is enabled. Individual files appear only when visibility and publication consent are confirmed, a caption is present, and photographs have alternative text. An active gallery with no eligible media shows a contextual empty state.

Accepted formats and per-file limits:

| Media | Formats | Maximum |
| --- | --- | --- |
| Photographs and poster images | JPEG, PNG, WebP | 10 MB |
| Videos | MP4, WebM | 100 MB |
| Report documents | PDF | 20 MB |
| Video captions | WebVTT (`.vtt`) | 1 MB |

Videos can have a poster image, caption file and text transcript. Upload browser-compatible video encoding; automatic transcoding is not provided. The uploader supports chunked transfers and retry/resume. Image thumbnails are generated using PHP GD; FFmpeg is not required. Gallery tabs support keyboard activation and shareable `/gallery?gallery=gallery-slug` links. Videos play on demand and stop when switching galleries.

The public gallery displays photos at their natural proportions in a responsive masonry layout: one column on small screens, two from 640px and three from 1024px. Photo tiles and previews show no visible captions or credit/organisation labels. Keep descriptions, credits, consent and alternative text in the CMS; publication checks still use this metadata. Video controls, caption tracks and expandable transcripts remain available.

Photo previews use the installed `lightbox.js-react` package, loaded only when a visitor opens a photo. The preview supports zoom, previous/next controls and keyboard navigation, traps focus while open and returns focus to the thumbnail when closed. It exposes descriptive photo announcements to assistive technology and respects reduced motion. The gallery itself remains server-rendered.

For a Lightbox.js licence, set `VITE_LIGHTBOX_LICENSE_KEY` and `VITE_LIGHTBOX_PLAN_TYPE` (`individual` or `team`) in the build environment, then rebuild. These are public client configuration values. The vendor describes its commercial and open-source licence options in the [Lightbox.js installation guide](https://www.getlightboxjs.com/docs/). No licence key is bundled or invented.

Uploads and thumbnails are stored in the private `storage/app/cms` directory. `CMS_MEDIA_ROOT` can point to a persistent storage directory. `/media/{uuid}/{conversion}` checks current publication eligibility for every request; active authorised staff can preview drafts. Anonymous requests cannot retrieve draft, archived or hidden uploads. Video responses support byte ranges for seeking, and reports download as attachments. Media responses use `private, no-store` to enforce current access decisions.

## SEO without changing page design

Use **Website Settings → SEO** for site-wide defaults and the fixed public page entries. These controls affect metadata, social previews, robots and sitemap output; they do not change visible headings, founder’s note, page copy, layout or navigation.

Site-wide values include canonical website URL, title suffix, default description, social image and search indexing. Per-page values include SEO title, description, canonical override, social image and indexing eligibility. Blank overrides use the existing JSON defaults. A social image can be an HTTPS URL or a local path such as `/images/social-card.png`.

Indexing remains disabled by default, following the existing JSON setting. Enabling it exposes eligible public URLs in the sitemap; individual pages can still be excluded. Canonical overrides on another domain are excluded from this site’s sitemap. Admin pages and media-serving endpoints remain excluded from crawling.

## Hosting and backups

Serve the application from `public/`. Keep the database, `storage/` and `bootstrap/cache/` writable by PHP. Upload files must persist across deployments. Keep GD installed for image thumbnails. Livewire accepts assembled uploads up to 100 MB; Fluppy uses small chunks, so proxy/PHP request limits must accommodate those individual requests. Actual hosting capacity and upload behaviour should be checked on the destination server.

Run Laravel’s scheduler every minute through the host’s cron facility:

```cron
* * * * * cd /path/to/jedidiahcares && php artisan schedule:run >> /dev/null 2>&1
```

A daily `fluppy:prune` task removes abandoned uploads. `php artisan fluppy:doctor --url=https://your-domain.example` can check hosting upload limits. Back up the database, CMS media directory and application key together. Protect backups and retain the key across deployments. Keep the production Inertia SSR process supervised and restart it after asset releases. No external deployment is included in this implementation.

## Verification

No automated tests or test tooling were added. Build/type checks, ESLint, PHP syntax/style checks and local manual integration inspection are used. See `QA_NOTES.md` for checks actually performed and remaining browser/hosting checks.
