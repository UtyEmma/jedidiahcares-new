import type { Project } from '../types/content';
import { projectStatusLabels } from '../lib/projects';

export function ProjectStatusBadge({ stage }: { stage: Project['stage'] }) {
  return (
    <span className={`project-status project-status-${stage}`}>{projectStatusLabels[stage]}</span>
  );
}
