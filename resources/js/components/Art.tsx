export function Art({
  variant = 'hero',
  className = '',
}: {
  variant?: string;
  className?: string;
}) {
  const education = variant === 'hero' || variant === 'education';
  return (
    <div className={`art art-${variant} ${className}`} aria-hidden="true">
      <svg viewBox="0 0 600 600" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <pattern id={`grid-${variant}`} width="30" height="30" patternUnits="userSpaceOnUse">
            <path d="M30 0H0V30" stroke="currentColor" strokeOpacity=".075" />
          </pattern>
        </defs>
        <rect width="600" height="600" fill="var(--art-bg)" />
        <rect width="600" height="600" fill={`url(#grid-${variant})`} />
        <path
          d="M110 510V260C110 155 190 85 300 85C410 85 490 155 490 260V510H110Z"
          fill="var(--art-arch)"
        />
        <circle cx="395" cy="183" r="81" fill="var(--jc-lemon)" />
        <path d="M45 510H555" stroke="var(--jc-navy)" strokeWidth="2" strokeOpacity=".25" />
        <path
          d="M451 57V82M438 70H464M141 169V188M131 178H151"
          stroke="var(--jc-navy)"
          strokeWidth="3"
          strokeLinecap="round"
        />
        <circle cx="103" cy="104" r="6" stroke="var(--jc-navy)" strokeWidth="2" />
        <path
          d="M75 395C33 312 76 237 131 225"
          stroke="var(--jc-navy)"
          strokeWidth="2"
          strokeDasharray="5 7"
        />
        {education ? (
          <>
            <path d="M202 510V454H283V398H365V342H490V510H202Z" fill="var(--jc-navy)" />
            <path
              d="M214 456H280M293 400H362M375 345H489"
              stroke="white"
              strokeOpacity=".16"
              strokeWidth="2"
            />
            <g transform="rotate(-12 265 295)">
              <rect x="125" y="187" width="231" height="267" rx="9" fill="var(--jc-navy)" />
              <rect x="139" y="178" width="224" height="263" rx="7" fill="var(--jc-white)" />
              <rect x="159" y="178" width="5" height="263" fill="var(--jc-lemon)" />
              <path
                d="M186 220H327M186 239H298M186 313H327M186 334H327M186 355H300"
                stroke="var(--jc-navy)"
                strokeOpacity=".2"
                strokeWidth="3"
              />
              <path
                d="M211 280C230 251 255 258 270 280C285 258 311 251 328 280C312 299 287 306 270 318C252 306 229 299 211 280Z"
                fill="var(--jc-lemon)"
              />
              <path d="M180 197V413" stroke="var(--jc-navy)" strokeOpacity=".08" strokeWidth="2" />
            </g>
            <g transform="rotate(27 398 315)">
              <path d="M387 219H410V411L398 440L387 411V219Z" fill="var(--jc-lemon)" />
              <path d="M398 230V407" stroke="var(--jc-navy)" strokeOpacity=".25" strokeWidth="2" />
              <path d="M387 411L398 440L410 411" fill="var(--jc-white)" />
              <path d="M394 430L398 440L402 430" fill="var(--jc-navy)" />
              <rect x="387" y="201" width="23" height="24" rx="4" fill="var(--art-warm)" />
            </g>
          </>
        ) : variant === 'youth-drug-prevention' ? (
          <>
            <circle
              cx="293"
              cy="317"
              r="129"
              fill="var(--jc-white)"
              stroke="var(--jc-navy)"
              strokeWidth="9"
            />
            <circle
              cx="293"
              cy="317"
              r="102"
              stroke="var(--jc-navy)"
              strokeOpacity=".2"
              strokeWidth="2"
            />
            <path d="M294 212L339 350L294 326L249 350L294 212Z" fill="var(--jc-navy)" />
            <path d="M294 423L249 284L294 309L339 284L294 423Z" fill="var(--jc-lemon)" />
            <circle cx="294" cy="317" r="14" fill="var(--art-warm)" />
            <path
              d="M292 193V218M292 414V440M168 317H192M395 317H419"
              stroke="var(--jc-navy)"
              strokeWidth="5"
            />
            <path
              d="M143 464C181 502 279 496 332 479"
              stroke="var(--jc-navy)"
              strokeWidth="3"
              strokeDasharray="7 7"
            />
          </>
        ) : variant === 'girls-health' ? (
          <>
            <path
              d="M302 488V319M302 424C247 424 229 394 230 367C277 364 301 386 302 424ZM302 388C354 388 379 355 377 329C329 330 304 351 302 388Z"
              fill="var(--jc-teal)"
              stroke="var(--jc-teal)"
              strokeWidth="7"
            />
            <path
              d="M302 194C232 108 174 203 232 258C129 257 148 363 232 345C194 444 292 461 302 367C335 460 421 415 378 345C475 363 477 250 378 258C439 176 352 137 302 194Z"
              fill="var(--art-warm)"
              stroke="var(--jc-navy)"
              strokeWidth="3"
            />
            <circle
              cx="302"
              cy="287"
              r="49"
              fill="var(--jc-lemon)"
              stroke="var(--jc-navy)"
              strokeWidth="3"
            />
            <path
              d="M270 291L292 312L336 264"
              stroke="var(--jc-navy)"
              strokeWidth="6"
              strokeLinecap="round"
              strokeLinejoin="round"
            />
          </>
        ) : (
          <>
            <path
              d="M284 447V225M284 328C205 328 181 272 182 239C250 239 284 279 284 328ZM284 376C365 376 403 322 401 285C327 286 287 325 284 376Z"
              fill="var(--jc-teal)"
              stroke="var(--jc-teal)"
              strokeWidth="7"
            />
            <path
              d="M285 267C263 209 277 162 320 141C347 198 331 242 285 267Z"
              fill="var(--jc-teal)"
            />
            <path
              d="M208 409H366L348 510H227L208 409Z"
              fill="var(--art-warm)"
              stroke="var(--jc-navy)"
              strokeWidth="3"
            />
            <path
              d="M227 435H348M231 455H344"
              stroke="var(--jc-navy)"
              strokeOpacity=".25"
              strokeWidth="2"
            />
            <path d="M407 510V443H492V510H407Z" fill="var(--jc-navy)" />
            <path d="M406 456H491" stroke="var(--jc-lemon)" strokeWidth="5" />
          </>
        )}
        <path
          d="M119 484C137 455 151 425 152 383M152 430C130 419 117 400 118 384C142 390 151 406 152 430M150 452C175 439 182 421 180 407C160 415 153 433 150 452"
          stroke="var(--jc-teal)"
          strokeWidth="5"
          fill="var(--jc-teal)"
        />
        <path
          d="M119 477H175L167 510H128L119 477Z"
          fill="var(--art-warm)"
          stroke="var(--jc-navy)"
          strokeWidth="2"
        />
        <path
          d="M510 261L515 275L530 279L516 285L512 300L506 286L491 281L505 275L510 261Z"
          fill="var(--jc-navy)"
        />
        <circle cx="458" cy="470" r="7" fill="var(--jc-lemon)" />
      </svg>
    </div>
  );
}
