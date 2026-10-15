# Default collection media

These files are the local source bundle for database seeding. Include this directory and `database/seeders/data/media-manifest.json` in checkouts and deployment packages. The source bundle lives outside the public document root; seeding copies files into the private CMS disk and generates thumbnails there.

The CMS copy is automatically resized and encoded as WebP during import (1920-pixel maximum edge, quality 82; smaller 640-pixel thumbnails). These source files and their manifest checksums remain unchanged. Generated image flags, credits and consent metadata are retained.

```text
people/                         2 available member portraits
programmes/                     4 existing AI-generated programme images
projects/                       2 project campaign images
stories/                        1 story cover
galleries/
  enugu-vapp-law-stop-guidelines-workshop/   8 photos
  menstrual-health-project/                41 photos
  women-empowerment/                       20 photos
```

The bundle contains 78 images (about 31 MiB). Original source URLs, actual file types, byte sizes and SHA-256 checksums are retained in the manifest. Source URLs are provenance: normal database seeding does not fetch them. Generated programme imagery retains its generated/representative metadata and is not used as documentary gallery content.

Seed with the usual commands:

```sh
php artisan cms:import-content
php artisan db:seed --class=GallerySeeder
```

To collect media after changing source records, run:

```sh
php artisan cms:bundle-seed-media
php artisan cms:bundle-seed-media --collection=galleries
```

Bundling may download missing official website images over verified HTTPS and reuse the existing download cache. It preserves valid bundled files. Database seeding never downloads images, overwrites CMS edits or recreates successfully seeded media that staff later remove.

Cover records use `seedImage` paths relative to this directory; gallery items use `seedFile`. For example, `"seedImage": "people/ebere-ubesie.jpg"` or `"seedFile": "galleries/menstrual-health-project/legacy-gallery-file-37.jpg"`. `seedDocument` supports the equivalent local report-PDF source when a report is supplied. The manifest also resolves existing remote source references to their local files. After replacing a bundled image, run the bundling command to update its checksum. Keep original IDs and source URLs to retain import identities.

Two default portraits remain unavailable: **Rev. Can. Chukwudi Chukwubuihem** and **Dr. Ebes Esho**. The live organisation website still points to the legacy `admin.jedidiahcares.org` image host, which fails its TLS connection. Matching main-site storage URLs return HTTP 404. The manifest records these unavailable sources, and bundling returns a nonzero exit status while any remain missing. Their existing initials fallback is retained. Supply the correct authorised portrait in `people/` and set its `seedImage` value, or upload it through the CMS. No substitute people photographs are included.
