import { useContent } from '../content';
import { useState } from 'react';

import { Quotes } from './icons';
import { Eyebrow, TextLink } from './UI';

export function FounderNote({ compact = false }: { compact?: boolean }) {
  const { site } = useContent();
  const note = site.founderNote;
  const [failedPortrait, setFailedPortrait] = useState('');
  const showPortrait = Boolean(note.portraitPath && failedPortrait !== note.portraitPath);
  if (!note.enabled) return null;
  return (
    <section
      id="founder-note"
      className={`section founder-note-section ${compact ? 'founder-note-compact' : ''}`}
      aria-labelledby={`founder-note-${compact ? 'home' : 'about'}`}
    >
      <div className="max-w-7xl mx-auto container">
        <div className="founder-note-layout" data-entrance>
          <div className="founder-note-portrait">
            {showPortrait ? (
              <img
                src={note.portraitPath}
                alt={
                  note.portraitAlt ||
                  (note.name ? `Portrait of ${note.name}` : `Founder of ${site.brand.publicName}`)
                }
                width="600"
                height="750"
                loading="lazy"
                decoding="async"
                onError={() => setFailedPortrait(note.portraitPath)}
              />
            ) : (
              <div className="founder-portrait-placeholder" aria-hidden="true">
                <span className="founder-portrait-initials">
                  {note.name
                    .replace(/^Dr\.\s*/i, '')
                    .split(' ')
                    .map((part) => part[0])
                    .slice(0, 2)
                    .join('')}
                </span>
                <span>{note.motifLines.join(' ')}</span>
              </div>
            )}
            <div className="founder-portrait-quote" aria-hidden="true">
              <Quotes size={40} weight="duotone" />
            </div>
          </div>
          <div className="founder-note-copy">
            <Eyebrow>{note.label}</Eyebrow>
            <h2 id={`founder-note-${compact ? 'home' : 'about'}`}>{note.headline}</h2>
            <p className="founder-note-message">{note.text}</p>
            <div className="founder-note-signature">
              <div>
                {note.name && <strong>{note.name}</strong>}
                <span>{note.role}</span>
              </div>
              {compact && <TextLink to="/about#founder-note">More about our purpose</TextLink>}
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
