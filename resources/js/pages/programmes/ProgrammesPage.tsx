import { useContent } from '../../content';

import { Seo } from '../../lib/seo';
import { Art } from '../../components/Art';
import { Photo } from '../../components/Photo';
import { ButtonLink, CTA, FAQList, Icon, PageHero } from '../../components/UI';

export default function ProgrammesPage() {
  const { pages, programmes, ui } = useContent();
  const copy = pages.programmes;
  return (
    <>
      <Seo />
      <PageHero
        title={copy.headline!}
        text={copy.fields['Intro']}
        eyebrow="OUR PROGRAMMES"
        current="Our programmes"
      />
      <section className="section">
        <div className="max-w-7xl mx-auto container programme-features">
          {programmes.map((item, index) => (
            <article
              key={item.id}
              className={`programme-feature ${index % 2 ? 'programme-feature-reverse' : ''}`}
            >
              {item.photoId ? <Photo id={item.photoId} /> : <Art variant={item.illustration} />}
              <div>
                <div className="programme-feature-meta">
                  <span>{item.number}</span>
                  <Icon name={item.icon} size={32} />
                </div>
                <p className="small-label">{item.summary}</p>
                <h2>{item.title}</h2>
                <p>{item.introduction}</p>
                <ul className="activity-tags">
                  {item.activities.map((activity) => (
                    <li key={activity}>{activity}</li>
                  ))}
                </ul>
                <ButtonLink to={item.detailUrl}>Explore this programme</ButtonLink>
              </div>
            </article>
          ))}
        </div>
      </section>
      <section className="section">
        <div className="max-w-7xl mx-auto container">
          <FAQList />
        </div>
      </section>
      <CTA title={ui.programme.hostTitle} text={copy.fields['Closing Invitation']} />
    </>
  );
}
