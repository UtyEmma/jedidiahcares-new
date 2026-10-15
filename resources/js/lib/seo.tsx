import { Head, usePage } from '@inertiajs/react';
import type { WebsitePageProps } from '../types/inertia';

export function Seo() {
  const { seo, content } = usePage<WebsitePageProps>().props;
  return (
    <Head title={seo.title}>
      <style head-key="brand-colours">{`:root { --jc-navy: ${content.site.brand.primaryColor}; --jc-lemon: ${content.site.brand.accentColor}; }`}</style>
      <meta head-key="theme-color" name="theme-color" content={content.site.brand.primaryColor} />
      <link head-key="favicon" rel="icon" href={content.site.brand.faviconPath} type="image/webp" />
      <meta head-key="description" name="description" content={seo.description} />
      <meta head-key="robots" name="robots" content={seo.robots} />
      <link head-key="canonical" rel="canonical" href={seo.canonical} />
      <meta head-key="og-title" property="og:title" content={seo.title} />
      <meta head-key="og-description" property="og:description" content={seo.description} />
      <meta head-key="og-url" property="og:url" content={seo.canonical} />
      <meta head-key="og-type" property="og:type" content={seo.type} />
      <meta head-key="og-image" property="og:image" content={seo.image} />
      <meta head-key="twitter-card" name="twitter:card" content="summary_large_image" />
      <meta head-key="twitter-title" name="twitter:title" content={seo.title} />
      <meta head-key="twitter-description" name="twitter:description" content={seo.description} />
      <meta head-key="twitter-image" name="twitter:image" content={seo.image} />
      {seo.structuredData && (
        <script
          head-key="structured-data"
          type="application/ld+json"
          dangerouslySetInnerHTML={{
            __html: JSON.stringify(seo.structuredData).replace(/</g, '\\u003c'),
          }}
        />
      )}
    </Head>
  );
}
