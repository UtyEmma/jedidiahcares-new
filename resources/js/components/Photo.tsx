import { useContent } from '../content';
import { useState } from 'react';

import { Art } from './Art';
import { cn } from '@/lib/utils';
export function Photo({
  id,
  priority = false,
  className,
  image,
}: {
  id: string;
  priority?: boolean;
  className?: string;
  caption?: boolean;
  image?: { src: string; alt: string };
}) {
  const { media } = useContent();
  const base = media[id];
  const photo = image ? { ...base, ...image, smallSrc: undefined } : base;
  const [failedSource, setFailedSource] = useState('');
  if (!photo || failedSource === photo.src)
    return <Art variant="education" className={className} />;
  return (
    <figure className={cn('context-photo', className)}>
      <img
        src={photo.src}
        srcSet={photo.smallSrc ? `${photo.smallSrc} 600w, ${photo.src} ${photo.width}w` : undefined}
        sizes="(min-width: 1280px) 600px, (min-width: 641px) 45vw, 90vw"
        alt={photo.alt}
        width={photo.width}
        height={photo.height}
        style={photo.objectPosition ? { objectPosition: photo.objectPosition } : undefined}
        loading={priority ? 'eager' : 'lazy'}
        fetchPriority={priority ? 'high' : 'auto'}
        decoding="async"
        onError={() => setFailedSource(photo.src)}
      />
    </figure>
  );
}
