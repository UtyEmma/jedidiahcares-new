import type { Project } from '../types/content';

export const projectStatusLabels: Record<Project['stage'], string> = {
  planned: 'Upcoming',
  active: 'Ongoing',
  completed: 'Completed',
};

export function projectFunding(
  project: Pick<Project, 'fundingTarget' | 'fundsRaised' | 'fundingCurrency'>,
) {
  const target = project.fundingTarget;
  if (typeof target !== 'number' || !Number.isFinite(target) || target <= 0) return null;

  const raised = Math.max(0, project.fundsRaised ?? 0);
  if (!Number.isFinite(raised)) return null;

  return {
    target,
    raised,
    currency: project.fundingCurrency ?? 'NGN',
    percentage: (raised / target) * 100,
  };
}
