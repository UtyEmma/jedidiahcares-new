# Media credits

The original `assets/logo.webp` was supplied by Jedidiah Cares and is copied unchanged to `public/logo.webp`.

The supplied `assets/icon.webp` is copied unchanged to `public/icon.webp` and used as the site favicon.

The homepage's default hero uses the Menstrual Health Project photograph supplied by the website owner on 21 October 2026. The original WebP is retained unchanged at `public/images/home/menstrual-health-outreach.webp` (1600 × 1280), with a 600-pixel responsive version. It is configured separately as `home-hero` in `resources/data/media.php`; About retains its existing community photograph. This organisation photograph is not AI-generated.

Default collection images are also bundled for offline database seeding in `database/seeders/assets`, grouped by collection and gallery. Original source URLs, file types and SHA-256 checksums are recorded in `database/seeders/data/media-manifest.json`; they remain provenance rather than runtime download dependencies. The bundle includes 69 authentic gallery photos, project/story artwork, two available portraits and copies of the four generated programme images described below. Two unavailable legacy portraits are listed separately in the manifest. See the [seed asset guide](database/seeders/assets/README.md).

The website's contextual images are a coordinated set generated specifically for Jedidiah Cares using the built-in OpenAI image generation tool on 14 October 2026. The scenes draw on contemporary southeastern Nigerian settings, the organisation's programme themes and its navy/lemon identity. They depict fictional people and illustrative activities; they are not evidence of delivered programmes or portraits of actual staff or beneficiaries. Existing organisation photographs, project artwork, gallery records and founder/team portraits retain their original provenance.

The final [prompt set](output/imagegen/prompts.json) is retained with the project. [Asset dimensions and file sizes](output/imagegen/assets.json) are recorded separately. Active files live in `public/images/generated/`, with a 1200-pixel WebP and a 600-pixel responsive version for each scene. The website loads them locally and retains lazy loading, priority loading for hero images, descriptive alternative text and reserved image dimensions.

| Asset | Context |
| --- | --- |
| `generated/jedidiah-community-connection.webp` | About: intergenerational connection in a community courtyard |
| `generated/jedidiah-education-support.webp` | Education programme and homepage education feature: shared reading with a mentor |
| `generated/jedidiah-youth-mentoring.webp` | Youth Development & Drug Abuse Prevention: a supportive discussion circle |
| `generated/jedidiah-women-skills.webp` | Women & Community Empowerment: collaborative tailoring and practical skills |
| `generated/jedidiah-volunteer-care.webp` | Volunteering and the About approach section: preparing practical community support |
| `generated/jedidiah-girls-wellbeing.webp` | Girls’ Health, Safety & Empowerment: an encouraging school discussion |

Each scene also has a sibling filename ending in `-600.webp`. The `generated` and `representative` flags in `media.php` retain the image's illustrative status internally. These assets are not imported into the authentic activity galleries, evidence, testimonials or team records.

The following earlier Pexels files are retained as inactive assets; active contextual photo references have been replaced. Their original [Pexels licence](https://www.pexels.com/license/) and credits remain recorded here.

| Asset                                   | Photographer       | Source                                                                                                        |
| --------------------------------------- | ------------------ | ------------------------------------------------------------------------------------------------------------- |
| `community-gathering.webp`              | Daniwura TCI       | [Women gathering](https://www.pexels.com/photo/african-women-in-traditional-attire-gathering-28702872/)       |
| `learning-resources.webp`               | Yazid N            | [Stack of books](https://www.pexels.com/photo/stack-of-books-in-hands-18698754/)                              |
| `learning-together.webp`                | Charlotte May      | [A learner making notes](https://www.pexels.com/photo/black-woman-correcting-text-on-paper-at-table-5965902/) |
| `skills-workshop.webp`                  | cottonbro studio   | [Adult woman sewing](https://www.pexels.com/photo/a-woman-sewing-a-fabric-9541053/)                           |
| `community-care.webp`                   | Julia M Cameron    | [Food preparation](https://www.pexels.com/photo/people-preparing-food-packs-6995247/)                         |
| `community-team.webp` (unused option)   | RDNE Stock project | [Volunteers with supplies](https://www.pexels.com/photo/food-love-people-street-6646864/)                     |
| `practical-skills.webp` (unused option) | Anna Tarazevich    | [Sewing workshop](https://www.pexels.com/photo/woman-in-a-black-shirt-working-near-a-sewing-machine-6358873/) |

Organisation-approved authentic photographs can replace contextual images when appropriate. Photographs involving children or sensitive programme participants require appropriate consent and safe, accurate captions.

`Art.tsx` contains original vector illustrations. The social SVG/PNG are original brand graphics; `scripts/generate-social.swift` is an optional macOS authoring helper, not a build or hosting dependency.

The Founder’s Note uses Dr. Ebere Ubesie’s real portrait, with her founder identity and title supplied by the project owner. Existing organisational portraits were retrieved on 14 October 2026 and bundled as optimised JPEGs, retaining the subjects and original appearance.

| Asset                    | Existing source                                                                          |
| ------------------------ | ---------------------------------------------------------------------------------------- |
| `team/ebere-ubesie.jpg`  | [Ebere Ubesie portrait](https://jedidiahcares.org/assets/images/team/Ebere_Ubesie.jpg)   |
| `team/agozie-ubesie.jpg` | [Agozie Ubesie portrait](https://jedidiahcares.org/assets/images/team/Agozie_Ubesie.jpg) |

Other director and volunteer image URLs are retained in `people.php` as provenance. Their old admin host currently fails its TLS connection, so those images are not bundled or presented as loaded. Name initials appear instead. No stock photographs substitute for missing team portraits.

Inter and Manrope are self-hosted through Fontsource with their package font licences. The locally owned button and accordion follow [shadcn/ui](https://github.com/shadcn-ui/ui) patterns using Radix primitives. See `THIRD_PARTY_NOTICES.md`.
