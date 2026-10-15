export function publicShareUrl(canonical: string, query: Record<string, string> = {}) {
  try {
    const url = new URL(canonical);
    if (!['https:', 'http:'].includes(url.protocol) || url.username || url.password)
      return undefined;
    url.hash = '';
    for (const [key, value] of Object.entries(query)) url.searchParams.set(key, value);
    return url.href;
  } catch {
    return undefined;
  }
}

export function buildShareLinks(url: string, title: string, description = '') {
  const subject = title.replace(/[\r\n]+/g, ' ').trim();
  const message = [subject, description.trim(), url].filter(Boolean).join('\n\n');
  const encodedUrl = encodeURIComponent(url);
  return {
    facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`,
    x: `https://twitter.com/intent/tweet?url=${encodedUrl}&text=${encodeURIComponent(subject)}`,
    linkedin: `https://www.linkedin.com/sharing/share-offsite/?url=${encodedUrl}`,
    whatsapp: `https://wa.me/?text=${encodeURIComponent(`${subject}\n${url}`)}`,
    email: `mailto:?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(message)}`,
  };
}
