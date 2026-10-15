export function buildMailto(
  destination: string,
  subject: string,
  fields: { name: string; replyEmail: string; message: string },
) {
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(destination) || /[\r\n]/.test(destination))
    return undefined;
  const body = `Name: ${fields.name}\nReply email: ${fields.replyEmail}\n\n${fields.message}`;
  return `mailto:${destination}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
}
