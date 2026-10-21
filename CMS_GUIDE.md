# Managing Jedidiah Cares Global Outreach for Women, Youth and Children content

The admin interface is available at `/admin`. It manages dynamic records, fixed page-section content, SEO and site settings. PHP data supplies the default copy; navigation and visual layouts stay in application code. Active administrators can manage general identity, brand assets/colours, contact/social details, custom scripts and robots.txt.

## First-time setup

Keep an existing `.env` and application key. For a new installation, install Composer/npm dependencies, copy `.env.example` to `.env`, generate an application key, and configure the database and `APP_URL`.

```sh
php artisan migrate --force
php artisan cms:import-content
php artisan cms:create-admin
npm run build
```

The configured SQLite connection creates `database/database.sqlite` during migration. MySQL can also be configured through Laravel’s standard database settings. The import combines the PHP collection arrays with a curated snapshot of the organisation’s public website. Repeating it preserves CMS edits, SEO values and records in Trash.

The organisation's full name is **Jedidiah Cares Global Outreach for Women, Youth and Children**. Use it when identifying the organisation in CMS text and media credits. The 15 October name-correction migration expands existing display text while preserving IDs, slugs, source URLs, media consent, funding values and authorship fields. Browser/search title suffixes may use the compact name. The correction is idempotent and remains in place on migration rollback so later editorial changes are preserved.

## Collection seeders and source media

`php artisan db:seed` runs `WebsiteContentSeeder`, using the same importer as `cms:import-content`. The current source catalogue includes four programmes, four people, five FAQs, two projects, one story and three galleries containing 69 photographs. Existing records with matching IDs or slugs are retained; counts can be lower when staff have removed or archived content. The website snapshot was checked on 14 October 2026 and is stored in `database/seeders/data/website-content.json`. Local PHP collection data takes precedence over matching snapshot records.

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
| General, scripts and robots defaults | `SiteSettingSeeder` |

For example:

```sh
php artisan db:seed --class=GallerySeeder
php artisan cms:import-content --collection=projects --collection=stories
php artisan cms:import-content --without-media
```

Referenced source records are included automatically, and relationships are resolved after record creation. No staff accounts or passwords are seeded. PHP supplies page/policy defaults; CMS page edits are preserved. Navigation and design settings remain in application code.

Default images are bundled in `database/seeders/assets`, grouped into people, programmes, projects, stories and individual gallery folders. The current bundle contains 78 images: two portraits, four existing generated programme images, two project images, one story cover and 69 gallery photos. `seedImage`/`seedFile` references and `database/seeders/data/media-manifest.json` resolve sources locally. Original remote URLs remain as provenance; normal seeding never requests them. Actual file types, sizes, checksums and safe local paths are checked before files are copied to the private CMS disk with thumbnails. Include the source bundle and manifest in deployments.

Existing cover uploads are kept. Gallery files retain source ordering, credits, descriptive alternative text and their existing public publication status. They can be hidden or have their consent approval withdrawn in the admin interface. The source website does not supply additional consent documents. Programme images retain their generated/representative flags; they do not become documentary gallery content.

`cms_seed_imports` records source IDs, their matching CMS records and completed media imports. The existing media keys are unchanged by the move to local files. This prevents duplicates and preserves intentional media removal, renamed records and permanent deletion on later runs. Keep this table in database backups. Failed files are reported and retried on the next run; they do not prevent other available media from importing. Portraits for Rev. Can. Chukwudi Chukwubuihem and Dr. Ebes Esho remain unavailable from the legacy image host; the matching main-site storage URLs return 404. Both are listed in the manifest's `missing` section. The bundled founder and executive director portraits remain available, and no substitute people photos are created.

Database seeding with media now works offline. `--without-media` remains available when only records are wanted. To collect new source images or repair a bundle, run `php artisan cms:bundle-seed-media`, optionally with `--collection=galleries` or another collection key. Only this explicit preparation command may download files; it checks verified HTTPS sources, reuses the private download cache and preserves existing valid bundle files. It returns a nonzero status while any requested source remains unavailable, while retaining successful files and recording missing sources in the manifest. Supply missing authorised files with `seedImage`, `seedFile` or `seedDocument` paths relative to `database/seeders/assets`, then rerun bundling and seeding. See the [asset bundle guide](database/seeders/assets/README.md).

The old cache at `storage/app/private/cms-seed-cache` can be removed to reclaim space once the bundle is prepared. Keep the versioned source bundle, plus the CMS originals and thumbnails on the configured `cms` disk. Bundled source files are preserved when imported into the database.

The public events list and project report lists were empty, and no verified impact metrics or partner directory were available. Those seeders still import any records supplied in the respective PHP data files; they do not generate fictional events, PDFs, partners or impact figures. Project plans are recorded as intended activities, without converting fundraising totals or targets into achieved outcomes.

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
- Programme illustrations, decorative icons and numbers remain defined by the PHP design data. Programme content and uploaded photographs are editable.
- Projects support programme associations, status, location, activities, documented outcomes and related reports. Status labels are Upcoming, Ongoing and Completed; existing `planned` and `active` records retain their stored values and display the new labels. Project cards show the status over the image and limit their excerpt to four lines. Only verified, source-attributed outcomes appear publicly.
- Under **Project funding**, optionally enter a Funding target, Amount raised and Funding currency (NGN by default; USD, GBP and EUR are also supported). Use confirmed totals and update the amount raised manually. A positive target enables the funding block on cards and the detail page; leaving or clearing the target hides it. Blank amounts raised display as zero. Amounts support up to two decimal places, and negative values are rejected. Progress can exceed 100% in the displayed figures while the visual bar remains full. These fields do not process donations or synchronise with a payment provider.
- Reports accept a PDF up to 20 MB and include a reporting period and publication date. Associate a report with a project to make its download available on the project page.
- Stories use paragraph, heading, quotation and list blocks.
- Events retain dates, timezone, location, audience and optional HTTPS registration links. Assign one or more Programmes to enable programme filtering and related programme links. `/events` shows all published events by default, with search, programme and status filters that work together. Ongoing events appear first, followed by upcoming events in date order and past events with the most recent first.
- Event badges are calculated from the start/end times: Upcoming before the start, Ongoing through the end, and Past afterwards. When no end is supplied, the event remains ongoing until the end of its start day in the selected timezone. Status refreshes every 30 seconds while the page is open and when returning to the tab. Cards and event detail pages use the same status rules.
- Partners require confirmed publication rights for their logos. Impact Metrics require a value, period, evidence source and verification before publishing.

## Editing pages without changing the design

Open **Website Settings → Pages**, select a page and use **Page content**, **Images** (where the template has photos), or **SEO**. The Content tab groups populated fields by the sections already visible on that page. Edit headings, descriptions, fixed lists/cards, button labels, supported destination links and alternative text. Home includes the hero's three lines, introductions, education feature, founder's note and partner invitations. About includes mission, vision, values and approach. Policy pages include their existing sections, paragraphs, bullet lists and contact invitations. Programme/project/person/event/story/gallery records still belong in their collection screens; page forms edit the surrounding page content.

Saving publishes page edits immediately for both active administrators and editors. **Site-wide SEO → Shared content** manages shared founder/CTA/partner copy, involvement cards, footer/top-bar text and enquiry/empty-state introductions. A page's own fields can override these defaults for that page; editing Home's founder note does not change About's override. General identity, logos, contact details and social accounts remain in **General & Brand**.

PHP defaults live in `resources/data/site.php`, `pages.php`, `interface.php`, `page-sections.php`, `media.php` and `policies.php`. Overrides retain that nested structure in the database, and only differences are stored. **Use default content** fills the text/link fields with inherited shared/PHP defaults; review and save to apply. Image and SEO overrides are independent. The Images tab shows only the current upload when one is selected or saved; the inherited preview returns when the upload is removed. Removing an uploaded page photo restores the inherited/bundled image. Page images use the same private sources, saved public WebP files and thumbnails as other CMS uploads. JPEG, PNG and WebP up to 10 MB are accepted. Descriptive alternative text is required. Section image frames and cropping styles remain fixed.

There are no layout/class/HTML/style controls, section builders, reordering, add/remove controls, icon selectors, font/colour controls or image-position controls on page forms. Existing list sizes and policy section/paragraph counts are fixed. Text renders as plain text; submitted design keys, unknown fields, extra indices and unsafe links are rejected by the server. Navigation stays in `navigation.php`. Retired page-copy/policy records from the initial CMS implementation are retained without automatically importing old copy. Run `php artisan migrate --force` for the new override column; content seeding does not overwrite saved edits. No frontend rebuild is needed after routine CMS content changes.

PHP data is evaluated at runtime. The default SEO URL follows `APP_URL`; a site-wide CMS canonical URL takes priority. Navigation and React links use Laravel named routes, and detail URLs use current record slugs. PHP copy edits need no frontend rebuild. After changing route/configuration files, refresh the Laravel caches; production PHP workers may also need their OPcache refreshed. See the [PHP data guide](resources/data/README.md).

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

JPEG, PNG and WebP uploads are automatically converted to compressed WebP when saved, including portraits, covers, partner logos, gallery photos and video posters. The stored image fits within 1920 × 1920 pixels at quality 82, preserving its proportions and transparency without upscaling. Phone photo orientation is corrected and embedded source metadata is not copied into the encoded image. Thumbnails fit within 640 × 640 pixels at quality 78. The same server-side process handles chunked uploads, edited images, URL imports and local seed imports. Original seed-bundle files are retained.

These settings live in `config/cms-media.php`. Images still have a 10 MB input limit, and excessively large pixel dimensions or images that exceed the server's processing memory are rejected before decoding. PHP GD must support WebP. Processing happens synchronously before thumbnails are generated; no queue worker or external image-optimizer binary is required. Failed new-image processing removes the incomplete CMS media record and reports a validation error. PDFs, videos and WebVTT files retain their original formats and bytes.

Existing stored images can be processed with:

```sh
php artisan cms:optimize-images --dry-run
php artisan cms:optimize-images
php artisan cms:optimize-images --id=123
```

The command is repeatable: images already optimized under the current settings are skipped. Existing UUIDs, relationships, ordering, captions, credits, consent and public URLs are preserved. Before replacement, original image bytes and media metadata are backed up privately under `storage/app/private/image-optimization-backups/{uuid}/`; backups are not served by the media routes. Keep these until the conversion is reviewed and follow your backup retention policy. The reported space saving excludes backups. Temporary processing copies are removed after completion, and failed conversions restore the original file and metadata.

Videos can have a poster image, caption file and text transcript. Upload browser-compatible video encoding; automatic transcoding is not provided. The uploader supports chunked transfers and retry/resume. Image thumbnails are generated using PHP GD; FFmpeg is not required. Gallery tabs support keyboard activation and shareable `/gallery?gallery=gallery-slug` links. Videos play on demand and stop when switching galleries.

The public gallery displays photos at their natural proportions in a responsive masonry layout: one column on small screens, two from 640px and three from 1024px. Photo tiles and previews show no visible captions or credit/organisation labels. Keep descriptions, credits, consent and alternative text in the CMS; publication checks still use this metadata. Video controls, caption tracks and expandable transcripts remain available.

Photo previews use the installed `lightbox.js-react` package, loaded only when a visitor opens a photo. The preview supports zoom, previous/next controls and keyboard navigation, traps focus while open and returns focus to the thumbnail when closed. It exposes descriptive photo announcements to assistive technology and respects reduced motion. The gallery itself remains server-rendered.

For a Lightbox.js licence, set `VITE_LIGHTBOX_LICENSE_KEY` and `VITE_LIGHTBOX_PLAN_TYPE` (`individual` or `team`) in the build environment, then rebuild. These are public client configuration values. The vendor describes its commercial and open-source licence options in the [Lightbox.js installation guide](https://www.getlightboxjs.com/docs/). No licence key is bundled or invented.

Full-size optimized images and their thumbnails are generated and saved on upload, not at page-load time. The private source files stay in `storage/app/cms` (`CMS_MEDIA_ROOT` may override that location). Eligible published image files are also saved into `storage/app/public/images/{uuid}` and served directly by the web server through Laravel's public storage symlink:

```text
/storage/images/{uuid}/photo.webp
/storage/images/{uuid}/thumbnails/photo-thumb.webp
```

Set `APP_URL` to the correct website origin. For an existing installation, run:

```sh
php artisan config:clear
php artisan storage:link
php artisan cms:sync-public-images --dry-run
php artisan cms:sync-public-images
```

The synchronization command repairs missing saved thumbnails, copies eligible public files, removes hidden/orphaned public copies and leaves unchanged files untouched. It does not re-encode full-size originals; `cms:optimize-images` remains available separately. Upload, conversion and content/media-save events synchronize public copies automatically after database commit, including video posters and branding images. Rolled-back edits do not publish files. Hiding, deactivating, archiving, trashing, consent withdrawal and deletion remove public copies while retaining any private originals that still belong to the record; restoring eligible records recreates them. UUID directories prevent collisions and public filenames are URL-safe. Existing public `/media/{uuid}/original` and `/thumb` image links redirect to the saved file.

Draft image previews, videos, WebVTT and PDF downloads continue through `/media/{uuid}/{conversion}` with current staff/publication checks and `private, no-store` responses. Anonymous users cannot retrieve unpublished files; video byte-range seeking and report attachments remain supported. The private CMS disk is never linked publicly. Only published image copies belong in the public disk.

Apache/LiteSpeed receives `Cache-Control: public, max-age=0, must-revalidate` and `nosniff` rules for `/storage/images/` in `public/.htaccess`. For Nginx/CDN hosting, configure equivalent revalidation and do not retain withdrawn images in an independent edge cache. Preserve both `storage/app/cms` and `storage/app/public` between releases. The public image copies can be rebuilt from the private source and database with `cms:sync-public-images`; already downloaded copies cannot be recalled from visitors' devices. Direct database/bulk SQL edits bypass model events, so run the synchronization command after those operations.

## SEO without changing page design

Use the **SEO** tab under **Website Settings → Pages** for site-wide defaults and fixed public page entries. Edit **Page title** to set the browser tab, search result and social preview title. The site-wide title suffix is appended automatically; leave the field blank to restore the default page title. Changes appear immediately after saving. SEO controls affect metadata, social previews, robots and sitemap output; visible headings and section content are managed separately in **Page content**, while layout and navigation stay in code.

The **Site-wide SEO** entry includes canonical website URL, title suffix, default description, social image and search indexing. Per-page values include page title, description, canonical override, social image and indexing eligibility. Blank overrides use the existing PHP defaults. A social image can be an HTTPS URL or a local path such as `/images/social-card.png`.

Indexing is enabled by default. The site-settings migration removes the old launch-time global and page exclusions on existing installations. Later deliberate choices are preserved by repeat seeding/deployments. Published public URLs appear in the sitemap; individual pages can still be excluded. Canonical overrides on another domain are excluded from this site’s sitemap. Admin and upload utilities retain exclusions; eligible public images and reports can be crawled. Unavailable records return 404 and duplicate donation utility pages retain noindex.

## General, brand, script and crawler settings

Only active administrators can view or save these screens, including through direct URLs and subsequent Livewire requests. Editors retain their existing content/SEO permissions.

**General & Brand** sets the full site/organisation name, compact name, tagline, default description, primary/accent colours, public email, telephone, office address and six HTTPS social profile links. Blank contact/social fields disappear from the public interface. Page-specific SEO descriptions and social-image overrides take priority over general defaults. The main logo, optional footer logo, favicon and default social image use private CMS sources, saved public image-file URLs and automatic WebP optimization. Removing an upload restores the bundled default; an absent footer logo uses the main logo. Keep a dark primary and a light contrasting accent to retain legible controls.

**Scripts** accepts complete trusted HTML/JavaScript tags up to 100,000 characters in each location. Header code is placed immediately before `</head>` and footer code before `</body>` on public pages. The enable toggle pauses both locations without deleting their contents. These fields are executable administrator configuration, not sanitized editorial text. They are not rendered on admin pages or serialized in public Inertia props. Integrations execute once per document load; subscribe to Inertia navigation events when tracking later page visits.

**Robots.txt** edits the plain-text response served at `/robots.txt`. The seeded defaults allow `/`, exclude `/admin`, `/livewire`, `/fluppy` and `/up`, and include `Sitemap: {site_url}/sitemap.xml`. `{site_url}` resolves from the canonical URL in **Pages → Site-wide SEO**. Restore defaults fills the editor; Save changes commits it. If the site-wide SEO indexing switch is deliberately disabled, the response becomes `User-agent: *` / `Disallow: /` and page metadata/sitemap follow that choice. Robots rules are crawler instructions; existing authentication and media publication checks remain authoritative.

Our People includes an optional **Public email address**. Individual addresses are blank by default, as requested. Adding one publishes a mail link on the card/profile and includes the address in profile structured data. Invalid addresses and header-injection strings are rejected. Do not put private staff addresses in a public field.

## Deployment command

Configure `.env` first: production environment, `APP_DEBUG=false`, correct `APP_URL`, database, mail and persistent storage paths. Bootstrap PHP dependencies with `composer install --no-dev --prefer-dist --no-interaction` so Artisan can run. Include `database/seeders/assets` and its manifest in the release. Keep existing application keys and content/media storage; take a database/media backup before deployment.

```sh
php artisan cms:deploy --dry-run
php artisan cms:deploy --force
# Optional interactive account creation after deployment
php artisan cms:deploy --force --create-admin
```

The command enters maintenance mode before replacing installed dependencies or build assets, installs locked Composer production dependencies and npm build dependencies, builds client/SSR assets, clears bootstrap caches, generates a key only when absent, applies pending migrations and runs all collection/SEO/site seeders. It then creates the configured public storage link, synchronizes saved public image/thumbnail files, publishes Filament assets, caches configuration/events/routes/views/Filament metadata, and signals queue workers, scheduler and SSR to restart. The existing full-size image optimization deployment step remains disabled; uploads still optimize automatically and `cms:optimize-images` can be run separately for backfills. The private CMS source disk is never publicly symlinked. Add `--create-admin` to run `cms:create-admin` after deployment succeeds and maintenance owned by this run has ended. It uses a fresh PHP process with the deployed configuration and private terminal prompts; no account/password is seeded. File imports stay offline, report missing sources and preserve successful imports and subsequent CMS edits.

- `--skip-dependencies`: use installed dependencies. Required for the local verification workflow or prepared CI artifacts.
- `--skip-build`: require existing client and SSR artifacts. Combine with `--skip-dependencies` when the target has no Node build tools.
- `--without-media`: import records/settings without copying seed media; existing image optimization still runs.
- `--skip-reload`: leave process restarts to your deployment platform.
- `--create-admin`: optionally create an administrator after success. Requires an interactive terminal; `--force` does not bypass account prompts. `--no-interaction` is rejected with this option before deployment changes begin. Dry runs list the step without prompting. Validation failure or cancellation does not undo the deployment or put a live website back into maintenance; rerun `php artisan cms:create-admin` separately.
- `--no-maintenance`: use only when preparing an isolated release directory or when maintenance is managed externally.
- `--force`: suppress Laravel's production confirmation. Without it, production runs ask for confirmation.

Commands run sequentially in fresh PHP processes, stop on failure and use a storage-directory deployment lock. A failure after entering maintenance leaves the site down for correction. Fix the cause and repeat the command, then run `php artisan up` once ready. An already enabled maintenance mode is preserved, including on a successful retry. Do not use `migrate:fresh` for deployments. Queue/SSR processes need an external supervisor to restart them; the command does not install cron jobs, provision hosting, configure TLS or deploy to an external server.

## Hosting and backups

Serve the application from `public/`. Keep the database, `storage/` and `bootstrap/cache/` writable by PHP. Upload files must persist across deployments. Keep GD installed with WebP support for image conversion and thumbnails. Livewire accepts assembled uploads up to 100 MB; Fluppy uses small chunks, so proxy/PHP request limits must accommodate those individual requests. Actual hosting capacity and upload behaviour should be checked on the destination server.

Run Laravel’s scheduler every minute through the host’s cron facility:

```cron
* * * * * cd /path/to/jedidiahcares && php artisan schedule:run >> /dev/null 2>&1
```

A daily `fluppy:prune` task removes abandoned uploads. `php artisan fluppy:doctor --url=https://your-domain.example` can check hosting upload limits. Back up the database, CMS media directory and application key together. Protect backups and retain the key across deployments. Keep the production Inertia SSR process supervised and restart it after asset releases. No external deployment is included in this implementation.

## Verification

No automated tests or test tooling were added. Build/type checks, ESLint, PHP syntax/style checks and local manual integration inspection are used. See `QA_NOTES.md` for checks actually performed and remaining browser/hosting checks.
