/** A font-independent, eight-spoked decorative mark. */
export function BrandAsterisk({ className = '' }: { className?: string }) {
  return (
    <span className={`brand-asterisk ${className}`.trim()} aria-hidden="true">
      <svg viewBox="0 0 100 100" width="1em" height="1em" focusable="false">
        <path
          d="M50 8v84M8 50h84M20.3 20.3l59.4 59.4M20.3 79.7l59.4-59.4"
          fill="none"
          stroke="currentColor"
          strokeWidth="6"
          strokeLinecap="round"
        />
      </svg>
    </span>
  );
}
