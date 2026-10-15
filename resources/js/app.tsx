import { createInertiaApp } from '@inertiajs/react';
import type { ComponentType, ReactNode } from 'react';
import '@fontsource/manrope/latin-600.css';
import '@fontsource/manrope/latin-700.css';
import '@fontsource/manrope/latin-800.css';
import '@fontsource/inter/latin-400.css';
import '@fontsource/inter/latin-500.css';
import '@fontsource/inter/latin-600.css';
import './styles/tokens.css';
import './styles/tailwind.css';
import './styles/globals.css';
import './styles/refinements.css';
import './styles/people.css';
import './styles/events.css';
import './styles/projects.css';
import './styles/gallery.css';
import './styles/sharing.css';
import './styles/motion.css';
import { Layout } from './components/Layout';

type PageComponent = ComponentType & { layout?: (page: ReactNode) => ReactNode };
const pages = import.meta.glob<{ default: PageComponent }>('./pages/**/*.tsx');

createInertiaApp({
  strictMode: true,
  resolve: async (name) => {
    const load = pages[`./pages/${name}.tsx`];
    if (!load) throw new Error(`Unknown Inertia page: ${name}`);
    const page = await load();
    page.default.layout = (content) => <Layout>{content}</Layout>;
    return page.default;
  },
  progress: { color: '#072266', showSpinner: false },
});
