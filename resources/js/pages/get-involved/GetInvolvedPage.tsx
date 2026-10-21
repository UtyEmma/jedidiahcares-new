import { useContent } from '../../content';

import { Seo } from '../../lib/seo';
import { Art } from '../../components/Art';
import { ButtonLink, CTA, Eyebrow, Icon, PageHero } from '../../components/UI';

export default function GetInvolvedPage() {
  const { pages, site, ui, routes, sections } = useContent();
  const labels = sections['get-involved'];
  return (
    <>
      <Seo />
      <PageHero
        title={pages['get-involved'].headline!}
        text={pages['get-involved'].fields['Introduction']}
        eyebrow={labels.heroEyebrow}
        current={labels.heroCurrent}
        art="community-empowerment"
      />
      <section className="section">
        <div className="max-w-7xl mx-auto container involvement-paths">
          {site.involvement.map((item, index) => (
            <article className={`involvement-path involvement-path-${index}`} key={item.href}>
              <div>
                <span className="small-label">
                  0{index + 1} · {labels.pathEyebrow}
                </span>
                <h2>{item.title}</h2>
                <p>{item.description}</p>
                <ButtonLink to={item.href} variant={index === 1 ? 'lemon' : 'primary'}>
                  {item.label}
                </ButtonLink>
              </div>
              <Icon name={item.icon} size={120} />
            </article>
          ))}
        </div>
      </section>
      <section className="section soft-section">
        <div className="max-w-7xl mx-auto container split-grid">
          <div>
            <Eyebrow>{labels.hostEyebrow}</Eyebrow>
            <h2>
              {ui.getInvolved.hostTitle[0]}
              <br />
              {ui.getInvolved.hostTitle[1]}
            </h2>
            <p>{pages.programmes.fields['Closing Invitation']}</p>
            <ButtonLink
              to={`${routes.contact}?${new URLSearchParams({ topic: 'Host a programme' })}`}
            >
              {labels.hostButton}
            </ButtonLink>
          </div>
          <Art variant="education" />
        </div>
      </section>
      <CTA />
    </>
  );
}
