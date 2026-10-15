import { useContent } from '../content';
import { useState } from 'react';

import { Art } from './Art';
import { cn } from '@/lib/utils';
export function Photo({
  id,
  priority = false,
  className,
}: {
  id: string;
  priority?: boolean;
  className?: string;
  caption?: boolean;
}) {
  const { media } = useContent();
  const photo = media[id];
  const [failed, setFailed] = useState(false);
  if (!photo || failed) return <Art variant="education" className={className} />;
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
        onError={() => setFailed(true)}
      />
    </figure>
  );
}
