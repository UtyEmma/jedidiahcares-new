import { useState } from 'react';
import { router } from '@inertiajs/react';
import { useContent } from '../../content';
import { Seo } from '../../lib/seo';
import { CTA, EmptyState, PageHero } from '../../components/UI';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '../../components/ui/tabs';
import { ShareButtons } from '../../components/ShareButtons';
import { GalleryMediaGrid } from '../../components/gallery/GalleryMediaGrid';

export default function GalleryPage() {
  const { pages, galleries, selectedGallery } = useContent();
  const [loading, setLoading] = useState(false);
  const [message, setMessage] = useState('');

  function selectGallery(slug: string) {
    if (slug === selectedGallery?.slug || loading) return;
    setMessage('');
    router.get(
      '/gallery',
      { gallery: slug },
      {
        only: ['content'],
        preserveScroll: true,
        preserveState: true,
        onStart: () => setLoading(true),
        onFinish: () => setLoading(false),
        onError: () => setMessage('Unable to load this gallery. Please try again.'),
      },
    );
  }

  return (
    <>
      <Seo />
      <PageHero
        title={pages.gallery.headline!}
        text={pages.gallery.fields['Intro']}
        eyebrow="PHOTO & VIDEO GALLERY"
        current="Gallery"
      />
      <section className="section">
        <div className="max-w-7xl mx-auto container">
          {galleries.length > 0 && selectedGallery ? (
            <Tabs
              value={selectedGallery.slug}
              onValueChange={selectGallery}
              activationMode="manual"
            >
              <TabsList aria-label="Our galleries">
                {galleries.map((gallery) => (
                  <TabsTrigger key={gallery.id} value={gallery.slug} disabled={loading}>
                    {gallery.title}
                  </TabsTrigger>
                ))}
              </TabsList>
              <p className="sr-only" role="status">
                {loading ? 'Loading gallery…' : message}
              </p>
              {message && <p role="alert">{message}</p>}
              <TabsContent value={selectedGallery.slug} aria-busy={loading}>
                <div className="gallery-introduction">
                  <p className="eyebrow">MOMENTS FROM OUR WORK</p>
                  <h2>{selectedGallery.title}</h2>
                  <p>{selectedGallery.summary}</p>
                </div>
                {selectedGallery.media.length ? (
                  <GalleryMediaGrid
                    key={selectedGallery.id}
                    gallery={selectedGallery}
                    switching={loading}
                  />
                ) : (
                  <div className="gallery-empty">
                    <h3>More moments to share.</h3>
                    <p>
                      Photos and videos from this gallery will appear here as they become available.
                    </p>
                  </div>
                )}
                <ShareButtons
                  title={selectedGallery.title}
                  description={selectedGallery.summary}
                  label="Share this gallery"
                  query={{ gallery: selectedGallery.slug }}
                />
              </TabsContent>
            </Tabs>
          ) : (
            <EmptyState kind="gallery" />
          )}
        </div>
      </section>
      <CTA />
    </>
  );
}
