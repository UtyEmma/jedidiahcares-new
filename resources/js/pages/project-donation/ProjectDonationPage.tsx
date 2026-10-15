import { useContent } from '../../content';
import type { WebsitePageProps } from '../../types/inertia';
import { usePage } from '@inertiajs/react';

import DonationContent from '../../components/DonationContent';
import NotFoundPage from '../not-found/NotFoundPage';

export default function ProjectDonationPage() {
  const { findProject } = useContent();
  const { projectSlug = '' } = usePage<WebsitePageProps>().props;
  const project = findProject(projectSlug);
  if (!project) return <NotFoundPage />;
  return <DonationContent project={project} />;
}
