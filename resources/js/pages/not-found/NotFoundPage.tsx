import { Seo } from '../../lib/seo';
import { Art } from '../../components/Art';
import { BrandAsterisk } from '../../components/BrandAsterisk';
import { ButtonLink, Eyebrow } from '../../components/UI';

export default function NotFoundPage() {
  return (
    <>
      <Seo />
      <section className="section not-found">
        <div className="max-w-7xl mx-auto container">
          <div>
            <Eyebrow>A LITTLE DETOUR</Eyebrow>
            <p className="not-found-code" aria-hidden="true">
              404
              <BrandAsterisk />
            </p>
            <h1>
              Let’s find your way
              <br />
              back to possibility.
            </h1>
            <p>This page isn’t available. Our programme areas are a good place to begin.</p>
            <div className="hero-actions">
              <ButtonLink to="/">Back to home</ButtonLink>
              <ButtonLink to="/programmes" variant="outline">
                Explore programmes
              </ButtonLink>
            </div>
          </div>
          <Art variant="youth-drug-prevention" />
        </div>
      </section>
    </>
  );
}
