import type { Project } from '../types/content';
import { projectFunding } from '../lib/projects';

export function ProjectFunding({
  project,
  variant = 'card',
}: {
  project: Project;
  variant?: 'card' | 'detail';
}) {
  const funding = projectFunding(project);
  if (!funding) return null;

  const money = (value: number) =>
    new Intl.NumberFormat('en-NG', {
      style: 'currency',
      currency: funding.currency,
      minimumFractionDigits: Number.isInteger(value) ? 0 : 2,
      maximumFractionDigits: 2,
    }).format(value);
  const percentage = new Intl.NumberFormat('en-NG', { maximumFractionDigits: 1 }).format(
    funding.percentage,
  );
  const raised = money(funding.raised);
  const target = money(funding.target);

  return (
    <div className={`project-funding project-funding-${variant}`}>
      <p className="project-funding-label">Funding progress</p>
      <div className="project-funding-summary">
        <p>
          <strong>{raised}</strong> <span>raised</span>
        </p>
        <span className="project-funding-percentage">{percentage}% funded</span>
      </div>
      <progress
        className="project-funding-bar"
        max={100}
        value={Math.min(100, funding.percentage)}
        aria-label={`Funding progress for ${project.title}`}
        aria-valuetext={`${percentage}% funded. ${raised} raised of a ${target} target.`}
      />
      <p className="project-funding-target">Target: {target}</p>
    </div>
  );
}
