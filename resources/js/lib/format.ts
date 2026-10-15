export function dateLabel(value: string, timezone = 'Africa/Lagos') {
  const date = new Date(value);
  return Number.isNaN(date.getTime())
    ? 'Date to be confirmed'
    : new Intl.DateTimeFormat('en-NG', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        timeZone: timezone,
      }).format(date);
}
