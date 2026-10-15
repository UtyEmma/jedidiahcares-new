import { useContent } from '../../content';
import type { WebsitePageProps } from '../../types/inertia';
import { usePage } from '@inertiajs/react';

import { Seo } from '../../lib/seo';
import { dateLabel } from '../../lib/format';
import { CTA, Media, PageHero, TextLink } from '../../components/UI';
import { ShareButtons } from '../../components/ShareButtons';
import NotFoundPage from '../not-found/NotFoundPage';

export default function StoryDetailPage() {
  const { findStory, routes } = useContent();
  const { slug = '' } = usePage<WebsitePageProps>().props;
  const story = findStory(slug);
  if (!story) return <NotFoundPage />;
  return (
    <>
      <Seo />
      <PageHero title={story.title} text={story.summary} eyebrow={story.category} current="Story" />
      <article className="section">
        <div className="max-w-5xl mx-auto container">
          <div className="article-meta">
            <span>{dateLabel(story.publishedAt)}</span>
            {story.author && <span>By {story.author}</span>}
          </div>
          <Media src={story.coverImage} alt={story.coverAlt} />
          <div className="prose min-w-full max-w-full article-prose">
            {story.blocks.map((block, index) =>
              block.type === 'bullets' ? (
                <ul key={index}>
                  {block.items.map((item) => (
                    <li key={item}>{item}</li>
                  ))}
                </ul>
              ) : block.type === 'heading' ? (
                <h2 key={index}>{block.text}</h2>
              ) : block.type === 'quote' ? (
                <blockquote key={index}>
                  {block.text}
                  {block.attribution && <cite>{block.attribution}</cite>}
                </blockquote>
              ) : (
                <p key={index}>{block.text}</p>
              ),
            )}
          </div>
          <ShareButtons title={story.title} description={story.summary} label="Share this story" />
          <div className="article-back">
            <TextLink to={routes.stories}>Back to stories</TextLink>
          </div>
        </div>
      </article>
      <CTA />
    </>
  );
}
